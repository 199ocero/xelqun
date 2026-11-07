<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Layouts\Step;
use Xelqun\Forms\Layouts\Wizard;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\SelectColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

/**
 * A resource whose form is a wizard.
 */
class CustomerResource extends Resource
{
    protected static string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'users';

    protected static ?string $navigationGroup = 'Shop';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('country');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function form(): array
    {
        return [
            Wizard::make()->columnSpanFull()->skippable(fn (string $operation): bool => $operation === 'edit')->steps([
                Step::make('Account')->description('Who they are')->icon('user')->columns(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->liveValidation(),
                ]),
                Step::make('Contact')->description('How to reach them')->icon('phone')->columns(2)->schema([
                    TextInput::make('phone')->tel()->mask('+1 999 999 9999'),
                    DatePicker::make('birthday')->maxDate(now()),
                    Select::make('country_id')->label('Country')->relationship('country', 'name')->searchable()->preload(),
                ]),
                Step::make('Plan')->description('What they get')->icon('star')->schema([
                    Radio::make('tier')
                        ->required()
                        ->default('standard')
                        ->options(['standard' => 'Standard', 'silver' => 'Silver', 'gold' => 'Gold'])
                        ->descriptions(['standard' => 'Everything to start with.', 'silver' => 'Free delivery.', 'gold' => 'Free delivery, and a person to call.'])
                        ->disableOptionWhen(fn (string $value, string $operation): bool => $value === 'gold' && $operation === 'create'),
                ]),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable()->icon('mail'),
            TextColumn::make('country.name')->label('Country')->sortable(),
            SelectColumn::make('tier')->authorize('update')->options(['standard' => 'Standard', 'silver' => 'Silver', 'gold' => 'Gold']),
            TextColumn::make('birthday')->date()->toggleable(isHiddenByDefault: true),
        ];
    }

    public static function filters(): array
    {
        return [SelectFilter::make('tier')->multiple()->options(['standard' => 'Standard', 'silver' => 'Silver', 'gold' => 'Gold'])];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('name')->paginationPageOptions([10, 25, 50]);
    }
}
