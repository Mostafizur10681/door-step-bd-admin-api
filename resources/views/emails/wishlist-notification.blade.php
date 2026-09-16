<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
        }
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #0f172a;
        }
        .message-box {
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
            background-color: #f1f5f9;
            padding: 18px 22px;
            border-radius: 14px;
            border-left: 4px solid #10b981;
            margin-bottom: 25px;
        }
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            background-color: #fafafa;
            margin-bottom: 25px;
        }
        .product-info h3 {
            margin: 0 0 6px 0;
            font-size: 16px;
            color: #0f172a;
        }
        .product-price {
            font-size: 16px;
            font-weight: 800;
            color: #059669;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn {
            display: inline-block;
            background-color: #059669;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>ShopiaBD Updates</h1>
        </div>
        <div class="content">
            <div class="greeting">Hello {{ $customer->name ?? 'Valued Customer' }},</div>
            
            <div class="message-box">
                {!! nl2br(e($customMessage)) !!}
            </div>

            @if($product)
                <div class="product-card">
                    <div class="product-info">
                        <h3>{{ $product->name }}</h3>
                        @if($product->sku)
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 6px;">SKU: {{ $product->sku }}</div>
                        @endif
                        <div class="product-price">
                            ${{ number_format($product->sale_price && $product->sale_price < $product->price ? $product->sale_price : $product->price, 2) }}
                        </div>
                    </div>
                </div>
            @endif

            @if($actionUrl)
                <div class="btn-container">
                    <a href="{{ $actionUrl }}" class="btn" target="_blank">View Wishlisted Product</a>
                </div>
            @endif
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} ShopiaBD. All rights reserved. You received this email regarding an item in your wishlist.
        </div>
    </div>
</body>
</html>
