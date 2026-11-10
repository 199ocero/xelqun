<?php

namespace App\Examples\Forms;

use Closure;
use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\Layouts\Tab;
use Xelqun\Forms\Layouts\Tabs;

/**
 * Starts with invalid data, so pressing submit shows Laravel validation
 * errors on top-level fields, inside tabs and on nested repeater rows, with
 * custom messages and rules that compare fields.
 */
class ValidationErrorsForm extends Form
{
    public function schema(): array
    {
        return [
            Tabs::make()->tabs([
                Tab::make('Account')->columns(2)->schema([
                    TextInput::make('username')
                        ->default('ad')
                        ->required()
                        ->minLength(3)
                        ->alphaDash()
                        ->validationMessages(['min' => 'Pick a :attribute of at least :min characters.'])
                        ->helperText('Letters, numbers, dashes. At least 3 characters.'),
                    TextInput::make('email')
                        ->email()
                        ->default('not-an-email')
                        ->required()
                        ->validationAttribute('contact email'),
                    TextInput::make('website')->url()->default('example dot com'),
                    TextInput::make('age')->integer()->minValue(18)->default(16),
                    TextInput::make('pin')->label('PIN')->default('1234')->confirmed(),
                    TextInput::make('pin_confirmation')->label('Confirm PIN')->default('4321')->saved(false),
                ]),
                Tab::make('Event')->columns(2)->schema([
                    DatePicker::make('event_date')->minDate(now()->addDay())->default(now()->subWeek()->format('Y-m-d'))->required(),
                    DatePicker::make('event_end')->default(now()->subMonth()->format('Y-m-d'))->after('event_date'),
                    Select::make('seat')->options(['standard' => 'Standard', 'vip' => 'VIP'])->required(),
                    TextInput::make('coupon')
                        ->default('FREE')
                        ->rules([function (string $attribute, mixed $value, Closure $fail): void {
                            if ($value !== null && ! str_starts_with((string) $value, 'XQ-')) {
                                $fail('Coupons start with “XQ-”.');
                            }
                        }])
                        ->helperText('A custom closure rule.'),
                    Checkbox::make('terms')->label('I accept the terms')->required(),
                ]),
            ]),

            Section::make('Guests')->description('Errors on nested rows map straight to the field inside the row.')->schema([
                Repeater::make('guests')
                    ->itemLabel('Guest')
                    ->columns(3)
                    ->default([
                        ['name' => 'Grace Hopper', 'email' => 'grace@example.com', 'age' => 40],
                        ['name' => '', 'email' => 'missing-at-sign', 'age' => 12],
                        ['name' => 'Linus', 'email' => 'linus@example.com', 'age' => null],
                    ])
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('age')->integer()->minValue(16)->required(),
                    ]),
            ]),
        ];
    }
}
