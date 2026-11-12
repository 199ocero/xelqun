---
title: Navigation
description: Groups, items, clusters, the sidebar card, the user menu and top navigation.
---

## Groups

`navigationGroups()` sets their order.

```php
$panel->navigationGroups(['Shop', 'Catalog', 'Blog']);
```

Pass a `NavigationGroup` to give one an icon, or to let the viewer fold it. A folded group stays folded on the next visit, and `collapsed()` starts it folded.

```php
use Xelqun\Panels\Navigation\NavigationGroup;

$panel->navigationGroups([
    NavigationGroup::make('Shop')->icon('shopping-cart'),
    NavigationGroup::make('Catalog')->icon('package')->collapsible(),
    NavigationGroup::make('Blog')->collapsed(),
]);
```

<!-- screenshot: sidebar | The sidebar, with groups, badges, and a card from a render hook -->

<!-- screenshot: navigation-groups | Navigation groups, with a collapsible group folded away -->

## Items

`navigationItems()` adds links of your own.

```php
use Xelqun\Panels\Navigation\NavigationItem;

$panel->navigationItems([
    NavigationItem::make('Docs')
        ->icon('file-text')
        ->group('Help')
        ->sort(2)
        ->badge('New')
        ->badgeColor('success')
        ->badgeTooltip('Updated this week')
        ->url(fn (): string => route('docs')),
]);
```

An icon is a Lucide name, and it has to be [registered](/panels/resources/overview#icons) to show. A badge color is `primary`, `danger`, `info`, `success`, `warning` or `gray`, and without one the badge is plain text.

## Badges, children and hiding

Pages and resources set the same things with methods and properties:

```php
public static function getNavigationBadge(): int
{
    return static::getEloquentQuery()->where('status', 'new')->count();
}

public static function getNavigationBadgeColor(): string
{
    return 'warning';
}

public static function getNavigationBadgeTooltip(): string
{
    return 'New orders';
}
```

`$navigationParentItem` puts an item under another of its group, by that item's label. The child shows while either of them is open. On a `NavigationItem`, it's `parentItem()`.

```php
protected static ?string $navigationGroup = 'Shop';

protected static ?string $navigationParentItem = 'Orders';
```

`shouldRegisterNavigation()` returning `false` leaves a page or resource out of the sidebar. Its pages still work, for those who may open them.

## Clusters

Clusters put several pages and resources behind one sidebar link, with tabs between them. Extend `Cluster`, then set `$cluster` on each member. The link goes to the first member the user can reach.

```php
use Xelqun\Panels\Navigation\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'settings';
}
```

```php
protected static ?string $cluster = Settings::class;
```

Members' URLs start with the cluster's slug, so a page `Billing` in `Settings` is at `/admin/settings/billing`. The slug is the class name in kebab case. Set `$slug` to change it:

```php
protected static ?string $slug = 'preferences';
```

A page or resource outside the cluster can't use its slug. The panel refuses it when the routes load.

## The sidebar card

```php
use Xelqun\Panels\Navigation\SidebarCard;

$panel->sidebarFooter(fn (): SidebarCard => SidebarCard::make('Credits')
    ->icon('zap')
    ->badge('Pro')
    ->value('760', 'left of 2,000')
    ->progress(0.38)
    ->footer('Resets Nov 1')
    ->link('Upgrade', route('billing')));
```

## The user menu

```php
$panel->userMenuItems([NavigationItem::make('Billing')->url(fn (): string => route('billing'))]);
```

## Top navigation

`topNavigation()` moves the navigation across the top, in place of the sidebar. Phones keep the drawer.

## Breadcrumbs

`breadcrumbs(false)` removes them.

[See the sidebar in the demo](demo:/): Showcase folds away, and the Orders badge has a color and a tooltip.
