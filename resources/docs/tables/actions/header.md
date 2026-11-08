---
title: Header actions
description: Buttons above the table, with no record.
---

```php
use App\Models\Product;
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\TextInput;

public function headerActions(): array
{
    return [
        Action::make('create')
            ->authorize('create')
            ->schema([TextInput::make('name')->required()])
            ->action(fn (array $data) => Product::create([...$data, 'team_id' => $this->context['team']])),
    ];
}
```

<!-- example: table-header-actions -->

`authorize('create')` asks the model's [policy](/tables/your-first-table#things-to-know), so without one the button is hidden. Set anything the table's `query()` filters on, such as `team_id` here, or the new row won't show.
