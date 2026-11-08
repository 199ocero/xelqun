---
title: Overview
description: Actions are buttons that run PHP, with confirmation, modal fields, authorization and notifications.
---

An action is a button that runs PHP on the server. Put them in a form with the `Actions` layout.

```php
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Layouts\Actions;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

Actions::make([
    Action::make('discount')
        ->authorize(true)
        ->icon('tag')
        ->fillForm(['percent' => 10])
        ->schema([
            TextInput::make('percent')->integer()->minValue(1)->maxValue(90)->required(),
        ])
        ->action(function (array $data, Get $get, Set $set): void {
            $set('price', round($get('price') * (100 - $data['percent']) / 100, 2));
        }),
]);
```

<!-- example: actions -->

Each click rebuilds the form on the server and checks the action again.

## Authorization

> [!WARNING]
> An action does nothing without `authorize()`, and the form's `authorize()` doesn't count.

`authorize()` takes a boolean, a closure, or a policy ability checked against the record. An ability needs a policy for the record's model, such as `OrderPolicy`. Without one, the button is hidden.

```php
Action::make('publish')->authorize('update');

// isManager() stands for a method on your own User model.
Action::make('refund')->authorize(fn (): bool => auth()->user()->isManager());
```

## What an action can do

`action()` runs inside a transaction. It gets `$data`, `$get`, `$set` and `$record`.

```php
Action::make('archive')
    ->authorize('update')
    ->requiresConfirmation()
    ->action(fn (Order $record) => $record->update(['archived_at' => now()]));
```

`url()` goes to a URL and runs nothing.

```php
Action::make('invoice')->authorize(true)->url(fn (): string => route('invoice'), openInNewTab: true);
```

Throw a `ValidationException` to send an error back to the form.

## How it looks

| Method                                      | What it does                                             |
| ------------------------------------------- | -------------------------------------------------------- |
| `label()`, `icon()`, `color()`, `tooltip()` | Change what's shown.                                     |
| `button()`                                  | Shows a button, which is the default.                    |
| `link()`                                    | Shows a link.                                            |
| `iconButton()`                              | Shows only the icon. The label stays for screen readers. |
| `disabled()`, `hidden()`, `visible()`       | Follow a boolean or a closure.                           |

## After it runs

```php
Action::make('save')
    ->authorize(true)
    ->successNotificationTitle('Saved')
    ->successRedirectUrl(fn (): string => route('orders.index'))
    ->action(fn () => null);
```

`record()` gives the action another record to work on, and `model()` names the model its modal fields and policy use.

## Actions in other places

- `prefixAction()` and `suffixAction()` sit beside a field.
- `extraItemActions()` sit in a repeater row's header.
- [Standalone actions](/forms/actions/standalone) sit on a page, with no form.
- [Table actions](/tables/actions/record) sit on a table's rows.
