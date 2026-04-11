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
    public function index()
    {
        $proyeks = Proyek::with([
            'permohonan',
            'invoices' => function ($query) {
                $query->with('pembayarans')->orderBy('tanggal_invoice', 'asc');
            }
        ])
            ->whereHas('invoices')
            ->latest()
            ->get();

        $pembayarans = Pembayaran::with([
            'invoice.proyek.permohonan'
        ])
            ->latest()
            ->paginate(10);


        $proyekBelumInvoice = Proyek::with('permohonan')
            ->whereDoesntHave('invoices')
            ->latest()
            ->get();

        return view('pembayaran.index', compact(
            'proyeks',
            'pembayarans',
            'proyekBelumInvoice'
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
