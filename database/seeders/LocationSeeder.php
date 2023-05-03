<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = base_path("database/data/locations.csv");

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {

                $type_id = (int) $item['location_type_id'];

                $item = [
                    'id' => (int) $item['id'],
                    'name' => $item['name'],
                    'user_id' => (int) $item['user_id'],
                    'parent_id' => (int) $item['parent_id'],
                    'location_type_id' => $this->getType($type_id),
                    'logic_location' => (int) $item['logic_location']
                ];

                Location::updateOrCreate(
                    ["id" => $item['id']],
                    $item
                );
            });
    }

    public function getType($id)
    {
        return match ($id) {
            1 => 6,
            2 => 3,
            3 => 2,
            4 => 1,
            5 => 5,
            6 => 7,
            7 => 4
        };
    }
}
