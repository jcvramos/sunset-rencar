<?php

namespace App\Http\Controllers;

use App\Models\Sublessor;
use App\Models\SublessorPayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SublessorPayableController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SublessorPayable::with([
            'sublessor:id,name,phone,whatsapp',
            'reservation:id,code,start_date,end_date',
            'vehicle:id,name,plate',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sublessor_id')) {
            $query->where('sublessor_id', $request->sublessor_id);
        }

        return Inertia::render('Sublessors/Payables', [
            'payables'   => $query->orderByDesc('id')->paginate(20)->withQueryString(),
            'sublessors' => Sublessor::orderBy('name')->get(['id', 'name']),
            'filters'    => $request->only('status', 'sublessor_id'),
            'totals'     => [
                'pendiente' => (float) SublessorPayable::where('status', 'pendiente')->sum('amount'),
                'pagado'    => (float) SublessorPayable::where('status', 'pagado')->sum('amount'),
            ],
        ]);
    }

    public function pay(Request $request, SublessorPayable $payable): RedirectResponse
    {
        $data = $request->validate([
            'payment_reference' => 'nullable|string|max:120',
            'notes'             => 'nullable|string',
        ]);

        $payable->update([
            'status'            => 'pagado',
            'paid_at'           => now(),
            'payment_reference' => $data['payment_reference'] ?? null,
            'notes'             => $data['notes'] ?? $payable->notes,
            'paid_by'           => $request->user()->id,
        ]);

        return back()->with('success', 'Pago registrado.');
    }

    public function update(Request $request, SublessorPayable $payable): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes'  => 'nullable|string',
        ]);

        $payable->update($data);
        return back()->with('success', 'Cuenta por pagar actualizada.');
    }
}
