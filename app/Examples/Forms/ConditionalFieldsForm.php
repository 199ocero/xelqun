<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Conditions\When;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Fieldset;
use Xelqun\Forms\Layouts\Section;

/**
 * Show, hide, require and disable fields with declarative rules.
 * They run in the browser and are re-checked on the server.
 */
class ConditionalFieldsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Account')->columns(2)->schema([
                Radio::make('type')
                    ->options(['individual' => 'Individual', 'business' => 'Business'])
                    ->default('individual')
                    ->inline()
                    ->columnSpanFull(),
                TextInput::make('name')->required(),
                TextInput::make('company')
                    ->visibleWhen(When::eq('type', 'business'))
                    ->requiredWhen(When::eq('type', 'business')),
                TextInput::make('vat_number')
                    ->label('VAT number')
                    ->helperText('Required for EU businesses.')
                    ->visibleWhen(When::eq('type', 'business'))
                    ->requiredWhen(When::and(When::eq('type', 'business'), When::in('region', ['eu']))),
                Select::make('region')
                    ->options(['eu' => 'European Union', 'us' => 'United States', 'apac' => 'Asia Pacific'])
                    ->placeholder('Where are you based?'),
            ]),

            Section::make('Delivery')->schema([
                Toggle::make('ship_elsewhere')->label('Ship to a different address'),
                Fieldset::make('Shipping address')
                    ->visibleWhen(When::eq('ship_elsewhere', true))
                    ->columns(2)
                    ->schema([
                        TextInput::make('shipping_street')->label('Street')->requiredWhen(When::eq('ship_elsewhere', true))->columnSpanFull(),
                        TextInput::make('shipping_city')->label('City')->requiredWhen(When::eq('ship_elsewhere', true)),
                        TextInput::make('shipping_zip')->label('ZIP code'),
                    ]),
            ]),

            Section::make('About you')->columns(2)->schema([
                TextInput::make('age')->integer()->minValue(0)->helperText('Try a number over 17.'),
                Toggle::make('wants_wine')
                    ->label('Add a bottle of wine')
                    ->visibleWhen(When::gte('age', 18)),
                Select::make('referral')
                    ->label('How did you hear about us?')
                    ->options(['search' => 'Search engine', 'friend' => 'A friend', 'other' => 'Something else']),
                Textarea::make('referral_other')
                    ->label('Tell us more')
                    ->visibleWhen(When::eq('referral', 'other'))
                    ->requiredWhen(When::eq('referral', 'other')),
                Toggle::make('use_account_email')->label('Use my account email'),
                TextInput::make('contact_email')
                    ->email()
                    ->disabledWhen(When::eq('use_account_email', true))
                    ->helperText('Disabled while “Use my account email” is on. Disabled values are never submitted.'),
                TextInput::make('nickname')
                    ->hiddenWhen(When::filled('name'))
                    ->helperText('Hidden as soon as a name is filled.'),
            ]),
        ];
    }
}
