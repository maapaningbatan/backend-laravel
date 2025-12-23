<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = [];
        $usedUsernames = [];
        $usedEmails = [];

        for ($i = 0; $i < 1000; $i++) {
            $firstName = $faker->firstName;
            $middleName = $faker->optional()->firstName;
            $lastName = $faker->lastName;

            // Generate a unique username
            do {
                $username = strtolower(substr($firstName, 0, 1) . $lastName . $faker->numberBetween(1, 9999));
            } while (in_array($username, $usedUsernames));
            $usedUsernames[] = $username;

            // Generate a unique email
            do {
                $email = strtolower($firstName . '.' . $lastName . $faker->numberBetween(1, 9999) . '@example.com');
            } while (in_array($email, $usedEmails));
            $usedEmails[] = $email;

            $users[] = [
                'username'       => $username,
                'email'          => $email,
                'password'       => bcrypt('password'),
                'activated'      => $faker->boolean(90),
                'center_id'      => $faker->numberBetween(1, 10),
                'employee_no'    => $faker->unique()->numberBetween(10000, 99999),
                'first_name'     => $firstName,
                'middle_name'    => $middleName,
                'last_name'      => $lastName,
                'suffix'         => $faker->optional()->randomElement(['Jr.', 'Sr.', 'III']),
                'position_id'    => $faker->numberBetween(1, 209),
                'region_id'      => $faker->numberBetween(1, 23),
                'office_id'      => $faker->numberBetween(1, 62),
                'division_id'    => $faker->numberBetween(1, 195),
                'cluster_id'     => $faker->numberBetween(1, 5),
                'user_level_id'  => $faker->numberBetween(1, 5),
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ];
        }

        DB::table('tbl_users')->insert($users);
        $this->command->info('Inserted 1000 unique users successfully!');
    }
}
