<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PakItem;
use App\Models\Pembayaran;
use App\Models\Permohonan;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->integer('year', now()->year);
        $selectedMonth = $request->filled('month') ? max(1, min(12, (int) $request->integer('month'))) : null;

        $availableYears = collect([
            Permohonan::query()->selectRaw('YEAR(created_at) as year'),
            Proyek::query()->selectRaw('YEAR(created_at) as year'),
            Invoice::query()->selectRaw('YEAR(created_at) as year'),
            Pembayaran::query()->selectRaw('YEAR(tanggal_bayar) as year')->whereNotNull('tanggal_bayar'),
            PakItem::query()->selectRaw('YEAR(created_at) as year'),
        ])->flatMap(function ($query) {
            return $query->pluck('year');
        })->filter()->unique()->sort()->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        if (! $availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->last();
        }

        $monthOptions = collect(range(1, 12))->mapWithKeys(fn($month) => [$month => now()->startOfYear()->month($month)->translatedFormat('F')]);

        $permohonanCollection = Permohonan::with('items')
            ->whereYear('created_at', $selectedYear)
            ->get();

        $permohonanTotal = $permohonanCollection->count();
        $permohonanOpen = $permohonanCollection->where('status', 'OPEN')->count();
        $permohonanClose = $permohonanCollection->where('status', 'CLOSE')->count();

        $proyekAktif = Proyek::whereYear('created_at', $selectedYear)
            ->whereIn('status', [Proyek::STATUS_PROGRESS, Proyek::STATUS_REPORTING, Proyek::STATUS_ENDORSE])
            ->count();

        $invoiceWithPayments = Invoice::withSum('pembayarans', 'nominal_bayar')
            ->whereYear('created_at', $selectedYear)
            ->get();

        $invoiceTotal = (float) $invoiceWithPayments->sum('grand_total');
        $outstandingTotal = $invoiceWithPayments->sum(fn($invoice) => $invoice->sisa_tagihan);
        $invoiceLunas = $invoiceWithPayments->where('status_pembayaran', 'lunas')->count();
        $invoiceSebagian = $invoiceWithPayments->where('status_pembayaran', 'sebagian')->count();
        $invoiceBelumBayar = $invoiceWithPayments->where('status_pembayaran', 'belum_bayar')->count();

        $projectStatusChart = Proyek::whereYear('created_at', $selectedYear)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $paymentsByMethodQuery = Pembayaran::query()
            ->selectRaw("COALESCE(NULLIF(metode_pembayaran, ''), 'Tanpa Metode') as label, SUM(nominal_bayar) as total")
            ->whereYear('tanggal_bayar', $selectedYear)
            ->whereNotNull('tanggal_bayar');

        if ($selectedMonth) {
            $paymentsByMethodQuery->whereMonth('tanggal_bayar', $selectedMonth);
        }

        $paymentsByMethod = $paymentsByMethodQuery
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        $pakByCategoryQuery = PakItem::query()
            ->join('categories', 'categories.id', '=', 'pak_items.category_id')
            ->selectRaw('categories.name as label, SUM(pak_items.total_cost) as total')
            ->whereYear('pak_items.created_at', $selectedYear);

        if ($selectedMonth) {
            $pakByCategoryQuery->whereMonth('pak_items.created_at', $selectedMonth);
        }

        $pakByCategory = $pakByCategoryQuery
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        $topOutstandingInvoices = $invoiceWithPayments
            ->filter(fn($invoice) => $invoice->sisa_tagihan > 0)
            ->sortByDesc('sisa_tagihan')
            ->take(5)
            ->values();

        // Proyek aktif yang belum punya invoice sama sekali
        $proyekTanpaInvoice = Proyek::with('permohonan')
            ->whereIn('status', [Proyek::STATUS_PROGRESS, Proyek::STATUS_REPORTING, Proyek::STATUS_ENDORSE])
            ->whereDoesntHave('invoices')
            ->get();

        // Invoice per bulan untuk bar chart (array 12 bulan)
        $invoicePerBulan = array_fill(0, 12, 0);
        $bayarPerBulan   = array_fill(0, 12, 0);

        Invoice::whereYear('created_at', $selectedYear)
            ->get()
            ->each(function ($inv) use (&$invoicePerBulan) {
                $invoicePerBulan[$inv->created_at->month - 1] += (float) $inv->grand_total;
            });

        Pembayaran::whereYear('tanggal_bayar', $selectedYear)
            ->whereNotNull('tanggal_bayar')
            ->get()
            ->each(function ($p) use (&$bayarPerBulan) {
                $bayarPerBulan[$p->tanggal_bayar->month - 1] += (float) $p->nominal_bayar;
            });

        return view('dashboard.index', compact(
            'selectedYear',
            'selectedMonth',
            'monthOptions',
            'availableYears',
            'permohonanTotal',
            'permohonanOpen',
            'permohonanClose',
            'proyekAktif',
            'invoiceTotal',
            'outstandingTotal',
            'invoiceLunas',
            'invoiceSebagian',
            'invoiceBelumBayar',
            'projectStatusChart',
            'paymentsByMethod',
            'pakByCategory',
            'topOutstandingInvoices',
            'proyekTanpaInvoice',
            'invoicePerBulan',
            'bayarPerBulan',
        ));
    }
}
