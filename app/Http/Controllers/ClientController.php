<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('identity_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_type')) {
            $query->where('client_type', $request->client_type);
        }

        $clients = $query->orderByRaw("FIELD(status, 'bloqueado', 'observacion', 'normal', 'vip')")
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search', 'status', 'client_type'),
        ]);
    }

    public function show(Client $client): Response
    {
        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:80',
            'last_name'       => 'required|string|max:80',
            'identity_number' => 'nullable|string|max:30',
            'phone'           => 'nullable|string|max:20',
            'whatsapp'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'nationality'     => 'nullable|string|max:5',
            'client_type'     => 'required|in:nacional,extranjero,corporativo',
            'status'          => 'required|in:normal,vip,observacion,bloqueado',
            'block_reason'    => 'nullable|string|required_if:status,bloqueado',
            'notes'           => 'nullable|string',
            'source'          => 'nullable|string|max:50',
            'city'            => 'nullable|string|max:80',
            'country'         => 'nullable|string|max:60',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:80',
            'last_name'       => 'required|string|max:80',
            'identity_number' => 'nullable|string|max:30',
            'phone'           => 'nullable|string|max:20',
            'whatsapp'        => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:100',
            'nationality'     => 'nullable|string|max:5',
            'client_type'     => 'required|in:nacional,extranjero,corporativo',
            'status'          => 'required|in:normal,vip,observacion,bloqueado',
            'block_reason'    => 'nullable|string|required_if:status,bloqueado',
            'notes'           => 'nullable|string',
            'source'          => 'nullable|string|max:50',
            'city'            => 'nullable|string|max:80',
            'country'         => 'nullable|string|max:60',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function updateStatus(Request $request, Client $client): RedirectResponse
    {
        $request->validate([
            'status'       => 'required|in:normal,vip,observacion,bloqueado',
            'block_reason' => 'nullable|string|required_if:status,bloqueado',
        ]);

        $client->update([
            'status'       => $request->status,
            'block_reason' => $request->block_reason,
        ]);

        return back()->with('success', 'Estado del cliente actualizado.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado.');
    }
}
