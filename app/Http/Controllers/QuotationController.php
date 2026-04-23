<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($validated) {
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
        });

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

        return redirect()->route('quotation.index')->with('success', 'Quotation berhasil diperbarui.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotation.index')->with('success', 'Quotation berhasil dihapus.');
    }

    public function exportPdf(Quotation $quotation)
    {
        $quotation->load(['client', 'items', 'terms']);
        $scanUrl = route('quotation.public-show', $quotation->id);

        $pdf = Pdf::loadView('quotation.pdf', compact('quotation', 'scanUrl'))
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
}

