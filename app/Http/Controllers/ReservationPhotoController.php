<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationPhoto;
use App\Services\ReservationFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReservationPhotoController extends Controller
{
    public function store(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'type'  => 'required|in:entrega,recepcion',
            'zone'  => 'required|in:' . implode(',', ReservationFlowService::REQUIRED_PHOTO_ZONES),
            'photo' => 'required|file|image|max:10240',
            'notes' => 'nullable|string',
        ]);

        $path = $request->file('photo')->store(
            "reservations/{$reservation->id}/{$data['type']}",
            'public'
        );

        ReservationPhoto::updateOrCreate(
            [
                'reservation_id' => $reservation->id,
                'type'           => $data['type'],
                'zone'           => $data['zone'],
            ],
            [
                'path'         => $path,
                'original_name'=> $request->file('photo')->getClientOriginalName(),
                'mime_type'    => $request->file('photo')->getClientMimeType(),
                'size'         => $request->file('photo')->getSize(),
                'notes'        => $data['notes'] ?? null,
                'captured_by'  => $request->user()->id,
                'captured_at'  => now(),
            ]
        );

        return back()->with('success', "Foto {$data['zone']} ({$data['type']}) guardada.");
    }

    public function destroy(Reservation $reservation, ReservationPhoto $photo): RedirectResponse
    {
        abort_unless($photo->reservation_id === $reservation->id, 404);

        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        $photo->delete();

        return back()->with('success', 'Foto eliminada.');
    }
}
