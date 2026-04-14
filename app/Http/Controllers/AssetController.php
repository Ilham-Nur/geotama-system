<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->paginate(10);

        return view('assets.index', [
            'assets' => $assets,
            'generatedNoAset' => Asset::generateNoAset(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'no_seri' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'file_faktur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'tahun' => 'required|integer|min:1900|max:2100',
            'remark' => 'nullable|in:baik,perlu perbaikan,rusak,hilang',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $validated['no_aset'] = Asset::generateNoAset();
        $validated['total'] = $validated['jumlah'] * $validated['harga'];
        $validated['qr_token'] = Asset::generateQrToken();

        if ($request->hasFile('file_faktur')) {
            $validated['file_faktur'] = $request->file('file_faktur')->store('asset-faktur', 'public');
        }

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('asset-gambar', 'public');
        }

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil ditambahkan.');
    }

    public function publicShow(Asset $asset)
    {
        return view('assets.public-show', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'no_seri' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'file_faktur' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'tahun' => 'required|integer|min:1900|max:2100',
            'remark' => 'nullable|in:baik,perlu perbaikan,rusak,hilang',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $validated['total'] = $validated['jumlah'] * $validated['harga'];

        if ($request->hasFile('file_faktur')) {
            if ($asset->file_faktur) {
                Storage::disk('public')->delete($asset->file_faktur);
            }
            $validated['file_faktur'] = $request->file('file_faktur')->store('asset-faktur', 'public');
        }

        if ($request->hasFile('gambar')) {
            if ($asset->gambar) {
                Storage::disk('public')->delete($asset->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('asset-gambar', 'public');
        }

        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->file_faktur) {
            Storage::disk('public')->delete($asset->file_faktur);
        }

        if ($asset->gambar) {
            Storage::disk('public')->delete($asset->gambar);
        }

        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil dihapus.');
    }
}
