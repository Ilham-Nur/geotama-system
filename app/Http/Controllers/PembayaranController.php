<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('invoice_per_page'), [10, 25, 50], true)
            ? (int) $request->input('invoice_per_page')
            : 10;
        $invoiceSearch = trim((string) $request->input('invoice_search'));
        $invoiceStatus = $request->input('invoice_status');
        $invoiceJenis = $request->input('invoice_jenis');
        $invoiceSigned = $request->input('invoice_signed');
        $invoiceDateFrom = $request->input('invoice_date_from');
        $invoiceDateTo = $request->input('invoice_date_to');

        $invoiceTotalSubQuery = Invoice::query()
            ->selectRaw('COALESCE(SUM(grand_total), 0)')
            ->whereColumn('proyek_id', 'proyek.id');
        $paymentTotalSubQuery = Invoice::query()
            ->leftJoin('pembayarans', 'pembayarans.invoice_id', '=', 'invoices.id')
            ->selectRaw('COALESCE(SUM(pembayarans.nominal_bayar), 0)')
            ->whereColumn('invoices.proyek_id', 'proyek.id');

        $proyeks = Proyek::query()
            ->select('proyek.*')
            ->selectSub($invoiceTotalSubQuery, 'invoice_total')
            ->selectSub($paymentTotalSubQuery, 'payment_total')
            ->with([
            'permohonan',
            'invoices' => function ($query) {
                $query->with('pembayarans')->orderBy('tanggal_invoice', 'asc');
            }
            ])
            ->whereHas('invoices')
            ->latest()
            ->when($invoiceSearch !== '', function ($query) use ($invoiceSearch) {
                $search = '%' . $invoiceSearch . '%';

                $query->where(function ($query) use ($search) {
                    $query->where('no_proyek', 'like', $search)
                        ->orWhere('deskripsi', 'like', $search)
                        ->orWhereHas('permohonan', function ($query) use ($search) {
                            $query->where(function ($query) use ($search) {
                                $query->where('nomor', 'like', $search)
                                    ->orWhere('nama_proyek', 'like', $search)
                                    ->orWhere('nama_perusahaan', 'like', $search)
                                    ->orWhere('nama_pic', 'like', $search);
                            });
                        })
                        ->orWhereHas('invoices', function ($query) use ($search) {
                            $query->where('no_invoice', 'like', $search);
                        });
                });
            })
            ->when(in_array($invoiceJenis, ['dp', 'termin', 'pelunasan'], true), function ($query) use ($invoiceJenis) {
                $query->whereHas('invoices', fn ($query) => $query->where('jenis_invoice', $invoiceJenis));
            })
            ->when($invoiceDateFrom, function ($query) use ($invoiceDateFrom) {
                $query->whereHas('invoices', fn ($query) => $query->whereDate('tanggal_invoice', '>=', $invoiceDateFrom));
            })
            ->when($invoiceDateTo, function ($query) use ($invoiceDateTo) {
                $query->whereHas('invoices', fn ($query) => $query->whereDate('tanggal_invoice', '<=', $invoiceDateTo));
            })
            ->when($invoiceSigned === 'uploaded', function ($query) {
                $query->whereHas('invoices', fn ($query) => $query->whereNotNull('file_invoice_signed')->where('file_invoice_signed', '!=', ''));
            })
            ->when($invoiceSigned === 'missing', function ($query) {
                $query->whereHas('invoices', function ($query) {
                    $query->where(function ($query) {
                        $query->whereNull('file_invoice_signed')
                            ->orWhere('file_invoice_signed', '');
                    });
                });
            })
            ->when($invoiceStatus === 'belum_bayar', function ($query) {
                $query->havingRaw('payment_total <= 0');
            })
            ->when($invoiceStatus === 'sebagian', function ($query) {
                $query->havingRaw('payment_total > 0 AND payment_total < invoice_total');
            })
            ->when($invoiceStatus === 'lunas', function ($query) {
                $query->havingRaw('invoice_total > 0 AND payment_total >= invoice_total');
            })
            ->paginate($perPage, ['*'], 'invoice_page')
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('pembayaran.partials.invoice-table', compact('proyeks'))->render(),
            ]);
        }

        $pembayarans = Pembayaran::with([
            'invoice.proyek.permohonan'
        ])
            ->latest()
            ->get();


        $proyekBelumInvoice = Proyek::with('permohonan')
            ->whereDoesntHave('invoices')
            ->latest()
            ->get();
        $activeTab = in_array($request->input('tab'), ['invoice', 'pembayaran'], true)
            ? $request->input('tab')
            : 'invoice';

        return view('pembayaran.index', compact(
            'proyeks',
            'pembayarans',
            'proyekBelumInvoice',
            'activeTab'
        ));
    }

    public function create(Request $request)
    {
        $generatedNoPembayaran = Pembayaran::generateNoPembayaran();

        $invoices = Invoice::with(['proyek.permohonan', 'pembayarans'])
            ->orderBy('tanggal_invoice', 'desc')
            ->get()
            ->map(function ($invoice) {
                $totalDibayar = $invoice->pembayarans->sum('nominal_bayar');
                $sisaTagihan = max((float) $invoice->grand_total - (float) $totalDibayar, 0);

                $invoice->total_dibayar = $totalDibayar;
                $invoice->sisa_tagihan = $sisaTagihan;

                return $invoice;
            });

        $selectedInvoiceId = old('invoice_id', $request->invoice_id);

        return view('pembayaran.create', compact(
            'generatedNoPembayaran',
            'invoices',
            'selectedInvoiceId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'tanggal_bayar' => 'required|date',
            'nominal_bayar' => 'required|numeric|min:0.01',
            'metode_pembayaran' => 'required|in:transfer,cash,giro,cek,lainnya',
            'nama_pengirim' => 'nullable|string|max:255',
            'bank_pengirim' => 'nullable|string|max:255',
            'no_referensi' => 'nullable|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::with('pembayarans')->lockForUpdate()->findOrFail($request->invoice_id);

            $totalDibayar = (float) $invoice->pembayarans()->sum('nominal_bayar');
            $sisaTagihan = (float) $invoice->grand_total - $totalDibayar;

            if ((float) $request->nominal_bayar > $sisaTagihan) {
                throw ValidationException::withMessages([
                    'nominal_bayar' => 'Nominal pembayaran melebihi sisa tagihan invoice.',
                ]);
            }

            $filePath = null;

            if ($request->hasFile('bukti_pembayaran')) {
                $filePath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
            }

            Pembayaran::create([
                'invoice_id' => $invoice->id,
                'no_pembayaran' => Pembayaran::generateNoPembayaran(),
                'tanggal_bayar' => $request->tanggal_bayar,
                'nominal_bayar' => $request->nominal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nama_pengirim' => $request->nama_pengirim,
                'bank_pengirim' => $request->bank_pengirim,
                'no_referensi' => $request->no_referensi,
                'bukti_pembayaran' => $filePath,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return redirect()
                ->route('pembayaran.index')
                ->with('success', 'Pembayaran berhasil disimpan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }
}
