---
title: Plugins
description: Bundle resources, pages and settings for a panel.
---

```php
use Xelqun\Panels\Contracts\Plugin;
use Xelqun\Panels\Panel;

class BlogPlugin implements Plugin
{
    public function getId(): string
    {
        return 'blog';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([PostResource::class]);
    }

    public function boot(Panel $panel): void {}
}
```

```php
$panel->plugin(new BlogPlugin);
```

`register()` runs as the panel is declared, and `boot()` once the app has booted.

The demo's sidebar card, palette button and login hint come from a plugin like this. For fields and columns, see [forms plugins](/forms/plugins).
