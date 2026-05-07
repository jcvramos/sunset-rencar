<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Damage;
use App\Models\ReservationPhoto;
use App\Models\Vehicle;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompressExistingPhotos extends Command
{
    protected $signature = 'photos:compress
        {--dry-run : Solo muestra lo que haría, sin tocar archivos}
        {--min-size=100 : Tamaño mínimo en KB para procesar una foto (default 100)}
        {--max-width=1600 : Ancho máximo en px (default 1600)}
        {--quality=80 : Calidad JPEG 1-100 (default 80)}';

    protected $description = 'Recomprime todas las fotos ya almacenadas (clientes, reservas, vehículos, daños) para liberar espacio.';

    public function handle(): int
    {
        $dry      = $this->option('dry-run');
        $minBytes = (int) $this->option('min-size') * 1024;
        $maxW     = (int) $this->option('max-width');
        $quality  = (int) $this->option('quality');

        $this->info($dry ? '🧪 DRY RUN — sin cambios' : '🔧 Comprimiendo fotos en disco...');
        $this->line("   ancho máx: {$maxW}px · calidad: {$quality} · saltar si <" . round($minBytes / 1024) . "KB");
        $this->newLine();

        $totalProcessed = 0;
        $totalSaved     = 0;

        // 1) Clientes — 4 campos KYC
        $totalProcessed += $this->processColumns(
            Client::class,
            ['license_photo', 'identity_photo', 'selfie_with_id_photo', 'face_photo'],
            'Clientes',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
            1200,  // KYC: 1200px más que suficiente
        );

        // 2) Fotos de reservas (8 zonas × entrega/recepción)
        $totalProcessed += $this->processColumns(
            ReservationPhoto::class,
            ['path'],
            'Reservas (8 zonas)',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
        );

        // 3) Vehículos
        $totalProcessed += $this->processColumns(
            Vehicle::class,
            ['main_photo'],
            'Vehículos (foto principal)',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
        );
        $totalProcessed += $this->processJsonArray(
            Vehicle::class,
            'photos',
            'Vehículos (galería)',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
        );

        // 4) Daños
        $totalProcessed += $this->processColumns(
            Damage::class,
            ['evidence_photo'],
            'Daños (evidencia)',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
        );
        $totalProcessed += $this->processJsonArray(
            Damage::class,
            'extra_photos',
            'Daños (extras)',
            $minBytes, $maxW, $quality, $dry, $totalSaved,
        );

        $this->newLine();
        $this->info("✅ Total procesadas: {$totalProcessed}");
        $this->info('💾 Espacio liberado: ' . $this->humanBytes($totalSaved));

        return self::SUCCESS;
    }

    private function processColumns(
        string $modelClass,
        array  $columns,
        string $label,
        int    $minBytes,
        int    $maxW,
        int    $quality,
        bool   $dry,
        int    &$totalSaved,
        ?int   $overrideMaxW = null,
    ): int {
        $count   = 0;
        $maxWUsed = $overrideMaxW ?? $maxW;

        $this->line("📂 {$label}");
        $bar = $this->output->createProgressBar($modelClass::query()->count());
        $bar->start();

        $modelClass::query()->chunkById(100, function ($rows) use ($columns, &$count, &$totalSaved, $minBytes, $maxWUsed, $quality, $dry, $bar) {
            foreach ($rows as $row) {
                foreach ($columns as $col) {
                    $path = $row->{$col};
                    if (!$path) continue;

                    $saved = $this->recompressOne($path, $minBytes, $maxWUsed, $quality, $dry);
                    if ($saved !== null) {
                        $count++;
                        $totalSaved += $saved;
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        return $count;
    }

    private function processJsonArray(
        string $modelClass,
        string $jsonColumn,
        string $label,
        int    $minBytes,
        int    $maxW,
        int    $quality,
        bool   $dry,
        int    &$totalSaved,
    ): int {
        $count = 0;
        $this->line("📂 {$label}");
        $bar = $this->output->createProgressBar($modelClass::query()->whereNotNull($jsonColumn)->count());
        $bar->start();

        $modelClass::query()->whereNotNull($jsonColumn)->chunkById(100, function ($rows) use ($jsonColumn, &$count, &$totalSaved, $minBytes, $maxW, $quality, $dry, $bar) {
            foreach ($rows as $row) {
                $paths = $row->{$jsonColumn};
                if (!is_array($paths)) { $bar->advance(); continue; }
                foreach ($paths as $p) {
                    if (!is_string($p)) continue;
                    $saved = $this->recompressOne($p, $minBytes, $maxW, $quality, $dry);
                    if ($saved !== null) {
                        $count++;
                        $totalSaved += $saved;
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        return $count;
    }

    private function recompressOne(string $path, int $minBytes, int $maxW, int $quality, bool $dry): ?int
    {
        $disk = Storage::disk('public');
        if (!$disk->exists($path)) return null;

        $size = $disk->size($path);
        if ($size < $minBytes) return null;  // ya pequeña

        if ($dry) return 0;

        $result = ImageService::recompressFile($path, 'public', $maxW, $quality);
        if (!$result) return null;

        [$before, $after] = $result;
        return max(0, $before - $after);
    }

    private function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, 2) . ' ' . $units[$i];
    }
}
