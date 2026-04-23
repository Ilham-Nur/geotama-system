<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('client')
            ->latest()
            ->get();

        return view('quotation.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Client::orderBy('nama_perusahaan')->get();
        $previousQuotations = Quotation::with(['items', 'terms'])
            ->orderByDesc('id')
            ->get(['id', 'no_quo', 'client_id']);

        return view('quotation.create', [
            'clients' => $clients,
            'generatedNoQuotation' => Quotation::generateNoQuotation(),
            'previousQuotations' => $previousQuotations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_quo' => ['required', 'string', 'max:255', 'unique:quotations,no_quo'],
            'tanggal' => ['required', 'date'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.satuan' => ['nullable', 'string', 'max:100'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
            'terms' => ['nullable', 'array'],
            'terms.*.name' => ['required', 'string', 'max:255'],
        ]);

        $quotation = DB::transaction(function () use ($validated) {
            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return (float) $item['total'];
            });

            $quotation = Quotation::create([
                'no_quo' => $validated['no_quo'],
                'tanggal' => $validated['tanggal'],
                'client_id' => $validated['client_id'] ?? null,
                'grand_total_quo' => $grandTotal,
            ]);

            foreach ($validated['items'] as $item) {
                $quotation->items()->create([
                    'description' => $item['description'],
                    'satuan' => $item['satuan'] ?? null,
                    'qty' => $item['qty'],
                    'total' => $item['total'],
                ]);
            }

            foreach ($validated['terms'] ?? [] as $term) {
                $quotation->terms()->create([
                    'name' => $term['name'],
                ]);
            }

            return $quotation;
        });

        $this->ensureQrCodePath($quotation);

        return redirect()->route('quotation.index')->with('success', 'Quotation berhasil dibuat.');
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load(['items', 'terms']);
        $clients = Client::orderBy('nama_perusahaan')->get();

        return view('quotation.edit', compact('quotation', 'clients'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'no_quo' => ['required', 'string', 'max:255', 'unique:quotations,no_quo,' . $quotation->id],
            'tanggal' => ['required', 'date'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.satuan' => ['nullable', 'string', 'max:100'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.total' => ['required', 'numeric', 'min:0'],
            'terms' => ['nullable', 'array'],
            'terms.*.name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $quotation) {
            $grandTotal = collect($validated['items'])->sum(function ($item) {
                return (float) $item['total'];
            });

            $quotation->update([
                'no_quo' => $validated['no_quo'],
                'tanggal' => $validated['tanggal'],
                'client_id' => $validated['client_id'] ?? null,
                'grand_total_quo' => $grandTotal,
            ]);

            $quotation->items()->delete();
            foreach ($validated['items'] as $item) {
                $quotation->items()->create([
                    'description' => $item['description'],
                    'satuan' => $item['satuan'] ?? null,
                    'qty' => $item['qty'],
                    'total' => $item['total'],
                ]);
            }

            $quotation->terms()->delete();
            foreach ($validated['terms'] ?? [] as $term) {
                $quotation->terms()->create([
                    'name' => $term['name'],
                ]);
            }
        });

        $this->ensureQrCodePath($quotation, true);

        return redirect()->route('quotation.index')->with('success', 'Quotation berhasil diperbarui.');
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->qr_code_path && Storage::disk('public')->exists($quotation->qr_code_path)) {
            Storage::disk('public')->delete($quotation->qr_code_path);
        }

        $quotation->delete();

        return redirect()->route('quotation.index')->with('success', 'Quotation berhasil dihapus.');
    }

    public function exportPdf(Quotation $quotation)
    {
        $quotation->load(['client', 'items', 'terms']);
        $qrCodePath = $this->ensureQrCodePath($quotation);
        $qrBase64 = $this->qrPathToBase64($qrCodePath);

        $pdf = Pdf::loadView('quotation.pdf', compact('quotation', 'qrBase64'))
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ])
            ->setPaper('a4', 'portrait');

        return $pdf->stream($quotation->no_quo . '.pdf');
    }

    public function publicShow(Quotation $quotation)
    {
        $quotation->load(['client', 'items', 'terms']);

        $approval = [
            'approver_name' => 'Authorized Signatory',
            'approver_position' => 'Management Representative',
            'approval_date' => optional($quotation->updated_at)->format('d F Y H:i') ?? '-',
        ];

        return view('quotation.scan', compact('quotation', 'approval'));
    }

    private function ensureQrCodePath(Quotation $quotation, bool $forceRegenerate = false): string
    {
        $scanUrl = route('quotation.public-show', $quotation->id);
        $fileName = 'quotation-' . $quotation->id . '.svg';
        $storagePath = 'quotation/qr/' . $fileName;

        if ($forceRegenerate || !$quotation->qr_code_path || !Storage::disk('public')->exists($quotation->qr_code_path)) {
            $qrSvg = QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->generate($scanUrl);

            Storage::disk('public')->put($storagePath, $qrSvg);

            $quotation->update([
                'qr_code_path' => $storagePath,
            ]);

            return $storagePath;
        }

        return $quotation->qr_code_path;
    }

    private function qrPathToBase64(?string $path): string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return '';
        }

        $content = Storage::disk('public')->get($path);

        $mime = str_ends_with(strtolower($path), '.svg') ? 'image/svg+xml' : 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }
}
