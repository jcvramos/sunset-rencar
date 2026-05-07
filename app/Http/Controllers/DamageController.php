<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DamageController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Damage::with([
            'vehicle:id,name,plate',
            'client:id,first_name,last_name,status',
            'reservation:id,code',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        return Inertia::render('Damages/Index', [
            'damages'  => $query->orderByDesc('id')->paginate(20)->withQueryString(),
            'vehicles' => Vehicle::orderBy('name')->get(['id', 'name', 'plate']),
            'filters'  => $request->only('status', 'vehicle_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reservation_id'    => 'nullable|exists:reservations,id',
            'vehicle_id'        => 'required|exists:vehicles,id',
            'client_id'         => 'nullable|exists:clients,id',
            'zone'              => 'required|string|max:30',
            'description'       => 'required|string|max:1000',
            'evidence_photo'    => 'nullable|file|image|max:10240',
            'amount_charged'    => 'nullable|numeric|min:0',
            'linked_to_deposit' => 'boolean',
            'occurred_at'       => 'nullable|date',
            'notes'             => 'nullable|string',
        ]);

        if ($request->hasFile('evidence_photo')) {
            $data['evidence_photo'] = $request->file('evidence_photo')->store('damages', 'public');
        }

        $data['reported_by'] = $request->user()->id;
        $data['reported_at'] = now();
        $data['status']      = 'pendiente';

        // Si no se eligió cliente pero hay reserva, usar el cliente de la reserva
        if (empty($data['client_id']) && !empty($data['reservation_id'])) {
            $data['client_id'] = Reservation::find($data['reservation_id'])?->client_id;
        }

        Damage::create($data);

        return back()->with('success', 'Daño registrado.');
    }

    public function update(Request $request, Damage $damage): RedirectResponse
    {
        $data = $request->validate([
            'status'            => 'required|in:pendiente,cobrado,disputado,absorbido',
            'amount_charged'    => 'nullable|numeric|min:0',
            'linked_to_deposit' => 'boolean',
            'notes'             => 'nullable|string',
        ]);

        if (in_array($data['status'], ['cobrado', 'absorbido', 'disputado']) && !$damage->resolved_at) {
            $data['resolved_by'] = $request->user()->id;
            $data['resolved_at'] = now();
        }

        $damage->update($data);

        return back()->with('success', 'Daño actualizado.');
    }

    public function destroy(Damage $damage): RedirectResponse
    {
        $damage->delete();
        return back()->with('success', 'Daño eliminado.');
    }
}
