@extends('layouts.app')

@section('title', 'Konfigurasi CV Karyawan')

@section('content')
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="title">
                    <h2>Generate CV: {{ $employee->full_name }}</h2>
                    <p class="text-muted mb-0">Pilih isi CV, proyek, dan urutan lampiran sebelum generate.</p>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Konfigurasi belum valid.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.cv.generate', $employee) }}" id="cv-configuration-form">
        @csrf

        <div class="row">
            <div class="col-lg-5">
                <div class="card-style mb-30">
                    <h6 class="mb-3">Data yang Ditampilkan</h6>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="include_photo" value="1" id="include-photo"
                            {{ old('include_photo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="include-photo">Tampilkan foto di kanan atas</label>
                    </div>

                    @if (!$employee->photo_path)
                        <div class="alert alert-warning py-2">Foto karyawan belum tersedia. CV tetap dapat dibuat tanpa foto.</div>
                    @endif

                    @php
                        $sectionLabels = [
                            'contact' => 'Kontak',
                            'personal' => 'Informasi Personal Ringkas',
                            'education' => 'Riwayat Pendidikan',
                            'work_experiences' => 'Pengalaman Kerja',
                            'certificates' => 'Sertifikat',
                            'projects' => 'Pengalaman Proyek',
                        ];
                        $selectedSections = old('sections', $sections);
                    @endphp

                    <div class="row g-2">
                        @foreach ($sectionLabels as $value => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="sections[]" value="{{ $value }}"
                                        id="section-{{ $value }}" {{ in_array($value, $selectedSections, true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="section-{{ $value }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-3">Pengalaman kerja pada CV dibatasi maksimal 3 data terbaru. Pengalaman yang masih aktif ditampilkan paling atas.</small>
                </div>

                <div class="card-style mb-30">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <h6 class="mb-0">Proyek yang Ditampilkan</h6>
                        @if ($projects->isNotEmpty())
                            <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-all-projects">
                                Pilih Semua
                            </button>
                        @endif
                    </div>
                    <p class="text-muted small">Hanya proyek yang terhubung ke akun karyawan ini yang tersedia.</p>
                    @forelse ($projects as $project)
                        <div class="form-check mb-2">
                            <input class="form-check-input project-checkbox" type="checkbox" name="project_ids[]" value="{{ $project->id }}"
                                id="project-{{ $project->id }}" {{ in_array($project->id, old('project_ids', $projects->pluck('id')->all())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="project-{{ $project->id }}">
                                {{ $project->permohonan?->nama_proyek ?? $project->no_proyek }}
                                <small class="text-muted d-block">{{ $project->permohonan?->nama_perusahaan }}</small>
                            </label>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada proyek yang terhubung ke karyawan.</p>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-style mb-30">
                    <h6 class="mb-2">Lampiran Setelah Halaman CV</h6>
                    <p class="text-muted small">Pilih lampiran lalu gunakan tombol naik/turun untuk mengatur urutan. Hanya PDF, PNG, JPG, dan JPEG yang ditampilkan.</p>

                    <div class="table-responsive">
                        <table class="table align-middle" id="attachment-table">
                            <thead>
                                <tr>
                                    <th width="60">Pilih</th>
                                    <th>Lampiran</th>
                                    <th width="150">Urutan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attachments as $index => $attachment)
                                    <tr class="attachment-row">
                                        <td>
                                            <input class="form-check-input attachment-checkbox" type="checkbox" name="attachments[]"
                                                value="{{ $attachment['token'] }}" {{ in_array($attachment['token'], old('attachments', []), true) ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <strong>{{ $attachment['name'] }}</strong>
                                            <small class="text-muted d-block">{{ $attachment['type'] }}</small>
                                        </td>
                                        <td>
                                            <input type="hidden" class="attachment-order" name="attachment_orders[{{ $attachment['token'] }}]" value="{{ old('attachment_orders.' . $attachment['token'], $index + 1) }}">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-secondary btn-up" title="Naik">Naik</button>
                                                <button type="button" class="btn btn-outline-secondary btn-down" title="Turun">Turun</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Belum ada lampiran dengan format yang didukung.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 flex-wrap">
                        <button type="submit" class="btn btn-outline-primary"
                            formaction="{{ route('employees.cv.preview', $employee) }}" formtarget="_blank">
                            Preview CV
                        </button>
                        <button type="submit" class="btn btn-primary">Generate dan Download PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            function syncProjectToggleLabel() {
                const projects = $('.project-checkbox');
                const allSelected = projects.length && projects.filter(':checked').length === projects.length;
                $('#toggle-all-projects').text(allSelected ? 'Batalkan Semua' : 'Pilih Semua');
            }

            function syncOrder() {
                $('#attachment-table tbody .attachment-row').each(function(index) {
                    $(this).find('.attachment-order').val(index + 1);
                });
            }

            $('#toggle-all-projects').on('click', function() {
                const projects = $('.project-checkbox');
                const selectAll = projects.filter(':checked').length !== projects.length;

                projects.prop('checked', selectAll);
                syncProjectToggleLabel();
            });

            $(document).on('change', '.project-checkbox', syncProjectToggleLabel);

            $(document).on('click', '.btn-up', function() {
                const row = $(this).closest('tr');
                row.prev('.attachment-row').before(row);
                syncOrder();
            });

            $(document).on('click', '.btn-down', function() {
                const row = $(this).closest('tr');
                row.next('.attachment-row').after(row);
                syncOrder();
            });

            syncOrder();
            syncProjectToggleLabel();
        });
    </script>
@endpush
