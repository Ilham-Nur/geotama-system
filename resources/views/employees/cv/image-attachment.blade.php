<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; text-align: center; }
        .label {
            border-bottom: 1px solid #cfd8dc;
            color: #455a64;
            font-size: 8px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            text-align: left;
        }
        img { max-height: 710px; max-width: 100%; }
    </style>
</head>
<body>
    <div class="label">{{ $label }}</div>
    <img src="{{ $imageDataUri }}" alt="Lampiran">
</body>
</html>
