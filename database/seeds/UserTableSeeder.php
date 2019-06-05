<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('students')->delete();

        User::create([
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password')
        ]);

        User::create([
            'name' => 'test2',
            'email' => 'test2@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
