<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // إنشاء حساب الأدمن الرئيسي إذا لم يكن موجوداً
        User::updateOrCreate(
            ['email' => 'admin@goldradar.com'],
            [
                'name' => 'الأدمن الرئيسي',
                'password' => Hash::make('Admin@123456'), // كلمة المرور
                'role' => 'admin'
            ]
        );
    }
}
