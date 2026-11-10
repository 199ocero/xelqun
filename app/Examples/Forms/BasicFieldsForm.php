<?php

namespace App\Examples\Forms;

use App\Models\Country;
use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Hidden;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Fieldset;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\Layouts\Tab;
use Xelqun\Forms\Layouts\Tabs;

/**
 * Every MVP field type and layout, with no rules or server logic.
 */
class BasicFieldsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Text inputs')
                ->description('TextInput in its text, email, password and numeric modes, plus Textarea.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->placeholder('Ada Lovelace')->required(),
                    TextInput::make('email')->email()->placeholder('ada@example.com'),
                    TextInput::make('password')->password()->helperText('At least 8 characters.')->minLength(8),
                    TextInput::make('budget')->numeric()->prefix('$')->suffix('USD')->minValue(0)->placeholder('2500'),
                    Textarea::make('bio')->rows(3)->placeholder('A few words about yourself…')->columnSpanFull(),
                ]),

            Section::make('Choices')
                ->description('Static, searchable, multiple and server-searched selects, radios, checkboxes and toggles.')
                ->columns(2)
                ->schema([
                    Select::make('plan')
                        ->options(['hobby' => 'Hobby', 'pro' => 'Pro', 'team' => 'Team'])
                        ->placeholder('Choose a plan'),
                    Select::make('timezone')
                        ->options(collect(['UTC', 'Asia/Manila', 'Asia/Tokyo', 'Europe/Berlin', 'America/New_York', 'Australia/Sydney'])->mapWithKeys(fn (string $zone): array => [$zone => $zone]))
                        ->searchable()
                        ->placeholder('Search time zones'),
                    Select::make('languages')
                        ->options(['php' => 'PHP', 'ts' => 'TypeScript', 'go' => 'Go', 'rust' => 'Rust', 'python' => 'Python'])
                        ->multiple()
                        ->placeholder('Pick languages'),
                    Select::make('country')
                        ->label('Country (searched on the server)')
                        ->getSearchResultsUsing(fn (string $search): array => Country::query()->where('name', 'like', "%{$search}%")->orderBy('name')->limit(10)->pluck('name', 'id')->all())
                        ->getOptionLabelUsing(fn (mixed $value): ?string => Country::query()->whereKey($value)->first()?->name)
                        ->placeholder('Type a country name'),
                    Radio::make('contact')
                        ->options(['email' => 'Email', 'phone' => 'Phone', 'none' => 'Don’t contact me'])
                        ->inline()
                        ->default('email')
                        ->columnSpanFull(),
                    Checkbox::make('newsletter')->label('Subscribe to the newsletter')->description('One email a month, no spam.'),
                    Toggle::make('notifications')->label('Push notifications')->default(true),
                ]),

            Tabs::make()->tabs([
                Tab::make('Dates')->columns(2)->schema([
                    DatePicker::make('starts_on')->label('Start date')->minDate(now()),
                    DatePicker::make('birthday'),
                ]),
                Tab::make('Fieldset')->schema([
                    Fieldset::make('Address')->columns(2)->schema([
                        TextInput::make('street')->columnSpanFull(),
                        TextInput::make('city'),
                        TextInput::make('zip')->label('ZIP code'),
                    ]),
                ]),
            ]),

            Hidden::make('source')->default('docs'),
        ];
    }
}
