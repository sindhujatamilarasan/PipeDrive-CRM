<!DOCTYPE html>
<html>
<head>
    <title>Stripe Invoices</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background: #f9f9f9;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab {
            padding: 8px 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background: #fff;
            transition: all 0.2s;
        }

        .tab:hover {
            background: #f0f0f0;
        }

        .tab.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .invoice-card {
            background: #fff;
            border-left: 5px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .invoice-id {
            font-weight: bold;
            color: #333;
        }

        .invoice-amounts {
            margin-top: 5px;
            color: #555;
        }

        .status {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 12px;
            color: white;
            display: inline-block;
        }

        .status.paid { background: #28a745; }
        .status.draft { background: #6c757d; }

        .date-info {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h3>Invoices <strong>({{ count($invoices) }})</strong></h3>
    <div id="tab-content">
        @foreach($invoices as $invoice)
            <div class="invoice-card">
                <strong>Invoice #{{ $invoice['number'] }}</strong><br>
                Amount: ${{ number_format($invoice['amount'], 2) }}<br>
                Status: {{ $invoice['status'] }}<br>
                Customer: {{ $invoice['customer'] }}<br>
                {{ $invoice['paid'] }}
                @if(isset($invoice['created']))
                    Date: {{ \Carbon\Carbon::parse($invoice['created'])->format('Y-m-d H:i:s') }}<br>
                @endif
                <a href="{{ $invoice['hosted_invoice_url'] }}" target="_blank">View Receipt</a>

            </div>
        @endforeach
    </div>
    <h3>Transactions <strong>({{ count($charges) }})</strong></h3>
    <div id="tab-content">
        @foreach($charges as $charge)
            <div class="invoice-card">
                <strong>Charge #{{ $charge['id'] }}</strong><br>
                Amount: ${{ number_format($charge['amount'], 2) }}<br>
                Status: {{ $charge['status'] }}<br>
                Customer: {{ $charge['customer'] }}<br>
                @if(isset($charge['created']))
                    Date: {{ \Carbon\Carbon::parse($charge['created'])->format('Y-m-d H:i:s') }}
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
