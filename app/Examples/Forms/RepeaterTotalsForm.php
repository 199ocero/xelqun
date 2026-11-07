<?php

namespace App\Examples\Forms;

use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Grid;
use Xelqun\Forms\Layouts\Section;

/**
 * Repeater rows with stable keys, and client-side calculations that the
 * server recomputes on submit.
 */
class RepeaterTotalsForm extends Form
{
    public function schema(): array
    {
        return [
            Repeater::make('items')
                ->label('Line items')
                ->itemLabel('Item')
                ->defaultItems(2)
                ->minItems(1)
                ->maxItems(10)
                ->columns(['default' => 2, 'md' => 4])
                ->schema([
                    TextInput::make('description')->required()->columnSpan(['default' => 'full', 'md' => 4]),
                    TextInput::make('qty')->label('Qty')->numeric()->minValue(1)->default(1)->required(),
                    TextInput::make('price')->numeric()->prefix('$')->minValue(0)->required(),
                    TextInput::make('subtotal')
                        ->numeric()
                        ->prefix('$')
                        ->computed('round(qty * price, 2)')
                        ->disabled()
                        ->columnSpan(['default' => 'full', 'md' => 2]),
                ]),

            Section::make('Totals')->schema([
                Grid::make(['default' => 2, 'md' => 4])->schema([
                    TextInput::make('items_total')->label('Items')->numeric()->prefix('$')->computed('sum(items.*.subtotal)')->disabled(),
                    TextInput::make('discount_rate')->label('Discount rate')->numeric()->suffix('%')->minValue(0)->maxValue(100)->default(0),
                    TextInput::make('tax_rate')->label('Tax rate')->numeric()->suffix('%')->minValue(0)->default(12),
                    TextInput::make('item_count')->label('Line count')->numeric()->computed('count(items.*)')->disabled(),
                ]),
                Grid::make(['default' => 1, 'md' => 3])->schema([
                    TextInput::make('discount')->numeric()->prefix('−$')->computed('round(items_total * discount_rate / 100, 2)')->disabled(),
                    TextInput::make('tax')->numeric()->prefix('$')->computed('round((items_total - discount) * tax_rate / 100, 2)')->disabled(),
                    TextInput::make('grand_total')->label('Grand total')->numeric()->prefix('$')->computed('round(items_total - discount + tax, 2)')->disabled(),
                ]),
            ]),
        ];
    }
}
