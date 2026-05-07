<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationExtension;
use App\Models\VehicleAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationExtensionController extends Controller
{
    public function store(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'new_end_date'      => 'required|date|after:' . $reservation->end_date->toDateString(),
            'additional_days'   => 'required|integer|min:1',
            'additional_amount' => 'required|numeric|min:0',
            'reason'            => 'nullable|string|max:500',
            'document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'signature'         => 'nullable|file|image|max:5120',
        ]);

        if (!in_array($reservation->current_stage, ['activa', 'entrega'])) {
            return back()->with('error', 'Solo se puede extender una reserva activa.');
        }

        DB::transaction(function () use ($reservation, $data, $request) {
            $previous = $reservation->end_date->copy();
            $newEnd   = Carbon::parse($data['new_end_date']);

            $extension = ReservationExtension::create([
                'reservation_id'    => $reservation->id,
                'previous_end_date' => $previous,
                'new_end_date'      => $newEnd,
                'additional_days'   => $data['additional_days'],
                'additional_amount' => $data['additional_amount'],
                'reason'            => $data['reason'] ?? null,
                'document_path'     => $request->hasFile('document') ? $request->file('document')->store("reservations/{$reservation->id}/extensions", 'public') : null,
                'signature_path'    => $request->hasFile('signature') ? $request->file('signature')->store("reservations/{$reservation->id}/extensions", 'public') : null,
                'approved_by'       => $request->user()->id,
                'approved_at'       => now(),
            ]);

            // Actualizar reserva
            $reservation->update([
                'end_date' => $newEnd,
                'days'     => $reservation->days + $data['additional_days'],
                'subtotal' => $reservation->subtotal + $data['additional_amount'],
                'total'    => $reservation->total + $data['additional_amount'],
            ]);

            // Bloquear nuevas fechas
            if ($reservation->vehicle_id) {
                $period = $previous->copy()->addDay()->daysUntil($newEnd->copy()->endOfDay());
                foreach ($period as $day) {
                    VehicleAvailability::updateOrCreate(
                        ['vehicle_id' => $reservation->vehicle_id, 'date' => $day->toDateString()],
                        ['status' => 'rentado', 'reservation_id' => $reservation->id]
                    );
                }
            }
        });

        return back()->with('success', 'Extensión registrada.');
    }
}
