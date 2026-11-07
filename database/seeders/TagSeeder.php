<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Tags offered by the order form.
     */
    public function run(): void
    {
        foreach (['Priority', 'Fragile', 'Wholesale', 'Repeat customer'] as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
