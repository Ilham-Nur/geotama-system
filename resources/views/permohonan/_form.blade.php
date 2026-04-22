@php
    $isEdit = isset($permohonan);
    $existingDokumens = $isEdit ? $permohonan->dokumens->keyBy('jenis') : collect();
    $selectedClientId = old('client_id', $permohonan->client_id ?? '');
    $clientMode = old('client_mode', $selectedClientId ? 'existing' : 'new');
@endphp

<div id="form-alert" class="alert d-none mb-3"></div>

<div class="row">
    <div class="col-md-12 mb-3">
        <label class="d-block">Sumber Data Client</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="client_mode_switch"
                {{ $clientMode === 'existing' ? 'checked' : '' }}>
            <label class="form-check-label" for="client_mode_switch">
                Gunakan Client Existing
            </label>
        </div>
        <input type="hidden" name="client_mode" id="client_mode" value="{{ $clientMode }}">
        <small class="text-muted d-block">Switch OFF = Input Client Baru, Switch ON = Pilih Client Existing</small>
        <small class="text-danger field-error" data-field="client_mode"></small>
    </div>

    <div class="col-md-12 mb-3" id="client_select_wrapper">
        <label>Pilih Client Existing</label>
        <select name="client_id" id="client_id" class="form-control">
            <option value="">-- Pilih Client --</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" data-nama_perusahaan="{{ $client->nama_perusahaan }}"
                    data-alamat="{{ $client->alamat }}" data-nama_pic="{{ $client->nama_pic }}"
                    data-no_telp="{{ $client->no_telp }}" data-email="{{ $client->email }}"
                    {{ (string) $selectedClientId === (string) $client->id ? 'selected' : '' }}>
                    {{ $client->nama_perusahaan }} - {{ $client->nama_pic }}
                </option>
            @endforeach
        </select>
        <small class="text-danger field-error" data-field="client_id"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>Nama Perusahaan</label>
        <input type="text" name="nama_perusahaan" class="form-control"
            value="{{ old('nama_perusahaan', $permohonan->nama_perusahaan ?? '') }}">
        <small class="text-danger field-error" data-field="nama_perusahaan"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>Nama PIC</label>
        <input type="text" name="nama_pic" class="form-control"
            value="{{ old('nama_pic', $permohonan->nama_pic ?? '') }}">
        <small class="text-danger field-error" data-field="nama_pic"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>No Telp</label>
        <input type="text" name="no_telp" class="form-control"
            value="{{ old('no_telp', $permohonan->no_telp ?? '') }}">
        <small class="text-danger field-error" data-field="no_telp"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $permohonan->email ?? '') }}">
        <small class="text-danger field-error" data-field="email"></small>
    </div>

    <div class="col-md-12 mb-3">
        <label>Alamat Perusahaan</label>
        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $permohonan->alamat ?? '') }}</textarea>
        <small class="text-danger field-error" data-field="alamat"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>Nama Proyek</label>
        <input type="text" name="nama_proyek" class="form-control"
            value="{{ old('nama_proyek', $permohonan->nama_proyek ?? '') }}">
        <small class="text-danger field-error" data-field="nama_proyek"></small>
    </div>

    <div class="col-md-6 mb-3">
        <label>Lokasi Inspeksi</label>
        <textarea name="lokasi" class="form-control" rows="2">{{ old('lokasi', $permohonan->lokasi ?? '') }}</textarea>
        <small class="text-danger field-error" data-field="lokasi"></small>
    </div>

    <div class="col-md-12 mb-3">
        <label>Test Uji</label><br>

        @php
            $testuji = old('testuji', $permohonan->testuji ?? '');
        @endphp

        <div class="form-check form-check-inline">
            <input class="form-check-input testuji-radio" type="radio" name="testuji" value="quality_internal"
                {{ $testuji == 'quality_internal' ? 'checked' : '' }}>
            <label class="form-check-label">Quality Internal</label>
        </div>

        <div class="form-check form-check-inline">
            <input class="form-check-input testuji-radio" type="radio" name="testuji" value="quality_external"
                {{ $testuji == 'quality_external' ? 'checked' : '' }}>
            <label class="form-check-label">Quality External</label>
        </div>

        <small class="text-danger field-error d-block" data-field="testuji"></small>
    </div>

    <div class="col-md-12 mb-3" id="external_keterangan_wrapper" style="display:none;">
        <label>Keterangan Quality External</label>
        <input type="text" name="testuji_external_keterangan" class="form-control"
            value="{{ old('testuji_external_keterangan', $permohonan->testuji_external_keterangan ?? '') }}">
        <small class="text-danger field-error" data-field="testuji_external_keterangan"></small>
    </div>

    <div class="col-md-12 mb-3">
        <label>Permintaan Khusus</label>
        <textarea name="permintaan_khusus" class="form-control" rows="3">{{ old('permintaan_khusus', $permohonan->permintaan_khusus ?? '') }}</textarea>
        <small class="text-danger field-error" data-field="permintaan_khusus"></small>
    </div>
</div>

<hr>

<h6>Dokumen Pendukung</h6>

@php
    $selectedDokumen = old('dokumen_pendukung', $isEdit ? $permohonan->dokumens->pluck('jenis')->toArray() : []);
    $dokumenLainnya = old(
        'dokumen_lainnya_text',
        $isEdit ? optional($permohonan->dokumens->where('jenis', 'lainnya')->first())->label : '',
    );
@endphp

<div class="row">
    @foreach ([
        'drawing' => 'Drawing',
        'p_id_isometric' => 'P&ID / Isometric',
        'wps_pqr' => 'WPS / PQR',
        'standar' => 'Standar',
        'foto' => 'Foto',
        'schedule' => 'Schedule',
        'lainnya' => 'Lainnya',
    ] as $key => $label)
        @php
            $existingDok = $existingDokumens->get($key);
        @endphp

        <div class="col-md-6 mb-3">
            <div class="form-check">
                <input class="form-check-input dokumen-check" type="checkbox" name="dokumen_pendukung[]"
                    value="{{ $key }}" id="dok_{{ $key }}"
                    {{ in_array($key, $selectedDokumen) ? 'checked' : '' }}>
                <label class="form-check-label" for="dok_{{ $key }}">{{ $label }}</label>
            </div>

            <div class="mt-2 dokumen-file-wrapper" id="file_wrapper_{{ $key }}" style="display:none;">
                @if ($key == 'lainnya')
                    <input type="text" name="dokumen_lainnya_text" class="form-control mb-2"
                        placeholder="Isi dokumen lainnya" value="{{ $dokumenLainnya }}">
                    <small class="text-danger field-error d-block" data-field="dokumen_lainnya_text"></small>
                @endif

                <input type="file" name="dokumen_files[{{ $key }}]" class="form-control">
                <small class="text-danger field-error d-block"
                    data-field="dokumen_files.{{ $key }}"></small>

                @if ($existingDok)
                    <div class="mt-2">

                        <small class="d-block">
                            File lama: {{ $existingDok->file_name ?? '-' }}
                        </small>

                        {{-- 🔥 JIKA DARI DB (permohonan asli) --}}
                        @if (isset($existingDok->id))
                            <a href="{{ route('permohonan.dokumen.preview', $existingDok->id) }}" target="_blank"
                                class="btn btn-sm btn-info mt-1">
                                Preview
                            </a>

                            <a href="{{ route('permohonan.dokumen.download', $existingDok->id) }}"
                                class="btn btn-sm btn-secondary mt-1">
                                Download
                            </a>

                            {{-- 🔥 JIKA DARI JSON PAK --}}
                        @elseif(isset($existingDok->file_path))
                            <a href="{{ asset('storage/' . $existingDok->file_path) }}" target="_blank"
                                class="btn btn-sm btn-info mt-1">
                                Preview
                            </a>

                            <a href="{{ asset('storage/' . $existingDok->file_path) }}" download
                                class="btn btn-sm btn-secondary mt-1">
                                Download
                            </a>
                        @endif

                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6>Item Permohonan</h6>
    <button type="button" class="btn btn-sm btn-primary" id="btn-add-item">+ Tambah Item</button>
</div>

<div class="table-wrapper table-responsive">
    <table class="table" id="items-table">
        <thead>
            <tr>
                <th>
                    <h6>Detail Pekerjaan</h6>
                </th>
                <th>
                    <h6>Jenis Layanan</h6>
                </th>
                <th>
                    <h6>Tanggal Permintaan</h6>
                </th>
                <th>
                    <h6>Aksi</h6>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items');
                $itemsData = [];

                if ($oldItems) {
                    $itemsData = $oldItems;
                } elseif ($isEdit) {
                    $itemsData = $permohonan->items
                        ->map(function ($item) {
                            return [
                                'detail_pekerjaan' => $item->detail_pekerjaan,
                                'layanan_ids' => $item->layanans->pluck('id')->toArray(),
                                'tanggal_permintaan' => $item->tanggal_permintaan,
                            ];
                        })
                        ->toArray();
                } else {
                    $itemsData[] = [
                        'detail_pekerjaan' => '',
                        'layanan_ids' => [],
                        'tanggal_permintaan' => '',
                    ];
                }
            @endphp

            @foreach ($itemsData as $i => $item)
                <tr>
                    <td>
                        <input type="text" name="items[{{ $i }}][detail_pekerjaan]"
                            class="form-control" value="{{ $item['detail_pekerjaan'] ?? '' }}">
                        <small class="text-danger field-error d-block"
                            data-field="items.{{ $i }}.detail_pekerjaan"></small>
                    </td>
                    <td>
                        <select name="items[{{ $i }}][layanan_ids][]"
                            class="form-control layanan-multiple" multiple="multiple"
                            data-placeholder="Pilih layanan">
                            @foreach ($layanans as $layanan)
                                <option value="{{ $layanan->id }}"
                                    {{ in_array($layanan->id, $item['layanan_ids'] ?? []) ? 'selected' : '' }}>
                                    {{ $layanan->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger field-error d-block"
                            data-field="items.{{ $i }}.layanan_ids"></small>
                    </td>
                    <td>
                        <input type="date" name="items[{{ $i }}][tanggal_permintaan]"
                            class="form-control" value="{{ $item['tanggal_permintaan'] ?? '' }}">
                        <small class="text-danger field-error d-block"
                            data-field="items.{{ $i }}.tanggal_permintaan"></small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <small class="text-danger field-error d-block mt-2" data-field="items"></small>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = $('#permohonan-form');
        const submitBtn = $('#btn-submit-form');

        function initSelect2(scope = document) {
            $(scope).find('.layanan-multiple').select2({
                width: '100%',
                placeholder: 'Pilih layanan',
                allowClear: true
            });
        }

        function clearErrors() {
            $('.field-error').text('');
            $('#form-alert')
                .addClass('d-none')
                .removeClass('alert-danger alert-success')
                .html('');
        }

        function showErrors(errors) {
            clearErrors();

            $('#form-alert')
                .removeClass('d-none')
                .addClass('alert alert-danger')
                .html('Ada data yang belum sesuai. Silakan cek form.');

            Object.keys(errors).forEach(function(field) {
                const message = errors[field][0];
                $(`.field-error[data-field="${field}"]`).text(message);
            });
        }

        function setLoading(isLoading) {
            if (isLoading) {
                submitBtn.prop('disabled', true);
                submitBtn.find('.btn-text').addClass('d-none');
                submitBtn.find('.btn-loading').removeClass('d-none');
            } else {
                submitBtn.prop('disabled', false);
                submitBtn.find('.btn-text').removeClass('d-none');
                submitBtn.find('.btn-loading').addClass('d-none');
            }
        }

        function toggleExternalKeterangan() {
            const selected = document.querySelector('input[name="testuji"]:checked');
            const wrapper = document.getElementById('external_keterangan_wrapper');
            wrapper.style.display = (selected && selected.value === 'quality_external') ? 'block' : 'none';
        }

        function toggleClientMode() {
            const hiddenClientMode = document.getElementById('client_mode');
            const clientSwitch = document.getElementById('client_mode_switch');
            const mode = hiddenClientMode?.value || (clientSwitch?.checked ? 'existing' : 'new');
            const isExisting = mode === 'existing';
            const clientSelect = document.getElementById('client_id');
            const clientSelectWrapper = document.getElementById('client_select_wrapper');
            const fields = ['nama_perusahaan', 'alamat', 'nama_pic', 'no_telp', 'email'];

            if (clientSelect) {
                clientSelect.disabled = !isExisting;
            }
            if (clientSelectWrapper) {
                clientSelectWrapper.style.display = isExisting ? 'block' : 'none';
            }

            fields.forEach(function(fieldName) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field) return;
                field.readOnly = isExisting;
                if (isExisting) {
                    field.classList.add('bg-light');
                } else {
                    field.classList.remove('bg-light');
                }
            });

            if (isExisting) {
                fillClientData();
            }
        }

        function syncClientModeFromSwitch() {
            const clientSwitch = document.getElementById('client_mode_switch');
            const hiddenClientMode = document.getElementById('client_mode');
            if (!clientSwitch || !hiddenClientMode) return;
            hiddenClientMode.value = clientSwitch.checked ? 'existing' : 'new';
            toggleClientMode();
        }

        function fillClientData() {
            const clientSelect = document.getElementById('client_id');
            if (!clientSelect) return;
            const option = clientSelect.options[clientSelect.selectedIndex];
            if (!option || !option.value) return;

            ['nama_perusahaan', 'alamat', 'nama_pic', 'no_telp', 'email'].forEach(function(field) {
                const el = document.querySelector(`[name="${field}"]`);
                if (el) {
                    el.value = option.dataset[field] || '';
                }
            });
        }

        document.querySelectorAll('.testuji-radio').forEach(el => {
            el.addEventListener('change', toggleExternalKeterangan);
        });
        toggleExternalKeterangan();

        $('#client_mode_switch').on('change', syncClientModeFromSwitch);
        $('#client_id').on('change', fillClientData);
        toggleClientMode();

        function toggleDokumenFile(key) {
            const checkbox = document.getElementById('dok_' + key);
            const wrapper = document.getElementById('file_wrapper_' + key);
            wrapper.style.display = checkbox.checked ? 'block' : 'none';
        }

        document.querySelectorAll('.dokumen-check').forEach(el => {
            el.addEventListener('change', function() {
                toggleDokumenFile(this.value);
            });
            toggleDokumenFile(el.value);
        });

        initSelect2();

        let itemIndex = {{ count($itemsData) }};

        $('#btn-add-item').on('click', function() {
            const row = `
                <tr>
                    <td>
                        <input type="text" name="items[${itemIndex}][detail_pekerjaan]" class="form-control">
                        <small class="text-danger field-error d-block" data-field="items.${itemIndex}.detail_pekerjaan"></small>
                    </td>
                    <td>
                        <select name="items[${itemIndex}][layanan_ids][]" class="form-control layanan-multiple" multiple="multiple" data-placeholder="Pilih layanan">
                            @foreach ($layanans as $layanan)
                                <option value="{{ $layanan->id }}">{{ $layanan->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger field-error d-block" data-field="items.${itemIndex}.layanan_ids"></small>
                    </td>
                    <td>
                        <input type="date" name="items[${itemIndex}][tanggal_permintaan]" class="form-control">
                        <small class="text-danger field-error d-block" data-field="items.${itemIndex}.tanggal_permintaan"></small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-item">Hapus</button>
                    </td>
                </tr>
            `;

            $('#items-table tbody').append(row);
            const lastRow = $('#items-table tbody tr:last');
            initSelect2(lastRow);
            itemIndex++;
        });

        $(document).on('click', '.btn-remove-item', function() {
            const rows = document.querySelectorAll('#items-table tbody tr');
            if (rows.length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Minimal harus ada 1 item.');
            }
        });

        form.on('submit', function(e) {
            e.preventDefault();
            clearErrors();
            setLoading(true);

            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $('#form-alert')
                        .removeClass('d-none alert-danger')
                        .addClass('alert alert-success')
                        .html(response.message || 'Data berhasil disimpan.');

                    if (response.success && response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 800);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        showErrors(xhr.responseJSON.errors || {});
                    } else {
                        $('#form-alert')
                            .removeClass('d-none alert-success')
                            .addClass('alert alert-danger')
                            .html(xhr.responseJSON?.message ||
                                'Terjadi kesalahan pada server.');
                    }
                },
                complete: function() {
                    setLoading(false);
                }
            });
        });
    });
</script>
