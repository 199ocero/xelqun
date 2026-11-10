---
title: Modals
description: Open a confirmation, a form, a slide-over or a wizard before an action runs, and shape how it looks.
---

## Confirming

`requiresConfirmation()` asks before running. It only helps the person using it: nothing stops a request from being sent, so use `authorize()` for access.

```php
Action::make('delete')
    ->authorize('delete')
    ->color('danger')
    ->requiresConfirmation()
    ->modalDescription('This cannot be undone.');
```

<!-- example: actions-modals -->

## Fields

`schema()` opens a modal with fields. Their validated values reach `action()` as `$data`, and `fillForm()` sets their starting values.

```php
Action::make('feedback')
    ->authorize(true)
    ->fillForm(['rating' => 5])
    ->schema([
        TextInput::make('rating')->integer()->required(),
        Textarea::make('message')->required(),
    ])
    ->action(fn (array $data) => Feedback::create($data));
```

Any field works here, with its own validation. The heading and the buttons stay in view while the fields scroll.

## Slide-overs

`slideOver()` opens the modal as a full-height panel that slides in from the side. It suits a long form.

```php
Action::make('edit_customer')
    ->authorize('update')
    ->slideOver()
    ->fillForm(fn (Customer $record): array => ['name' => $record->name, 'email' => $record->email])
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
        Textarea::make('notes')->rows(6),
    ])
    ->action(fn (Customer $record, array $data) => $record->update($data));
```

<!-- example: actions-slide-over -->

## Steps

`steps()` puts a [wizard](/forms/layouts/wizard) in the modal. The submit button shows on the last step.

```php
use Xelqun\Forms\Layouts\Step;

Action::make('invite')
    ->authorize('create')
    ->steps([
        Step::make('Person')->schema([
            TextInput::make('email')->email()->required(),
        ]),
        Step::make('Access')->schema([
            Select::make('role')->options(['member' => 'Member', 'admin' => 'Admin'])->required(),
        ]),
    ])
    ->action(fn (array $data) => Invitation::create($data));
```

## More buttons in the footer

`extraModalFooterActions()` puts more actions beside the modal's buttons. They run with the modal's state, which `$get` reads, and can open modals of their own.

`cancelParentActions()` closes the modals an action sits in once it has run.

```php
use Xelqun\Forms\State\Get;

Action::make('reply')
    ->authorize(true)
    ->schema([Textarea::make('message')->required()])
    ->extraModalFooterActions([
        Action::make('save_draft')
            ->authorize(true)
            ->color('gray')
            ->action(fn (Get $get) => Draft::create(['message' => $get('message')]))
            ->cancelParentActions(),
    ])
    ->action(fn (array $data) => Reply::create($data));
```

## Halting an action

`$action->halt()` stops an action from any closure that takes `$action`, and keeps the modal open. `$action->cancel()` stops it and closes the modal. Both roll back what the action wrote, and notifications sent so far still show.

```php
use Xelqun\Actions\Action;
use Xelqun\Notifications\Notification;

Action::make('charge')
    ->authorize('update')
    ->schema([TextInput::make('amount')->numeric()->required()])
    ->action(function (Action $action, Order $record, array $data): void {
        if ($data['amount'] > $record->balance) {
            Notification::make()->title('That is more than the balance.')->danger()->send();

            $action->halt();
        }

        $record->update(['balance' => $record->balance - $data['amount']]);
    });
```

## Options

| Method                            | What it does                                               |
| --------------------------------- | ---------------------------------------------------------- |
| `modalHeading()`                  | The heading.                                               |
| `modalDescription()`              | The text under it.                                         |
| `modalIcon()`, `modalIconColor()` | An icon in a tinted circle.                                |
| `modalSubmitActionLabel()`        | The submit button's label.                                 |
| `modalCancelActionLabel()`        | The cancel button's label.                                 |
| `modalWidth()`                    | The width, such as `sm`.                                   |
| `slideOver()`                     | A panel from the side instead of a centered modal.         |
| `modalAlignment()`                | How the heading sits: `start` or `center`.                 |
| `modalFooterActionsAlignment()`   | Where the buttons sit: `start`, `center`, `end` or `full`. |
| `modalCloseButton(false)`         | Drops the X in the corner.                                 |
| `closeModalByClickingAway(false)` | Stops a click outside from closing it.                     |
