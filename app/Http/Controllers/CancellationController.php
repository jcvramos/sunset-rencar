<?php

namespace App\Http\Controllers;

use App\Models\Cancellation;
use App\Models\Reservation;
use App\Models\SublessorPayable;
use App\Models\SystemNotification;
use App\Models\VehicleAvailability;
use App\Services\ReservationFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancellationController extends Controller
{
    public function __construct(private ReservationFlowService $flow) {}

    public function index(Request $request)
    {
        $cancellations = Cancellation::with(['reservation.client:id,first_name,last_name', 'reservation.vehicle:id,name,plate'])
            ->orderByDesc('id')
            ->paginate(20);

        return inertia('Cancellations/Index', [
            'cancellations' => $cancellations,
        ]);
    }

    public function store(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'reason'    => 'required|string|max:500',
            'cancel_at' => 'nullable|date',
        ]);

        if (in_array($reservation->current_stage, ['cerrada', 'cancelada'])) {
            return back()->with('error', 'La reserva ya está cerrada o cancelada.');
        }

        $cancelAt  = $data['cancel_at'] ?? now();
        $applied   = $this->flow->applyCancellationPolicy($reservation, \Carbon\Carbon::parse($cancelAt));

        DB::transaction(function () use ($reservation, $request, $data, $applied) {
            Cancellation::create([
                'reservation_id'         => $reservation->id,
                'policy_cancellation_id' => $applied['policy']?->id,
                'reason'                 => $data['reason'],
                'hours_anticipation'     => $applied['hours_anticipation'],
                'refund_percentage'      => $applied['refund_percentage'],
                'total_paid'             => $applied['total_paid'],
                'amount_refunded'        => $applied['amount_refunded'],
                'amount_retained'        => $applied['amount_retained'],
                'status'                 => 'pendiente_firma',
                'created_by'             => $request->user()->id,
            ]);

            $reservation->update(['current_stage' => 'cancelada']);

            // Liberar fechas en calendario
            VehicleAvailability::where('reservation_id', $reservation->id)->delete();

            // Reversar cuenta por pagar al subarrendador
            SublessorPayable::where('reservation_id', $reservation->id)
                ->where('status', 'pendiente')
                ->update(['status' => 'reversado']);

            // Notificar
            SystemNotification::create([
                'type'           => 'reserva_cancelada',
                'recipient_type' => \App\Models\Client::class,
                'recipient_id'   => $reservation->client_id,
                'source_type'    => Reservation::class,
                'source_id'      => $reservation->id,
                'channel'        => 'whatsapp',
                'title'          => 'Reserva cancelada',
                'message'        => "Tu reserva {$reservation->code} fue cancelada. Devolución: {$applied['refund_percentage']}%.",
                'status'         => 'pendiente',
            ]);
        });

        return back()->with('success', "Cancelación registrada. Devolución: L. " . number_format($applied['amount_refunded'], 2));
    }

    public function sign(Request $request, Cancellation $cancellation): RedirectResponse
    {
        $request->validate([
            'signature' => 'required|file|image|max:5120',
        ]);

        $path = $request->file('signature')->store("cancellations/{$cancellation->id}", 'public');
        $cancellation->update([
            'signature_path' => $path,
            'status'         => 'firmado',
        ]);

        return back()->with('success', 'Firma del cliente registrada.');
    }
}
