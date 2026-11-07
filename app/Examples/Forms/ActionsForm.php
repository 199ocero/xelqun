<?php

namespace App\Examples\Forms;

use Illuminate\Validation\ValidationException;
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Actions;
use Xelqun\Forms\Layouts\Callout;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

/**
 * Actions run on the server. Each call rebuilds the form and checks the action
 * again, and one with no authorize() never runs.
 */
class ActionsForm extends Form
{
    public function schema(): array
    {
        return [
            Section::make('Basket')->schema([
                TextInput::make('total')->disabled()->computed('sum(lines.*.price)')->prefix('$'),

                Repeater::make('lines')
                    ->default([['name' => 'Keyboard', 'price' => 120], ['name' => 'Mouse', 'price' => 45]])
                    ->itemLabel('Line')
                    ->extraItemActions([
                        Action::make('round')
                            ->authorize(true)
                            ->label('Round the price')
                            ->iconButton()
                            ->icon('hash')
                            ->action(fn (Get $get, Set $set) => $set('price', round((float) $get('price')))),
                    ])
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('price')->numeric()->required()->prefix('$'),
                        Actions::make([
                            Action::make('discount')
                                ->authorize(true)
                                ->icon('tag')
                                ->modalHeading('Discount this line')
                                ->modalDescription('The new price lands in the row you clicked.')
                                ->modalSubmitActionLabel('Apply')
                                ->fillForm(['percent' => 10])
                                ->schema([TextInput::make('percent')->integer()->minValue(1)->maxValue(90)->required()->suffix('%')])
                                ->action(function (array $data, Get $get, Set $set): void {
                                    $set('price', round($get('price') * (100 - $data['percent']) / 100, 2));
                                }),
                            Action::make('free')
                                ->authorize(true)
                                ->label('Make it free')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalDescription('The price goes to zero.')
                                ->action(fn (Set $set) => $set('price', 0)),
                            Action::make('refuse')
                                ->authorize(true)
                                ->label('Fails on the server')
                                ->color('gray')
                                ->action(fn () => throw ValidationException::withMessages(['data.total' => 'The server said no.'])),
                            Action::make('never')->label('You never see this')->action(fn () => abort(500)),
                        ])->columnSpanFull(),
                    ]),
            ]),

            Callout::make('Actions fit anywhere a component does')
                ->info()
                ->footer([
                    Action::make('fill_basket')
                        ->authorize(true)
                        ->label('Reset the total note')
                        ->action(fn (Set $set) => $set('note', 'Checked at '.now()->format('H:i'))),
                    Action::make('guide')
                        ->authorize(true)
                        ->label('Read the guide')
                        ->color('gray')
                        ->url('https://github.com/199ocero/xelqun-forms/blob/main/docs/plugins.md', openInNewTab: true),
                ]),

            TextInput::make('note')
                ->prefixAction(Action::make('clear')->authorize(true)->label('Clear')->action(fn (Set $set) => $set('note', null)))
                ->suffixAction(Action::make('shout')->authorize(true)->label('Shout')->action(fn (Get $get, Set $set) => $set('note', strtoupper((string) $get('note'))))),
        ];
    }
}
