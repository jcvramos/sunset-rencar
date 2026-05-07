<?php

namespace App\Services;

use App\Models\PolicyCancellation;
use App\Models\Reservation;
use App\Models\ReservationPhoto;
use App\Models\ReservationStage;
use App\Models\SublessorPayable;
use App\Models\SystemNotification;
use App\Models\VehicleAvailability;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationFlowService
{
    public const FLOW = [
        'cotizacion',
        'deposito',
        'validacion',
        'confirmacion',
        'entrega',
        'activa',
        'extension',
        'recepcion',
        'factura',
    ];

    public const REQUIRED_PHOTO_ZONES = [
        'frontal', 'trasera', 'lateral_izq', 'lateral_der',
        'interior_frontal', 'interior_trasero', 'tablero', 'odometro',
    ];

    public function bootstrapStages(Reservation $reservation): void
    {
        foreach (self::FLOW as $stage) {
            ReservationStage::firstOrCreate(
                ['reservation_id' => $reservation->id, 'stage' => $stage],
                ['status' => $stage === 'cotizacion' ? 'completada' : 'pendiente']
            );
        }
    }

    public function canAdvance(Reservation $reservation, string $toStage): bool
    {
        $currentIndex = array_search($reservation->current_stage, self::FLOW, true);
        $targetIndex  = array_search($toStage, self::FLOW, true);
        if ($currentIndex === false || $targetIndex === false) return false;

        // No saltar etapas
        if ($targetIndex !== $currentIndex + 1 && $toStage !== 'extension') {
            return false;
        }

        // Validaciones específicas por etapa
        return match ($toStage) {
            'confirmacion' => $reservation->deposit_status === 'pagado',
            'activa'       => $this->hasAllDeliveryPhotos($reservation),
            'factura'      => $this->hasAllReceptionPhotos($reservation),
            default        => true,
        };
    }

    public function advanceTo(Reservation $reservation, string $toStage, array $payload = [], ?int $userId = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $toStage, $payload, $userId) {
            // Marcar la etapa actual como completada
            ReservationStage::updateOrCreate(
                ['reservation_id' => $reservation->id, 'stage' => $reservation->current_stage],
                [
                    'status'       => 'completada',
                    'data'         => $payload,
                    'completed_by' => $userId,
                    'completed_at' => now(),
                ]
            );

            $reservation->current_stage = $toStage;
            $reservation->save();

            // Disparadores automáticos por etapa
            match ($toStage) {
                'confirmacion' => $this->onConfirm($reservation),
                'entrega'      => $this->onDelivery($reservation),
                'recepcion'    => $this->onReception($reservation),
                'cerrada'      => $this->onClose($reservation),
                default        => null,
            };

            return $reservation->fresh();
        });
    }

    public function hasAllDeliveryPhotos(Reservation $reservation): bool
    {
        return $this->countPhotosByType($reservation, 'entrega') === count(self::REQUIRED_PHOTO_ZONES);
    }

    public function hasAllReceptionPhotos(Reservation $reservation): bool
    {
        return $this->countPhotosByType($reservation, 'recepcion') === count(self::REQUIRED_PHOTO_ZONES);
    }

    private function countPhotosByType(Reservation $reservation, string $type): int
    {
        return ReservationPhoto::where('reservation_id', $reservation->id)
            ->where('type', $type)
            ->distinct('zone')
            ->count('zone');
    }

    public function applyCancellationPolicy(Reservation $reservation, Carbon $cancelAt): array
    {
        $hours = $cancelAt->diffInHours($reservation->start_date->startOfDay(), false);
        $policy = PolicyCancellation::where('is_active', true)
            ->where('hours_before', '<=', $hours)
            ->orderByDesc('hours_before')
            ->first();

        $refundPct = $policy?->refund_percentage ?? 0;
        $totalPaid = (float) $reservation->total;
        $refunded  = round($totalPaid * $refundPct / 100, 2);
        $retained  = round($totalPaid - $refunded, 2);

        return [
            'policy'             => $policy,
            'hours_anticipation' => max(0, $hours),
            'refund_percentage'  => $refundPct,
            'total_paid'         => $totalPaid,
            'amount_refunded'    => $refunded,
            'amount_retained'    => $retained,
        ];
    }

    /**
     * Bloquea las fechas de la reserva en el calendario.
     */
    private function lockCalendar(Reservation $reservation): void
    {
        if (!$reservation->vehicle_id) return;

        $period = Carbon::parse($reservation->start_date)
            ->daysUntil(Carbon::parse($reservation->end_date)->endOfDay());

        foreach ($period as $day) {
            VehicleAvailability::updateOrCreate(
                ['vehicle_id' => $reservation->vehicle_id, 'date' => $day->toDateString()],
                [
                    'status'         => 'rentado',
                    'reservation_id' => $reservation->id,
                ]
            );
        }
    }

    /**
     * Confirmación de reserva (post-depósito):
     * - Bloquea calendario
     * - Genera cuenta por pagar al subarrendador (si aplica)
     * - Notifica al subarrendador y al cliente
     */
    private function onConfirm(Reservation $reservation): void
    {
        $this->lockCalendar($reservation);
        $this->createPayableIfApplicable($reservation);

        SystemNotification::create([
            'type'           => 'reserva_confirmada',
            'recipient_type' => \App\Models\Client::class,
            'recipient_id'   => $reservation->client_id,
            'source_type'    => Reservation::class,
            'source_id'      => $reservation->id,
            'channel'        => 'whatsapp',
            'title'          => 'Reserva confirmada',
            'message'        => "Tu reserva {$reservation->code} ha sido confirmada.",
            'status'         => 'pendiente',
        ]);
    }

    private function createPayableIfApplicable(Reservation $reservation): void
    {
        if (!$reservation->sublessor_id) return;

        $sublessor = $reservation->sublessor;
        $sharePct  = (float) ($sublessor->default_share ?? 0);
        $amount    = $sharePct > 0
            ? round((float) $reservation->total * $sharePct / 100, 2)
            : (float) $reservation->total;

        SublessorPayable::firstOrCreate(
            ['reservation_id' => $reservation->id, 'sublessor_id' => $sublessor->id],
            [
                'vehicle_id' => $reservation->vehicle_id,
                'amount'     => $amount,
                'currency'   => 'HNL',
                'status'     => 'pendiente',
                'due_date'   => $reservation->end_date,
            ]
        );

        SystemNotification::create([
            'type'           => 'subarrendador_asignado',
            'recipient_type' => \App\Models\Sublessor::class,
            'recipient_id'   => $sublessor->id,
            'source_type'    => Reservation::class,
            'source_id'      => $reservation->id,
            'channel'        => 'whatsapp',
            'title'          => 'Nueva renta asignada',
            'message'        => "Reserva {$reservation->code} asignada a tu vehículo.",
            'status'         => 'pendiente',
        ]);
    }

    private function onDelivery(Reservation $reservation): void
    {
        if ($reservation->vehicle) {
            $reservation->vehicle->update(['status' => 'rentado']);
        }
    }

    private function onReception(Reservation $reservation): void
    {
        if ($reservation->vehicle) {
            $reservation->vehicle->update(['status' => 'disponible']);
        }
    }

    private function onClose(Reservation $reservation): void
    {
        $reservation->update(['current_stage' => 'cerrada']);
    }
}
