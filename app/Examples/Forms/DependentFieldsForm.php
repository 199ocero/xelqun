<?php

namespace App\Examples\Forms;

use App\Models\Country;
use App\Models\State;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

/**
 * Server closures: options and values that come from the database.
 * The browser only calls the server when a value a closure read changes.
 */
class DependentFieldsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Location')
                ->description('Pick a country: the states load from the server, and the dial code and currency are set by a hook.')
                ->columns(2)
                ->schema([
                    Select::make('country_id')
                        ->label('Country')
                        ->options(fn (): array => Country::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->placeholder('Choose a country')
                        ->required()
                        ->afterStateUpdated(function (Get $get, Set $set): void {
                            $country = Country::query()->whereKey($get('country_id'))->first();

                            $set('state_id', null);
                            $set('dial_code', $country?->dial_code);
                            $set('currency', $country?->currency);
                        }),
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
                    TextInput::make('dial_code')->label('Dial code')->disabled()->placeholder('—'),
                    TextInput::make('currency')->disabled()->placeholder('—'),
                    TextInput::make('phone')
                        ->tel()
                        ->prefix(fn (Get $get): ?string => Country::query()->whereKey($get('country_id'))->first()?->dial_code)
                        ->placeholder('912 345 6789')
                        ->columnSpanFull(),
                ]),

            Section::make('Slug preview')
                ->description('A blur-mode live field: the slug is built on the server when the title loses focus.')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) => $set('slug', str($get('title'))->slug()->value())),
                    TextInput::make('slug')->prefix('/blog/'),
                ]),
        ];
    }
}
