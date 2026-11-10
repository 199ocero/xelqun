<?php

namespace App\Examples\Snippets;

use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;
use Xelqun\Actions\ActionSet;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;

/**
 * Previews of actions. None of them changes anything.
 */
class SnippetActions extends ActionSet
{
    public const array SNIPPETS = ['modals', 'slide-over', 'grouping'];

    public function actions(): array
    {
        return match ($this->context['snippet']) {
            'modals' => [
                Action::make('delete')
                    ->authorize(true)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This cannot be undone.')
                    ->successNotificationTitle('Nothing was deleted, this is a preview.')
                    ->action(fn () => null),
                Action::make('feedback')
                    ->authorize(true)
                    ->color('gray')
                    ->icon('mail')
                    ->modalHeading('Send feedback')
                    ->modalSubmitActionLabel('Send')
                    ->fillForm(['rating' => 5])
                    ->schema([
                        TextInput::make('rating')->integer()->minValue(1)->maxValue(5)->required(),
                        Textarea::make('message')->required(),
                    ])
                    ->successNotificationTitle('Thanks! Nothing was sent, this is a preview.')
                    ->action(fn () => null),
            ],
            'slide-over' => [
                Action::make('edit_customer')
                    ->authorize(true)
                    ->icon('pencil')
                    ->slideOver()
                    ->fillForm(['name' => 'Ada Lovelace', 'email' => 'ada@example.com'])
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email()->required(),
                        Textarea::make('notes')->rows(6),
                    ])
                    ->successNotificationTitle('Nothing was saved, this is a preview.')
                    ->action(fn () => null),
            ],
            default => [
                ActionGroup::make([
                    Action::make('edit')->authorize(true)->icon('pencil')->successNotificationTitle('Edit')->action(fn () => null),
                    Action::make('archive')->authorize(true)->icon('folder')->successNotificationTitle('Archive')->action(fn () => null),
                ])
                    ->label('More')
                    ->icon('ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('More actions'),
            ],
        };
    }
}
