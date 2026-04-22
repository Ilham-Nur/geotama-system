@extends('layouts.app')

@section('title', 'Tambah Proposal Anggaran Kerja')

<style>
    .select2-selection__choice {
        padding-left: 1.6em !important;
        position: relative;
        display: inline-flex;
        align-items: center;
        height: 1.6em;
        line-height: 1.6em;
    }

    .btn-add-row {
        font-size: 11px;
        padding: 3px 10px;
    }

    .btn-remove-row {
        font-size: 11px;
        padding: 2px 6px;
    }

    .section-total-row {
        background-color: #f0f0f0;
        font-weight: bold;
    }


    .status-ok {
        color: #007A33 !important;
        /* hijau */
        font-weight: bold;
    }

    .status-over {
        color: #d9534f !important;
        /* merah */
        font-weight: bold;
    }
</style>

@section('content')
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title">
                        <h2> Buat PAK</h2>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('pak.index') }}">PAK</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Buat PAK
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->


        <div class="card">
            <div class="card-body">
                <form id="pakForm" action="{{ route('pak.store') }}" method="POST" enctype="multipart/form-data"
                    novalidate> @csrf
                    {{-- ============================= --}} {{-- 🔥 PERMOHONAN (REUSE) --}} {{-- ============================= --}} @php $permohonan = null; @endphp
                    @include('permohonan._form') {{-- ============================= --}} {{-- 🔥 HEADER PAK --}} {{-- ============================= --}}
                    <hr>

                    <!-- Informasi Project -->
                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_number">Project Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('project_number') is-invalid @enderror"
                                    id="project_number" name="project_number" value="{{ $newPakNo }} " readonly
                                    required>
                                @error('project_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="project_value">Project Value <span class="text-danger">*</span></label>
                                <input type="text" id="project_value_display" class="form-control" placeholder="Rp 0"
                                    required>
                                <input type="hidden" id="project_value" name="project_value">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Komponen</label>
                                <input type="number" class="form-control" id="komponen" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="table-wrapper table-responsive my-3">
                        <table class="table table-bordered text-center align-middle table-sm" id="pak-table">
                            <thead style="background-color: #000c7a; color: white;">
                                <tr style="font-weight:bold;">
                                    <th style="width:40px;">
                                        <h6 class="text-white">No</h6>
                                    </th>
                                    <th style="width:180px;">
                                        <h6 class="text-white">Operational Needs</h6>
                                    </th>
                                    <th>
                                        <h6 class="text-white">Description</h6>
                                    </th>
                                    <th style="width:90px;">
                                        <h6 class="text-white">Qty</h6>
                                    </th>
                                    <th style="width:150px;">
                                        <h6 class="text-white">Unit Cost</h6>
                                    </th>
                                    <th style="width:150px;">
                                        <h6 class="text-white">Total Cost</h6>
                                    </th>
                                    <th style="width:150px;">
                                        <h6 class="text-white">Max Cost</h6>
                                    </th>
                                    <th style="width:120px;">
                                        <h6 class="text-white">%</h6>
                                    </th>
                                    <th style="width:80px;">
                                        <h6 class="text-white">Status</h6>
                                    </th>
                                    <th style="width:40px;">
                                        <h6 class="text-white">#</h6>
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="pak-dynamic-body">

                                @foreach ($categories as $cat)
                                    <!-- SECTION HEADER -->
                                    <tr class="section-header">
                                        <td colspan="10" style="text-align:left; background:#e9ecef; font-weight:bold;">
                                            {{ $cat->code }}. {{ $cat->name }}
                                        </td>
                                    </tr>

                                    <!-- ITEM TEMPLATE (INDEX = 0) -->
                                    <tr class="item-row" data-section="{{ $cat->code }}"
                                        data-category="{{ $cat->id }}" data-index="0">

                                        <td class="numbering">1</td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="items[{{ $cat->id }}][0][operational_needs]" required>
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="items[{{ $cat->id }}][0][description]">
                                        </td>

                                        <td>
                                            <input type="number" class="form-control form-control-sm unit_qty"
                                                name="items[{{ $cat->id }}][0][qty]" value="0" min="0">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm unit_cost_display"
                                                placeholder="Rp 0">
                                            <input type="hidden" class="unit_cost"
                                                name="items[{{ $cat->id }}][0][unit_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control form-control-sm total_cost_display"
                                                readonly placeholder="Rp 0">
                                            <input type="hidden" class="total_cost"
                                                name="items[{{ $cat->id }}][0][total_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="max_cost"
                                                name="items[{{ $cat->id }}][0][max_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="percent"
                                                name="items[{{ $cat->id }}][0][percent]" value="0">
                                        </td>

                                        <td>
                                            <input type="hidden" class="status-field"
                                                name="items[{{ $cat->id }}][0][status]" value="OK">
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-row"
                                                style="display:none;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)">
                                                    <path
                                                        d="M14.7223 12.7585C14.7426 12.3448 14.4237 11.9929 14.01 11.9726C13.5963 11.9522 13.2444 12.2711 13.2241 12.6848L12.9999 17.2415C12.9796 17.6552 13.2985 18.0071 13.7122 18.0274C14.1259 18.0478 14.4778 17.7289 14.4981 17.3152L14.7223 12.7585Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M9.98802 11.9726C9.5743 11.9929 9.25542 12.3448 9.27577 12.7585L9.49993 17.3152C9.52028 17.7289 9.87216 18.0478 10.2859 18.0274C10.6996 18.0071 11.0185 17.6552 10.9981 17.2415L10.774 12.6848C10.7536 12.2711 10.4017 11.9522 9.98802 11.9726Z"
                                                        fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M10.249 2C9.00638 2 7.99902 3.00736 7.99902 4.25V5H5.5C4.25736 5 3.25 6.00736 3.25 7.25C3.25 8.28958 3.95503 9.16449 4.91303 9.42267L5.54076 19.8848C5.61205 21.0729 6.59642 22 7.78672 22H16.2113C17.4016 22 18.386 21.0729 18.4573 19.8848L19.085 9.42267C20.043 9.16449 20.748 8.28958 20.748 7.25C20.748 6.00736 19.7407 5 18.498 5H15.999V4.25C15.999 3.00736 14.9917 2 13.749 2H10.249ZM14.499 5V4.25C14.499 3.83579 14.1632 3.5 13.749 3.5H10.249C9.83481 3.5 9.49902 3.83579 9.49902 4.25V5H14.499ZM5.5 6.5C5.08579 6.5 4.75 6.83579 4.75 7.25C4.75 7.66421 5.08579 8 5.5 8H18.498C18.9123 8 19.248 7.66421 19.248 7.25C19.248 6.83579 18.9123 6.5 18.498 6.5H5.5ZM6.42037 9.5H17.5777L16.96 19.7949C16.9362 20.191 16.6081 20.5 16.2113 20.5H7.78672C7.38995 20.5 7.06183 20.191 7.03807 19.7949L6.42037 9.5Z"
                                                        fill="currentColor" />
                                                </svg>

                                            </button>
                                        </td>
                                    </tr>

                                    <!-- SECTION TOTAL ROW -->
                                    <tr class="section-total-row" data-section="{{ $cat->code }}"
                                        data-category="{{ $cat->id }}"
                                        data-max-percentage="{{ $cat->max_percentage }}">

                                        <td colspan="2" style="text-align:left; font-weight:bold;">
                                            TOTAL {{ $cat->order }} (MAX {{ $cat->max_percentage }}%)
                                        </td>

                                        <td colspan="2"></td>

                                        <td>
                                            <span class="section-max-display">Rp 0</span>
                                        </td>

                                        <td>
                                            <span class="section-total-display">Rp 0</span>
                                        </td>

                                        <td>
                                            <span class="section-percent-display">0%</span>
                                        </td>

                                        <td>
                                            <span class="section-status-display status-ok">OK</span>
                                        </td>

                                        <td colspan="2">
                                            <button type="button" class="btn btn-sm btn-primary btn-add-row"
                                                data-category="{{ $cat->id }}" data-section="{{ $cat->code }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.0002 4.875C12.6216 4.875 13.1252 5.37868 13.1252 6V10.8752H18.0007C18.622 10.8752 19.1257 11.3789 19.1257 12.0002C19.1257 12.6216 18.622 13.1252 18.0007 13.1252H13.1252V18.0007C13.1252 18.622 12.6216 19.1257 12.0002 19.1257C11.3789 19.1257 10.8752 18.622 10.8752 18.0007V13.1252H6C5.37868 13.1252 4.875 12.6216 4.875 12.0002C4.875 11.3789 5.37868 10.8752 6 10.8752H10.8752V6C10.8752 5.37868 11.3789 4.875 12.0002 4.875Z"
                                                        fill="currentColor" />
                                                </svg>

                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- GRAND TOTAL -->
                                <tr>
                                    <td colspan="5">
                                        <div class="col-md-4" style="text-align:left; font-weight:bold;">
                                            Grand Total
                                        </div>
                                    </td>
                                    <td colspan="5">
                                        <div class="col-md-4">
                                            <div id="grand-total-display" style="font-weight:bold;">
                                                Rp 0
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- PROJECT VALUE & COST SUMMARY -->
                    <div class="card mt-3">
                        <div class="card-body">

                            <h5 class="mb-3"><strong>Project Financial Summary</strong></h5>

                            <div class="row g-3">

                                <!-- NILAI PROJECT -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nilai Project</label>
                                    <input type="text" id="nilai_project" class="form-control" readonly>
                                </div>

                                <!-- NILAI PAK -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nilai PAK</label>
                                    <input type="text" id="nilai_pak" name="nilai_pak" class="form-control" readonly>
                                </div>

                                <!-- PAJAK -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pajak (2%)</label>
                                    <input type="text" id="nilai_pajak" name="nilai_pajak" class="form-control">
                                </div>

                                <!-- MARGIN -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Margin</label>
                                    <input type="text" id="nilai_margin" name="nilai_margin" class="form-control"
                                        readonly>
                                </div>

                                <!-- MARGIN PERCENT -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Margin (%)</label>
                                    <input type="text" id="margin_percent" class="form-control" readonly>
                                </div>

                            </div>

                            <!-- Hidden raw values untuk DB -->
                            <input type="hidden" name="nilai_project_raw" id="nilai_project_raw">
                            <input type="hidden" name="nilai_pak_raw" id="nilai_pak_raw">
                            <input type="hidden" name="nilai_pajak_raw" id="nilai_pajak_raw">
                            <input type="hidden" name="nilai_margin_raw" id="nilai_margin_raw">

                        </div>
                    </div>


                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">
                            Simpan PAK
                        </button>
                        <a href="{{ route('pak.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Format Date
            function formatDate(dateStr) {
                if (!dateStr) return '';
                return dateStr.split('T')[0];
            }


            // ==================================================
            // Helper rupiah
            // ==================================================
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function parseRupiah(value) {
                if (!value) return 0;
                return Number(String(value).replace(/[^0-9]/g, '')) || 0;
            }

            let pajakManuallyAdjusted = false;

            function syncKomponenFromPermohonanItems() {
                const totalItemPermohonan = $('#items-table tbody tr').length || 0;
                $('#komponen').val(totalItemPermohonan);
                updateConsumableChemical();
            }

            // ==================================================
            // Ambil projectValue dengan fallback
            // ==================================================
            function getProjectValue() {
                return parseRupiah($('#project_value').val()) ||
                    parseRupiah($('#project_value_display').val()) || 0;
            }



            // =========================
            // HONORIUM (2%)
            // =========================
            function updateHonorium(projectValue) {
                let biaya = Math.round(projectValue * 0.02);

                $('tr.item-row[data-section="A"]').each(function() {
                    let need = $(this).find('[name*="[operational_needs]"]').val().toLowerCase();

                    if (need.includes('approval')) {
                        $(this).find('.unit_cost').val(biaya);
                        $(this).find('.unit_cost_display').val(formatRupiah(biaya));
                    }
                });
            }


            function updatePerlengkapan(projectValue) {
                let biaya = Math.round(projectValue * 0.08);

                $('tr.item-row[data-section="B"]').each(function() {
                    let need = $(this).find('[name*="[operational_needs]"]').val().toLowerCase();

                    if (need.includes('perlengkapan')) {
                        $(this).find('.unit_cost').val(biaya);
                        $(this).find('.unit_cost_display').val(formatRupiah(biaya));
                    }
                });
            }

            // =========================
            // CONSUMABLE (Chemical)
            // =========================
            function updateConsumableChemical() {
                let komponen = parseFloat($('#komponen').val()) || 0;
                let qty = komponen * 1.5;

                $('tr.item-row[data-section="C"]').each(function() {
                    let need = $(this).find('[name*="[operational_needs]"]').val().toLowerCase();

                    if (need.includes('chemical')) {
                        $(this).find('.unit_qty').val(qty);
                    }
                });
            }


            function replaceIndexInName(oldName, newIndex) {
                // handle pattern seperti: items[<cat>][<idx>][fieldname] atau any similar with two numbers
                // kita mengganti bagian index (angka kedua)
                return oldName.replace(/^(.+?\[\d+\]\[)\d+(\].*?)$/, '$1' + newIndex + '$2');
            }

            function reindexSectionNames(categoryId) {
                let $rows = $('tr.item-row[data-category="' + categoryId + '"]');
                $rows.each(function(i) {
                    // set data-index pada row
                    $(this).attr('data-index', i);

                    // update semua input/select/textarea name di row
                    $(this).find('input[name], select[name], textarea[name]').each(function() {
                        let oldName = $(this).attr('name');
                        if (!oldName) return;

                        let newName = replaceIndexInName(oldName, i);
                        $(this).attr('name', newName);
                    });
                });
            }

            function reindexAllSections() {
                $('.section-total-row').each(function() {
                    let categoryId = $(this).data('category');
                    reindexSectionNames(categoryId);
                });
            }

            // ==================================================
            // Recalc satu row
            // ==================================================
            function recalcRow($row) {
                let qty = Number($row.find('.unit_qty').val()) || 0;
                let cost = parseRupiah($row.find('.unit_cost_display').val());

                let total = qty * cost;

                $row.find('.total_cost').val(total);
                $row.find('.total_cost_display').val(formatRupiah(total));
            }


            // =========================
            // RECALC ALL
            // =========================
            function recalcAll() {
                let projectValue = getProjectValue();
                let grandTotal = 0;

                $('tr.item-row').each(function() {
                    recalcRow($(this));
                    let val = parseRupiah($(this).find('.total_cost').val());
                    grandTotal += val;
                });

                $('#grand-total-display').text(formatRupiah(grandTotal));

                recalcSectionTotals();
                hitungSummaryNew();


            }

            function recalcSectionTotals() {
                let projectValue = getProjectValue();

                $('.section-total-row').each(function() {

                    let sectionCode = $(this).data('section');

                    let rawMax = $(this).data('max-percentage');
                    let hasLimit = rawMax !== undefined && rawMax !== null && rawMax !== "";

                    let maxPercent = hasLimit ? parseFloat(rawMax) : null;

                    let total = 0;

                    // hitung total per section
                    $(`tr.item-row[data-section="${sectionCode}"]`).each(function() {
                        total += parseRupiah($(this).find('.total_cost').val());
                    });

                    let maxCost = maxPercent !== null ?
                        Math.round(projectValue * (maxPercent / 100)) :
                        null;

                    let percent = projectValue > 0 ? (total / projectValue * 100) : 0;

                    let isOver = maxPercent !== null ? total > maxCost : false;

                    // =========================
                    // RENDER
                    // =========================
                    $(this).find('.section-total-display').text(formatRupiah(total));

                    // kalau ada batas
                    if (maxPercent !== null) {
                        $(this).find('.section-max-display').text(formatRupiah(maxCost));
                    } else {
                        $(this).find('.section-max-display').text('-');
                    }

                    $(this).find('.section-percent-display').text(percent.toFixed(1) + "%");

                    let statusEl = $(this).find('.section-status-display');

                    if (maxPercent === null) {
                        // 🔥 TANPA BATAS
                        statusEl.text("OK");
                        statusEl.removeClass("status-over").addClass("status-ok");
                    } else {
                        if (isOver) {
                            statusEl.text("OVER");
                            statusEl.removeClass("status-ok").addClass("status-over");
                        } else {
                            statusEl.text("OK");
                            statusEl.removeClass("status-over").addClass("status-ok");
                        }
                    }
                });
            }
            // =========================
            // SUMMARY BARU
            // =========================
            function hitungSummaryNew() {
                let project = getProjectValue();
                let pak = parseRupiah($("#grand-total-display").text());
                let pajak = Math.round(project * 0.02);

                if (pajakManuallyAdjusted) {
                    pajak = parseRupiah($("#nilai_pajak").val());
                } else {
                    $("#nilai_pajak").val(formatRupiah(pajak));
                }

                let margin = project - pak - pajak;
                let percent = project > 0 ? (margin / project * 100) : 0;

                $("#nilai_project").val(formatRupiah(project));
                $("#nilai_pak").val(formatRupiah(pak));
                $("#nilai_margin").val(formatRupiah(margin));
                $("#margin_percent").val(percent.toFixed(1) + "%");

                $("#nilai_project_raw").val(project);
                $("#nilai_pak_raw").val(pak);
                $("#nilai_pajak_raw").val(pajak);
                $("#nilai_margin_raw").val(margin);
            }

            // ==================================================
            // renumber rows
            // ==================================================
            function renumberSection(sectionCode) {
                let i = 1;
                $('tr.item-row[data-section="' + sectionCode + '"]').each(function() {
                    $(this).find('.numbering').text(i++);
                });
            }

            // ==================================================
            // add row handler (robust fallback if no firstRow found)
            // ==================================================
            $(document).on('click', '.btn-add-row', function() {
                let categoryId = $(this).data('category');
                let sectionCode = $(this).data('section');

                // hitung existing rows di kategori ini
                let rowCount = $('tr.item-row[data-category="' + categoryId + '"]').length;
                let nextIndex = rowCount; // index baru (0-based)

                // clone the first row template (fallback)
                let $firstRow = $('tr.item-row[data-category="' + categoryId + '"]').first();
                let $new = $firstRow.clone(true, true); // true to copy data+events (safe)

                // perbarui atribut data-index
                $new.attr('data-index', nextIndex);

                // update semua input/select/textarea di row cloned
                $new.find('input, select, textarea').each(function() {
                    let $el = $(this);
                    let oldName = $el.attr('name');

                    // jika punya name, ganti hanya INDEX (angka kedua dalam items[CAT][INDEX][field])
                    if (oldName) {
                        // replace angka index yang diikuti oleh "][" (target index, bukan category id)
                        let newName = oldName.replace(/\[(\d+)\](?=\]\[)/, '[' + nextIndex + ']');
                        $el.attr('name', newName);
                    }

                    // reset value dengan pintar:
                    // - hidden numeric fields harus default 0
                    // - visible text/number kosong
                    if ($el.hasClass('unit_cost') || $el.hasClass('total_cost') || $el.hasClass(
                            'max_cost') || $el.hasClass('percent')) {
                        $el.val(0);
                    } else {
                        // jika input jenis display (mis. unit_cost_display, total_cost_display) set jadi format 0
                        if ($el.hasClass('unit_cost_display') || $el.hasClass(
                                'total_cost_display')) {
                            $el.val(formatRupiah(0));
                        } else {
                            $el.val('');
                        }
                    }

                    // jika ada atribut id yang mengandung index (jarang) — update juga (opsional)
                    if ($el.attr('id')) {
                        let newId = $el.attr('id').replace(/\d+$/, '') + nextIndex;
                        $el.attr('id', newId);
                    }
                });

                // show remove button pada row clone
                $new.find('.btn-remove-row').show();

                // insert sebelum section total row
                let $secTotalRow = $('tr.section-total-row[data-category="' + categoryId + '"]');
                $secTotalRow.before($new);
                reindexSectionNames(categoryId);
                renumberSection(sectionCode);

                hitungSummaryNew();
                recalcAll();
            });


            $(document).on('click', '.btn-remove-row', function() {
                let $row = $(this).closest('tr.item-row');
                let sectionCode = $row.data('section') + '';
                let $rows = $('tr.item-row[data-section="' + sectionCode + '"]');

                if ($rows.length <= 1) {
                    $row.find('input').val('');
                    recalcAll();
                    return;
                }
                $row.remove();
                reindexSectionNames(sectionCode ? $('[data-section="' + sectionCode + '"]').data(
                    'category') : categoryId); // atau panggil reindexAllSections();
                renumberSection(sectionCode);
                recalcAll();
            });


            $(document).on('input', '.unit_qty, .unit_cost_display', function() {
                let $r = $(this).closest('tr.item-row');
                recalcRow($r);
                recalcAll();
            });

            $(document).on('blur', '.unit_cost_display', function() {
                let v = parseRupiah($(this).val());
                $(this).val(formatRupiah(v));
                let $r = $(this).closest('tr.item-row');
                recalcRow($r);
                recalcAll();
            });

            // call recalc on init and whenever project_value_display blurred (so hidden updated)
            $(document).ready(function() {
                // If you have project_value_display input, attach blur to update hidden if needed
                $("#project_value_display").on("keyup change", function() {
                    let val = parseRupiah($(this).val());
                    $("#project_value").val(val);
                    $(this).val(formatRupiah(val));

                    updateHonorium(val);
                    updateBuilding(calculateBuildingCosts(val));
                    updateKaryawan(val);
                    updatePerlengkapan(val);
                    recalcAll();
                });

                $('#komponen').on('keyup change', function() {
                    syncKomponenFromPermohonanItems();
                    recalcAll();
                });

                $(document).on('click', '#btn-add-item, .btn-remove-item', function() {
                    setTimeout(function() {
                        syncKomponenFromPermohonanItems();
                        recalcAll();
                    }, 50);
                });



                // initial hide remove buttons except first in each section
                $('tr.item-row').each(function() {
                    let sectionCode = $(this).data('section') + '';
                    let $rows = $('tr.item-row[data-section="' + sectionCode + '"]');
                    if ($rows.index(this) === 0) $(this).find('.btn-remove-row').hide();
                });

                recalcAll();
                syncKomponenFromPermohonanItems();
            });

            function addDefaultItem(categoryId, sectionCode, preset) {

                let $firstRow = $('tr.item-row[data-category="' + categoryId + '"][data-index="0"]');

                let isFirstEmpty = (
                    $firstRow.find('[name*="[operational_needs]"]').val().trim() === "" &&
                    $firstRow.find('[name*="[description]"]').val().trim() === "" &&
                    parseInt($firstRow.find('[name*="[qty]"]').val()) === 0 &&
                    parseInt($firstRow.find('.unit_cost').val()) === 0
                );

                let $row;

                if (isFirstEmpty) {
                    $row = $firstRow;

                    // FIX: row pertama harus diberi data-section-code
                    $row.attr("data-section-code", sectionCode);

                } else {
                    let $btn = $('.btn-add-row[data-category="' + categoryId + '"]');
                    $btn.click();
                    $row = $('tr.item-row[data-category="' + categoryId + '"]').last();

                    // Row baru sudah benar diberi section code
                    $row.attr("data-section-code", sectionCode);
                }

                if (preset.operational_needs !== undefined) {
                    $row.find('[name*="[operational_needs]"]').val(preset.operational_needs);
                }
                if (preset.description !== undefined) {
                    $row.find('[name*="[description]"]').val(preset.description);
                }
                if (preset.qty !== undefined) {
                    $row.find('[name*="[qty]"]').val(preset.qty);
                }
                if (preset.unit_cost !== undefined) {
                    $row.find('.unit_cost').val(preset.unit_cost);
                    $row.find('.unit_cost_display').val(formatRupiah(preset.unit_cost));
                }

                recalcAll();
            }

            const categories = @json($categories);


            function getSectionTotal(sectionCode) {
                let raw = $('tr.section-total-row[data-section="' + sectionCode + '"]')
                    .find('.section-total-display')
                    .text()
                    .replace(/[^0-9]/g, "");

                return parseInt(raw || 0);
            }


            function calculateNewBudgets(projectValue) {
                // Ambil total Section A, B, C (pakai data-section)
                const honorium = getSectionTotal('A');
                const operational = getSectionTotal('B');
                const consumable = getSectionTotal('C');

                const sumABC = honorium + operational + consumable;

                const base = (projectValue * 0.9) - sumABC;


                const buildingBudget = Math.round(base * (0.34));
                const employeeBudget = Math.round(base * (0.66));

                return {
                    buildingBudget,
                    employeeBudget
                };
            }



            /* ============================================================
               3. RUMUS D – BUILDING
            ============================================================ */
            function calculateBuildingCosts(projectValue) {

                let total = Math.round(projectValue * 0.10);

                const persen = {
                    gedung: 43,
                    listrik: 37,
                    internet: 7,
                    air: 3,
                    server: 3,
                    keamanan: 7
                };

                let result = {};

                Object.keys(persen).forEach(key => {
                    result[key] = Math.round(total * (persen[key] / 100));
                });

                return result;
            }

            function updateBuilding(costs) {
                const mapping = {
                    "gedung": "gedung",
                    "listrik": "listrik",
                    "internet": "internet",
                    "air": "air",
                    "server": "server",
                    "keamanan": "keamanan",
                };

                $('tr.item-row[data-section-code="D"]').each(function() {
                    const need = $(this).find('[name*="[operational_needs]"]').val().toLowerCase();
                    const key = mapping[need];

                    if (key && costs[key] !== undefined) {
                        $(this).find('.unit_cost').val(costs[key]);
                        $(this).find('.unit_cost_display').val(formatRupiah(costs[key]));
                    }
                });
            }

            /* ============================================================
               4. RUMUS E – KARYAWAN
            ============================================================ */
            function updateKaryawan(projectValue) {
                let total = Math.round(projectValue * 0.10);

                $('tr.item-row[data-section="E"]').each(function() {
                    $(this).find('.unit_cost').val(total);
                    $(this).find('.unit_cost_display').val(formatRupiah(total));
                });
            }


            /* ============================================================
               5. DEFAULT ITEMS (unit_cost = 0 karena dihitung otomatis)
            ============================================================ */
            const defaultItemsByCategory = {
                A: [{
                        operational_needs: "Biaya Approval",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },

                ],
                B: [{
                        operational_needs: "BBM",
                        description: "-",
                        qty: 1,
                        unit_cost: 100000
                    },
                    {
                        operational_needs: "Sewa Mobil",
                        description: "-",
                        qty: 1,
                        unit_cost: 300000
                    },
                    {
                        operational_needs: "Makan",
                        description: "-",
                        qty: 2,
                        unit_cost: 50000
                    },
                    {
                        operational_needs: "Sewa Alat",
                        description: "-",
                        qty: 1,
                        unit_cost: 550000
                    },
                    {
                        operational_needs: "Perlengkapan",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    // {
                    //     operational_needs: "PDS",
                    //     description: "-",
                    //     qty: 1,
                    //     unit_cost: 0
                    // }
                ],
                C: [{
                    operational_needs: "Chemical",
                    description: "-",
                    qty: 1,
                    unit_cost: 80000
                }, {
                    operational_needs: "Sarung tangan",
                    description: "-",
                    qty: 0.5,
                    unit_cost: 32000
                }, {
                    operational_needs: "Marker",
                    description: "-",
                    qty: 2,
                    unit_cost: 10000
                }, {
                    operational_needs: "Majun",
                    description: "-",
                    qty: 1,
                    unit_cost: 50000
                }],
                D: [{
                        operational_needs: "Gedung",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    {
                        operational_needs: "Listrik",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    {
                        operational_needs: "Internet",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    {
                        operational_needs: "Air",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    {
                        operational_needs: "Server",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                    {
                        operational_needs: "Keamanan",
                        description: "-",
                        qty: 1,
                        unit_cost: 0
                    },
                ],
                E: [{
                    operational_needs: "Karyawan",
                    description: "-",
                    qty: 2,
                    unit_cost: 0
                }, ]
            };

            /* ============================================================
               6. GENERATE DEFAULT ROWS
            ============================================================ */
            categories.forEach(cat => {
                const defaults = defaultItemsByCategory[cat.code];
                if (defaults) {
                    defaults.forEach(item => addDefaultItem(cat.id, cat.code, item));
                }
            });

            /* ============================================================
               7. APPLY COSTS SAAT PROJECT VALUE BERUBAH
            ============================================================ */
            $("#project_value_display").on("keyup change", function() {
                let val = parseRupiah($(this).val());

                $("#project_value").val(val);
                $(this).val(formatRupiah(val));

                updateHonorium(val);
                updateBuilding(calculateBuildingCosts(val));
                updateKaryawan(val);
                updatePerlengkapan(val);

                recalcAll();
            });

            recalcAll();


            // Auto format Rupiah display + update hidden value
            $(".rupiah-display").on("keyup change", function() {
                let inputDisplay = $(this);

                // ambil id ketemu _display → ganti ke hidden
                let hiddenId = inputDisplay.attr("id").replace("_display", "");
                let inputHidden = $("#" + hiddenId);

                let angka = parseRupiah(inputDisplay.val());

                // Set angka murni ke hidden
                inputHidden.val(angka);

                // Update tampilan
                inputDisplay.val(formatRupiah(angka));

                // Hitung ulang summary
                hitungSummaryNew();
            });

            $("#nilai_pajak").on("keyup change", function() {
                pajakManuallyAdjusted = true;
                let pajak = parseRupiah($(this).val());
                $(this).val(formatRupiah(pajak));
                hitungSummaryNew();
            });


            // function handleBudgetRecalculation() {
            //     const projectValue = getProjectValue();

            //     updateBuildingDefaultCosts(calculateBuildingUnitCosts(projectValue));
            //     updateEmployeeDefaultCosts(calculateEmployeeCosts(projectValue));
            //     hitungSummaryNew();

            //     recalcAll();
            // }

            $(document).on("keyup change",
                'tr.item-row[data-section="A"] .unit_qty, tr.item-row[data-section="A"] .unit_cost_display,' +
                'tr.item-row[data-section="B"] .unit_qty, tr.item-row[data-section="B"] .unit_cost_display,' +
                'tr.item-row[data-section="C"] .unit_qty, tr.item-row[data-section="C"] .unit_cost_display',
                function() {
                    // handleBudgetRecalculation();
                }
            );




            $('#pakForm').on('submit', function(e) {
                e.preventDefault();

                let valid = true;

                // =========================
                // VALIDASI MINIMAL 1 ITEM PER KATEGORI
                // =========================
                $('.section-total-row').each(function() {

                    let categoryId = $(this).data('category');

                    let hasItem = $(`.item-row[data-category="${categoryId}"]`)
                        .find(`input[name^="items[${categoryId}]"][name$="[operational_needs]"]`)
                        .filter(function() {
                            return $(this).val().trim() !== "";
                        }).length > 0;

                    if (!hasItem) valid = false;
                });

                if (!valid) {
                    Swal.fire('Error!', 'Setiap kategori wajib minimal 1 item', 'error');
                    return;
                }

                reindexAllSections();

                const formEl = this;
                const $form = $(formEl);
                const btn = $form.find('button[type="submit"]');
                const btnOriginalText = btn.html();

                // =========================
                // 🔥 BUILD JSON PERMOHONAN (CLEAN)
                // =========================
                let permohonanData = {};

                $(formEl).find('[name]').each(function() {

                    let name = $(this).attr('name');
                    if (!name) return;

                    // ❗ SKIP PAK ITEMS
                    if (name.startsWith('items[')) return;


                    // ❗ SKIP permohonan_items karena kita handle manual
                    if (name.startsWith('permohonan_items[')) return;

                    // ❗ SKIP FIELD PAK
                    if (['project_value', 'date', 'project_number'].includes(name)) return;

                    // ❗ SKIP JSON ITSELF
                    if (name === 'permohonan_json') return;

                    let value = $(this).val();

                    // handle array (checkbox)
                    if (name.includes('[]')) {
                        name = name.replace('[]', '');

                        if (!permohonanData[name]) permohonanData[name] = [];
                        permohonanData[name].push(value);

                    } else {
                        permohonanData[name] = value;
                    }
                });

                let permohonanItems = [];
                permohonanData['items'] = permohonanItems;

                $('#items-table tbody tr').each(function() {

                    let detail = $(this).find('[name*="[detail_pekerjaan]"]').val();
                    let tanggal = $(this).find('[name*="[tanggal_permintaan]"]').val();
                    let layanan = $(this).find('[name*="[layanan_ids]"]').val() || [];

                    if (!detail) return;

                    permohonanItems.push({
                        detail_pekerjaan: detail,
                        tanggal_permintaan: tanggal,
                        tanggal_pelaksanaan: null,
                        durasi: null,
                        layanan_ids: layanan
                    });
                });

                // =========================
                // 🔥 HANDLE FILE (HANYA METADATA)
                // =========================
                permohonanData['dokumens'] = [];

                $(formEl).find('input[type="file"]').each(function() {

                    let name = $(this).attr('name');
                    if (!name || !this.files.length) return;

                    let jenis = name.match(/\[(.*?)\]/)?.[1] || name;

                    permohonanData['dokumens'].push({
                        jenis: jenis,
                        label: jenis,
                        file_name: this.files[0].name
                    });
                });

                // =========================
                // 🔥 FORMDATA
                // =========================
                let formData = new FormData(formEl);

                formData.append('permohonan_json', JSON.stringify(permohonanData));

                // =========================
                // 🔥 SUBMIT
                // =========================
                Swal.fire({
                    title: 'Simpan PAK?',
                    text: 'Pastikan data sudah benar',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                }).then((result) => {

                    if (!result.isConfirmed) return;

                    btn.prop('disabled', true).html('Menyimpan...');

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {

                            if (res.success) {
                                Swal.fire('Berhasil!', res.message, 'success')
                                    .then(() => window.location.href =
                                        "{{ route('pak.index') }}");
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                                btn.prop('disabled', false).html(btnOriginalText);
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message ||
                                'Server error', 'error');
                            btn.prop('disabled', false).html(btnOriginalText);
                        }
                    });

                });
            });
        });
    </script>
@endpush
