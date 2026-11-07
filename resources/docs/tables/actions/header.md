---
title: Header actions
description: Buttons above the table, with no record.
---

```php
use Xelqun\Actions\Action;

public function headerActions(): array
{
    return [
        Action::make('create')
            ->authorize('create')
            ->schema([TextInput::make('name')->required()])
            ->action(fn (array $data) => Product::create($data)),
    ];
}
```

<!-- example: table-header-actions -->
