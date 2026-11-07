---
title: Record actions
description: Buttons on each row, with modals, groups and authorization.
---

Table actions are the same [actions](/forms/actions/overview) forms use.

```php
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

## Modals

`schema()` opens a [modal](/forms/actions/modals) with fields, and `fillForm()` sets their values from the row.

```php
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
