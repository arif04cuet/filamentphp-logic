<?php

namespace Database\Seeders;

use App\Models\CrfBeneficiary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrfBeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = base_path("database/data/crf_beneficiaries.csv");

        CrfBeneficiary::truncate();

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {

                $item['round'] = (int) $item['round'];
                $item['location_id'] = (int) $item['location_id'];
                return $item;
            })
            ->chunk(200)
            ->each(fn ($chunk) => CrfBeneficiary::insert($chunk->toArray()));
    }
}
