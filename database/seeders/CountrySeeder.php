<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Countries and states used across the demo.
     */
    public function run(): void
    {
        $countries = [
            ['Philippines', 'PH', '+63', 'PHP', ['Cebu', 'Davao del Sur', 'Iloilo', 'Metro Manila', 'Palawan']],
            ['United States', 'US', '+1', 'USD', ['California', 'New York', 'Texas', 'Washington', 'Florida']],
            ['Japan', 'JP', '+81', 'JPY', ['Hokkaido', 'Kyoto', 'Osaka', 'Tokyo', 'Okinawa']],
            ['Germany', 'DE', '+49', 'EUR', ['Bavaria', 'Berlin', 'Hamburg', 'Saxony']],
            ['Australia', 'AU', '+61', 'AUD', ['New South Wales', 'Queensland', 'Victoria', 'Western Australia']],
            ['Canada', 'CA', '+1', 'CAD', ['Alberta', 'British Columbia', 'Ontario', 'Quebec']],
            ['Netherlands', 'NL', '+31', 'EUR', ['North Holland', 'South Holland', 'Utrecht']],
            ['Brazil', 'BR', '+55', 'BRL', ['Bahia', 'Minas Gerais', 'Rio de Janeiro', 'São Paulo']],
        ];

        foreach ($countries as [$name, $code, $dialCode, $currency, $states]) {
            $country = Country::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'dial_code' => $dialCode, 'currency' => $currency],
            );

            foreach ($states as $state) {
                $country->states()->updateOrCreate(['name' => $state]);
            }
        }
    }
}
