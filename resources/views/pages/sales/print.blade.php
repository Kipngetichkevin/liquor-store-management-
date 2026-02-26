<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: white;
            color: black;
        }
        .receipt {
            max-width: 300px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        td {
            padding: 3px 0;
        }
        .item-name {
            max-width: 150px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .amount {
            text-align: right;
        }
        .totals {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .totals div {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        @media print {
            body { padding: 0; }
            .receipt { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>LIQUOR STORE</h2>
            <p>Point of Sale Receipt</p>
        </div>

        <div class="info">
            <div>
                <span>Invoice:</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div>
                <span>Date:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span>Cashier:</span>
                <span>{{ $sale->user->name ?? 'System' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td class="item-name">{{ $item->product->name ?? 'Unknown' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format($item->unit_price, 0) }}</td>
                    <td class="amount">{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div>
                <span>Subtotal:</span>
                <span>KSh {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div>
                <span>Tax (16%):</span>
                <span>KSh {{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            <div>
                <span>Discount:</span>
                <span>KSh {{ number_format($sale->discount_amount, 2) }}</span>
            </div>
        </div>

        <div class="grand-total">
            <div>
                <span>TOTAL:</span>
                <span>KSh {{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>

        <div style="margin-top: 10px; padding-top: 5px; border-top: 1px solid #000;">
            <div>
                <span>Payment Method:</span>
                <span>{{ ucfirst($sale->payment_method) }}</span>
            </div>
            <div>
                <span>Amount Paid:</span>
                <span>KSh {{ number_format($sale->amount_paid, 2) }}</span>
            </div>
            <div>
                <span>Change:</span>
                <span>KSh {{ number_format($sale->change, 2) }}</span>
            </div>
        </div>

        @if($sale->notes)
            <div style="margin-top: 10px; font-size: 11px;">
                <strong>Notes:</strong> {{ $sale->notes }}
            </div>
        @endif

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Goods sold are not returnable</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
