---
title: Wizard
description: Wizard splits a form into steps, and checks each step on the server.
---

```php
use Xelqun\Forms\Layouts\Step;
use Xelqun\Forms\Layouts\Wizard;

Wizard::make()->steps([
    Step::make('Account')
        ->description('Who you are')
        ->icon('user')
        ->schema([...]),
    Step::make('Delivery')
        ->icon('map-pin')
        ->schema([...]),
]);
```

<!-- example: wizard -->

Next checks the step in the browser, then on the server. The server keeps no record of the steps, so submitting checks all of them again.

## Running code between steps

`beforeValidation()` runs on Next, before the step's rules, and a `ValidationException` stops it. `afterValidation()` runs once they pass.

```php
Step::make('Account')
    ->schema([...])
    ->afterValidation(function (Get $get, Set $set): void {
        $set('slug', Str::slug($get('name')));
    });
```

> [!WARNING]
> Never rely on `afterValidation()` to have run. Someone can post the form without pressing Next, so do anything that matters on submit.

## Options

| Method                       | What it does                                                        |
| ---------------------------- | ------------------------------------------------------------------- |
| `skippable()`                | Lets people move freely between steps, with no checks until submit. |
| `startOnStep()`              | Starts on this step, counting from 1.                               |
| `persistStepInQueryString()` | Keeps the step in the URL. Anyone can open any step this way.       |
| `completedIcon()`            | The icon of a finished step.                                        |
