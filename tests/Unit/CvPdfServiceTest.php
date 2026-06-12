<?php

namespace Tests\Unit;

use App\Services\CvPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Tests\TestCase;

class CvPdfServiceTest extends TestCase
{
    public function test_it_merges_generated_cv_with_pdf_and_image_attachments(): void
    {
        Storage::fake('public');

        $attachmentPdf = Pdf::loadHtml('<h1>Lampiran PDF</h1>')->setPaper('a4')->output();
        Storage::disk('public')->put('attachments/document.pdf', $attachmentPdf);
        Storage::disk('public')->put(
            'attachments/image.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=')
        );

        $service = app(CvPdfService::class);
        $cv = Pdf::loadHtml('<h1>Curriculum Vitae</h1>')->setPaper('a4')->output();

        $merged = $service->merge($cv, [
            ['type' => 'Dokumen', 'name' => 'Surat Keterangan', 'label' => 'Dokumen: Surat Keterangan', 'path' => 'attachments/document.pdf'],
            ['type' => 'Sertifikat', 'name' => 'Sertifikat Kompetensi', 'label' => 'Sertifikat: Sertifikat Kompetensi', 'path' => 'attachments/image.png'],
        ]);

        Storage::disk('public')->put('attachments/merged.pdf', $merged);
        $mergedPdf = new Fpdi;

        $this->assertStringStartsWith('%PDF-', $merged);
        $this->assertGreaterThan(strlen($cv), strlen($merged));
        $this->assertSame(3, $mergedPdf->setSourceFile(Storage::disk('public')->path('attachments/merged.pdf')));
    }
}
