<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'firstname' => 'admin1',
            'lastname' => 'admin1',
            'phone' => '222233333',
            'email' => 'admin1@admin.com',
            'password' => bcrypt('admin'),
            'status' => '1',
        ]);
    }
}
