<?php

namespace App\Examples;

use App\Examples\Tables\CategoriesTable;
use App\Examples\Tables\ProductsTable;
use ReflectionClass;
use Xelqun\Tables\Table;

/**
 * The live table examples on the docs pages.
 */
enum TableExample: string
{
    case Products = 'products';
    case TwoTables = 'two-tables';

    public function title(): string
    {
        return match ($this) {
            self::Products => 'Products',
            self::TwoTables => 'Two tables on a page',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Products => 'A table defined in a PHP class and rendered with no Vue written for it. Search, sort or change the page: the browser sends only the state, the server rebuilds the table from a token, and the URL keeps up, so a refresh or a shared link shows the same records. Search and sorting reach across the category relation, and filters are real form fields: pick a year and the months follow from the server. Actions reuse forms: the edit modal has its own fields and upload, and the server finds each row again through the table\'s query.',
            self::TwoTables => 'Each table keeps its own state in the URL under its own name, so paging one leaves the other alone. Refresh and both stay where they were.',
        };
    }

    /**
     * Short labels for the features an example demonstrates.
     *
     * @return array<int, string>
     */
    public function highlights(): array
    {
        return match ($this) {
            self::Products => ['Table', 'TextColumn', 'searchable()', 'sortable()', 'defaultSort()', 'relationship columns', 'badge()', 'money()', 'date()', 'url()', 'IconColumn', 'ImageColumn', 'a custom column', 'SelectFilter', 'TernaryFilter', 'Filter', 'dependent filter', 'recordActions()', 'headerActions()', 'ActionGroup', 'notifications', 'modal with upload', 'BulkAction', 'selection across pages', 'URL state'],
            self::TwoTables => ['two tables', 'URL state', 'withCount'],
        };
    }

    /**
     * @return class-string<Table>
     */
    public function tableClass(): string
    {
        return match ($this) {
            self::Products => ProductsTable::class,
            self::TwoTables => CategoriesTable::class,
        };
    }

    public function makeTable(): Table
    {
        return $this->tableClass()::make();
    }

    public function makeSecondTable(): ?Table
    {
        return $this === self::TwoTables ? ProductsTable::make() : null;
    }

    /**
     * The example's PHP source, shown beside the table.
     */
    public function source(): string
    {
        return (string) file_get_contents((string) (new ReflectionClass($this->tableClass()))->getFileName());
    }

    public function sourcePath(): string
    {
        return str_replace(base_path().'/', '', (string) (new ReflectionClass($this->tableClass()))->getFileName());
    }

    /**
     * @return array<int, array{slug: string, title: string}>
     */
    public static function navigation(): array
    {
        return array_map(
            fn (self $example): array => ['slug' => $example->value, 'title' => $example->title()],
            self::cases(),
        );
    }
}
