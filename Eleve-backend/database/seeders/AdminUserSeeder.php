<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrator Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@eleve.com'],
            [
                'name' => 'Élevé Director',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+63 917 123 4567',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
            ]
        );

        // Test Customer Account
        $customer = User::firstOrCreate(
            ['email' => 'customer@eleve.com'],
            [
                'name' => 'Sophia Laurent',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+63 918 987 6543',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80',
            ]
        );

        // Additional sample customers for analytics
        $customers = [
            [
                'name' => 'Alexander Vance',
                'email' => 'alexander@example.com',
                'phone' => '+63 920 111 2233',
            ],
            [
                'name' => 'Camille Dubois',
                'email' => 'camille@example.com',
                'phone' => '+63 917 444 5566',
            ],
            [
                'name' => 'Julian Montgomery',
                'email' => 'julian@example.com',
                'phone' => '+63 919 777 8899',
            ],
        ];

        foreach ($customers as $c) {
            $user = User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'phone' => $c['phone'],
                ]
            );

            Address::firstOrCreate(
                ['user_id' => $user->id, 'is_default' => true],
                [
                    'recipient_name' => $c['name'],
                    'phone_number' => $c['phone'],
                    'street_address' => '128 Paseo de Roxas, Legaspi Village',
                    'apartment' => 'Unit 24B, The Residences',
                    'city' => 'Makati City',
                    'province_state' => 'Metro Manila',
                    'postal_code' => '1229',
                    'country' => 'Philippines',
                ]
            );
        }

        // Default Address for primary customer
        Address::firstOrCreate(
            ['user_id' => $customer->id, 'is_default' => true],
            [
                'recipient_name' => 'Sophia Laurent',
                'phone_number' => '+63 918 987 6543',
                'street_address' => 'Aura Tower, 5th Avenue cor 26th Street',
                'apartment' => 'Penthouse 3A',
                'city' => 'Taguig City',
                'province_state' => 'Metro Manila',
                'postal_code' => '1634',
                'country' => 'Philippines',
            ]
        );
    }
}
