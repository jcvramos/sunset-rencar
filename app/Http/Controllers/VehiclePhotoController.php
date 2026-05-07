<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehiclePhotoController extends Controller
{
    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate([
            'zone'  => 'required|string|in:' . implode(',', array_keys(Vehicle::CATALOG_ZONES)),
            'photo' => 'required|file|image|max:10240',
        ]);

        $photos = $vehicle->photos ?? [];

        // Borrar la previa de esa zona si existe
        if (!empty($photos[$request->zone])) {
            ImageService::delete($photos[$request->zone]);
        }

        $path = ImageService::compressAndStore(
            $request->file('photo'),
            "vehicles/{$vehicle->id}",
            'public',
            1600,
            82,
        );

        $photos[$request->zone] = $path;
        $vehicle->photos = $photos;

        // Si no había foto principal, esta es la primera
        if (!$vehicle->main_photo) {
            $vehicle->main_photo = $path;
        }

        $vehicle->save();

        return back()->with('success', 'Foto guardada.');
    }

    public function destroy(Vehicle $vehicle, string $zone): RedirectResponse
    {
        $photos = $vehicle->photos ?? [];
        if (!isset($photos[$zone])) {
            return back();
        }

        $path = $photos[$zone];
        ImageService::delete($path);
        unset($photos[$zone]);

        $vehicle->photos = $photos;

        // Si era la principal, asignar otra disponible o limpiar
        if ($vehicle->main_photo === $path) {
            $vehicle->main_photo = !empty($photos) ? array_values($photos)[0] : null;
        }

        $vehicle->save();

        return back()->with('success', 'Foto eliminada.');
    }

    public function setMain(Vehicle $vehicle, string $zone): RedirectResponse
    {
        $photos = $vehicle->photos ?? [];
        if (!isset($photos[$zone])) {
            return back();
        }

        $vehicle->update(['main_photo' => $photos[$zone]]);
        return back()->with('success', 'Foto principal actualizada.');
    }
}
