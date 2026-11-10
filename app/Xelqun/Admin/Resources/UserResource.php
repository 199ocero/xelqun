<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Panels\Resources\Tab;
use Xelqun\Tables\Columns\IconColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Table;

class UserResource extends Resource
{
    protected static string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'users';

    protected static ?string $navigationGroup = 'Shop';

    protected static int $navigationSort = 1;

    /**
     * Seeded staff and yourself, so an address typed at registration stays private.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(fn (Builder $query) => $query
            ->where('id', '<=', DemoSeeder::SEEDED_USERS)
            ->orWhere('id', auth()->id()));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function form(): array
    {
        return [
            Section::make('Account')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Leave blank to keep the current one.')
                    ->autocomplete('new-password'),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable()->sortable(),
            IconColumn::make('verified')->state(fn (User $record): bool => $record->email_verified_at !== null)->boolean(),
            TextColumn::make('created_at')->label('Joined')->date()->sortable(),
        ];
    }

    public static function filters(): array
    {
        return [
            Filter::make('verified')->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),
        ];
    }

    public static function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'recent' => Tab::make('Joined this month')->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth())),
        ];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('name');
    }
}
