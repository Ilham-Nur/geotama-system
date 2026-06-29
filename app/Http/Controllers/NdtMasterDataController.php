<?php

namespace App\Http\Controllers;

use App\Models\NdtAcceptanceCriterion;
use App\Models\NdtApprovalPerson;
use App\Models\NdtCertificate;
use App\Models\NdtInspectionDescription;
use App\Models\NdtProcedure;
use App\Models\NdtTestingStandard;
use App\Services\NdtCertificatePreviewService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NdtMasterDataController extends Controller
{
    private const TYPES = [
        'procedures' => [
            'model' => NdtProcedure::class,
            'label' => 'Procedure No',
            'file_field' => null,
            'file_column' => null,
            'storage' => null,
        ],
        'criteria' => [
            'model' => NdtAcceptanceCriterion::class,
            'label' => 'Acceptance Criteria',
            'file_field' => null,
            'file_column' => null,
            'storage' => null,
        ],
        'standards' => [
            'model' => NdtTestingStandard::class,
            'label' => 'Testing Standard',
            'file_field' => null,
            'file_column' => null,
            'storage' => null,
        ],
        'descriptions' => [
            'model' => NdtInspectionDescription::class,
            'label' => 'Inspection Description & Sketch',
            'file_field' => 'sketch',
            'file_column' => 'sketch_path',
            'storage' => 'ndt/sketches',
        ],
        'certificates' => [
            'model' => NdtCertificate::class,
            'label' => 'Certificate',
            'file_field' => 'certificate_file',
            'file_column' => 'file_path',
            'storage' => 'ndt/certificates',
        ],
        'approvals' => [
            'model' => NdtApprovalPerson::class,
            'label' => 'Approval Person',
            'file_field' => null,
            'file_column' => null,
            'storage' => null,
        ],
    ];

    public function index(Request $request)
    {
        $activeType = $this->normalizeType($request->query('type', 'procedures'));

        $data = [
            'procedures' => NdtProcedure::latest()->get(),
            'criteria' => NdtAcceptanceCriterion::latest()->get(),
            'standards' => NdtTestingStandard::latest()->get(),
            'descriptions' => NdtInspectionDescription::latest()->get(),
            'certificates' => NdtCertificate::latest()->get(),
            'approvals' => NdtApprovalPerson::latest()->get(),
        ];

        return view('ndt-master-data.index', [
            'types' => self::TYPES,
            'activeType' => $activeType,
            'data' => $data,
            'approvalRoles' => NdtApprovalPerson::ROLES,
        ]);
    }

    public function store(Request $request, string $type)
    {
        $type = $this->normalizeType($type);
        $payload = $this->validatedPayload($request, $type);

        $fileData = $this->storeUploadedFile($request, $type);
        $record = $this->modelClass($type)::create(array_merge($payload, $fileData));

        return redirect()
            ->route('ndt-master-data.index', ['type' => $type])
            ->with('success', self::TYPES[$type]['label'] . ' berhasil ditambahkan.');
    }

    public function update(Request $request, string $type, int $id)
    {
        $type = $this->normalizeType($type);
        $record = $this->findRecord($type, $id);
        $payload = $this->validatedPayload($request, $type, $record);
        $fileData = $this->storeUploadedFile($request, $type, $record);

        $record->update(array_merge($payload, $fileData));

        return redirect()
            ->route('ndt-master-data.index', ['type' => $type])
            ->with('success', self::TYPES[$type]['label'] . ' berhasil diperbarui.');
    }

    public function destroy(string $type, int $id)
    {
        $type = $this->normalizeType($type);
        $record = $this->findRecord($type, $id);

        $fileColumn = self::TYPES[$type]['file_column'];
        if ($fileColumn && $record->{$fileColumn}) {
            Storage::disk('public')->delete($record->{$fileColumn});
        }

        if ($type === 'certificates' && $record->preview_path && $record->preview_path !== $record->file_path) {
            Storage::disk('public')->delete($record->preview_path);
        }

        $record->delete();

        return redirect()
            ->route('ndt-master-data.index', ['type' => $type])
            ->with('success', self::TYPES[$type]['label'] . ' berhasil dihapus.');
    }

    private function validatedPayload(Request $request, string $type, ?Model $record = null): array
    {
        $payload = match ($type) {
            'procedures', 'criteria', 'standards' => $request->validate([
                'code' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique($this->modelClass($type)::query()->getModel()->getTable(), 'code')->ignore($record?->id),
                ],
                'name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'is_active' => ['nullable', 'boolean'],
            ]) + ['is_active' => $request->boolean('is_active')],
            'descriptions' => $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('ndt_inspection_descriptions', 'name')->ignore($record?->id),
                ],
                'description' => ['nullable', 'string'],
                'sketch' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'is_active' => ['nullable', 'boolean'],
            ]) + ['is_active' => $request->boolean('is_active')],
            'certificates' => $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'certificate_no' => ['nullable', 'string', 'max:255'],
                'type' => ['nullable', 'string', 'max:100'],
                'issued_at' => ['nullable', 'date'],
                'expired_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
                'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
                'is_active' => ['nullable', 'boolean'],
            ]) + ['is_active' => $request->boolean('is_active')],
            'approvals' => $request->validate([
                'role' => ['required', Rule::in(array_keys(NdtApprovalPerson::ROLES))],
                'name' => ['required', 'string', 'max:255'],
                'position' => ['nullable', 'string', 'max:255'],
                'is_active' => ['nullable', 'boolean'],
            ]) + ['is_active' => $request->boolean('is_active')],
        };

        unset($payload['sketch'], $payload['certificate_file']);

        return $payload;
    }

    private function storeUploadedFile(Request $request, string $type, ?Model $record = null): array
    {
        $config = self::TYPES[$type];
        if (! $config['file_field'] || ! $request->hasFile($config['file_field'])) {
            return [];
        }

        $oldFilePath = $record?->{$config['file_column']};
        $oldPreviewPath = $type === 'certificates' ? $record?->preview_path : null;

        $uploadedPath = $request->file($config['file_field'])->store($config['storage'], 'public');
        $fileData = [
            $config['file_column'] => $uploadedPath,
        ];

        if ($type !== 'certificates') {
            if ($oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }

            return $fileData;
        }

        $fileData['preview_path'] = null;

        if (! app(NdtCertificatePreviewService::class)->isPdfPath($uploadedPath)) {
            if ($oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }

            if ($oldPreviewPath && $oldPreviewPath !== $oldFilePath) {
                Storage::disk('public')->delete($oldPreviewPath);
            }

            return $fileData;
        }

        try {
            $fileData['preview_path'] = app(NdtCertificatePreviewService::class)
                ->createFromPdf(Storage::disk('public')->path($uploadedPath));
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($uploadedPath);

            throw ValidationException::withMessages([
                'certificate_file' => 'File PDF berhasil dibaca sebagai upload, tapi gagal dibuat preview gambar. Detail: ' . $e->getMessage(),
            ]);
        }

        if ($oldFilePath) {
            Storage::disk('public')->delete($oldFilePath);
        }

        if ($oldPreviewPath && $oldPreviewPath !== $oldFilePath) {
            Storage::disk('public')->delete($oldPreviewPath);
        }

        return $fileData;
    }

    private function normalizeType(string $type): string
    {
        abort_unless(array_key_exists($type, self::TYPES), 404);

        return $type;
    }

    private function modelClass(string $type): string
    {
        return self::TYPES[$type]['model'];
    }

    private function findRecord(string $type, int $id): Model
    {
        return $this->modelClass($type)::findOrFail($id);
    }
}
