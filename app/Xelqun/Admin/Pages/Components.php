<?php

namespace App\Xelqun\Admin\Pages;

use App\Examples\FormExample;
use App\Examples\Tables\ProductsTable;
use Xelqun\Actions\ActionSet;
use Xelqun\Panels\Pages\FormContent;
use Xelqun\Panels\Pages\Page;

/**
 * Every field, layout and action of forms, and a full table, inside the panel.
 */
class Components extends Page
{
    private const string TABLE = 'table';

    protected static ?string $navigationIcon = 'layers';

    protected static ?string $navigationGroup = 'Showcase';

    public function getSubheading(): ?string
    {
        return $this->active()?->description() ?? 'Filters, sorting, search, column toggles, bulk actions and modals.';
    }

    public function getTabs(): array
    {
        $tabs = array_map(fn (array $example): array => $this->tab($example['slug'], $example['title']), FormExample::navigation());

        return [...array_values($tabs), $this->tab(self::TABLE, 'Full table')];
    }

    public function content(): array
    {
        $example = $this->active();

        if ($example === null) {
            return [ProductsTable::make()];
        }

        $form = $example->makeForm();

        return [$form instanceof ActionSet ? $form : new FormContent($form, route('examples.submit', $example))];
    }

    private function active(): ?FormExample
    {
        $slug = request()->query('tab');

        return $slug === self::TABLE ? null : (FormExample::tryFrom(is_string($slug) ? $slug : '') ?? FormExample::AllFields);
    }

    /**
     * @return array{key: string, label: string, icon: null, badge: null, url: string, active: bool}
     */
    private function tab(string $key, string $label): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'icon' => null,
            'badge' => null,
            'url' => static::getUrl(['tab' => $key]),
            'active' => ($this->active()->value ?? self::TABLE) === $key,
        ];
    }
}
