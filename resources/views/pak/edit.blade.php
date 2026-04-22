@extends('layouts.app')

@section('title', 'Edit Proposal Anggaran Kerja')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title">
                    <h2> Edit PAK</h2>
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
                                Edit PAK
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

            <form id="pakForm" action="{{ route('pak.update', $pak->id) }}" method="POST">
                @csrf
                @method('PUT')

                @include('permohonan._form')

                {{-- HEADER --}}
                <div class="row">
                    <div class="col-md-6">
                        <label>Project Number</label>
                        <input type="text" name="project_number" class="form-control" value="{{ $pak->pak_number }}"
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Project Value</label>
                        <input type="text" id="project_value_display" class="form-control"
                            value="{{ number_format((int) $pak->project_value, 0, ',', '.') }}">

                        <input type="hidden" id="project_value" name="project_value"
                            value="{{ number_format((int) $pak->project_value, 0, ',', '.') }}"">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control"
                            value="{{ $pak->created_at->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label>Komponen</label>
                        <input type="number" id="komponen" class="form-control" readonly
                            value="{{ isset($permohonan->items) ? collect($permohonan->items)->count() : 0 }}">
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="table-responsive mt-3">
                    <table class="table table-bordered text-center align-middle" id="pak-table">

                        <thead style="background:#000c7a;color:white;">
                            <tr>
                                <th>No</th>
                                <th>Operational Needs</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>Total</th>
                                <th>#</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($categories as $cat)
                                {{-- HEADER --}}
                                <tr style="background:#eee;font-weight:bold;">
                                    <td colspan="7">{{ $cat->code }}. {{ $cat->name }}</td>
                                </tr>

                                @php
                                    $items = $pak->items->where('category_id', $cat->id)->values();
                                @endphp

                                @if ($items->count())
                                    @foreach ($items as $i => $item)
                                        <tr class="item-row" data-category="{{ $cat->id }}"
                                            data-section="{{ $cat->code }}" data-index="{{ $i }}">

                                            <td class="numbering">{{ $i + 1 }}</td>

                                            <td>
                                                <input type="text"
                                                    name="items[{{ $cat->id }}][{{ $i }}][operational_needs]"
                                                    class="form-control" value="{{ $item->name }}">
                                            </td>

                                            <td>
                                                <input type="text"
                                                    name="items[{{ $cat->id }}][{{ $i }}][description]"
                                                    class="form-control" value="{{ $item->description }}">
                                            </td>

                                            <td>
                                                <input type="number"
                                                    name="items[{{ $cat->id }}][{{ $i }}][qty]"
                                                    class="form-control unit_qty" value="{{ $item->qty }}">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control unit_cost_display"
                                                    value="{{ number_format($item->unit_cost, 0, ',', '.') }}">
                                                <input type="hidden" class="unit_cost"
                                                    name="items[{{ $cat->id }}][{{ $i }}][unit_cost]"
                                                    value="{{ $item->unit_cost }}">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control total_cost_display"
                                                    value="{{ number_format($item->total_cost, 0, ',', '.') }}">
                                                <input type="hidden" class="total_cost"
                                                    name="items[{{ $cat->id }}][{{ $i }}][total_cost]"
                                                    value="{{ $item->total_cost }}">
                                            </td>

                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm btn-remove-row">X</button>
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    {{-- fallback row kosong --}}
                                    <tr class="item-row" data-category="{{ $cat->id }}"
                                        data-section="{{ $cat->code }}" data-index="0">

                                        <td class="numbering">1</td>

                                        <td>
                                            <input type="text" name="items[{{ $cat->id }}][0][operational_needs]"
                                                class="form-control">
                                        </td>

                                        <td>
                                            <input type="text" name="items[{{ $cat->id }}][0][description]"
                                                class="form-control">
                                        </td>

                                        <td>
                                            <input type="number" name="items[{{ $cat->id }}][0][qty]"
                                                class="form-control unit_qty" value="0">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control unit_cost_display" value="0">
                                            <input type="hidden" class="unit_cost"
                                                name="items[{{ $cat->id }}][0][unit_cost]" value="0">
                                        </td>

                                        <td>
                                            <input type="text" class="form-control total_cost_display" value="0">
                                            <input type="hidden" class="total_cost"
                                                name="items[{{ $cat->id }}][0][total_cost]" value="0">
                                        </td>

                                        <td></td>

                                    </tr>
                                @endif

                                {{-- BUTTON ADD --}}
                                <tr>
                                    <td colspan="7">
                                        <button type="button" class="btn btn-primary btn-add-row"
                                            data-category="{{ $cat->id }}" data-section="{{ $cat->code }}">
                                            + Tambah Row
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <tr>
                        <td colspan="5" style="text-align:left;font-weight:bold;">
                            Grand Total
                        </td>
                        <td colspan="2">
                            <div id="grand-total-display" style="font-weight:bold;">
                                Rp 0
                            </div>
                        </td>
                    </tr>
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
                                <input type="text" id="nilai_pajak" name="nilai_pajak" class="form-control"
                                    value="{{ number_format($pak->tax ?? 0, 0, ',', '.') }}">
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


                <button type="submit" class="btn btn-success mt-3">
                    Update PAK
                </button>

            </form>

        </div>
    </div>

@endsection
@push('scripts')
    <script>
        /* =========================
                           HELPER
                        ========================= */
        function parseRupiah(val) {
            if (!val) return 0;

            val = String(val).trim();

            // 🔥 hapus semua selain angka
            val = val.replace(/[^\d]/g, '');

            return Number(val) || 0;
        }

        let pajakManuallyAdjusted = parseRupiah($('#nilai_pajak').val()) > 0;

        function syncKomponenFromPermohonanItems() {
            const totalItemPermohonan = $('#items-table tbody tr').length || 0;
            $('#komponen').val(totalItemPermohonan);
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        /* =========================
           HITUNG PER ROW
        ========================= */
        function recalcRow(row) {
            let qty = parseFloat(row.find('.unit_qty').val()) || 0;
            let cost = parseRupiah(row.find('.unit_cost_display').val());

            let total = qty * cost;

            row.find('.total_cost').val(total);
            row.find('.total_cost_display').val(formatRupiah(total));
        }

        /* =========================
           HITUNG GRAND TOTAL + SUMMARY
        ========================= */
        function getProjectValue() {
            return parseRupiah($('#project_value').val()) ||
                parseRupiah($('#project_value_display').val()) || 0;
        }

        function recalcAll() {
            let grandTotal = 0;

            $('.item-row').each(function() {

                let qty = parseFloat($(this).find('.unit_qty').val()) || 0;
                let cost = parseRupiah($(this).find('.unit_cost_display').val());

                let total = qty * cost;

                $(this).find('.total_cost').val(total);
                $(this).find('.total_cost_display').val(formatRupiah(total));

                grandTotal += total;
            });

            $("#grand-total-display").text(formatRupiah(grandTotal));

            hitungSummaryNew();
        }

        /* =========================
           SUMMARY
        ========================= */
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

        $(document).ready(function() {

            let val = parseRupiah($('#project_value_display').val());

            $('#project_value_display').val(formatRupiah(val));

            recalcAll();
            syncKomponenFromPermohonanItems();
        });

        /* =========================
           EVENT INPUT
        ========================= */
        $(document).on('keyup change', '.unit_qty, .unit_cost_display', function() {
            let row = $(this).closest('tr');

            recalcRow(row);
            recalcAll(); // 🔥 penting
        });

        /* =========================
           ADD ROW (FIX CLONE BUG)
        ========================= */
        $(document).on('click', '.btn-add-row', function() {

            let cat = $(this).data('category');

            let rows = $('tr.item-row[data-category="' + cat + '"]');
            let index = rows.length;

            let newRow = rows.first().clone();

            newRow.attr('data-index', index);

            newRow.find('input').each(function() {

                let name = $(this).attr('name');

                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', name);
                }

                // 🔥 RESET VALUE AMAN
                if ($(this).hasClass('unit_cost') || $(this).hasClass('total_cost')) {
                    $(this).val(0);
                } else if ($(this).hasClass('unit_cost_display') || $(this).hasClass(
                        'total_cost_display')) {
                    $(this).val('0');
                } else if ($(this).hasClass('unit_qty')) {
                    $(this).val(0);
                } else {
                    $(this).val('');
                }
            });

            newRow.insertBefore($(this).closest('tr'));

            recalcAll(); // 🔥 update total
            syncKomponenFromPermohonanItems();
        });

        /* =========================
           REMOVE ROW
        ========================= */
        $(document).on('click', '.btn-remove-row', function() {

            let rows = $('.item-row');

            if (rows.length <= 1) {
                alert('Minimal 1 item');
                return;
            }

            $(this).closest('tr').remove();

            recalcAll();
            syncKomponenFromPermohonanItems();
        });

        $(document).on('click', '#btn-add-item, .btn-remove-item', function() {
            setTimeout(function() {
                syncKomponenFromPermohonanItems();
            }, 50);
        });

        /* =========================
           FORMAT INPUT RUPIAH
        ========================= */
        $(document).on('blur', '.unit_cost_display', function() {

            let val = parseRupiah($(this).val());
            $(this).val(formatRupiah(val));

            recalcAll();
        });

        /* =========================
           PROJECT VALUE CHANGE
        ========================= */
        $('#project_value_display').on('keyup change', function() {

            let val = parseRupiah($(this).val());

            $('#project_value').val(val);
            $(this).val(formatRupiah(val));

            recalcAll();
        });

        $('#nilai_pajak').on('keyup change', function() {
            pajakManuallyAdjusted = true;
            let pajak = parseRupiah($(this).val());
            $(this).val(formatRupiah(pajak));
            hitungSummaryNew();
        });

        /* =========================
           INIT LOAD (WAJIB BANGET)
        ========================= */
        $(document).ready(function() {

            // 🔥 hitung ulang semua dari nol
            recalcAll();
        });



        $('#pakForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let url = form.attr('action');
            let formData = new FormData(this);

            // 🔥 Loading dulu
            Swal.fire({
                title: 'Processing...',
                text: 'Sedang menyimpan data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {

                    if (res.success) {

                        // ✅ SUCCESS
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {

                            // 🔥 REDIRECT KE INDEX
                            window.location.href = "{{ route('pak.index') }}";
                        });

                    } else {

                        // ❌ FAIL (dari backend)
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.message
                        });
                    }
                },

                error: function(xhr) {

                    let message = 'Terjadi kesalahan';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                }
            });
        });
    </script>
@endpush
