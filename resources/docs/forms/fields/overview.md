---
title: Overview
description: Every field starts with make(), and shares labels, hints, helper text, validation, defaults and visibility rules.
---

A field is one input in a form. You create it with `make()`, passing the name of the attribute it holds, then chain the options you want.

```php
use Xelqun\Forms\Components\TextInput;

TextInput::make('name')
    ->label('Full name')
    ->placeholder('Ada Lovelace')
    ->helperText('As it appears on your ID.')
    ->required();
```

<!-- example: form-field-options -->

The options on this page work on every field.

## The fields

| Field                                                             | Use it for                                         |
| ----------------------------------------------------------------- | -------------------------------------------------- |
| [TextInput](/forms/fields/text-input)                             | Text, email, numbers, passwords and more           |
| [Textarea](/forms/fields/textarea)                                | Longer plain text                                  |
| [Select](/forms/fields/select)                                    | One or many options, from a list or a relationship |
| [Checkbox](/forms/fields/checkbox)                                | A single yes or no                                 |
| [CheckboxList](/forms/fields/checkbox-list)                       | Many options, all visible                          |
| [Radio](/forms/fields/radio)                                      | One option, all visible                            |
| [Toggle](/forms/fields/toggle)                                    | An on and off switch                               |
| [ToggleButtons](/forms/fields/toggle-buttons)                     | Options as a row of buttons                        |
| [DatePicker, TimePicker, DateTimePicker](/forms/fields/date-time) | Dates and times                                    |
| [FileUpload](/forms/fields/file-upload)                           | Files and images                                   |
| [RichEditor](/forms/fields/rich-editor)                           | Formatted text as clean HTML                       |
| [MarkdownEditor](/forms/fields/markdown-editor)                   | Markdown with a preview                            |
| [CodeEditor](/forms/fields/code-editor)                           | Code, with highlighting                            |
| [ColorPicker](/forms/fields/color-picker)                         | Colors                                             |
| [Slider](/forms/fields/slider)                                    | A number on a track                                |
| [TagsInput](/forms/fields/tags-input)                             | A list of short values                             |
| [KeyValue](/forms/fields/key-value)                               | Pairs of keys and values                           |
| [Repeater](/forms/fields/repeater)                                | Rows of fields                                     |
| [Builder](/forms/fields/builder)                                  | Rows of different block types                      |
| [MorphToSelect](/forms/fields/morph-to-select)                    | A polymorphic relationship                         |
| [Hidden](/forms/fields/hidden)                                    | A value with no control                            |

## Labels and help text

A label comes from the field's name in title case, so `first_name` reads as "First Name" and `country_id` as "Country". Change it with `label()`.

```php
TextInput::make('email')
    ->label('Work email')
    ->placeholder('you@company.com')
    ->helperText('We only use this to sign you in.');
```

`helperText()` sits under the field, and screen readers read it with the field. `hint()` is a short note beside the label, at the end of its row.

```php
use Illuminate\Support\Str;
use Xelqun\Actions\Action;
use Xelqun\Forms\State\Set;

TextInput::make('password')
    ->password()
    ->hint('Case sensitive')
    ->hintColor('warning')
    ->hintIcon('info', tooltip: 'Ask an admin to reset it.')
    ->helperText('At least twelve characters.')
    ->markAsRequired();

TextInput::make('api_key')
    ->hintAction(
        Action::make('generate')
            ->authorize(true)
            ->link()
            ->action(fn (Set $set) => $set('api_key', Str::random(32))),
    );
```

<!-- example: form-hints -->

| Method             | What it does                                                                      |
| ------------------ | --------------------------------------------------------------------------------- |
| `hint()`           | Short text beside the label.                                                      |
| `hintIcon()`       | An icon beside the label, with an optional tooltip.                               |
| `hintColor()`      | The hint's color: `neutral`, `primary`, `danger`, `info`, `success` or `warning`. |
| `hintAction()`     | An [action](/forms/actions/overview) at the end of the label's row.               |
| `helperText()`     | Help under the field.                                                             |
| `markAsRequired()` | Shows or hides the asterisk, whatever the rules say. It changes no validation.    |

`hiddenLabel()` hides the label on screen, and screen readers still announce it. `inlineLabel()` puts it beside the field.

> [!NOTE]
> Before 0.9, `hint()` was the text under the field. That is `helperText()` now.

## Attributes on the input

`extraInputAttributes()` puts HTML attributes on the input itself, on a text input or textarea.

```php
TextInput::make('code')->extraInputAttributes(['autocomplete' => 'one-time-code', 'spellcheck' => 'false']);
```

The attributes reach the page as given, so never build an attribute name from user input.

## Default values

`default()` sets the value of a new form. A form filled from a record uses the record's value.

```php
Toggle::make('is_active')->default(true);
```

## Disabling a field

```php
TextInput::make('slug')->disabled();

TextInput::make('slug')->disabledOn('edit');

TextInput::make('reference')->readOnly();
```

A disabled field isn't submitted. `readOnly()` shows the value without letting anyone change it, and the server drops whatever the browser sends for it.

## Hiding a field

`hidden()` and `visible()` take a boolean or a closure. `hiddenOn()` and `visibleOn()` take an operation.

```php
TextInput::make('password')->hiddenOn('edit');

TextInput::make('discount')->visible(fn (): bool => auth()->user()->isManager());
```

To show a field based on another field's value, see [conditional logic](/forms/conditional-logic).

## Content around a field

You can place text or components around a field: `aboveLabel()`, `beforeLabel()`, `afterLabel()`, `belowLabel()`, `aboveContent()`, `beforeContent()`, `afterContent()`, `belowContent()`, `aboveErrorMessage()` and `belowErrorMessage()`.

```php
use Xelqun\Forms\Layouts\Text;

TextInput::make('password')
    ->password()
    ->belowContent(Text::make('Use at least twelve characters.'));
```

## Buttons beside a field

`prefixAction()` and `suffixAction()` place an [action](/forms/actions/overview) right before or after the control.

## Fields that aren't saved

`saved(false)` is for a field with no column. It's still validated, and it's still in `validate()`'s data. `dehydrated(false)` leaves the value out of the submitted data altogether.

```php
TextInput::make('terms_accepted')->saved(false);
```

## Changing every field at once

`configureUsing()` runs on every new instance of a class. Put it in a service provider.

```php
TextInput::configureUsing(function (TextInput $input): void {
    $input->trim();
});
```

## Column span

Fields sit in the grid of their parent [layout](/forms/layouts/overview). `columnSpan()` sets how many columns one takes, and `columnSpanFull()` gives it the whole row.

```php
TextInput::make('notes')->columnSpanFull();

TextInput::make('city')->columnSpan(['md' => 2, 'xl' => 1]);
```
