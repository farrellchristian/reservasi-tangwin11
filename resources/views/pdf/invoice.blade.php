<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $reservation->id_reservation }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Classic Receipt/Industrial Look */
            color: #2c3e50;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        
        /* Barber Pole Accent */
        .barber-pole-top {
            width: 100%;
            height: 15px;
            background: repeating-linear-gradient(
                45deg,
                #c0392b,
                #c0392b 20px,
                #ecf0f1 20px,
                #ecf0f1 40px,
                #2980b9 40px,
                #2980b9 60px,
                #ecf0f1 60px,
                #ecf0f1 80px
            );
            border-bottom: 2px solid #34495e;
        }

        .container {
            padding: 40px 50px;
        }

        /* Header */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px dashed #2c3e50;
            padding-bottom: 20px;
        }
        .brand-name {
            font-family: 'Georgia', serif;
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #2c3e50;
            margin-bottom: 5px;
            text-shadow: 1px 1px 0px #ddd;
        }
        .brand-subtitle {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #c0392b; /* Red accent */
            font-weight: bold;
            margin-bottom: 15px;
        }
        .brand-info {
            font-size: 12px;
            color: #555;
        }

        /* Invoice Details */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-title-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-meta-box {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .big-label {
            font-family: 'Georgia', serif;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            color: #2c3e50;
            border-bottom: 3px solid #c0392b;
            display: inline-block;
            margin-bottom: 10px;
        }

        /* Customer & Appt Info */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }
        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #7f8c8d;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #2c3e50;
            color: #fff;
            padding: 12px;
            text-align: left;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        .items-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tr:last-child td {
            border-bottom: 2px solid #2c3e50;
        }
        
        /* Totals */
        .total-section {
            width: 100%;
            text-align: right;
        }
        .total-label {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            margin-right: 20px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #c0392b;
            font-family: 'Georgia', serif;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .tagline {
            font-family: 'Georgia', serif;
            font-style: italic;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        /* Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            background: #2c3e50;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- Barber Pole Header -->
    <div class="barber-pole-top"></div>

    <div class="container">
        
        <!-- Brand Header -->
        <div class="header-section">
            <div class="brand-name">Tangwin Cut</div>
            <div class="brand-subtitle">Premium Gentlemen's Cuts</div>
            <div class="brand-info">
                Jl. Tlogo Poso, Palebon, Kec. Pedurungan, Kota Semarang<br>
                WhatsApp: +62 823-2870-1038
            </div>
        </div>

        <!-- Invoice Meta -->
        <div class="invoice-header">
            <div class="invoice-title-box">
                <div class="big-label">INVOICE</div>
                <div>Status: <span class="badge">LUNAS / PAID</span></div>
            </div>
            <div class="invoice-meta-box">
                <div><strong>Invoice #:</strong> INV-{{ str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Date:</strong> {{ date('d F Y', strtotime($reservation->booking_date)) }}</div>
            </div>
        </div>

        <!-- Customer & Service Info -->
        <div class="info-grid">
            <div class="info-col" style="border-right: 1px solid #eee;">
                <span class="info-label">Billed To</span>
                <div class="info-value">{{ $reservation->customer_name }}</div>
                <div>{{ $reservation->customer_phone }}</div>
                <div>{{ $reservation->customer_email ?? '' }}</div>
            </div>
            <div class="info-col">
                <span class="info-label">Appointment Details</span>
                <div class="info-value">Stylist: {{ $reservation->employee->employee_name ?? 'Any Stylist' }}</div>
                <div>{{ date('l, d F Y', strtotime($reservation->booking_date)) }}</div>
                <div>{{ date('H:i', strtotime($reservation->booking_time)) }} WIB</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%">Service Description</th>
                    <th width="20%" class="text-right">Price</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="font-size: 16px;">{{ $reservation->service->service_name }}</strong><br>
                        <span style="color: #7f8c8d; font-size: 12px; font-style: italic;">{{ $reservation->service->description }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-section">
            <span class="total-label">Grand Total</span>
            <span class="total-amount">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</span>
        </div>

        @if($reservation->notes)
        <div style="margin-top: 30px; padding: 15px; border: 1px dashed #ccc; background: #fff;">
            <strong style="text-transform: uppercase; font-size: 11px;">Notes:</strong><br>
            {{ $reservation->notes }}
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="tagline">"Stay Sharp, Look Good."</div>
            <p>Thank you for choosing Tangwin Cut Studio.</p>
        </div>

    </div>

</body>
</html>