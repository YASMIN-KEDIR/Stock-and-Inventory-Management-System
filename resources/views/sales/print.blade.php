<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #e2e8f0;
            padding: 32px;
            border-radius: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }
        .logo span {
            color: #4f46e5;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals {
            margin-left: auto;
            width: 300px;
            margin-bottom: 24px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
        }
        .totals-row.grand-total {
            border-top: 2px solid #0f172a;
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: bold;
            background: #ecfdf5;
            color: #065f46;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 0 auto 16px; text-align: right;">
        <button onclick="window.print()" style="background: #4f46e5; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            Print / Save as PDF
        </button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div>
                <div class="logo">Merkato<span style="color: #059669;">.</span></div>
                <div style="color: #059669; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;">Retail POS & Finance</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 18px; font-weight: bold; font-family: monospace;">{{ $sale->invoice_number }}</div>
                <div style="color: #64748b; font-size: 12px; margin-top: 2px;">Date: {{ $sale->sale_date->format('M d, Y') }}</div>
                <div style="margin-top: 6px;"><span class="badge">{{ $sale->payment_status }}</span></div>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase;">Customer:</div>
                <div style="font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 4px;">{{ $sale->customer->name ?? 'Walk-In Customer (Cash)' }}</div>
                @if($sale->customer)
                    <div style="color: #64748b; font-size: 12px; margin-top: 2px;">{{ $sale->customer->phone }}</div>
                    <div style="color: #64748b; font-size: 12px;">{{ $sale->customer->address }}</div>
                @endif
            </div>
            <div style="text-align: right;">
                <div style="font-size: 11px; font-weight: bold; color: #94a3b8; text-transform: uppercase;">Store / Terminal:</div>
                <div style="font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 4px;">Merkato Store</div>
                <div style="color: #64748b; font-size: 12px; margin-top: 2px;">Cashier: {{ $sale->user->name ?? 'Cashier' }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item & SKU</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Discount</th>
                    <th style="text-align: right;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->product->name ?? 'Product' }}</strong>
                            <div style="color: #94a3b8; font-size: 11px; font-family: monospace;">SKU: {{ $item->product->sku ?? '—' }}</div>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right; color: #d97706;">${{ number_format($item->line_discount, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span style="color: #64748b;">Subtotal:</span>
                <span>${{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
                <div class="totals-row">
                    <span style="color: #64748b;">Order Discount:</span>
                    <span style="color: #d97706;">-${{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            @if($sale->tax_amount > 0)
                <div class="totals-row">
                    <span style="color: #64748b;">Tax / VAT:</span>
                    <span>+${{ number_format($sale->tax_amount, 2) }}</span>
                </div>
            @endif
            <div class="totals-row grand-total">
                <span>Grand Total:</span>
                <span>${{ number_format($sale->grand_total, 2) }}</span>
            </div>
            <div class="totals-row" style="color: #059669; font-weight: bold;">
                <span>Amount Paid:</span>
                <span>${{ number_format($sale->amount_paid, 2) }}</span>
            </div>
            @if($sale->remaining_balance > 0)
                <div class="totals-row" style="color: #d97706; font-weight: bold;">
                    <span>Balance Due:</span>
                    <span>${{ number_format($sale->remaining_balance, 2) }}</span>
                </div>
            @endif
        </div>

        <div class="footer">
            Thank you for your business! Please keep this invoice receipt for your records.
        </div>
    </div>
</body>
</html>
