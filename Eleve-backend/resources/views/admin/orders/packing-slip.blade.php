<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Atelier Packing Slip — {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Playfair Display', Georgia, serif;
            color: #171717;
            background: #FFFFFF;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #171717;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 3px;
        }
        .tagline {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #A67C52;
            margin-top: 4px;
        }
        .meta {
            text-align: right;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
        }
        .meta strong {
            display: block;
            font-size: 16px;
            font-family: monospace;
            color: #171717;
            margin-bottom: 4px;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .col {
            flex: 1;
        }
        .col-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #A67C52;
            margin-bottom: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 13px;
        }
        th {
            background: #F7F5F0;
            padding: 10px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            border-bottom: 1px solid #D8D0C4;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #F0ECE4;
        }
        .total-row {
            text-align: right;
            font-weight: bold;
            font-size: 15px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            font-style: italic;
            border-top: 1px solid #E8E2D9;
            padding-top: 20px;
            color: #66615B;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #171717; color: #FFFFFF; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Print / Save PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="brand">É L E V É</div>
            <div class="tagline">Haute Cordonnerie &bull; Atelier Paris &bull; Manila</div>
        </div>
        <div class="meta">
            <strong>{{ $order->order_number }}</strong>
            <div>Date: {{ $order->created_at->format('M d, Y h:i A') }}</div>
            <div>Status: {{ strtoupper($order->status) }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="col">
            <div class="col-title">Client Information</div>
            <div><strong>{{ $order->customer_name }}</strong></div>
            <div>{{ $order->customer_email }}</div>
            <div>{{ $order->customer_phone }}</div>
        </div>

        <div class="col" style="margin-left: 40px;">
            <div class="col-title">Delivery Destination</div>
            @php $addr = $order->shipping_address_snapshot ?? []; @endphp
            <div>{{ $addr['street_address'] ?? '' }}</div>
            @if(!empty($addr['apartment']))
                <div>{{ $addr['apartment'] }}</div>
            @endif
            <div>{{ $addr['city'] ?? '' }}, {{ $addr['province_state'] ?? '' }} {{ $addr['postal_code'] ?? '' }}</div>
            <div>{{ $addr['country'] ?? 'Philippines' }}</div>
        </div>

        <div class="col" style="margin-left: 40px;">
            <div class="col-title">Settlement Details</div>
            <div>Method: {{ strtoupper($order->payment?->payment_method ?? 'COD') }}</div>
            <div>Payment: {{ strtoupper($order->payment?->status ?? 'PENDING') }}</div>
            @if($order->payment?->transaction_reference)
                <div style="font-family: monospace; font-size: 11px;">Ref: {{ $order->payment->transaction_reference }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Footwear Silhouette</th>
                <th>Specification</th>
                <th>Unit Price</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td><strong>{{ $item->product_name }}</strong></td>
                <td>Size US {{ $item->size }} &bull; {{ $item->color }}</td>
                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-weight: 600;">₱{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; border-bottom: none; padding-top: 16px;">Footwear Subtotal:</td>
                <td style="text-align: right; border-bottom: none; padding-top: 16px;">₱{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: right; border-bottom: none;">White-Glove Courier Courier:</td>
                <td style="text-align: right; border-bottom: none;">{{ $order->shipping_fee == 0 ? 'COMPLIMENTARY' : '₱' . number_format($order->shipping_fee, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="total-row" style="border-top: 2px solid #171717;">Total Settled (PHP):</td>
                <td class="total-row" style="border-top: 2px solid #171717;">₱{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($order->notes)
        <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; background: #F7F5F0; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            <strong style="color: #A67C52; text-transform: uppercase; font-size: 10px;">Atelier Dispatch Instructions:</strong>
            <p style="margin: 4px 0 0 0; color: #444;">{{ $order->notes }}</p>
        </div>
    @endif

    <div class="footer">
        Every Step, Elevated. &bull; Thank you for choosing ÉLEVÉ Atelier.
    </div>

</body>
</html>
