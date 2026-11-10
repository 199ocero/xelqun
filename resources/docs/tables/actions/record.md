---
title: Record actions
description: Buttons on each row, with modals, groups and authorization.
---

Table actions are the same [actions](/forms/actions/overview) forms use.

```php
use App\Models\Product;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;

public function recordActions(): array
{
    return [
        Action::make('edit')->authorize('update')->url(fn (Product $record): string => route('products.edit', $record)),
        ActionGroup::make([
            Action::make('archive')
                ->authorize('update')
                ->requiresConfirmation()
                ->action(fn (Product $record) => $record->update(['status' => 'archived'])),
        ]),
    ];
}
```

<!-- example: table-record-actions -->

The server finds each row again through the table's `query()`, so an action can't reach a row outside it.

> [!WARNING]
> An action does nothing until its own `authorize()` says so, and a missing one hides its button.

`authorize('update')` asks the row's [policy](/tables/your-first-table#things-to-know), so without one these buttons are hidden. `products.edit` and `products.show` stand for routes in your own app.

## Modals

`schema()` opens a [modal](/forms/actions/modals) with fields, and `fillForm()` sets their values from the row.

```php
use Xelqun\Forms\Components\TextInput;

Action::make('rename')
    ->authorize('update')
    ->fillForm(fn (Product $record): array => ['name' => $record->name])
    ->schema([TextInput::make('name')->required()])
    ->action(fn (array $data, Product $record) => $record->update($data));
```

## Clicking a row

`recordUrl()` sets where clicking a row goes. A link of a cell's own wins over it.

```php
use Illuminate\Database\Eloquent\Model;

public function recordUrl(Model $record): ?string
{
    return route('products.show', $record);
}
```

`recordAction()` names a record action for the row click to open instead, such as a modal. `recordUrl()` wins when both give an answer.

```php
public function recordAction(Model $record): ?string
{
    return 'rename';
}
```

## Keeping the modal open

`$action->halt()` stops the action and keeps its modal open, so the viewer can fix what they typed. What the action wrote is rolled back, and notifications sent so far still show.

```php
use Xelqun\Actions\Action;
use Xelqun\Notifications\Notification;

Action::make('rename')
    ->authorize('update')
    ->schema([TextInput::make('name')->required()])
    ->action(function (array $data, Product $record, Action $action) {
        if (Product::where('name', $data['name'])->exists()) {
            Notification::make()->title('That name is taken')->danger()->send();
            $action->halt();
        }

        $record->update($data);
    });
```
