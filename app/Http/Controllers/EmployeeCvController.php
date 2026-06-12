<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\CvPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

class EmployeeCvController extends Controller
{
    private const SECTIONS = [
        'contact',
        'personal',
        'education',
        'work_experiences',
        'certificates',
        'projects',
    ];

    public function configure(Employee $employee)
    {
        $this->loadCvRelations($employee);

        return view('employees.cv.configure', [
            'employee' => $employee,
            'projects' => $employee->user?->proyeks ?? collect(),
            'attachments' => $this->availableAttachments($employee),
            'sections' => self::SECTIONS,
        ]);
    }

    public function preview(Request $request, Employee $employee, CvPdfService $cvPdfService)
    {
        $configuration = $this->validatedConfiguration($request, $employee);
        $pdf = $this->buildCvPdf($employee, $configuration, $cvPdfService);

        return $pdf->stream($this->fileName($employee));
    }

    public function generate(Request $request, Employee $employee, CvPdfService $cvPdfService)
    {
        $configuration = $this->validatedConfiguration($request, $employee);
        $cvBinary = $this->buildCvPdf($employee, $configuration, $cvPdfService)->output();

        try {
            $binary = $cvPdfService->merge($cvBinary, $configuration['attachments']);
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->fileName($employee).'"',
        ]);
    }

    private function validatedConfiguration(Request $request, Employee $employee): array
    {
        $validated = $request->validate([
            'include_photo' => ['nullable', 'boolean'],
            'sections' => ['nullable', 'array'],
            'sections.*' => [Rule::in(self::SECTIONS)],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string'],
            'attachment_orders' => ['nullable', 'array'],
            'attachment_orders.*' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->loadCvRelations($employee);

        $allowedProjectIds = $employee->user?->proyeks->pluck('id')->map(fn ($id) => (int) $id) ?? collect();
        $selectedProjectIds = collect($validated['project_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->intersect($allowedProjectIds);

        $availableAttachments = $this->availableAttachments($employee)->keyBy('token');
        $orders = $validated['attachment_orders'] ?? [];
        $selectedAttachments = collect($validated['attachments'] ?? [])
            ->unique()
            ->filter(fn ($token) => $availableAttachments->has($token))
            ->map(function ($token) use ($availableAttachments, $orders) {
                $attachment = $availableAttachments->get($token);
                $attachment['order'] = (int) ($orders[$token] ?? 999);

                return $attachment;
            })
            ->sortBy('order')
            ->values()
            ->all();

        return [
            'include_photo' => $request->boolean('include_photo'),
            'sections' => collect($validated['sections'] ?? self::SECTIONS),
            'projects' => ($employee->user?->proyeks ?? collect())
                ->whereIn('id', $selectedProjectIds)
                ->values(),
            'attachments' => $selectedAttachments,
        ];
    }

    private function buildCvPdf(Employee $employee, array $configuration, CvPdfService $cvPdfService)
    {
        return Pdf::loadView('employees.cv.pdf', [
            'employee' => $employee,
            'sections' => $configuration['sections'],
            'projects' => $configuration['projects'],
            'latestWorkExperiences' => $employee->latestWorkExperiencesForCv(),
            'photoDataUri' => $configuration['include_photo']
                ? $cvPdfService->imageDataUri($employee->photo_path)
                : null,
        ])->setPaper('a4');
    }

    private function loadCvRelations(Employee $employee): void
    {
        $employee->load([
            'user.proyeks.permohonan',
            'educations',
            'workExperiences',
            'certificates',
            'documents',
        ]);
    }

    private function availableAttachments(Employee $employee): Collection
    {
        $attachments = collect();

        foreach ($employee->educations as $education) {
            $name = collect([$education->education_level, $education->major, $education->institution_name])
                ->filter()
                ->join(' - ');

            $this->pushAttachment($attachments, 'education:'.$education->id, 'Pendidikan', $name, $education->file_path);
        }

        foreach ($employee->workExperiences as $experience) {
            $name = collect([$experience->position, $experience->company_name])
                ->filter()
                ->join(' - ');

            $this->pushAttachment($attachments, 'experience:'.$experience->id, 'Pengalaman Kerja', $name, $experience->certificate_file_path);
        }

        foreach ($employee->certificates as $certificate) {
            $this->pushAttachment($attachments, 'certificate:'.$certificate->id, 'Sertifikat', $certificate->certificate_name, $certificate->file_path);
        }

        foreach ($employee->documents as $document) {
            $this->pushAttachment($attachments, 'document:'.$document->id, 'Dokumen', $document->document_label, $document->file_path);
        }

        return $attachments->values();
    }

    private function pushAttachment(Collection $attachments, string $token, string $type, ?string $name, ?string $path): void
    {
        if (! $path || ! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['pdf', 'png', 'jpg', 'jpeg'], true)) {
            return;
        }

        $name = filled($name) ? $name : $type;

        $attachments->push([
            'token' => $token,
            'type' => $type,
            'name' => $name,
            'label' => "{$type}: {$name}",
            'path' => $path,
        ]);
    }

    private function fileName(Employee $employee): string
    {
        return 'cv-'.Str::slug($employee->full_name).'.pdf';
    }
}
