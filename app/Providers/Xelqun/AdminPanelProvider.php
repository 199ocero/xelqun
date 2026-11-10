<?php

namespace App\Providers\Xelqun;

use App\Demo\DemoAppearance;
use App\Demo\DemoLoginForm;
use App\Demo\DemoPlugin;
use App\Xelqun\Admin\Pages\Components;
use App\Xelqun\Admin\Pages\Home;
use App\Xelqun\Admin\Widgets\CategoryTiles;
use App\Xelqun\Admin\Widgets\LowStock;
use App\Xelqun\Admin\Widgets\OrdersByType;
use App\Xelqun\Admin\Widgets\OrdersChart;
use App\Xelqun\Admin\Widgets\PendingProducts;
use App\Xelqun\Admin\Widgets\ProductsByCategory;
use App\Xelqun\Admin\Widgets\RecentProducts;
use App\Xelqun\Admin\Widgets\StoreStats;
use Xelqun\Panels\Navigation\NavigationGroup;
use Xelqun\Panels\Panel;
use Xelqun\Panels\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->domain(config('xelqun.demo_domain'))
            ->path('')
            ->brandName('Xelqun')
            ->brandLogo(fn (): string => DemoAppearance::logoUrl('light'))
            ->darkModeBrandLogo(fn (): string => DemoAppearance::logoUrl('dark'))
            ->brandLogoHeight('1.75rem')
            ->favicon('/favicon.svg')
            ->font('Inter')
            ->colors(fn (): array => DemoAppearance::colors())
            ->maxContentWidth('7xl')
            ->darkMode()
            ->defaultThemeMode('system')
            ->animations()
            ->breadcrumbs()
            ->globalSearch()
            ->login(DemoLoginForm::class)
            ->passwordReset()
            ->registration()
            ->emailVerification()
            ->profile()
            ->emailChangeVerification()
            ->multiFactorAuthentication()
            ->emailAuthentication()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->pages([Home::class, Components::class])
            ->discoverResources(in: app_path('Xelqun/Admin/Resources'), for: 'App\\Xelqun\\Admin\\Resources')
            ->widgets([StoreStats::class, OrdersChart::class, OrdersByType::class, ProductsByCategory::class, PendingProducts::class, RecentProducts::class, LowStock::class, CategoryTiles::class])
            ->navigationGroups([
                NavigationGroup::make('Shop')->icon('shopping-cart'),
                NavigationGroup::make('Catalog')->icon('package'),
                NavigationGroup::make('Blog')->icon('newspaper'),
                NavigationGroup::make('Showcase')->icon('layers')->collapsible(),
            ])
            ->plugin(new DemoPlugin);
    }
}
