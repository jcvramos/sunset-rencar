<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(): \Inertia\Response
    {
        $vehicleTypes = \App\Models\VehicleType::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'emoji', 'seats', 'description']);

        $vehicles = \App\Models\Vehicle::with('vehicleType')
            ->where('is_active', true)
            ->where('status', 'disponible')
            ->select('id', 'name', 'vehicle_type_id', 'brand', 'model', 'year', 'color', 'seats', 'daily_rate', 'main_photo', 'photos')
            ->orderBy('vehicle_type_id')
            ->get();

        return \Inertia\Inertia::render('Landing', [
            'vehicleTypes' => $vehicleTypes,
            'vehicles'     => $vehicles,
        ]);
    }
}
