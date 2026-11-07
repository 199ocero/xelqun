<?php

namespace App\Examples\Forms;

use Xelqun\Actions\Action;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Actions;
use Xelqun\Forms\Layouts\Section;

/**
 * The same fields a form would have, locked. disabled() is kept in the token,
 * so the requests after this page are locked too.
 */
class ViewOnlyForm extends Form
{
    protected function boot(): void
    {
        $this->disabled();
    }

    public function schema(): array
    {
        return [
            Section::make('Order')->schema([
                TextInput::make('customer')->default('Ada Lovelace'),
                Select::make('status')->options(['open' => 'Open', 'paid' => 'Paid'])->default('paid'),
                Toggle::make('is_gift')->label('A gift')->default(true),
                Repeater::make('items')
                    ->schema([TextInput::make('name')])
                    ->default([['name' => 'Notebook'], ['name' => 'Pen']]),
            ]),
            Actions::make([
                Action::make('guide')
                    ->authorize(true)
                    ->label('This still works')
                    ->color('gray')
                    ->url('/forms'),
            ]),
        ];
    }
}
