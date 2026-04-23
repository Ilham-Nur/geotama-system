<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $quotation->no_quo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h2, h4 { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        .no-border td { border: none; padding: 2px 0; }
        .text-right { text-align: right; }
        .section { margin-top: 18px; }
        ul { margin: 8px 0 0 18px; padding: 0; }
    </style>
</head>

<body>
    <h2>QUOTATION</h2>

    <table class="no-border">
        <tr>
            <td width="20%"><strong>No Quotation</strong></td>
            <td width="2%">:</td>
            <td>{{ $quotation->no_quo }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ optional($quotation->tanggal)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td><strong>Client</strong></td>
            <td>:</td>
            <td>{{ $quotation->client->nama_perusahaan ?? '-' }}</td>
        </tr>
    </table>

    <div class="section">
        <h4>Item Quotation</h4>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Description</th>
                    <th width="15%">Satuan</th>
                    <th width="10%">Qty</th>
                    <th width="20%">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotation->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->satuan ?? '-' }}</td>
                        <td class="text-right">{{ number_format((float) $item->qty, 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format((float) $item->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-right">Tidak ada item.</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format((float) $quotation->grand_total_quo, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h4>Terms</h4>
        @if($quotation->terms->isNotEmpty())
            <ul>
                @foreach($quotation->terms as $term)
                    <li>{{ $term->name }}</li>
                @endforeach
            </ul>
        @else
            <p>-</p>
        @endif
    </div>
</body>

</html>
