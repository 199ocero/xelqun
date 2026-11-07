<?php

namespace App\Examples\Forms;

use Illuminate\Validation\ValidationException;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;
use Xelqun\Actions\ActionSet;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Notifications\Notification;

/**
 * Buttons with no form around them. The page reloads after each one, so the
 * count and the buttons below always show what the server has.
 */
class StandaloneActions extends ActionSet
{
    private const string CLICKS = 'examples.clicks';

    public function actions(): array
    {
        $clicks = (int) session(self::CLICKS, 0);

        return [
            Action::make('count')
                ->authorize(true)
                ->label("Clicked {$clicks} times")
                ->color('primary')
                ->action(fn () => session()->increment(self::CLICKS)),
            Action::make('add')
                ->authorize(true)
                ->label('Add several')
                ->icon('hash')
                ->modalIcon('hash')
                ->modalIconColor('info')
                ->modalHeading('Add to the count')
                ->modalDescription('Pick how many clicks to add in one go.')
                ->modalSubmitActionLabel('Add')
                ->modalWidth('sm')
                ->successNotificationTitle(fn (array $data): string => "Added {$data['amount']}")
                ->fillForm(['amount' => 5])
                ->schema([TextInput::make('amount')->integer()->minValue(1)->maxValue(100)->required()])
                ->action(fn (array $data) => session()->increment(self::CLICKS, (int) $data['amount'])),
            Action::make('reset')
                ->authorize($clicks > 0)
                ->label('Reset')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('The count goes back to zero.')
                ->action(fn () => session()->forget(self::CLICKS)),
            Action::make('feedback')
                ->authorize(true)
                ->label('Send feedback')
                ->icon('mail')
                ->color('gray')
                ->modalIcon('heart')
                ->modalIconColor('success')
                ->modalHeading('Tell us what you think')
                ->modalDescription('A centered modal, with full-width buttons. A click outside does not close it.')
                ->modalAlignment('center')
                ->modalFooterActionsAlignment('full')
                ->modalCloseButton(false)
                ->closeModalByClickingAway(false)
                ->modalSubmitActionLabel('Send')
                ->schema([Textarea::make('message')->required()])
                ->action(fn () => null),
            Action::make('leave')
                ->authorize(true)
                ->label('Go to the first example')
                ->color('gray')
                ->successRedirectUrl('/forms')
                ->successNotificationTitle('Welcome back')
                ->action(fn () => null),
            Action::make('refuse')
                ->authorize(true)
                ->label('Fails on the server')
                ->color('gray')
                ->action(fn () => throw ValidationException::withMessages(['data' => 'The server said no.'])),
            Action::make('guide')
                ->authorize(true)
                ->label('Read the guide')
                ->color('gray')
                ->url('https://github.com/199ocero/xelqun-forms/blob/main/docs/plugins.md', openInNewTab: true),
            Action::make('never')->label('You never see this')->action(fn () => abort(500)),
            ActionGroup::make([
                Action::make('double')
                    ->authorize(true)
                    ->label('Double it')
                    ->icon('plus')
                    ->successNotificationTitle('Doubled')
                    ->action(fn () => session()->increment(self::CLICKS, $clicks)),
                Action::make('note')
                    ->authorize(true)
                    ->label('Leave a note')
                    ->icon('pencil')
                    ->modalSubmitActionLabel('Save note')
                    ->successNotificationTitle('Note saved')
                    ->schema([
                        TextInput::make('note')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn () => Notification::make()->title('Sent from a hook')->info()->persistent()->send()),
                    ])
                    ->action(fn () => null),
                Action::make('warn')
                    ->authorize(true)
                    ->label('Warn me')
                    ->icon('triangle-alert')
                    ->action(fn () => Notification::make()
                        ->title('Careful')
                        ->body('This one stays until you close it.')
                        ->warning()
                        ->persistent()
                        ->send()),
                Action::make('clear')
                    ->authorize($clicks > 0)
                    ->label('Clear the count')
                    ->icon('trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Cleared')
                    ->action(fn () => session()->forget(self::CLICKS)),
                Action::make('home')
                    ->authorize(true)
                    ->label('First example')
                    ->url('/forms'),
            ])->label('More actions')->color('gray'),
        ];
    }
}
