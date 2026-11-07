<?php

namespace App\Xelqun\Admin\Forms;

use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\MorphToSelect;
use Xelqun\Forms\Components\MorphToSelect\Type;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Conditions\When;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Group;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

/**
 * The proof test: server-loaded states, a conditional business section, a
 * repeater with live subtotals and a grand total, uploads inside rows, nested
 * validation errors, and saving to Eloquent with a has-many relationship. The
 * same form changes on edit: the order number appears, the customer type locks
 * and the confirmation toggle goes away. Tags, a morph owner and a gift
 * recipient are saved to their own tables in one transaction, and each line
 * item can have extras, a second level of rows.
 *
 * @extends Form<Order>
 */
class OrderForm extends Form
{
    /**
     * @return class-string<Order>
     */
    public function model(): string
    {
        return Order::class;
    }

    public function schema(): array
    {
        return [
            Section::make('Customer')->columns(2)->schema([
                TextInput::make('id')
                    ->label('Order number')
                    ->prefix('#')
                    ->visibleOn('edit')
                    ->readOnly()
                    ->formatStateUsing(fn (?int $state): string => Str::padLeft((string) $state, 6, '0'))
                    ->columnSpanFull(),
                TextInput::make('customer_name')->label('Name')->required()->maxLength(120)->autofocus(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->dehydrateStateUsing(fn (string $state): string => Str::lower($state)),
                Radio::make('type')
                    ->label('Customer type')
                    ->options(['individual' => 'Individual', 'business' => 'Business'])
                    ->default('individual')
                    ->inline()
                    ->disabledOn('edit')
                    ->saved()
                    ->columnSpanFull(),
            ]),

            Section::make('Business details')
                ->description('Only shown, validated and saved for business customers.')
                ->visibleWhen(When::eq('type', 'business'))
                ->columns(2)
                ->schema([
                    TextInput::make('company')->requiredWhen(When::eq('type', 'business')),
                    TextInput::make('vat_number')->label('VAT number')->placeholder('PH123456789'),
                ]),

            Section::make('Shipping')->columns(2)->schema([
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Search countries')
                    ->required()
                    ->manageOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('code')->required()->length(2),
                        TextInput::make('dial_code')->required(),
                        TextInput::make('currency')->required()->length(3),
                    ])
                    ->manageOptionActions(fn (Action $action): Action => $action->authorize(fn (): bool => ! config('playground.read_only')))
                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null)),
                Select::make('state_id')
                    ->label('State / province')
                    ->options(fn (Get $get): array => State::query()
                        ->where('country_id', $get('country_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->placeholder(fn (Get $get): string => $get('country_id') ? 'Choose a state' : 'Choose a country first')
                    ->disabled(fn (Get $get): bool => blank($get('country_id')))
                    ->required(),
                DatePicker::make('delivery_date')->minDate(now()->startOfDay())->required(),
                Toggle::make('is_gift')->label('This order is a gift'),
                Toggle::make('send_confirmation')->label('Email a confirmation')->visibleOn('create')->saved(false),
            ]),

            Section::make('Gift recipient')
                ->description('Saved to its own table. While this is hidden, a saved recipient is left alone.')
                ->visibleWhen(When::eq('is_gift', true))
                ->schema([
                    Group::make([
                        TextInput::make('name')->requiredWhen(When::eq('$.is_gift', true)),
                        TextInput::make('phone')->tel(),
                    ])->relationship('giftRecipient')->columns(2),
                ]),

            Section::make('Organize')->columns(2)->schema([
                Select::make('tags')
                    ->multiple()
                    ->relationship(titleAttribute: 'name')
                    ->pivotData(fn (string $operation): array => ['added_from' => "{$operation} form"])
                    ->columnSpanFull(),
                MorphToSelect::make('owner')
                    ->label('Account owner')
                    ->types([
                        Type::make(User::class)->titleAttribute('name')->label('Person'),
                        Type::make(Country::class)
                            ->titleAttribute('name')
                            ->label('Regional office')
                            ->getOptionLabelFromRecordUsing(fn (Model $record): string => "{$record->getAttribute('name')} ({$record->getAttribute('code')})"),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]),

            Repeater::make('items')
                ->label('Line items')
                ->relationship(orderColumn: 'sort')
                ->itemLabel('Item')
                ->cloneable()
                ->requiresDeleteConfirmation()
                ->defaultItems(1)
                ->minItems(1)
                ->columns(['default' => 2, 'md' => 6])
                ->schema([
                    TextInput::make('product')->required()->columnSpan(['default' => 'full', 'md' => 3]),
                    TextInput::make('qty')->label('Qty')->numeric()->minValue(1)->default(1)->required(),
                    TextInput::make('price')->numeric()->prefix('$')->minValue(0.01)->required(),
                    TextInput::make('subtotal')->numeric()->prefix('$')->computed('round(qty * price, 2)')->disabled(),
                    FileUpload::make('attachment')
                        ->label('Spec sheet')
                        ->directory('demo/orders')
                        ->maxSize(4096)
                        ->hint('Optional. Uploaded right away, saved with the order.')
                        ->columnSpanFull(),
                    FileUpload::make('photos')
                        ->label('Photos')
                        ->relationship(orderColumn: 'sort')
                        ->directory('demo/orders/photos')
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'application/pdf'])
                        ->maxFiles(4)
                        ->reorderable()
                        ->openable()
                        ->downloadable()
                        ->hint('Up to four. Each one is a row in its own table.')
                        ->columnSpanFull(),
                    Repeater::make('extras')
                        ->relationship()
                        ->itemLabel('Extra')
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('price')->numeric()->prefix('$')->default(0)->required(),
                        ]),
                ]),

            Section::make('Summary')->columns(2)->schema([
                Textarea::make('notes')->rows(3)->placeholder(When::eq('is_gift', true)->then('Add a gift message…')->else('Anything we should know?')),
                TextInput::make('total')
                    ->label('Grand total')
                    ->numeric()
                    ->prefix('$')
                    ->computed('round(sum(items.*.subtotal), 2)')
                    ->readOnly()
                    ->inlineLabel()
                    ->extraAttributes(['data-testid' => 'grand-total'])
                    ->hint('Calculated in your browser, recalculated on the server.'),
            ]),
        ];
    }

    public function mutateBeforeSave(array $attributes): array
    {
        if (($attributes['type'] ?? null) !== 'business') {
            $attributes['company'] = null;
            $attributes['vat_number'] = null;
        }

        return $attributes;
    }
}
