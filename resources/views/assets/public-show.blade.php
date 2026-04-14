<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $asset->no_aset }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="mb-3">Detail Aset</h4>
                <p class="text-muted mb-4">Data ini dapat diakses dari QR Code tanpa login.</p>

                <div class="row g-3">
                    <div class="col-md-6"><strong>No Aset:</strong> {{ $asset->no_aset }}</div>
                    <div class="col-md-6"><strong>Nama:</strong> {{ $asset->nama }}</div>
                    <div class="col-md-6"><strong>Merek:</strong> {{ $asset->merek ?? '-' }}</div>
                    <div class="col-md-6"><strong>No Seri:</strong> {{ $asset->no_seri ?? '-' }}</div>
                    <div class="col-md-6"><strong>Lokasi:</strong> {{ $asset->lokasi }}</div>
                    <div class="col-md-6"><strong>Jumlah:</strong> {{ $asset->jumlah }}</div>
                    <div class="col-md-6"><strong>Harga:</strong> Rp {{ number_format($asset->harga, 0, ',', '.') }}</div>
                    <div class="col-md-6"><strong>Total:</strong> Rp {{ number_format($asset->total, 0, ',', '.') }}</div>
                    <div class="col-md-6"><strong>Tahun:</strong> {{ $asset->tahun }}</div>
                    <div class="col-md-6"><strong>Remark:</strong> {{ $asset->remark ?? '-' }}</div>
                    <div class="col-md-6">
                        <strong>File Faktur:</strong>
                        @if ($asset->file_faktur)
                            <a href="{{ asset('storage/' . $asset->file_faktur) }}" target="_blank">Lihat Faktur</a>
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Gambar:</strong><br>
                        @if ($asset->gambar)
                            <img src="{{ asset('storage/' . $asset->gambar) }}" alt="Gambar Aset" class="img-fluid rounded border"
                                style="max-height: 220px;">
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
