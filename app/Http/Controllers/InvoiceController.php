<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index()
    {
        return redirect()->route('pembayaran.index');
    }


    public function create()
    {
        $proyeks = Proyek::with(['invoices', 'permohonan.items'])
            ->get()
            ->map(function ($proyek) {
                $totalInvoice = $proyek->invoices->sum(function ($invoice) {
                    return (float) $invoice->grand_total;
                });

                $totalTax = $proyek->invoices->sum(function ($invoice) {
                    return (float) $invoice->tax;
                });

                $totalDiscount = $proyek->invoices->sum(function ($invoice) {
                    return (float) $invoice->discount;
                });

                // untuk sisa tagihan proyek:
                // discount diabaikan, tax juga diabaikan
                // yang dihitung adalah nilai bruto pekerjaan
                $totalInvoiceNet = $proyek->invoices->sum(function ($invoice) {
                    return (float) $invoice->sub_total;
                });

                $nominal = is_null($proyek->nominal) ? null : (float) $proyek->nominal;
                $hasInvoice = $proyek->invoices->isNotEmpty();
                $isNominalEmpty = is_null($proyek->nominal) || (float) $proyek->nominal <= 0;

                $proyek->total_invoice = $totalInvoice; // total invoice final
                $proyek->total_invoice_net = $totalInvoiceNet; // dasar sisa tagihan proyek
                $proyek->total_tax = $totalTax;
                $proyek->total_discount = $totalDiscount;
                $proyek->has_invoice = $hasInvoice;
                $proyek->is_nominal_empty = $isNominalEmpty;

                if ($isNominalEmpty) {
                    $proyek->sisa_tagihan = null;
                } else {
                    $proyek->sisa_tagihan = max($nominal - $totalInvoiceNet, 0);
                }

                $permohonanItems = optional($proyek->permohonan)->items ?? collect();
                $tanggalPelaksanaanTersedia = $permohonanItems
                    ->pluck('tanggal_pelaksanaan')
                    ->filter();

                $proyek->notes_template = [
                    'nama_perusahaan' => $proyek->permohonan->nama_perusahaan ?? '',
                    'nomor_permohonan' => $proyek->permohonan->nomor ?? '',
                    'tanggal_permohonan' => optional($proyek->permohonan?->created_at)->format('d-m-Y') ?? '',
                    'lokasi_permohonan' => $proyek->permohonan->lokasi ?? '',
                    'tanggal_pelaksanaan_awal' => $tanggalPelaksanaanTersedia->isNotEmpty()
                        ? $tanggalPelaksanaanTersedia->min()
                        : '',
                    'tanggal_pelaksanaan_akhir' => $tanggalPelaksanaanTersedia->isNotEmpty()
                        ? $tanggalPelaksanaanTersedia->max()
                        : '',
                ];

                return $proyek;
            })
            ->filter(function ($proyek) {
                return $proyek->is_nominal_empty || $proyek->sisa_tagihan > 0;
            })
            ->values();


        $selectedProyekId = old('proyek_id', request('proyek_id'));


        return view('invoice.create', [
            'generatedInvoiceNo' => Invoice::generateInvoiceNo(),
            'proyeks' => $proyeks,
            'selectedProyekId' => $selectedProyekId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|exists:proyek,id',
            'jenis_invoice' => 'required|in:dp,termin,pelunasan',
            'tanggal_invoice' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',

            // nominal proyek hanya wajib kalau invoice pertama
            'nominal_proyek' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.unit' => 'nullable|string|max:100',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $proyek = Proyek::with('invoices')->lockForUpdate()->findOrFail($request->proyek_id);

            $sudahPernahInvoice = $proyek->invoices()->exists();

            // kalau invoice pertama, nominal proyek wajib diisi
            if (! $sudahPernahInvoice) {
                if ($request->filled('nominal_proyek') === false || (float)$request->nominal_proyek <= 0) {
                    throw ValidationException::withMessages([
                        'nominal_proyek' => 'Nominal proyek wajib diisi untuk invoice pertama.',
                    ]);
                }

                // simpan nominal proyek hanya saat invoice pertama
                $proyek->update([
                    'nominal' => (float) $request->nominal_proyek,
                ]);
            }

            $nominalProyek = (float) ($proyek->nominal ?? 0);

            $subTotal = 0;
            foreach ($request->items as $item) {
                $lineTotal = (float) $item['qty'] * (float) $item['amount'];
                $subTotal += $lineTotal;
            }

            $discount = (float) ($request->discount ?? 0);
            $tax = (float) ($request->tax ?? 0);
            $grandTotal = $subTotal - $discount + $tax;

            if ($grandTotal < 0) {
                throw ValidationException::withMessages([
                    'discount' => 'Grand total tidak boleh kurang dari 0.',
                ]);
            }

            $totalInvoiceSebelumnya = (float) $proyek->invoices()->sum('grand_total');
            $sisaSebelumInvoiceIni = $nominalProyek - $totalInvoiceSebelumnya;

            if ($sudahPernahInvoice && $grandTotal > $sisaSebelumInvoiceIni) {
                throw ValidationException::withMessages([
                    'items' => 'Grand total invoice melebihi sisa tagihan proyek.',
                ]);
            }

            $invoice = Invoice::create([
                'proyek_id' => $proyek->id,
                'no_invoice' => Invoice::generateInvoiceNo(),
                'jenis_invoice' => $request->jenis_invoice,
                'tanggal_invoice' => $request->tanggal_invoice ?? now()->toDateString(),
                'sub_total' => $subTotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $lineTotal = (float) $item['qty'] * (float) $item['amount'];

                $invoice->items()->create([
                    'description' => $item['description'],
                    'unit' => $item['unit'] ?? null,
                    'qty' => $item['qty'],
                    'amount' => $item['amount'],
                    'total' => $lineTotal,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('invoice.index')
                ->with('success', 'Invoice berhasil disimpan.');
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

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load([
            'items',
            'proyek.permohonan',
        ]);

        $pdf = Pdf::loadView('invoice.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($invoice->no_invoice . '.pdf');
    }


    public function uploadSignedFile(Request $request, Invoice $invoice)
    {
        $request->validate([
            'file_invoice_signed' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'file_invoice_signed.required' => 'File hardcopy wajib diupload.',
            'file_invoice_signed.mimes' => 'File harus berupa PDF, JPG, JPEG, atau PNG.',
            'file_invoice_signed.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        if ($invoice->file_invoice_signed && Storage::disk('public')->exists($invoice->file_invoice_signed)) {
            Storage::disk('public')->delete($invoice->file_invoice_signed);
        }

        $file = $request->file('file_invoice_signed');
        $path = $file->store('invoice/signed', 'public');

        $invoice->update([
            'file_invoice_signed' => $path,
        ]);

        return redirect()
            ->route('pembayaran.index')
            ->with('success', 'File hardcopy invoice berhasil diupload.');
    }
}
