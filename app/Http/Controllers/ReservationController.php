<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PolicyDestination;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Services\ReservationFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function __construct(private ReservationFlowService $flow) {}

    public function index(Request $request): Response
    {
        $query = Reservation::with(['client:id,first_name,last_name', 'vehicle:id,name,plate', 'vehicleType:id,name,emoji']);

        if ($request->filled('stage')) {
            $query->where('current_stage', $request->stage);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhereHas('client', fn($qc) => $qc->where('first_name', 'like', "%{$s}%")
                                                     ->orWhere('last_name', 'like', "%{$s}%"));
            });
        }

        $reservations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
            'filters'      => $request->only('stage', 'search'),
            'stages'       => Reservation::STAGES,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Reservations/Create', [
            'clients'      => Client::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'phone', 'whatsapp', 'status']),
            'vehicleTypes' => VehicleType::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'emoji']),
            'vehicles'     => Vehicle::with('vehicleType:id,name,emoji')->where('is_active', true)
                                ->where('status', '!=', 'inactivo')
                                ->get(['id', 'name', 'plate', 'vehicle_type_id', 'daily_rate', 'deposit_amount', 'status']),
            'destinations' => PolicyDestination::where('is_active', true)->orderBy('municipality')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()?->id;
        $data = $request->validate([
            'client_id'                => 'required|exists:clients,id',
            'vehicle_type_id'          => 'required|exists:vehicle_types,id',
            'vehicle_id'               => 'nullable|exists:vehicles,id',
            'start_date'               => 'required|date',
            'end_date'                 => 'required|date|after_or_equal:start_date',
            'daily_rate'               => 'required|numeric|min:0',
            'discount'                 => 'nullable|numeric|min:0',
            'deposit_amount'           => 'nullable|numeric|min:0',
            'destination_municipality' => 'nullable|string|max:120',
            'destination'              => 'nullable|string|max:200',
            'notes'                    => 'nullable|string',
            'source'                   => 'nullable|string|max:30',
        ]);

        $client = Client::findOrFail($data['client_id']);
        if ($client->isBlocked()) {
            return back()->with('error', 'El cliente está bloqueado. Requiere aprobación gerencial.');
        }

        // Bloquear destinos prohibidos automáticamente
        if (!empty($data['destination_municipality'])) {
            $dest = PolicyDestination::where('municipality', $data['destination_municipality'])->first();
            if ($dest && $dest->status === 'bloqueado') {
                return back()->with('error', "Destino bloqueado: {$dest->notes}");
            }
        }

        $days = max(1, \Carbon\Carbon::parse($data['start_date'])->diffInDays($data['end_date']) + 1);
        $subtotal = round($data['daily_rate'] * $days, 2);
        $discount = (float) ($data['discount'] ?? 0);
        $total    = max(0, $subtotal - $discount);

        $reservation = DB::transaction(function () use ($data, $days, $subtotal, $discount, $total, $userId) {
            $vehicle = !empty($data['vehicle_id']) ? Vehicle::find($data['vehicle_id']) : null;
            $sublessorId = $vehicle?->sublessor_id;

            $r = Reservation::create([
                'code'                     => Reservation::generateCode(),
                'client_id'                => $data['client_id'],
                'vehicle_id'               => $data['vehicle_id'] ?? null,
                'vehicle_type_id'          => $data['vehicle_type_id'],
                'sublessor_id'             => $sublessorId,
                'start_date'               => $data['start_date'],
                'end_date'                 => $data['end_date'],
                'days'                     => $days,
                'daily_rate'               => $data['daily_rate'],
                'subtotal'                 => $subtotal,
                'discount'                 => $discount,
                'total'                    => $total,
                'deposit_amount'           => $data['deposit_amount'] ?? 0,
                'destination'              => $data['destination'] ?? null,
                'destination_municipality' => $data['destination_municipality'] ?? null,
                'notes'                    => $data['notes'] ?? null,
                'current_stage'            => 'cotizacion',
                'created_by'               => $userId,
                'source'                   => $data['source'] ?? 'manual',
            ]);

            $this->flow->bootstrapStages($r);
            return $r;
        });

        return redirect()->route('reservations.show', $reservation)
            ->with('success', "Reserva {$reservation->code} creada en etapa Cotización.");
    }

    public function show(Reservation $reservation): Response
    {
        $reservation->load([
            'client', 'vehicle.vehicleType', 'sublessor',
            'stages.completer:id,name',
            'photos', 'extensions', 'damages', 'cancellation', 'payables',
        ]);

        return Inertia::render('Reservations/Show', [
            'reservation'   => $reservation,
            'stages'        => Reservation::STAGES,
            'requiredZones' => ReservationFlowService::REQUIRED_PHOTO_ZONES,
        ]);
    }

    public function advance(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'to_stage' => 'required|string',
            'notes'    => 'nullable|string',
        ]);

        if (!$this->flow->canAdvance($reservation, $request->to_stage)) {
            return back()->with('error', 'No se puede avanzar a esa etapa todavía. Verifica los requisitos.');
        }

        $this->flow->advanceTo(
            $reservation,
            $request->to_stage,
            ['notes' => $request->notes],
            $request->user()->id
        );

        return back()->with('success', 'Etapa avanzada.');
    }

    public function payDeposit(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'deposit_amount' => 'required|numeric|min:0',
            'reference'      => 'nullable|string|max:120',
        ]);

        $reservation->update([
            'deposit_amount'  => $request->deposit_amount,
            'deposit_status'  => 'pagado',
            'deposit_paid_at' => now(),
        ]);

        return back()->with('success', 'Depósito registrado.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $reservation->delete();
        return redirect()->route('reservations.index')->with('success', 'Reserva eliminada.');
    }
}
