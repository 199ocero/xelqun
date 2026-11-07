<?php

namespace App\Examples\Forms;

use Xelqun\Actions\Action;
use Xelqun\Actions\ActionSet;
use Xelqun\Notifications\Notification;
use Xelqun\Notifications\NotificationAction;

/**
 * One button for each kind of notification. All of them come from PHP, and the
 * toaster needs no setup.
 */
class NotificationsActions extends ActionSet
{
    public function actions(): array
    {
        return [
            Action::make('success')
                ->authorize(true)
                ->label('Success')
                ->color('primary')
                ->successNotificationTitle('Saved')
                ->action(fn () => null),
            Action::make('info')
                ->authorize(true)
                ->label('Info')
                ->color('gray')
                ->action(fn () => Notification::make()->title('A new version is out')->info()->send()),
            Action::make('warning')
                ->authorize(true)
                ->label('Warning')
                ->color('gray')
                ->action(fn () => Notification::make()
                    ->title('Your plan ends soon')
                    ->body('This one has a body too.')
                    ->warning()
                    ->send()),
            Action::make('danger')
                ->authorize(true)
                ->label('Danger')
                ->color('danger')
                ->action(fn () => Notification::make()->title('That did not work')->danger()->send()),
            Action::make('persistent')
                ->authorize(true)
                ->label('Stays until closed')
                ->color('gray')
                ->action(fn () => Notification::make()->title('Close me yourself')->persistent()->send()),
            Action::make('with-actions')
                ->authorize(true)
                ->label('With actions')
                ->color('gray')
                ->action(fn () => Notification::make()
                    ->title('Order deleted')
                    ->body('You can still bring it back.')
                    ->success()
                    ->persistent()
                    ->actions([
                        NotificationAction::make('undo')->color('primary')->run(NotificationUndoActions::make(), 'restore'),
                        NotificationAction::make('examples')->label('See examples')->url('/forms'),
                        NotificationAction::make('dismiss'),
                    ])
                    ->send()),
            Action::make('several')
                ->authorize(true)
                ->label('Send seven')
                ->color('gray')
                ->action(function (): void {
                    foreach (range(1, 7) as $number) {
                        Notification::make()->title("Notification {$number}")->send();
                    }
                }),
        ];
    }
}
