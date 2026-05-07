<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DamageController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Damage::query()->select([
                'id', 'reservation_id', 'vehicle_id', 'client_id',
                'zone', 'description', 'evidence_photo', 'extra_photos',
                'amount_charged', 'linked_to_deposit', 'status',
                'occurred_at', 'reported_at', 'created_at',
            ])
            ->with([
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
            'photos'            => 'nullable|array|max:10',
            'photos.*'          => 'file|image|max:10240',
            'amount_charged'    => 'nullable|numeric|min:0',
            'linked_to_deposit' => 'boolean',
            'occurred_at'       => 'nullable|date',
            'notes'             => 'nullable|string',
        ]);

        // Procesar todas las fotos: la primera es la principal, las demás van al array extra_photos
        $stored = [];
        foreach ($request->file('photos', []) as $file) {
            $stored[] = ImageService::compressAndStore($file, 'damages', 'public', 1600, 80);
        }

        if (!empty($stored)) {
            $data['evidence_photo'] = $stored[0];
            $extras = array_slice($stored, 1);
            if (!empty($extras)) {
                $data['extra_photos'] = $extras;
            }
        }

        unset($data['photos']);

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

    public function addPhotos(Request $request, Damage $damage): RedirectResponse
    {
        $request->validate([
            'photos'   => 'required|array|max:10',
            'photos.*' => 'file|image|max:10240',
        ]);

        $extras = $damage->extra_photos ?? [];
        foreach ($request->file('photos') as $file) {
            $path = ImageService::compressAndStore($file, 'damages', 'public', 1600, 80);
            if (empty($damage->evidence_photo)) {
                $damage->evidence_photo = $path;
            } else {
                $extras[] = $path;
            }
        }
        $damage->extra_photos = $extras;
        $damage->save();

        return back()->with('success', 'Fotos agregadas.');
    }

    public function removePhoto(Request $request, Damage $damage): RedirectResponse
    {
        $request->validate(['path' => 'required|string']);
        $path = $request->path;

        // Si es la evidencia principal, mover una de las extras
        if ($damage->evidence_photo === $path) {
            $extras = $damage->extra_photos ?? [];
            ImageService::delete($path);
            $damage->evidence_photo = !empty($extras) ? array_shift($extras) : null;
            $damage->extra_photos   = $extras;
            $damage->save();
            return back()->with('success', 'Foto eliminada.');
        }

        // Es una extra
        $extras = collect($damage->extra_photos ?? [])->reject(fn($p) => $p === $path)->values()->all();
        ImageService::delete($path);
        $damage->extra_photos = $extras;
        $damage->save();

        return back()->with('success', 'Foto eliminada.');
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
