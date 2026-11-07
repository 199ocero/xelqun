---
title: Modals
description: Open a confirmation or a form in a modal before an action runs, and shape how it looks.
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

Any field works here, with its own validation.

## Options

| Method                            | What it does                                               |
| --------------------------------- | ---------------------------------------------------------- |
| `modalHeading()`                  | The heading.                                               |
| `modalDescription()`              | The text under it.                                         |
| `modalIcon()`, `modalIconColor()` | An icon in a tinted circle.                                |
| `modalSubmitActionLabel()`        | The submit button's label.                                 |
| `modalCancelActionLabel()`        | The cancel button's label.                                 |
| `modalWidth()`                    | The width, such as `sm`.                                   |
| `modalAlignment()`                | How the heading sits: `start` or `center`.                 |
| `modalFooterActionsAlignment()`   | Where the buttons sit: `start`, `center`, `end` or `full`. |
| `modalCloseButton(false)`         | Drops the X in the corner.                                 |
| `closeModalByClickingAway(false)` | Stops a click outside from closing it.                     |
