<!DOCTYPE html>
<html>
<head>
    <title>Product QR Codes</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .qr-code-container {
            display: inline-block;
            width: 200px;
            height: 250px;
            margin: 10px;
            border: 1px solid #ccc;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        .qr-code-container img {
            max-width: 180px;
            max-height: 180px;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .product-sku {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>Product QR Codes</h1>

    @foreach ($products as $product)
        <div class="qr-code-container">
            <img src="data:image/png;base64,{{ base64_encode($product->generateQrCode()) }}" alt="QR Code">
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-sku">SKU: {{ $product->sku }}</div>
        </div>
    @endforeach
</body>
</html>
