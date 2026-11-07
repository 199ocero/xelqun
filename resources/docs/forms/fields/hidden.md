---
title: Hidden
description: Hidden keeps a value in the form with no control on screen.
---

```php
use Xelqun\Forms\Components\Hidden;

Hidden::make('source')->default('landing-page');
```

> [!WARNING]
> People can change a hidden value in their browser, so never use it for something that decides access or price. Set those on the server, in `mutateBeforeSave()`.
