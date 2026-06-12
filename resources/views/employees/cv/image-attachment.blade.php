<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; text-align: center; }
        .label { color: #455a64; font-size: 10px; margin-bottom: 12px; text-align: left; }
        img { max-height: 720px; max-width: 100%; }
    </style>
</head>
<body>
    <div class="label">{{ $label }}</div>
    <img src="{{ $imageDataUri }}" alt="{{ $label }}">
</body>
</html>
