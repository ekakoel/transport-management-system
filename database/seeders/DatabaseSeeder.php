<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ServiceType;
use App\Models\EmergencyContact;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => env('TMS_ADMIN_EMAIL', 'admin@example.com')], ['name' => 'TMS Administrator', 'phone' => env('TMS_ADMIN_PHONE'), 'password' => Hash::make(env('TMS_ADMIN_PASSWORD', 'ChangeThis-Immediately-2026!')), 'role' => 'admin', 'is_active' => true]);
        User::updateOrCreate(['email' => env('TMS_MANAGER_EMAIL', 'manager@example.com')], ['name' => 'TMS Manager', 'phone' => env('TMS_MANAGER_PHONE'), 'password' => Hash::make(env('TMS_MANAGER_PASSWORD', 'ChangeThis-Immediately-2026!')), 'role' => 'manager', 'is_active' => true]);
        foreach (['Airport Shuttle', 'Point to Point', 'Hourly Charter', 'Full Day Charter', 'Tour Transport'] as $name) ServiceType::firstOrCreate(['name' => $name], ['description' => '', 'is_active' => true]);
        EmergencyContact::firstOrCreate(['name' => 'Operasional Bali Kami Tour'], ['phone' => '', 'whatsapp' => '', 'is_default' => true, 'is_active' => true]);
    }
}
