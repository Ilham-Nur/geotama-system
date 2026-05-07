<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PakItem;
use App\Models\Pembayaran;
use App\Models\Permohonan;
use App\Models\Proyek;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = now()->startOfMonth();

        $permohonanTotal = Permohonan::where('created_at', '>=', $startOfMonth)->count();
        $permohonanOpen = Permohonan::with('items')->get()->where('status', 'OPEN')->count();
        $permohonanClose = Permohonan::with('items')->get()->where('status', 'CLOSE')->count();

        $proyekAktif = Proyek::whereIn('status', [
            Proyek::STATUS_PROGRESS,
            Proyek::STATUS_REPORTING,
            Proyek::STATUS_ENDORSE,
        ])->count();

        $invoiceTotal = (float) Invoice::sum('grand_total');

        $invoiceWithPayments = Invoice::withSum('pembayarans', 'nominal_bayar')->get();
        $outstandingTotal = $invoiceWithPayments->sum(fn ($invoice) => $invoice->sisa_tagihan);
        $invoiceLunas = $invoiceWithPayments->where('status_pembayaran', 'lunas')->count();
        $invoiceSebagian = $invoiceWithPayments->where('status_pembayaran', 'sebagian')->count();
        $invoiceBelumBayar = $invoiceWithPayments->where('status_pembayaran', 'belum_bayar')->count();

        $projectStatusChart = Proyek::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $monthlyPayments = Pembayaran::selectRaw("DATE_FORMAT(tanggal_bayar, '%Y-%m') as month, SUM(nominal_bayar) as total")
            ->whereNotNull('tanggal_bayar')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        $pakMonthlyByCategory = PakItem::query()
            ->join('categories', 'categories.id', '=', 'pak_items.category_id')
            ->selectRaw("DATE_FORMAT(pak_items.created_at, '%Y-%m') as month, categories.name as category, SUM(pak_items.total_cost) as total")
            ->groupBy('month', 'categories.name')
            ->orderBy('month')
            ->get();

        $topOutstandingInvoices = $invoiceWithPayments
            ->filter(fn ($invoice) => $invoice->sisa_tagihan > 0)
            ->sortByDesc('sisa_tagihan')
            ->take(5)
            ->values();

        return view('dashboard.index', compact(
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
