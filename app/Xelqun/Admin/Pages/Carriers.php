<?php

namespace App\Xelqun\Admin\Pages;

use App\Examples\Tables\CarriersTable;
use Xelqun\Panels\Pages\Page;

/**
 * A table of custom data: its rows come from records(), not a query.
 */
class Carriers extends Page
{
    protected static ?string $navigationIcon = 'truck';

    protected static ?string $navigationGroup = 'Showcase';

    protected static int $navigationSort = 2;

    public function getSubheading(): ?string
    {
        return 'Rows from an array, the way an API would send them, with search, sorting, a filter and pages.';
    }

    public function content(): array
    {
        return [CarriersTable::make()];
    }
}
