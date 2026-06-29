<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ndt:certificate-previews {--force : Regenerate preview even when one already exists}', function () {
    $service = app(\App\Services\NdtCertificatePreviewService::class);
    $disk = Storage::disk('public');
    $updated = 0;
    $skipped = 0;
    $missing = 0;

    \App\Models\NdtCertificate::whereNotNull('file_path')
        ->orderBy('id')
        ->each(function (\App\Models\NdtCertificate $certificate) use ($service, $disk, &$updated, &$skipped, &$missing) {
            if (! $service->isPdfPath($certificate->file_path)) {
                $skipped++;
                return;
            }

            if (! $this->option('force') && $certificate->preview_path && $disk->exists($certificate->preview_path)) {
                $skipped++;
                return;
            }

            if (! $disk->exists($certificate->file_path)) {
                $missing++;
                $this->warn("File tidak ditemukan untuk sertifikat #{$certificate->id}: {$certificate->file_path}");
                return;
            }

            if ($certificate->preview_path && $certificate->preview_path !== $certificate->file_path) {
                $disk->delete($certificate->preview_path);
            }

            $certificate->update([
                'preview_path' => $service->createFromPdf($disk->path($certificate->file_path)),
            ]);

            $updated++;
            $this->line("Preview dibuat: {$certificate->title}");
        });

    \App\Models\LaporanNdtCertificate::with('certificate')
        ->whereNotNull('certificate_id')
        ->get()
        ->each(function (\App\Models\LaporanNdtCertificate $snapshot) {
            if (! $snapshot->certificate) {
                return;
            }

            $snapshot->update([
                'preview_path' => $snapshot->certificate->preview_path,
            ]);
        });

    $this->info("Selesai. Preview dibuat: {$updated}, dilewati: {$skipped}, file hilang: {$missing}.");
})->purpose('Generate preview images for NDT certificate PDFs');
