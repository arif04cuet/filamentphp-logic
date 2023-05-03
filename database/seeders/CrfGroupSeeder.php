<?php

namespace Database\Seeders;

use App\Models\CrfGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrfGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $csvFile = base_path("database/data/crf_groups.csv");

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {

                $item = $item->toArray();

                $item['location_id'] = (int) $item['location_id'];
                $item['crf_round'] = (int) $item['crf_round'];
                $item['male_beneficiaries'] = (int) $item['male_beneficiaries'];
                $item['female_beneficiaries'] = (int) $item['female_beneficiaries'];
                $item['crf_beneficiaries'] = (int) $item['crf_beneficiaries'];
                $item['money_received'] = (int) $item['money_received'];
                $item['money_invested'] = (int) $item['money_invested'];


                CrfGroup::updateOrCreate(
                    ["group_id" => $item['group_id']],
                    $item
                );
            });
    }
}
