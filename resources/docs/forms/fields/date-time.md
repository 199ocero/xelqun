---
title: Date and time pickers
description: DatePicker, TimePicker and DateTimePicker share formats, limits, timezones and disabled dates.
---

```php
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\DateTimePicker;
use Xelqun\Forms\Components\TimePicker;

DatePicker::make('date_of_birth');
DateTimePicker::make('published_at');
TimePicker::make('opens_at');
```

<!-- example: form-date-time -->

## Formats

`format()` sets how the value is stored, and `displayFormat()` how it's shown. Both use PHP date tokens.

```php
DatePicker::make('date_of_birth')
    ->format('Y-m-d')
    ->displayFormat('d/m/Y');
```

## Limits

```php
DatePicker::make('starts_on')
    ->minDate(now())
    ->maxDate(now()->addYear());
```

`disabledDates()` lists days that can't be chosen. The server refuses them too.

```php
DatePicker::make('delivery_on')->disabledDates(['2026-12-25', '2026-12-26']);
```

## Time

```php
TimePicker::make('opens_at')
    ->seconds(false)
    ->hoursStep(2)
    ->minutesStep(15);
```

`secondsStep()` works the same way.

## Timezones

`timezone()` shows the value in this timezone. It's always stored in the app's.

```php
DateTimePicker::make('published_at')->timezone('Asia/Manila');
```

## Comparing dates

The usual rules take another field's name or a date.

```php
DatePicker::make('starts_on')->required();
DatePicker::make('ends_on')->after('starts_on');
```
