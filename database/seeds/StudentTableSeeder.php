<?php

use Illuminate\Database\Seeder;
use App\Student;
use Illuminate\Support\Facades\DB;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('students')->delete();

        $faker = Faker\Factory::create();

        for ($i = 0; $i <= 20; $i++) {
            Student::create([
                'name' => $faker->name(),
                'user_id' => 1
            ]);
        }
    }
}
