<?php

namespace App\Http\Controllers;

use App\Models\Sublessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SublessorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Sublessor::with(['vehicles:id,name,plate,sublessor_id,status'])
            ->withCount(['reservations', 'payables as pending_payables_count' => fn($q) => $q->where('status', 'pendiente')])
            ->withSum(['payables as pending_amount' => fn($q) => $q->where('status', 'pendiente')], 'amount')
            ->withSum(['payables as paid_amount' => fn($q) => $q->where('status', 'pagado')], 'amount');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('whatsapp', 'like', "%{$s}%")
                  ->orWhere('identity_number', 'like', "%{$s}%");
            });
        }

        return Inertia::render('Sublessors/Index', [
            'sublessors' => $query->orderBy('name')->paginate(20)->withQueryString(),
            'filters'    => $request->only('search'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Sublessor::create($data);
        return back()->with('success', 'Subarrendador registrado.');
    }

    public function update(Request $request, Sublessor $sublessor): RedirectResponse
    {
        $data = $this->validateData($request, $sublessor->id);
        $sublessor->update($data);
        return back()->with('success', 'Subarrendador actualizado.');
    }

    public function destroy(Sublessor $sublessor): RedirectResponse
    {
        $sublessor->delete();
        return back()->with('success', 'Subarrendador eliminado.');
    }

    private function validateData(Request $request, ?int $ignore = null): array
    {
        return $request->validate([
            'name'            => 'required|string|max:120',
            'identity_number' => 'nullable|string|max:30',
            'phone'           => 'nullable|string|max:20',
            'whatsapp'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:120',
            'address'         => 'nullable|string|max:200',
            'city'            => 'nullable|string|max:80',
            'bank_name'       => 'nullable|string|max:80',
            'bank_account'    => 'nullable|string|max:60',
            'payment_method'  => 'required|in:transferencia,efectivo,cheque',
            'default_share'   => 'nullable|numeric|min:0|max:100',
            'notes'           => 'nullable|string',
            'is_active'       => 'boolean',
        ]);
    }
}
