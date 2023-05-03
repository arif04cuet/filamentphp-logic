<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserPasswordSeeder extends Seeder
{

    public function run()
    {
        $csvFile = base_path("database/data/user_password.csv");

        readCSV($csvFile, array('delimiter' => ','))
            ->each(function ($item) {

                $email = trim($item['username']) . '@logicbd.org';
                $password = $item['password'];

                if ($user = User::whereEmail($email)->first()) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            });
    }
}
