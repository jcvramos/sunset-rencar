<?php

namespace App\Http\Controllers;

use App\Models\PolicyCancellation;
use App\Models\PolicyDestination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PolicyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Policies/Index', [
            'destinations'  => PolicyDestination::orderBy('status')->orderBy('municipality')->get(),
            'cancellations' => PolicyCancellation::orderBy('hours_before')->get(),
        ]);
    }

    // --- Destinos ---

    public function storeDestination(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'municipality' => 'required|string|max:100|unique:policy_destinations,municipality',
            'department'   => 'nullable|string|max:100',
            'status'       => 'required|in:autorizado,restringido,bloqueado',
            'notes'        => 'nullable|string',
        ]);

        PolicyDestination::create($validated);

        return back()->with('success', 'Destino agregado.');
    }

    public function updateDestination(Request $request, PolicyDestination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'municipality' => 'required|string|max:100|unique:policy_destinations,municipality,' . $destination->id,
            'department'   => 'nullable|string|max:100',
            'status'       => 'required|in:autorizado,restringido,bloqueado',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $destination->update($validated);

        return back()->with('success', 'Destino actualizado.');
    }

    public function destroyDestination(PolicyDestination $destination): RedirectResponse
    {
        $destination->delete();

        return back()->with('success', 'Destino eliminado.');
    }

    // --- Políticas de cancelación ---

    public function storeCancellation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hours_before'     => 'required|integer|min:0',
            'hours_before_max' => 'nullable|integer|gt:hours_before',
            'refund_percentage'=> 'required|numeric|min:0|max:100',
            'label'            => 'required|string|max:150',
        ]);

        PolicyCancellation::create($validated);

        return back()->with('success', 'Política de cancelación creada.');
    }

    public function updateCancellation(Request $request, PolicyCancellation $cancellation): RedirectResponse
    {
        $validated = $request->validate([
            'hours_before'     => 'required|integer|min:0',
            'hours_before_max' => 'nullable|integer',
            'refund_percentage'=> 'required|numeric|min:0|max:100',
            'label'            => 'required|string|max:150',
            'is_active'        => 'boolean',
        ]);

        $cancellation->update($validated);

        return back()->with('success', 'Política actualizada.');
    }

    public function destroyCancellation(PolicyCancellation $cancellation): RedirectResponse
    {
        $cancellation->delete();

        return back()->with('success', 'Política eliminada.');
    }
}
