---
title: Text, icons and images
description: Text, Icon, Image and UnorderedList put plain content in a form.
---

These are components with no state.

## Text

```php
use Xelqun\Forms\Layouts\Text;

Text::make('Prices include tax.')
    ->color('gray')
    ->size('sm')
    ->weight('medium');

Text::make('Beta')->badge()->icon('star')->color('warning');
```

<!-- example: form-primes -->

`fontFamily()` and `tooltip()` work here too.

## Icon

```php
use Xelqun\Forms\Layouts\Icon;

Icon::make('info')->color('info')->size('lg')->tooltip('More details');
```

## Image

```php
use Xelqun\Forms\Layouts\Image;

Image::make('/images/map.png', 'A map of the store')
    ->imageWidth(320)
    ->alignCenter();
```

`imageHeight()` and `imageSize()` set the size too. A number means pixels, and a string is any CSS size. `alignStart()`, `alignCenter()`, `alignEnd()` and `alignment()` place it.

## UnorderedList

```php
use Xelqun\Forms\Layouts\Text;
use Xelqun\Forms\Layouts\UnorderedList;

UnorderedList::make([
    Text::make('Twelve characters or more'),
    Text::make('One number'),
])->size('sm');
```
