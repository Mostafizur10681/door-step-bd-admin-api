<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} — SMT Mart BD</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])

    <style>
        /* Standalone, 100% Reliable Print & Screen Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #1e293b;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding: 24px 16px 48px;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .invoice-sheet {
            background: #ffffff;
            max-width: 860px;
            margin: 0 auto;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .accent-bar {
            height: 8px;
            background: linear-gradient(90deg, #059669 0%, #10b981 50%, #14b8a6 100%);
        }

        .invoice-body {
            padding: 40px 48px;
        }

        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            padding-bottom: 28px;
            border-bottom: 2px solid #f1f5f9;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: #059669;
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .brand-contact {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .invoice-title-block {
            text-align: right;
        }

        .inv-pill {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .inv-number {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 6px;
        }

        .inv-meta-text {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .inv-meta-text strong {
            color: #1e293b;
        }

        .badge-row {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-paid { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .badge-pending { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-blue { background: #e0f2fe; color: #075985; border-color: #bae6fd; }
        .badge-rose { background: #ffe4e6; color: #9f1239; border-color: #fecdd3; }

        /* 2-Column Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 28px 0;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
        }

        .info-card-title {
            font-size: 10px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        .info-card-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .info-card-text {
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }

        /* Items Table */
        .items-table-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 28px;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            text-align: left;
        }

        .inv-table thead th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .inv-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .inv-table tbody tr:last-child td {
            border-bottom: none;
        }

        .product-thumb {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .attr-pill {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            margin-top: 3px;
            margin-right: 4px;
        }

        /* Bottom Section: Payment & Total Cards Side by Side */
        .bottom-section {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 24px;
            align-items: start;
            margin-bottom: 32px;
        }

        .payment-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 14px;
        }

        .payment-box-title {
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 10px;
            display: block;
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 6px;
            color: #64748b;
        }

        .payment-detail-row strong {
            color: #0f172a;
        }

        .barcode-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .totals-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .totals-row strong {
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #cbd5e1;
            padding-top: 14px;
            margin-top: 14px;
        }

        .grand-total-label {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .grand-total-value {
            font-size: 22px;
            font-weight: 800;
            color: #059669;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Footer */
        .inv-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
        }

        .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 24px;
        }

        .terms-block {
            max-width: 460px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }

        .terms-block strong {
            color: #334155;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 3px;
        }

        .signature-block {
            text-align: right;
            min-width: 170px;
        }

        .signature-line {
            width: 160px;
            border-bottom: 1.5px dashed #94a3b8;
            margin: 0 0 6px auto;
        }

        .signature-text {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-sub {
            font-size: 10px;
            color: #94a3b8;
        }

        .disclaimer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        /* Top Action Toolbar */
        .toolbar {
            max-width: 860px;
            margin: 0 auto 20px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.4);
        }

        .btn-print {
            background: #10b981;
            color: #0f172a;
            font-weight: 700;
            font-size: 12px;
            padding: 8px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
            transition: all 0.15s ease;
        }
        .btn-print:hover {
            background: #34d399;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #334155;
            color: #f1f5f9;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .btn-secondary:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Print Media Setup */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #0f172a !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .invoice-body {
                padding: 10px 15px !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page-break-inside-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- SCREEN-ONLY ACTION TOOLBAR -->
    <div class="toolbar no-print">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-secondary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Back to Order</span>
            </a>
            <span style="color: #64748b; font-size: 13px;">|</span>
            <span style="color: #cbd5e1; font-size: 12px;">Order <strong class="font-mono" style="color: #ffffff;">#{{ $order->order_number }}</strong></span>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <button onclick="window.print()" class="btn-print">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                <span>Print Invoice</span>
            </button>
            <button onclick="window.close()" class="btn-secondary" style="border: none; cursor: pointer;">
                Close
            </button>
        </div>
    </div>

    <!-- MAIN INVOICE CONTAINER -->
    <div class="invoice-sheet">
        
        <!-- TOP COLOR ACCENT -->
        <div class="accent-bar"></div>

        <div class="invoice-body">
            
            <!-- HEADER -->
            <div class="inv-header">
                <!-- Merchant Brand Details -->
                <div>
                    <div class="brand-logo-wrap">
                        <div class="brand-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                            </svg>
                        </div>
                        <div>
                            <div class="brand-title">SMT Mart BD</div>
                            <div class="brand-subtitle">Official Invoice & Memo</div>
                        </div>
                    </div>

                    <div class="brand-contact">
                        <p><strong>Address:</strong> {{ $contact->address ?? ($footer->contact_address ?? 'Dhaka, Bangladesh') }}</p>
                        <p><strong>Helpline:</strong> <span class="font-mono">{{ $contact->phone ?? ($footer->contact_phone ?? '+880 1879-198066') }}</span></p>
                        <p><strong>Email:</strong> <span class="font-mono">{{ $contact->email ?? ($footer->contact_email ?? 'support@smtmartbd.com') }}</span></p>
                        <p><strong>Website:</strong> <span class="font-mono">www.smtmartbd.com</span></p>
                    </div>
                </div>

                <!-- Invoice Meta & Badges -->
                <div class="invoice-title-block">
                    <span class="inv-pill">INVOICE</span>
                    <div class="inv-number">#{{ $order->order_number }}</div>

                    <div class="inv-meta-text">
                        <span>Order Date:</span> <strong>{{ $order->created_at ? $order->created_at->format('M d, Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="inv-meta-text">
                        <span>Invoice Date:</span> <strong>{{ now()->format('M d, Y') }}</strong>
                    </div>

                    <div class="badge-row">
                        @php
                            $pStatus = strtolower($order->payment_status ?? 'pending');
                            $payBadgeClass = match($pStatus) {
                                'paid' => 'badge-paid',
                                'failed' => 'badge-rose',
                                default => 'badge-pending',
                            };

                            $oStatus = strtolower($order->status ?? 'pending');
                            $orderBadgeClass = match($oStatus) {
                                'delivered', 'completed' => 'badge-paid',
                                'shipped', 'out-for-delivery' => 'badge-blue',
                                'cancelled' => 'badge-rose',
                                default => 'badge-pending',
                            };
                        @endphp
                        <span class="status-badge {{ $payBadgeClass }}">
                            Payment: {{ ucfirst($order->payment_status ?: 'Pending') }}
                        </span>
                        <span class="status-badge {{ $orderBadgeClass }}">
                            Status: {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2-COLUMN INFO: CUSTOMER & DELIVERY -->
            <div class="info-grid">
                <!-- Customer Details -->
                <div class="info-card">
                    <span class="info-card-title">Customer Information</span>
                    <div class="info-card-name">{{ $order->customer_name ?: ($order->user->name ?? 'Valued Customer') }}</div>
                    <div class="info-card-text">
                        <p><strong>Phone:</strong> <span class="font-mono">{{ $order->customer_phone ?: ($order->user->phone ?? 'N/A') }}</span></p>
                        @if($order->customer_email ?: ($order->user->email ?? null))
                            <p><strong>Email:</strong> <span class="font-mono">{{ $order->customer_email ?: $order->user->email }}</span></p>
                        @endif
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="info-card">
                    <span class="info-card-title">Delivery Address @if(!empty($order->ship_different))<small style="font-size: 10px; color: #d97706; margin-left: 6px;">(Different Shipping Address)</small>@endif</span>
                    @php
                        $isShipDiff = !empty($order->ship_different) || (!empty($order->ship_address) && $order->ship_address !== $order->address);
                        if ($isShipDiff && !empty($order->ship_address)) {
                            $addressList = array_filter([
                                $order->ship_address,
                                $order->ship_town_city,
                                $order->ship_district,
                                $order->ship_country
                            ]);
                        } else {
                            $addressList = array_filter([
                                $order->address,
                                $order->thana,
                                $order->district,
                                $order->division
                            ]);
                        }
                        $formattedAddress = implode(', ', $addressList);
                    @endphp
                    @if($isShipDiff && (!empty($order->ship_customer_name) || !empty($order->ship_phone)))
                        <div style="margin-bottom: 6px; font-size: 11px; color: #334155;">
                            @if(!empty($order->ship_customer_name))<div><strong>Recipient:</strong> {{ $order->ship_customer_name }}</div>@endif
                            @if(!empty($order->ship_phone))<div><strong>Recipient Phone:</strong> <span class="font-mono">{{ $order->ship_phone }}</span></div>@endif
                        </div>
                    @endif
                    <div class="info-card-name" style="font-size: 13px; font-weight: 600;">
                        {{ $formattedAddress ?: 'Standard Delivery Address' }}
                    </div>
                    @if($order->customer_notes)
                        <div style="margin-top: 6px; font-size: 11px; color: #64748b; font-style: italic; background: #ffffff; padding: 4px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <strong style="color: #475569; font-style: normal;">Note:</strong> {{ $order->customer_notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- ITEMS TABLE -->
            <div class="items-table-wrap">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th>Product Description</th>
                            <th style="width: 120px; text-align: right;">Unit Price</th>
                            <th style="width: 70px; text-align: center;">Qty</th>
                            <th style="width: 130px; text-align: right;">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $calculatedSubtotal = 0; @endphp
                        @foreach($order->items as $idx => $item)
                            @php
                                $prod = $item->product;
                                $prodImg = $prod ? ($prod->image ?: ($prod->images->first()->image_path ?? '')) : '';
                                $linePrice = $item->price * $item->quantity;
                                $calculatedSubtotal += $linePrice;
                            @endphp
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-family: 'JetBrains Mono', monospace; font-weight: 600;">{{ $idx + 1 }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @if($prodImg)
                                            <img src="{{ str_starts_with($prodImg, 'data:image') || str_starts_with($prodImg, 'http') ? $prodImg : asset('storage/' . $prodImg) }}" class="product-thumb" alt="Product">
                                        @endif
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 13px;">
                                                {{ $prod ? $prod->name : ($item->product_name ?? 'Product Item') }}
                                            </div>
                                            @if(!empty($item->attributes))
                                                 @php
                                                     $attrs = is_array($item->attributes) ? $item->attributes : json_decode($item->attributes, true);
                                                 @endphp
                                                 @if(is_array($attrs) && count($attrs) > 0)
                                                     <div style="margin-top: 2px;">
                                                         @foreach($attrs as $key => $val)
                                                             @php
                                                                 if (is_array($val)) {
                                                                     if (isset($val['name']) || isset($val['value'])) {
                                                                         $display = ($val['name'] ?? 'Option') . ': ' . ($val['value'] ?? '');
                                                                     } else {
                                                                         $display = is_numeric($key) ? implode(', ', $val) : ucfirst($key) . ': ' . implode(', ', $val);
                                                                     }
                                                                 } else {
                                                                     $display = is_numeric($key) ? $val : ucfirst($key) . ': ' . $val;
                                                                 }
                                                             @endphp
                                                             <span class="attr-pill">{{ $display }}</span>
                                                         @endforeach
                                                     </div>
                                                 @elseif(is_string($item->attributes) && trim($item->attributes) !== '')
                                                     <div style="margin-top: 2px;">
                                                         <span class="attr-pill">{{ $item->attributes }}</span>
                                                     </div>
                                                 @endif
                                             @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right; font-family: 'JetBrains Mono', monospace; color: #334155; font-weight: 600;">
                                    ৳{{ number_format($item->price, 2) }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #0f172a; font-size: 13px;">
                                    {{ $item->quantity }}
                                </td>
                                <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a; font-size: 13px;">
                                    ৳{{ number_format($linePrice, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- BOTTOM SECTION: PAYMENT & TOTALS -->
            <div class="bottom-section page-break-inside-avoid">
                
                <!-- Left: Payment details + Barcode -->
                <div>
                    <!-- Payment Information Card -->
                    <div class="payment-box">
                        <span class="payment-box-title">Payment Details</span>
                        
                        <div class="payment-detail-row">
                            <span>Payment Method:</span>
                            <strong>{{ $order->payment_method ?: 'Cash on Delivery (COD)' }}</strong>
                        </div>

                        <div class="payment-detail-row">
                            <span>Payment Status:</span>
                            <strong style="color: {{ strtolower($order->payment_status) === 'paid' ? '#059669' : '#d97706' }};">
                                {{ ucfirst($order->payment_status ?: 'Pending') }}
                            </strong>
                        </div>
                    </div>

                    <!-- Barcode Verification Box -->
                    <div class="barcode-box">
                        <div>
                            <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; display: block;">Tracking & Barcode</span>
                            <span class="font-mono" style="font-size: 12px; font-weight: 700; color: #334155;">#{{ $order->order_number }}</span>
                        </div>
                        
                        <!-- High Contrast CSS Barcode -->
                        <div style="display: flex; align-items: center; gap: 2px; height: 32px; padding: 2px 4px; background: #ffffff;">
                            <span style="width: 2px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 3px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 4px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 2px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 3px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 2px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 4px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 2px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 1px; height: 28px; background: #0f172a; display: inline-block;"></span>
                            <span style="width: 3px; height: 28px; background: #0f172a; display: inline-block;"></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Financial Calculation Box -->
                <div class="totals-box">
                    <div class="totals-row">
                        <span>Items Subtotal</span>
                        <strong>৳{{ number_format($order->subtotal ?: $calculatedSubtotal, 2) }}</strong>
                    </div>

                    <div class="totals-row">
                        <span>Shipping & Delivery</span>
                        <strong>৳{{ number_format($order->shipping_amount ?? 60, 2) }}</strong>
                    </div>

                    @if($order->discount_amount && $order->discount_amount > 0)
                        <div class="totals-row" style="color: #059669;">
                            <span>Coupon Discount</span>
                            <strong style="color: #059669;">-৳{{ number_format($order->discount_amount, 2) }}</strong>
                        </div>
                    @endif

                    <div class="grand-total-row">
                        <span class="grand-total-label">Grand Total</span>
                        <span class="grand-total-value">৳{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

            </div>

            <!-- FOOTER: TERMS & SIGNATURE -->
            <div class="inv-footer page-break-inside-avoid">
                <div class="footer-grid">
                    <div class="terms-block">
                        <strong>Terms & Return Policy</strong>
                        <p>
                            Items can be exchanged or returned within 7 days in original condition with this original invoice. 
                            For any queries, please call us at <span class="font-mono" style="color: #0f172a; font-weight: 600;">{{ $contact->phone ?? ($footer->contact_phone ?? '+880 1879-198066') }}</span>.
                        </p>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-text">Authorized Signature</div>
                        <div class="signature-sub">SMT Mart BD Operations</div>
                    </div>
                </div>

                <div class="disclaimer">
                    Thank you for choosing SMT Mart BD! This is an official computer-generated invoice and requires no physical signature for digital copies.
                </div>
            </div>

        </div>
    </div>

</body>
</html>
