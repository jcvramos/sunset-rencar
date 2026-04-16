<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleAvailability;
use App\Models\VehicleType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(): Response
    {
        $vehicleTypes = VehicleType::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'emoji']);

        $vehicles = Vehicle::with('vehicleType')
            ->where('is_active', true)
            ->orderBy('vehicle_type_id')
            ->orderBy('name')
            ->get(['id', 'name', 'plate', 'status', 'vehicle_type_id']);

        return Inertia::render('Calendar/Index', [
            'vehicleTypes' => $vehicleTypes,
            'vehicles'     => $vehicles,
        ]);
    }

    /**
     * Devuelve disponibilidad de todos los vehículos en un rango de fechas (JSON para el calendario)
     */
    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
        ]);

        $query = Vehicle::with([
            'vehicleType:id,name,emoji',
            'availability' => function ($q) use ($request) {
                $q->whereBetween('date', [$request->start, $request->end]);
            },
        ])->where('is_active', true);

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        $vehicles = $query->orderBy('vehicle_type_id')->orderBy('name')->get();

        // Construir mapa de ocupación por vehículo
        $data = $vehicles->map(function ($vehicle) {
            $busyDates = $vehicle->availability->pluck('status', 'date')->map(
                fn($status, $date) => ['date' => $date, 'status' => $status]
            )->values();

            return [
                'id'           => $vehicle->id,
                'name'         => $vehicle->name,
                'plate'        => $vehicle->plate,
                'status'       => $vehicle->status,
                'vehicle_type' => $vehicle->vehicleType?->only('id', 'name', 'emoji'),
                'busy_dates'   => $busyDates,
            ];
        });

        return response()->json($data);
    }

    /**
     * Bloquea fechas manualmente en un vehículo
     */
    public function block(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'dates'      => 'required|array|min:1',
            'dates.*'    => 'date',
            'status'     => 'required|in:mantenimiento,bloqueado,reservado',
            'notes'      => 'nullable|string',
        ]);

        foreach ($request->dates as $date) {
            VehicleAvailability::updateOrCreate(
                ['vehicle_id' => $request->vehicle_id, 'date' => $date],
                ['status' => $request->status, 'notes' => $request->notes]
            );
        }

        return response()->json(['message' => 'Fechas bloqueadas correctamente.']);
    }

    /**
     * Libera fechas de un vehículo
     */
    public function unblock(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'dates'      => 'required|array|min:1',
            'dates.*'    => 'date',
        ]);

        VehicleAvailability::where('vehicle_id', $request->vehicle_id)
            ->whereIn('date', $request->dates)
            ->whereNull('reservation_id')
            ->delete();

        return response()->json(['message' => 'Fechas liberadas correctamente.']);
    }

    /**
     * Verifica disponibilidad de un vehículo para fechas específicas
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'start_date'      => 'required|date|after_or_equal:today',
            'end_date'        => 'required|date|after_or_equal:start_date',
        ]);

        $query = Vehicle::where('is_active', true)->where('status', '!=', 'inactivo');

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        $availableVehicles = $query->whereDoesntHave('availability', function ($q) use ($request) {
            $q->whereBetween('date', [$request->start_date, $request->end_date]);
        })->with('vehicleType:id,name,emoji')->get(['id', 'name', 'plate', 'daily_rate', 'deposit_amount', 'vehicle_type_id']);

        return response()->json([
            'available' => $availableVehicles->count() > 0,
            'vehicles'  => $availableVehicles,
            'count'     => $availableVehicles->count(),
        ]);
    }
}
