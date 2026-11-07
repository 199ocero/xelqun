<?php

namespace App\Examples\Forms;

use Xelqun\Actions\Action;
use Xelqun\Actions\ActionSet;

/**
 * What the Undo button on a notification runs. It's a class because a closure
 * can't wait in the browser.
 */
class NotificationUndoActions extends ActionSet
{
    public function actions(): array
    {
        return [
            Action::make('restore')
                ->authorize(true)
                ->successNotificationTitle('Order restored')
                ->action(fn () => null),
        ];
    }
}
