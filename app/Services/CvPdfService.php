<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use Throwable;

class CvPdfService
{
    public function imageDataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($path));
    }

    public function merge(string $cvBinary, array $attachments): string
    {
        $directory = storage_path('app/private/cv-temp/'.Str::uuid());
        File::ensureDirectoryExists($directory);

        try {
            $sources = [$this->writePdf($directory, 'cv.pdf', $cvBinary)];

            foreach ($attachments as $index => $attachment) {
                $sources[] = $this->attachmentToPdf($directory, $attachment, $index);
            }

            $merged = new Fpdi;

            foreach ($sources as $source) {
                $pageCount = $merged->setSourceFile($source);

                for ($page = 1; $page <= $pageCount; $page++) {
                    $template = $merged->importPage($page);
                    $size = $merged->getTemplateSize($template);
                    $merged->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $merged->useTemplate($template);
                }
            }

            return $merged->Output('S');
        } catch (Throwable $exception) {
            throw new RuntimeException('Gagal menggabungkan CV dan lampiran: '.$exception->getMessage(), 0, $exception);
        } finally {
            File::deleteDirectory($directory);
        }
    }

    private function attachmentToPdf(string $directory, array $attachment, int $index): string
    {
        $path = $attachment['path'];

        if (! Storage::disk('public')->exists($path)) {
            throw new RuntimeException("Lampiran {$attachment['label']} tidak ditemukan.");
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return $this->writePdf(
                $directory,
                "attachment-{$index}.pdf",
                Storage::disk('public')->get($path)
            );
        }

        if (! in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
            throw new RuntimeException("Format lampiran {$attachment['label']} tidak didukung.");
        }

        $binary = Pdf::loadView('employees.cv.image-attachment', [
            'label' => $attachment['label'],
            'imageDataUri' => $this->imageDataUri($path),
        ])->setPaper('a4')->output();

        return $this->writePdf($directory, "attachment-{$index}.pdf", $binary);
    }

    private function writePdf(string $directory, string $fileName, string $binary): string
    {
        $path = $directory.DIRECTORY_SEPARATOR.$fileName;
        File::put($path, $binary);

        return $path;
    }
}
