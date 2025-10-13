<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'              => 1,
                'name'            => 'Admin',
                'email'           => 'admin@admin.com',
                'password'        => bcrypt('password'),
                'remember_token'  => null,
                'company_name'    => '',
                'gst_number'      => '',
                'date_joining'    => '',
                'mobile_number'   => '',
                'whatsapp_number' => '',
                'bank_name'       => '',
                'branch_name'     => '',
                'ifsc'            => '',
                'ac_holder_name'  => '',
                'pan_number'      => '',
            ],
        ];

        User::insert($users);
    }
}
