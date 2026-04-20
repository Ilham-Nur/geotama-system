<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export Data Aset</title>
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>No Aset</th>
                <th>Nama</th>
                <th>Merek</th>
                <th>No Seri</th>
                <th>Lokasi</th>
                <th>Jumlah</th>
                <th>Tahun</th>
                <th>Remark</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $asset->no_aset }}</td>
                    <td>{{ $asset->nama }}</td>
                    <td>{{ $asset->merek ?? '-' }}</td>
                    <td>{{ $asset->no_seri ?? '-' }}</td>
                    <td>{{ $asset->lokasi }}</td>
                    <td>{{ $asset->jumlah }}</td>
                    <td>{{ $asset->tahun }}</td>
                    <td>{{ $asset->remark ?? '-' }}</td>
                    <td>{{ (float) $asset->harga }}</td>
                    <td>{{ (float) $asset->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Belum ada data aset.</td>
                </tr>
            @endforelse

            <tr>
                <td colspan="10" style="text-align:right;"><strong>Grand Total</strong></td>
                <td><strong>{{ (float) $grandTotal }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
