<?php

namespace Database\Seeders;

use App\Models\LocationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $csvFile = base_path("database/data/location_types.csv");

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {
                $item['id'] = (int) $item['id'];
                LocationType::updateOrCreate(
                    ["id" => $item['id']],
                    [
                        "name" => $item['name']
                    ]
                );
            });
    }
}
