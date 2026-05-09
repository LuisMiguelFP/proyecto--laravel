<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@sala.com'],
            [
                'name'              => 'Administrador',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin'          => true,
            ]
        );

        // Usuario de prueba
        User::firstOrCreate(
            ['email' => 'usuario@sala.com'],
            [
                'name'              => 'Usuario Prueba',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin'          => false,
            ]
        );

        // Solo el laboratorio de electrónica
        $space = Space::firstOrCreate(
            ['slug' => 'laboratorio-electronica'],
            [
                'name'           => 'Laboratorio de Electrónica',
                'type'           => 'laboratorio',
                'capacity'       => 20,
                'description'    => 'Laboratorio con estaciones de trabajo, osciloscopios y equipos de medición.',
                'rules'          => 'Uso con supervisión. Equipos deben quedar calibrados al terminar.',
                'price_per_hour' => 15000,
                'is_active'      => true,
            ]
        );

        // Disponibilidad Lun-Vie 6am-8pm
        if ($space->availabilities()->count() === 0) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                $space->availabilities()->create([
                    'day_of_week' => $day,
                    'start_time'  => '06:00',
                    'end_time'    => '20:00',
                ]);
            }
        }
    }
}
