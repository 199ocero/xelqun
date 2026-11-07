<?php

namespace App\Xelqun\Admin\Infolists;

use Xelqun\Panels\Infolists\Entry;
use Xelqun\Tables\Columns\Column;
use Xelqun\Tables\Columns\TextColumn;

/**
 * An entry of our own: a number from 1 to 5, as stars.
 */
class RatingEntry extends Entry
{
    protected static function makeColumn(string $name): Column
    {
        return TextColumn::make($name)->formatStateUsing(fn (?int $state): string => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state));
    }
}
