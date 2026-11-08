---
title: Lists and tiles
description: ListWidget shows rows with actions, and TileWidget a grid of small cards.
---

## ListWidget

Rows with an icon, a title, detail, and actions, a progress bar or a link. It's a table underneath, so actions are checked on the server, and `query()` limits what it reaches.

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Widgets\ListWidget;

class LowStock extends ListWidget
{
    protected static ?string $heading = 'Restocking now';

    protected static int $limit = 4;

    public function query(): Builder
    {
        return Product::query()->where('stock', '<', 50);
    }

    protected function title(Model $record): string
    {
        return $record->name;
    }

    protected function meta(Model $record): string
    {
        return $record->sku;
    }

    protected function icon(Model $record): string
    {
        return 'truck';
    }
}
```

<!-- screenshot: list-widget | A list widget with actions on each row -->

## TileWidget

A grid of small cards with a status.

```php
use App\Xelqun\Admin\Resources\ProductResource;
use Xelqun\Panels\Widgets\Tile;

Tile::make('Keyboards')
    ->icon('package')
    ->status('12 live', 'success')
    ->url(ProductResource::getUrl());
```

<!-- screenshot: tile-widget | A grid of category tiles -->

[See them in the demo](demo:/).
