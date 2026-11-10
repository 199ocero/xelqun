<?php

namespace App\Examples\Forms;

use Closure;
use Xelqun\Forms\Components\Checkbox;
use Xelqun\Forms\Components\Radio;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Conditions\When;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Step;
use Xelqun\Forms\Layouts\Wizard;
use Xelqun\Forms\State\Set;

/**
 * Next checks one step on the server against the whole form. Submit checks them all.
 */
class WizardForm extends Form
{
    protected function boot(): void
    {
        $this->frontendValidation();
    }

    public function schema(): array
    {
        return [
            Wizard::make([
                Step::make('Account')
                    ->description('Who you are')
                    ->icon('user')
                    ->columns(2)
                    ->afterValidation(fn (Set $set) => $set('coupon', 'WELCOME'))
                    ->schema([
                        TextInput::make('name')->required()->minLength(2),
                        TextInput::make('email')->email()->required()->liveValidation(),
                        TextInput::make('username')
                            ->required()
                            ->helperText('Taken: admin. Only the server knows that.')
                            ->rule(fn (string $attribute, mixed $value, Closure $fail) => $value === 'admin' ? $fail('That username is taken.') : null),
                    ]),
                Step::make('Plan')
                    ->description('What you need')
                    ->schema([
                        Radio::make('plan')->required()->options(['free' => 'Free', 'team' => 'Team']),
                        TextInput::make('seats')
                            ->integer()
                            ->minValue(2)
                            ->requiredWhen(When::eq('plan', 'team'))
                            ->visibleWhen(When::eq('plan', 'team')),
                        TextInput::make('coupon')->helperText('Filled in by the first step.'),
                    ]),
                Step::make('Confirm')
                    ->schema([Checkbox::make('terms')->label('I accept the terms')->required()]),
            ])->persistStepInQueryString(),
        ];
    }
}
