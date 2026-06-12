<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EmployeeCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_open_preview_and_generate_employee_cv(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'employee-education/ijazah.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=')
        );

        $permission = Permission::create(['name' => 'employees.cv.generate']);
        $user = User::factory()->create(['username' => 'cv-admin']);
        $user->givePermissionTo($permission);

        $employee = Employee::create([
            'employee_code' => 'EMP-CV-001',
            'full_name' => 'Karyawan Contoh',
            'position' => 'Inspector',
            'employment_status' => 'tetap',
        ]);

        $education = $employee->educations()->create([
            'education_level' => 'S1',
            'institution_name' => 'Universitas Contoh',
            'major' => 'Teknik',
            'start_year' => 2018,
            'end_year' => 2022,
            'file_path' => 'employee-education/ijazah.png',
            'file_name' => 'ijazah.png',
        ]);

        $employee->workExperiences()->createMany([
            ['company_name' => 'Perusahaan Lama', 'position' => 'Junior', 'start_year' => 2018, 'end_year' => 2019],
            ['company_name' => 'Perusahaan Menengah', 'position' => 'Staff', 'start_year' => 2020, 'end_year' => 2022],
            ['company_name' => 'Perusahaan Baru', 'position' => 'Senior', 'start_year' => 2023, 'end_year' => 2024],
            ['company_name' => 'Perusahaan Aktif', 'position' => 'Lead', 'start_year' => 2025, 'is_current' => true],
        ]);

        $this->assertSame(
            ['Perusahaan Aktif', 'Perusahaan Baru', 'Perusahaan Menengah'],
            $employee->latestWorkExperiencesForCv()->pluck('company_name')->all()
        );

        $configuration = [
            'include_photo' => '1',
            'sections' => ['contact', 'personal', 'education', 'work_experiences'],
            'attachments' => ['education:'.$education->id],
            'attachment_orders' => ['education:'.$education->id => 1],
        ];

        $this->actingAs($user)
            ->get(route('employees.cv.configure', $employee))
            ->assertOk()
            ->assertSee('Generate CV: Karyawan Contoh');

        $this->actingAs($user)
            ->post(route('employees.cv.preview', $employee), $configuration)
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->post(route('employees.cv.generate', $employee), $configuration)
            ->assertOk()
            ->assertDownload('cv-karyawan-contoh.pdf');
    }

    public function test_employee_document_upload_only_accepts_pdf_and_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['username' => 'employee-upload']);
        Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-UPLOAD-001',
            'full_name' => 'Karyawan Upload',
            'employment_status' => 'tetap',
        ]);

        $this->actingAs($user)
            ->post(route('profile.documents.store'), [
                'document_label' => 'Dokumen Word',
                'document_file' => UploadedFile::fake()->create(
                    'dokumen.docx',
                    10,
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ),
            ])
            ->assertSessionHasErrors('document_file');

        $this->actingAs($user)
            ->post(route('profile.documents.store'), [
                'document_label' => 'Dokumen Gambar',
                'document_file' => UploadedFile::fake()->image('dokumen.jpg'),
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas('employee_documents', [
            'document_label' => 'Dokumen Gambar',
            'file_name' => 'dokumen.jpg',
        ]);
    }

    public function test_current_work_experience_is_saved_without_end_year(): void
    {
        $user = User::factory()->create(['username' => 'employee-current-work']);
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-WORK-001',
            'full_name' => 'Karyawan Aktif',
            'employment_status' => 'tetap',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'full_name' => $employee->full_name,
                'work_experiences' => [
                    [
                        'company_name' => 'Perusahaan Aktif',
                        'position' => 'Lead',
                        'start_year' => 2025,
                        'end_year' => 2026,
                        'is_current' => '1',
                    ],
                ],
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertDatabaseHas('employee_work_experiences', [
            'employee_id' => $employee->id,
            'company_name' => 'Perusahaan Aktif',
            'start_year' => 2025,
            'end_year' => null,
            'is_current' => true,
        ]);
    }
}
