<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $store_name
 * @property string $support_email
 * @property string $currency
 * @property bool $accepts_orders
 */
#[Fillable(['store_name', 'support_email', 'currency', 'accepts_orders'])]
class StoreSetting extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['accepts_orders' => 'boolean'];
    }
}
