<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function index(): Response
    {
        $vehicles = Vehicle::with('vehicleType')
            ->orderBy('status')
            ->orderBy('name')
            ->get()
            ->map(fn($v) => [
                'id'           => $v->id,
                'plate'        => $v->plate,
                'name'         => $v->name,
                'brand'        => $v->brand,
                'model'        => $v->model,
                'year'         => $v->year,
                'color'        => $v->color,
                'status'       => $v->status,
                'is_own'       => $v->is_own,
                'daily_rate'   => $v->daily_rate,
                'deposit_amount' => $v->deposit_amount,
                'is_active'    => $v->is_active,
                'vehicle_type' => $v->vehicleType?->only('id', 'name', 'emoji'),
                'main_photo'   => $v->main_photo,
            ]);

        $vehicleTypes = VehicleType::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'emoji']);

        return Inertia::render('Vehicles/Index', [
            'vehicles'     => $vehicles,
            'vehicleTypes' => $vehicleTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plate'           => 'required|string|max:20|unique:vehicles,plate',
            'name'            => 'required|string|max:100',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'brand'           => 'required|string|max:50',
            'model'           => 'required|string|max:50',
            'year'            => 'required|integer|min:2000|max:2030',
            'color'           => 'required|string|max:30',
            'seats'           => 'required|integer|min:1|max:60',
            'status'          => 'required|in:disponible,rentado,mantenimiento,inactivo',
            'is_own'          => 'boolean',
            'daily_rate'      => 'required|numeric|min:0',
            'deposit_amount'  => 'numeric|min:0',
            'description'     => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo registrado correctamente.');
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'plate'           => 'required|string|max:20|unique:vehicles,plate,' . $vehicle->id,
            'name'            => 'required|string|max:100',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'brand'           => 'required|string|max:50',
            'model'           => 'required|string|max:50',
            'year'            => 'required|integer|min:2000|max:2030',
            'color'           => 'required|string|max:30',
            'seats'           => 'required|integer|min:1|max:60',
            'status'          => 'required|in:disponible,rentado,mantenimiento,inactivo',
            'is_own'          => 'boolean',
            'daily_rate'      => 'required|numeric|min:0',
            'deposit_amount'  => 'numeric|min:0',
            'description'     => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }

    public function updateStatus(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:disponible,rentado,mantenimiento,inactivo',
        ]);

        $vehicle->update(['status' => $request->status]);

        return back()->with('success', 'Estado actualizado.');
    }
}
