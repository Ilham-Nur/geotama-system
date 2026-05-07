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

        $permohonanCollection = Permohonan::with('items')
            ->whereYear('created_at', $selectedYear)
            ->get();

        $permohonanTotal = $permohonanCollection->count();
        $permohonanOpen = $permohonanCollection->where('status', 'OPEN')->count();
        $permohonanClose = $permohonanCollection->where('status', 'CLOSE')->count();

        $proyekAktif = Proyek::whereYear('created_at', $selectedYear)
            ->whereIn('status', [
                Proyek::STATUS_PROGRESS,
                Proyek::STATUS_REPORTING,
                Proyek::STATUS_ENDORSE,
            ])->count();

        $invoiceWithPayments = Invoice::withSum('pembayarans', 'nominal_bayar')
            ->whereYear('created_at', $selectedYear)
            ->get();

        $invoiceTotal = (float) $invoiceWithPayments->sum('grand_total');
        $outstandingTotal = $invoiceWithPayments->sum(fn ($invoice) => $invoice->sisa_tagihan);
        $invoiceLunas = $invoiceWithPayments->where('status_pembayaran', 'lunas')->count();
        $invoiceSebagian = $invoiceWithPayments->where('status_pembayaran', 'sebagian')->count();
        $invoiceBelumBayar = $invoiceWithPayments->where('status_pembayaran', 'belum_bayar')->count();

        $projectStatusChart = Proyek::whereYear('created_at', $selectedYear)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $monthlyPayments = Pembayaran::selectRaw("MONTH(tanggal_bayar) as month_number, DATE_FORMAT(tanggal_bayar, '%Y-%m') as month, SUM(nominal_bayar) as total")
            ->whereYear('tanggal_bayar', $selectedYear)
            ->whereNotNull('tanggal_bayar')
            ->groupBy('month_number', 'month')
            ->orderBy('month_number')
            ->get();

        $pakMonthlyByCategory = PakItem::query()
            ->join('categories', 'categories.id', '=', 'pak_items.category_id')
            ->selectRaw("MONTH(pak_items.created_at) as month_number, DATE_FORMAT(pak_items.created_at, '%Y-%m') as month, categories.name as category, SUM(pak_items.total_cost) as total")
            ->whereYear('pak_items.created_at', $selectedYear)
            ->groupBy('month_number', 'month', 'categories.name')
            ->orderBy('month_number')
            ->get();

        $topOutstandingInvoices = $invoiceWithPayments
            ->filter(fn ($invoice) => $invoice->sisa_tagihan > 0)
            ->sortByDesc('sisa_tagihan')
            ->take(5)
            ->values();

        return view('dashboard.index', compact(
            'selectedYear',
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
            'monthlyPayments',
            'pakMonthlyByCategory',
            'topOutstandingInvoices'
        ));
    }
}
