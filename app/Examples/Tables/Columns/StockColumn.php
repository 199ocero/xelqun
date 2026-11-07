<?php

namespace App\Examples\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Xelqun\Tables\Columns\Column;

/**
 * A column of our own, made with make:xelqun-column. It uses the same contract
 * as the built-in columns.
 */
class StockColumn extends Column
{
    protected int $full = 250;

    public function getType(): string
    {
        return 'app/stock';
    }

    public function full(int $amount): static
    {
        $this->full = $amount;

        return $this;
    }

    protected function getCellProps(Model $record, mixed $state): array
    {
        return ['percent' => min(100, (int) round((int) $state / $this->full * 100))];
    }
}
