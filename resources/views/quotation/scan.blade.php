<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi TTD Digital Quotation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 0; padding: 24px; color: #1f2937; }
        .card { max-width: 760px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        th { width: 30%; background: #f9fafb; }
        .ok { color: #047857; font-weight: 600; margin-top: 16px; }
    </style>
</head>

<body>
    <div class="card">
        <h2>Validasi TTD Digital Quotation</h2>
        <p>Halaman ini menampilkan data quotation dari barcode/QR pada dokumen PDF.</p>

        <table>
            <tbody>
                <tr>
                    <th>No Quotation</th>
                    <td>{{ $quotation->no_quo }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ optional($quotation->tanggal)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th>Client</th>
                    <td>{{ $quotation->client->nama_perusahaan ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Grand Total</th>
                    <td>Rp {{ number_format((float) $quotation->grand_total_quo, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <p class="ok">Dokumen quotation valid dan terdaftar di sistem.</p>
    </div>
</body>

</html>
