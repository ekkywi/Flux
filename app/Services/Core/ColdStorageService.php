<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Enums\AuditSeverity;
use App\Models\Server;
use App\Models\User;
use App\Services\Core\AuditLogger;
use App\DTOs\AuditLogData;

class ColdStorageService
{
    public function getArchives(string $type)
    {
        $path = "archives/{$type}";
        if (!Storage::disk('local')->exists($path)) return collect();

        $files = Storage::disk('local')->files($path);

        return collect($files)->map(function ($filePath) {
            $content = json_decode(Storage::disk('local')->get($filePath), true);
            return (object) [
                'filename'   => basename($filePath),
                'name'       => $content['identity']['name'] ?? $content['identity']['username'] ?? 'Unknown',
                'identifier' => $content['identity']['ip_address'] ?? $content['identity']['email'] ?? 'N/A',
                'env'        => $content['identity']['environment'] ?? 'N/A',
                'pruned_at'  => $content['metadata']['prune_at'] ?? 'N/A',
                'logs_count' => count($content['audit_trail'] ?? []),
                'raw_data'   => $content
            ];
        })->sortByDesc('pruned_at');
    }

    public function generateCsv(string $type, string $filename): StreamedResponse
    {
        $path = "archives/{$type}/{$filename}";
        $data = json_decode(Storage::disk('local')->get($path), true);

        $identity = $data['identity'] ?? [];
        $metadata = $data['metadata'] ?? [];
        $logs     = $data['audit_trail'] ?? [];

        $callback = function () use ($type, $identity, $metadata, $logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["FLUX CONSOLE - " . strtoupper($type) . " ARCHIVE REPORT"]);
            fputcsv($file, []);

            fputcsv($file, ['ENTITY IDENTITY']);
            foreach ($identity as $key => $value) {
                fputcsv($file, [ucfirst(str_replace('_', ' ', $key)), $value]);
            }

            fputcsv($file, []);
            fputcsv($file, ['AUDIT LOG HISTORY']);
            fputcsv($file, ['Action', 'Actor', 'Category', 'Severity', 'Timestamp', 'Details']);

            foreach ($logs as $log) {
                $details = '';
                foreach ($log['metadata'] ?? [] as $k => $v) {
                    $details .= strtoupper($k) . ": " . (is_array($v) ? json_encode($v) : $v) . " | ";
                }
                fputcsv($file, [
                    $log['action'],
                    $log['actor'] ?? 'SYSTEM',
                    $log['category'],
                    $log['severity'],
                    $log['created_at'],
                    rtrim($details, " | ")
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Archive_{$type}_{$filename}.csv",
        ]);
    }

    public function restoreArchive(string $type, string $filename)
    {
        $path = "archives/{$type}/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            throw new \Exception("Archive file not found at path: {$path}");
        }

        try {
            $archive = json_decode(Storage::disk('local')->get($path), true);
            $identity = $archive['identity'];

            return \DB::transaction(function () use ($type, $identity, $path, $filename) {
                $modelClass = $type === 'infrastructure' ? Server::class : User::class;

                if ($type === 'identity') {
                    if (!isset($identity['username'])) {
                        $identity['username'] = \Str::before($identity['email'], '@');
                        if (User::where('username', $identity['username'])->exists()) {
                            $identity['username'] .= '_' . \Str::random(4);
                        }
                    }

                    if (!isset($identity['department'])) {
                        $identity['department'] = 'Information Technology';
                    }

                    if (isset($identity['name']) && !isset($identity['first_name'])) {
                        $nameParts = explode(' ', $identity['name'], 2);
                        $identity['first_name'] = $nameParts[0];
                        $identity['last_name']  = $nameParts[1] ?? '';
                    }

                    unset($identity['id'], $identity['name'], $identity['created_at'], $identity['updated_at']);
                    $identity['password'] = \Hash::make(\Str::random(16));
                }

                $entity = $modelClass::create($identity);

                AuditLogger::log(new AuditLogData(
                    action: strtoupper($type) . '_ENTITY_RESTORED',
                    category: $type,
                    severity: AuditSeverity::WARNING,
                    user_id: auth()->id(),
                    target_type: $modelClass,
                    target_id: $entity->id,
                    metadata: ['original_archive' => $filename]
                ));

                Storage::disk('local')->delete($path);
                return true;
            });
        } catch (\Throwable $e) {
            Log::error("RESTORE_FAILED: Error restoring {$type} archive [{$filename}]", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data_attempted' => $identity ?? null
            ]);

            throw $e;
        }
    }
}
