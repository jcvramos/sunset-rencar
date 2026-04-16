<?php

namespace Database\Seeders;

use App\Models\PolicyCancellation;
use App\Models\PolicyDestination;
use App\Models\User;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Roles del sistema ---
        $roles = [
            'gerente',
            'agente',
            'entregas',
            'contabilidad',
            'subarrendador',
            'freelance_nocturno',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // --- Usuario administrador ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@sunsetrencar.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin@2025!'),
            ]
        );
        $admin->assignRole('gerente');

        // --- Tipos de vehículo (los 6 del bot) ---
        $vehicleTypes = [
            ['name' => 'Pickup',           'slug' => 'pickup',          'emoji' => '🛻', 'seats' => 5,  'sort_order' => 1],
            ['name' => 'Camioneta 2 Filas', 'slug' => 'camioneta-2f',   'emoji' => '🚙', 'seats' => 5,  'sort_order' => 2],
            ['name' => 'Camioneta 3 Filas', 'slug' => 'camioneta-3f',   'emoji' => '🚐', 'seats' => 7,  'sort_order' => 3],
            ['name' => 'Turismo',           'slug' => 'turismo',         'emoji' => '🚗', 'seats' => 5,  'sort_order' => 4],
            ['name' => 'Microbus 11p',      'slug' => 'microbus-11p',    'emoji' => '🚌', 'seats' => 11, 'sort_order' => 5],
            ['name' => 'Bus 17p',           'slug' => 'bus-17p',         'emoji' => '🚍', 'seats' => 17, 'sort_order' => 6],
        ];

        foreach ($vehicleTypes as $type) {
            VehicleType::firstOrCreate(['slug' => $type['slug']], $type);
        }

        // --- Destinos autorizados base (Honduras) ---
        $destinations = [
            ['municipality' => 'San Pedro Sula',    'department' => 'Cortés',        'status' => 'autorizado'],
            ['municipality' => 'Tegucigalpa',        'department' => 'Francisco Morazán', 'status' => 'autorizado'],
            ['municipality' => 'La Ceiba',           'department' => 'Atlántida',     'status' => 'autorizado'],
            ['municipality' => 'El Progreso',        'department' => 'Yoro',          'status' => 'autorizado'],
            ['municipality' => 'Choloma',            'department' => 'Cortés',        'status' => 'autorizado'],
            ['municipality' => 'Puerto Cortés',      'department' => 'Cortés',        'status' => 'autorizado'],
            ['municipality' => 'Villanueva',         'department' => 'Cortés',        'status' => 'autorizado'],
            ['municipality' => 'Comayagua',          'department' => 'Comayagua',     'status' => 'autorizado'],
            ['municipality' => 'Choluteca',          'department' => 'Choluteca',     'status' => 'autorizado'],
            ['municipality' => 'Santa Rosa de Copán','department' => 'Copán',         'status' => 'autorizado'],
            ['municipality' => 'Roatán',             'department' => 'Islas de la Bahía', 'status' => 'restringido', 'notes' => 'Requiere aprobación gerencial'],
            ['municipality' => 'Frontera Guatemala', 'department' => null,            'status' => 'bloqueado', 'notes' => 'No se autoriza salida del país'],
            ['municipality' => 'Frontera El Salvador','department' => null,           'status' => 'bloqueado', 'notes' => 'No se autoriza salida del país'],
            ['municipality' => 'Frontera Nicaragua', 'department' => null,            'status' => 'bloqueado', 'notes' => 'No se autoriza salida del país'],
        ];

        foreach ($destinations as $dest) {
            PolicyDestination::firstOrCreate(
                ['municipality' => $dest['municipality']],
                $dest
            );
        }

        // --- Políticas de cancelación ---
        $cancellations = [
            ['hours_before' => 48,  'hours_before_max' => null, 'refund_percentage' => 100.00, 'label' => '+48h de anticipación = 100% devolución'],
            ['hours_before' => 24,  'hours_before_max' => 47,   'refund_percentage' => 75.00,  'label' => '24-47h de anticipación = 75% devolución'],
            ['hours_before' => 12,  'hours_before_max' => 23,   'refund_percentage' => 50.00,  'label' => '12-23h de anticipación = 50% devolución'],
            ['hours_before' => 0,   'hours_before_max' => 11,   'refund_percentage' => 0.00,   'label' => 'Menos de 12h = Sin devolución'],
        ];

        foreach ($cancellations as $policy) {
            PolicyCancellation::firstOrCreate(
                ['hours_before' => $policy['hours_before'], 'hours_before_max' => $policy['hours_before_max']],
                $policy
            );
        }
    }
}
