<?php

namespace App\Xelqun\Admin\Pages;

use App\Models\Product;
use App\Xelqun\Admin\Resources\ProductResource;
use App\Xelqun\Admin\Widgets\LatestOrders;
use Xelqun\Actions\Action;
use Xelqun\Notifications\Notification;
use Xelqun\Notifications\NotificationAction;
use Xelqun\Panels\Notifications\DatabaseNotification;
use Xelqun\Panels\Pages\Dashboard;

class Home extends Dashboard
{
    public static function getTitle(): string
    {
        return 'Home';
    }

    public function getHeading(): string
    {
        $name = str((string) auth()->user()?->name)->before(' ');

        return "Good morning, {$name}";
    }

    public function getBreadcrumbs(): array
    {
        return [['label' => static::getTitle()]];
    }

    public function getEyebrow(): string
    {
        return now()->format('l, F j');
    }

    public function getSubheading(): string
    {
        $drafts = Product::query()->where('status', 'draft')->count();

        return "{$drafts} products need your OK.";
    }

    protected function getFooterWidgets(): array
    {
        return [LatestOrders::class];
    }

    public function headerActions(): array
    {
        return [
            Action::make('ping')
                ->color('gray')
                ->authorize(true)
                ->action(fn () => Notification::make()->title('Pong')->success()->send()),
            Action::make('notify')
                ->label('Send me a notification')
                ->icon('bell')
                ->authorize(true)
                ->successNotificationTitle('Check the bell.')
                ->action(fn () => DatabaseNotification::make()
                    ->title('Your weekly report is ready')
                    ->body('Sales are up this week.')
                    ->success()
                    ->url(ProductResource::getUrl())
                    ->icon('chart-column')
                    ->actions([NotificationAction::make('products')->label('See products')->url(ProductResource::getUrl())])
                    ->sendToDatabase(auth()->user())),
        ];
    }
}
