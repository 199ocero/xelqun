<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Order;
use App\Xelqun\Admin\Forms\OrderForm;
use App\Xelqun\Admin\Resources\OrderResource\ItemsRelationManager;
use App\Xelqun\Admin\Resources\OrderResource\TagsRelationManager;
use App\Xelqun\Admin\Widgets\OrderStats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Notifications\Notification;
use Xelqun\Panels\Actions\DeleteAction;
use Xelqun\Panels\Actions\EditAction;
use Xelqun\Panels\Actions\ForceDeleteAction;
use Xelqun\Panels\Actions\ReplicateAction;
use Xelqun\Panels\Actions\RestoreAction;
use Xelqun\Panels\Actions\ViewAction;
use Xelqun\Panels\Infolists\IconEntry;
use Xelqun\Panels\Infolists\RepeatableEntry;
use Xelqun\Panels\Infolists\TextEntry;
use Xelqun\Panels\Navigation\NavigationItem;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Panels\Resources\Tab;
use Xelqun\Panels\Tables\TrashedFilter;
use Xelqun\Tables\Actions\BulkAction;
use Xelqun\Tables\Columns\IconColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Filters\TernaryFilter;
use Xelqun\Tables\Summarizers\Average;
use Xelqun\Tables\Summarizers\Count;
use Xelqun\Tables\Summarizers\Range;
use Xelqun\Tables\Summarizers\Sum;
use Xelqun\Tables\Table;

/**
 * Everything at once: a nested form, a table with every kind of filter, modals,
 * action groups, bulk actions, list tabs, an infolist and a relation manager.
 */
class OrderResource extends Resource
{
    protected static string $model = Order::class;

    protected static ?string $recordTitleAttribute = 'customer_name';

    protected static ?string $navigationIcon = 'shopping-cart';

    protected static ?string $navigationGroup = 'Shop';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('country');
    }

    public static function getNavigationBadge(): int
    {
        return static::getEloquentQuery()->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['customer_name', 'email', 'company'];
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): string
    {
        return 'Every order';
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [NavigationItem::make('Edit')->url(static::getUrl('edit', ['record' => $record]))];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['Total' => '$'.$record->getAttribute('total'), 'Email' => $record->getAttribute('email')];
    }

    public static function form(): array
    {
        return OrderForm::make()->schema();
    }

    public static function mutateBeforeSave(array $attributes): array
    {
        return OrderForm::make()->mutateBeforeSave($attributes);
    }

    public static function infolist(): array
    {
        return [
            Section::make('Customer')->columns(3)->schema([
                TextEntry::make('customer_name')->label('Name'),
                TextEntry::make('email')->icon('mail'),
                TextEntry::make('type')->badge()->color(fn (string $state): string => $state === 'business' ? 'info' : 'gray'),
                TextEntry::make('company'),
                TextEntry::make('vat_number')->label('VAT number'),
                TextEntry::make('country.name')->label('Country'),
            ]),
            Section::make('Line items')->schema([
                RepeatableEntry::make('items')->label('')->schema([
                    TextEntry::make('product'),
                    TextEntry::make('subtotal')->money('USD'),
                ]),
            ]),
            Section::make('Delivery')->columns(3)->schema([
                TextEntry::make('delivery_date')->date(),
                TextEntry::make('total')->money('USD'),
                IconEntry::make('is_gift')->label('Gift')->boolean(),
                TextEntry::make('notes')->columnSpanFull(),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('customer_name')->label('Customer')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('type')->badge()->color(fn (string $state): string => $state === 'business' ? 'info' : 'gray'),
            TextColumn::make('country.name')->label('Country')->sortable(),
            IconColumn::make('is_gift')->label('Gift')->boolean(),
            TextColumn::make('delivery_date')->date()->sortable(),
            TextColumn::make('total')->money('USD')->sortable()->summarize([Sum::make()->label('Total'), Average::make()->label('Average'), Range::make()->label('Range'), Count::make()->label('Orders')]),
        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('type')->options(['individual' => 'Individual', 'business' => 'Business']),
            SelectFilter::make('country')->relationship('country', 'name')->searchable(),
            TernaryFilter::make('is_gift')->label('Gift'),
            TrashedFilter::make('trashed')->label('Deleted orders'),
            Filter::make('big')->label('Over $500')->query(fn (Builder $query) => $query->where('total', '>', 500)),
            Filter::make('delivery')
                ->schema([
                    DatePicker::make('delivery_from')->label('Delivery from'),
                    DatePicker::make('delivery_until')->label('Delivery until'),
                ])
                ->query(fn (Builder $query, array $data) => $query
                    ->when($data['delivery_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('delivery_date', '>=', $date))
                    ->when($data['delivery_until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('delivery_date', '<=', $date))),
        ];
    }

    public static function recordActions(): array
    {
        $delete = DeleteAction::for(static::class)->button();

        return [
            EditAction::for(static::class),
            ActionGroup::make([
                ViewAction::for(static::class)->button(),
                ReplicateAction::for(static::class)->button(),
                Action::make('reschedule')
                    ->label('Reschedule')
                    ->icon('calendar')
                    ->authorize('update')
                    ->fillForm(fn (Order $record): array => ['delivery_date' => $record->delivery_date?->toDateString()])
                    ->modalHeading('Reschedule delivery')
                    ->modalDescription('The customer sees the new date on their order.')
                    ->schema([DatePicker::make('delivery_date')->required()])
                    ->modalSubmitActionLabel('Save date')
                    ->successNotificationTitle('Delivery rescheduled.')
                    ->action(fn (array $data, Order $record) => $record->update($data)),
                Action::make('note')
                    ->label('Add a note')
                    ->icon('pencil')
                    ->authorize('update')
                    ->modalWidth('lg')
                    ->fillForm(fn (Order $record): array => ['notes' => $record->notes])
                    ->schema([Textarea::make('notes')->required()->rows(4)])
                    ->successNotificationTitle('Note saved.')
                    ->action(fn (array $data, Order $record) => $record->update($data)),
                Action::make('gift')
                    ->label('Mark as a gift')
                    ->icon('check')
                    ->authorize('update')
                    ->requiresConfirmation()
                    ->modalDescription('The packing team will leave the price off.')
                    ->action(function (Order $record): void {
                        $record->update(['is_gift' => true]);

                        Notification::make()->title('Marked as a gift.')->success()->send();
                    }),
                $delete,
                RestoreAction::for(static::class)->button(),
                ForceDeleteAction::for(static::class)->button(),
            ]),
        ];
    }

    public static function bulkActions(): array
    {
        return [
            BulkAction::make('gift')
                ->label('Mark as gifts')
                ->authorize('update')
                ->requiresConfirmation()
                ->successNotificationTitle('Orders updated.')
                ->action(fn (Collection $records) => $records->each->update(['is_gift' => true])),
            ...parent::bulkActions(),
        ];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('delivery_date', 'desc')->paginationPageOptions([10, 25, 50])->groups(['type'])->poll('30s');
    }

    public static function getTabs(): array
    {
        $byType = fn (string $type): Tab => Tab::make()
            ->badge(fn (): int => Order::query()->where('type', $type)->count())
            ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type));

        return [
            'all' => Tab::make(),
            'individual' => $byType('individual'),
            'business' => $byType('business'),
            'gifts' => Tab::make()->icon('check')->modifyQueryUsing(fn (Builder $query) => $query->where('is_gift', true)),
        ];
    }

    public static function getHeaderWidgets(): array
    {
        return [OrderStats::class];
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class, TagsRelationManager::class];
    }
}
