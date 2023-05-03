<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = base_path("database/data/users.csv");

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {
                $item['id'] = (int) $item['id'];
                $item['location_id'] = (int) $item['location_id'];
                $item = $item->toArray();
                User::updateOrCreate(
                    ["id" => $item['id']],
                    $item
                );
            });
    }
}
