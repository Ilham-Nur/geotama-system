<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class NdtCertificatePreviewService
{
    public function createFromPdf(string $absolutePdfPath, string $outputDirectory = 'ndt/certificates/previews'): string
    {
        if (! is_file($absolutePdfPath)) {
            throw new RuntimeException('File PDF sertifikat tidak ditemukan.');
        }

        Storage::disk('public')->makeDirectory($outputDirectory);

        $outputBaseName = 'certificate-preview-' . Str::uuid();
        $relativeOutputPrefix = trim($outputDirectory, '/') . '/' . $outputBaseName;
        $absoluteOutputPrefix = Storage::disk('public')->path($relativeOutputPrefix);

        $process = new Process([
            $this->pdftoppmBinary(),
            '-f',
            '1',
            '-singlefile',
            '-png',
            '-r',
            '180',
            $absolutePdfPath,
            $absoluteOutputPrefix,
        ]);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Gagal membuat preview sertifikat PDF.');
        }

        $relativePreviewPath = $relativeOutputPrefix . '.png';

        if (! Storage::disk('public')->exists($relativePreviewPath)) {
            throw new RuntimeException('Preview sertifikat PDF tidak berhasil dibuat.');
        }

        return $relativePreviewPath;
    }

    public function isPdfPath(?string $path): bool
    {
        return $path !== null && preg_match('/\.pdf$/i', $path) === 1;
    }

    private function pdftoppmBinary(): string
    {
        $candidates = array_filter([
            env('PDFTOPPM_PATH'),
            'C:\\Users\\User\\.cache\\codex-runtimes\\codex-primary-runtime\\dependencies\\native\\poppler\\Library\\bin\\pdftoppm.exe',
            'C:\\laragon\\bin\\poppler\\bin\\pdftoppm.exe',
            'C:\\Program Files\\poppler\\Library\\bin\\pdftoppm.exe',
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return 'pdftoppm';
    }
}
