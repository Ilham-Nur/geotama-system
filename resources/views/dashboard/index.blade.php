@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <style>
        .dash-wrap {
            background: hsl(0, 0%, 100%);
            padding: 1.5rem;
            min-height: 100vh;
        }

        .dash-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 12px;
        }

        .dash-page-title {
            font-size: 18px;
            font-weight: 500;
            color: #1a1a1a;
            margin: 0;
        }

        .dash-page-sub {
            font-size: 12px;
            color: #888780;
            margin-top: 3px;
        }

        .dash-filters {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dash-filters label {
            font-size: 13px;
            color: #5f5e5a;
            margin-bottom: 0;
        }

        .dash-filters select {
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 0.5px solid #d3d1c7;
            background: #fff;
            color: #1a1a1a;
        }

        .dash-section-label {
            font-size: 11px;
            font-weight: 500;
            color: #888780;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .dash-kpi-row {
            display: grid;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .dash-kpi-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dash-kpi-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dash-kpi {
            background: #e8ebf1;
            border-radius: 8px;
            padding: 1rem 1.1rem;
        }

        .dash-kpi-lbl {
            font-size: 12px;
            color: #5f5e5a;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dash-kpi-lbl i {
            font-size: 14px;
        }

        .dash-kpi-val {
            font-size: 21px;
            font-weight: 500;
            color: #1a1a1a;
            line-height: 1.2;
        }

        .dash-kpi-hint {
            font-size: 11px;
            color: #888780;
            margin-top: 3px;
        }

        .dash-kpi-danger .dash-kpi-val {
            color: #A32D2D;
        }

        .dash-kpi-success .dash-kpi-val {
            color: #3B6D11;
        }

        .dash-kpi-warning .dash-kpi-val {
            color: #854F0B;
        }

        .dash-kpi-info .dash-kpi-val {
            color: #185FA5;
        }

        .dash-card {
            background: #fff;
            border: 0.5px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .dash-card-title {
            font-size: 13px;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dash-card-title i {
            font-size: 16px;
            color: #888780;
        }

        .dash-row-2 {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .dash-row-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .dash-divider {
            height: 0.5px;
            background: rgba(0, 0, 0, 0.08);
            margin: 10px 0;
        }

        .dash-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .dash-leg-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #5f5e5a;
        }

        .dash-leg-dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .dash-badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 99px;
            font-weight: 500;
            white-space: nowrap;
        }

        .dash-badge-danger {
            background: #FCEBEB;
            color: #791F1F;
        }

        .dash-badge-warning {
            background: #FAEEDA;
            color: #633806;
        }

        .dash-badge-success {
            background: #EAF3DE;
            color: #27500A;
        }

        .dash-badge-info {
            background: #E6F1FB;
            color: #0C447C;
        }

        .dash-status-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dash-status-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 8px;
            background: #f1efe8;
            font-size: 12px;
            color: #5f5e5a;
        }

        .dash-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dash-tbl {
            width: 100%;
            border-collapse: collapse;
        }

        .dash-tbl th {
            font-size: 11px;
            font-weight: 500;
            color: #888780;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.08);
        }

        .dash-tbl td {
            font-size: 12px;
            color: #1a1a1a;
            padding: 8px 8px;
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.06);
            vertical-align: middle;
        }

        .dash-tbl tr:last-child td {
            border-bottom: none;
        }

        .dash-tbl .td-num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .dash-tbl .td-danger {
            color: #A32D2D;
            text-align: right;
            font-weight: 500;
        }

        .dash-tbl .td-muted {
            font-size: 11px;
            color: #888780;
            margin-top: 2px;
        }

        .dash-no-inv-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 10px;
            border-radius: 8px;
            background: #f8f7f5;
            margin-bottom: 6px;
        }

        .dash-no-inv-name {
            font-size: 13px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .dash-no-inv-sub {
            font-size: 11px;
            color: #888780;
            margin-top: 2px;
        }

        .dash-empty {
            font-size: 12px;
            color: #888780;
            text-align: center;
            padding: 1.5rem 0;
        }

        @media (max-width: 992px) {
            .dash-kpi-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dash-kpi-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dash-row-2 {
                grid-template-columns: 1fr;
            }

            .dash-row-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {

            .dash-kpi-4,
            .dash-kpi-3 {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="row mt-3">
        <div class="dash-wrap">

            {{-- TOPBAR --}}
            <div class="dash-topbar">
                <div>
                    <h2 class="dash-page-title">Dashboard Operasional Tahun {{ $selectedYear }}</h2>
                    <div class="dash-page-sub">
                        {{ $selectedMonth ? 'Bulan: ' . $monthOptions[$selectedMonth] : 'Semua Bulan' }}
                    </div>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="dash-filters">
                    <label for="year">Tahun</label>
                    <select name="year" id="year" onchange="this.form.submit()">
                        @foreach ($availableYears as $year)
                            <option value="{{ $year }}" {{ (int) $year === (int) $selectedYear ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                    <label for="month">Bulan</label>
                    <select name="month" id="month" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        @foreach ($monthOptions as $m => $mName)
                            <option value="{{ $m }}" {{ (int) $m === (int) $selectedMonth ? 'selected' : '' }}>
                                {{ $mName }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- KPI: INVOICE & PEMBAYARAN --}}
            <div class="dash-section-label">Invoice &amp; Pembayaran</div>
            <div class="dash-kpi-row dash-kpi-4">
                <div class="dash-kpi dash-kpi-info">
                    <div class="dash-kpi-lbl"><i class="ti ti-file-invoice" aria-hidden="true"></i> Total invoice</div>
                    <div class="dash-kpi-val">Rp {{ number_format($invoiceTotal, 0, ',', '.') }}</div>
                    <div class="dash-kpi-hint">Tahun {{ $selectedYear }}</div>
                </div>
                <div class="dash-kpi dash-kpi-danger">
                    <div class="dash-kpi-lbl"><i class="ti ti-clock-exclamation" aria-hidden="true"></i> Outstanding</div>
                    <div class="dash-kpi-val">Rp {{ number_format($outstandingTotal, 0, ',', '.') }}</div>
                    <div class="dash-kpi-hint">{{ $invoiceBelumBayar + $invoiceSebagian }} invoice belum lunas</div>
                </div>
                <div class="dash-kpi dash-kpi-success">
                    <div class="dash-kpi-lbl"><i class="ti ti-circle-check" aria-hidden="true"></i> Invoice lunas</div>
                    <div class="dash-kpi-val">{{ number_format($invoiceLunas) }}</div>
                    <div class="dash-kpi-hint">Terbayar penuh</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi-lbl"><i class="ti ti-adjust" aria-hidden="true"></i> Sebagian / belum bayar</div>
                    <div class="dash-kpi-val">{{ number_format($invoiceSebagian) }} /
                        {{ number_format($invoiceBelumBayar) }}
                    </div>
                    <div class="dash-kpi-hint">Sebagian dibayar · belum dibayar</div>
                </div>
            </div>

            {{-- KPI: PROYEK & PERMOHONAN --}}
            <div class="dash-section-label">Proyek &amp; Permohonan</div>
            <div class="dash-kpi-row dash-kpi-3">
                <div class="dash-kpi dash-kpi-info">
                    <div class="dash-kpi-lbl"><i class="ti ti-folder-open" aria-hidden="true"></i> Proyek aktif</div>
                    <div class="dash-kpi-val">{{ number_format($proyekAktif) }}</div>
                    <div class="dash-kpi-hint">Progress / reporting / endorse</div>
                </div>
                <div class="dash-kpi">
                    <div class="dash-kpi-lbl"><i class="ti ti-clipboard-list" aria-hidden="true"></i> Total permohonan</div>
                    <div class="dash-kpi-val">{{ number_format($permohonanTotal) }}</div>
                    <div class="dash-kpi-hint">{{ $permohonanOpen }} open &middot; {{ $permohonanClose }} close</div>
                </div>
                <div class="dash-kpi dash-kpi-warning">
                    <div class="dash-kpi-lbl"><i class="ti ti-alert-triangle" aria-hidden="true"></i> Proyek tanpa invoice
                    </div>
                    <div class="dash-kpi-val">{{ $proyekTanpaInvoice->count() }} proyek</div>
                    <div class="dash-kpi-hint">Segera terbitkan invoice</div>
                </div>
            </div>

            {{-- CHART ROW: Bar + Donut PAK --}}
            <div class="dash-row-2">

                {{-- Bar Chart: Invoice vs Pembayaran --}}
                <div class="dash-card">
                    <div class="dash-card-title">
                        <i class="ti ti-chart-bar" aria-hidden="true"></i>
                        Invoice vs pembayaran per bulan
                    </div>
                    <div class="dash-legend">
                        <div class="dash-leg-item">
                            <div class="dash-leg-dot" style="background:#185FA5"></div> Invoice diterbitkan
                        </div>
                        <div class="dash-leg-item">
                            <div class="dash-leg-dot" style="background:#3B6D11"></div> Pembayaran masuk
                        </div>
                    </div>
                    <div style="position:relative;width:100%;height:220px">
                        <canvas id="chartBar" role="img"
                            aria-label="Bar chart invoice vs pembayaran per bulan tahun {{ $selectedYear }}">
                            Data invoice dan pembayaran per bulan tahun {{ $selectedYear }}.
                        </canvas>
                    </div>
                </div>

                {{-- Donut: PAK per Kategori --}}
                <div class="dash-card">
                    <div class="dash-card-title">
                        <i class="ti ti-chart-donut" aria-hidden="true"></i>
                        PAK per kategori
                        <span style="font-size:11px;color:#888780;font-weight:400;margin-left:4px">
                            {{ $selectedMonth ? '(' . $monthOptions[$selectedMonth] . ')' : '(Semua Bulan)' }}
                        </span>
                    </div>
                    <div class="dash-legend">
                        @php $pakColors = ['#185FA5','#1D9E75','#D85A30','#BA7517','#D4537E','#534AB7','#888780']; @endphp
                        @foreach ($pakByCategory as $i => $pak)
                            <div class="dash-leg-item">
                                <div class="dash-leg-dot" style="background:{{ $pakColors[$i % count($pakColors)] }}">
                                </div>
                                {{ $pak->label }} &mdash; Rp {{ number_format($pak->total, 0, ',', '.') }}
                            </div>
                        @endforeach
                        @if ($pakByCategory->isEmpty())
                            <div class="dash-empty">Belum ada data PAK</div>
                        @endif
                    </div>
                    <div style="position:relative;width:100%;height:190px">
                        <canvas id="chartPak" role="img"
                            aria-label="Donut chart PAK per kategori {{ $selectedYear }}">
                            Breakdown anggaran PAK per kategori.
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- BOTTOM ROW: Tabel outstanding, Status proyek + metode bayar, Proyek tanpa invoice --}}
            <div class="dash-row-3">

                {{-- Top Outstanding Invoice --}}
                <div class="dash-card">
                    <div class="dash-card-title">
                        <i class="ti ti-receipt-off" aria-hidden="true"></i>
                        Top 5 outstanding invoice
                    </div>
                    @if ($topOutstandingInvoices->isNotEmpty())
                        <table class="dash-tbl">
                            <thead>
                                <tr>
                                    <th>No invoice</th>
                                    <th class="td-num">Grand total</th>
                                    <th class="td-danger">Sisa tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topOutstandingInvoices as $inv)
                                    <tr>
                                        <td>
                                            <div style="font-size:12px;font-weight:500;">{{ $inv->no_invoice }}</div>
                                            @if ($inv->proyek)
                                                <div class="dash-tbl td-muted">{{ $inv->proyek->nama_proyek ?? '-' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="td-num">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                                        <td class="td-danger">Rp {{ number_format($inv->sisa_tagihan, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="dash-empty">Tidak ada outstanding invoice</div>
                    @endif
                </div>

                {{-- Status Proyek + Metode Pembayaran --}}
                <div class="dash-card">
                    <div class="dash-card-title">
                        <i class="ti ti-list-check" aria-hidden="true"></i>
                        Status proyek
                    </div>
                    <div class="dash-status-bar" style="margin-bottom:1rem">
                        @foreach ($projectStatusChart as $status => $total)
                            @php
                                $dotColor = match (strtolower($status)) {
                                    'progress' => '#185FA5',
                                    'selesai', 'done', 'endorse' => '#3B6D11',
                                    'reporting' => '#BA7517',
                                    'hold' => '#A32D2D',
                                    default => '#888780',
                                };
                            @endphp
                            <div class="dash-status-chip">
                                <div class="dash-status-dot" style="background:{{ $dotColor }}"></div>
                                {{ ucfirst($status) }}
                                <strong style="margin-left:4px">{{ $total }}</strong>
                            </div>
                        @endforeach
                        @if (empty($projectStatusChart))
                            <div class="dash-empty">Tidak ada proyek</div>
                        @endif
                    </div>

                    <div class="dash-divider"></div>

                    <div class="dash-card-title" style="margin-bottom:8px">
                        <i class="ti ti-credit-card" aria-hidden="true"></i>
                        Metode pembayaran
                        <span style="font-size:11px;color:#888780;font-weight:400;margin-left:4px">
                            {{ $selectedMonth ? '(' . $monthOptions[$selectedMonth] . ')' : '(Semua Bulan)' }}
                        </span>
                    </div>
                    @if ($paymentsByMethod->isNotEmpty())
                        <div
                            style="position:relative;width:100%;height:{{ max(100, $paymentsByMethod->count() * 40 + 40) }}px">
                            <canvas id="chartMetode" role="img"
                                aria-label="Bar chart horizontal metode pembayaran {{ $selectedYear }}">
                                Metode pembayaran yang digunakan.
                            </canvas>
                        </div>
                    @else
                        <div class="dash-empty">Belum ada data pembayaran</div>
                    @endif
                </div>

                {{-- Proyek tanpa invoice --}}
                <div class="dash-card">
                    <div class="dash-card-title">
                        <i class="ti ti-alert-circle" aria-hidden="true"></i>
                        Proyek belum ada invoice
                    </div>
                    @forelse($proyekTanpaInvoice as $p)
                        <div class="dash-no-inv-item">
                            <div>
                                <div class="dash-no-inv-name">{{ $p->nama_proyek }}</div>
                                <div class="dash-no-inv-sub">
                                    {{ $p->permohonan->nama_perusahaan ?? '-' }}
                                    &middot; {{ $p->created_at->format('d M Y') }}
                                </div>
                            </div>
                            <span class="dash-badge dash-badge-warning">Belum invoice</span>
                        </div>
                    @empty
                        <div class="dash-empty">
                            <i class="ti ti-circle-check"
                                style="font-size:24px;color:#3B6D11;display:block;margin-bottom:6px"
                                aria-hidden="true"></i>
                            Semua proyek aktif sudah memiliki invoice
                        </div>
                    @endforelse
                </div>

            </div>

        </div>{{-- end dash-wrap --}}
    </div>



    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            var palette = ['#185FA5', '#1D9E75', '#D85A30', '#BA7517', '#D4537E', '#534AB7', '#888780'];

            var fmtRp = function(v) {
                if (v === 0) return '0';
                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + 'jt';
                if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
                return 'Rp ' + v;
            };

            // ── 1. Bar chart: Invoice vs Pembayaran per bulan ──────────────────────
            var invoicePerBulan = @json($invoicePerBulan ?? array_fill(0, 12, 0));
            var bayarPerBulan = @json($bayarPerBulan ?? array_fill(0, 12, 0));

            new Chart(document.getElementById('chartBar'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ],
                    datasets: [{
                            label: 'Invoice',
                            data: invoicePerBulan,
                            backgroundColor: '#185FA5',
                            borderRadius: 4
                        },
                        {
                            label: 'Pembayaran',
                            data: bayarPerBulan,
                            backgroundColor: '#3B6D11',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#888780',
                                autoSkip: false,
                                maxRotation: 0
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(136,135,128,0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#888780',
                                callback: fmtRp
                            }
                        }
                    }
                }
            });

            // ── 2. Donut: PAK per Kategori ─────────────────────────────────────────
            var pakLabels = @json($pakByCategory->pluck('label')->values());
            var pakData = @json($pakByCategory->pluck('total')->map(fn($v) => (float) $v)->values());

            if (pakData.length > 0) {
                new Chart(document.getElementById('chartPak'), {
                    type: 'doughnut',
                    data: {
                        labels: pakLabels,
                        datasets: [{
                            data: pakData,
                            backgroundColor: palette,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return ' Rp ' + ctx.raw.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // ── 3. Bar horizontal: Metode Pembayaran ──────────────────────────────
            var metodeEl = document.getElementById('chartMetode');
            if (metodeEl) {
                var metodeLabels = @json($paymentsByMethod->pluck('label')->values());
                var metodeData = @json($paymentsByMethod->pluck('total')->map(fn($v) => (float) $v)->values());

                new Chart(metodeEl, {
                    type: 'bar',
                    data: {
                        labels: metodeLabels,
                        datasets: [{
                            data: metodeData,
                            backgroundColor: palette,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#888780',
                                    callback: fmtRp
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    color: '#888780'
                                }
                            }
                        }
                    }
                });
            }
        })();
    </script>

@endsection
