<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vehicle;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_vehicles'       => Vehicle::where('is_active', true)->count(),
            'available_vehicles'   => Vehicle::where('status', 'disponible')->where('is_active', true)->count(),
            'rented_vehicles'      => Vehicle::where('status', 'rentado')->count(),
            'maintenance_vehicles' => Vehicle::where('status', 'mantenimiento')->count(),
            'total_clients'        => Client::count(),
            'vip_clients'          => Client::where('status', 'vip')->count(),
            'blocked_clients'      => Client::where('status', 'bloqueado')->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
