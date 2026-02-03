<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Test\TestSize\Unknown;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
}
