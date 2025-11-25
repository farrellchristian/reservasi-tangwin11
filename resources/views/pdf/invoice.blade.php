<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $reservation->id_reservation }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header yang Rapi */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #000;
        }
        .brand-info {
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            text-align: right;
            color: #000;
        }
        .invoice-meta {
            text-align: right;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Informasi Customer */
        .info-section {
            width: 100%;
            margin-bottom: 40px;
        }
        .info-label {
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 5px;
            display: block;
            width: 90%;
        }

        /* Tabel Item */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f4f4f4;
            color: #000;
            padding: 12px;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
            border-bottom: 2px solid #000;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Total Amount */
        .total-row td {
            border-top: 2px solid #000;
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
            padding-top: 15px;
            color: #000;
        }

        /* Status Stamp */
        .paid-stamp {
            border: 2px solid #000;
            color: #000;
            padding: 5px 15px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin-top: 10px;
        }

        /* Footer */
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <table class="header-table">
            <tr>
                <td width="60%" valign="top">
                    <div class="brand-name">TANGWIN CUT</div>
                    <div class="brand-info">
                        Jl. Tlogo Poso, Palebon, Kec. Pedurungan<br>
                        Kota Semarang, Jawa Tengah<br>
                        WhatsApp: +62 823-2870-1038
                    </div>
                </td>
                <td width="40%" valign="top" style="text-align: right;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        Invoice #: <strong>INV-{{ str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</strong><br>
                        Date: {{ date('d F Y', strtotime($reservation->booking_date)) }}
                    </div>
                    <div class="paid-stamp">LUNAS / PAID</div>
                </td>
            </tr>
        </table>

        <table class="info-section">
            <tr>
                <td width="50%" valign="top">
                    <span class="info-label">BILLED TO</span>
                    <strong>{{ $reservation->customer_name }}</strong><br>
                    {{ $reservation->customer_phone }}<br>
                    {{ $reservation->customer_email ?? '-' }}
                </td>
                <td width="50%" valign="top">
                    <span class="info-label">APPOINTMENT DETAILS</span>
                    Stylist: <strong>{{ $reservation->employee->employee_name ?? 'Any Stylist' }}</strong><br>
                    Date: {{ date('l, d F Y', strtotime($reservation->booking_date)) }}<br>
                    Time: {{ date('H:i', strtotime($reservation->booking_time)) }} WIB
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%">Description / Service</th>
                    <th width="20%" class="text-right">Price</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $reservation->service->service_name }}</strong><br>
                        <span style="color: #666; font-size: 12px;">{{ $reservation->service->description }}</span>
                    </td>
                    <td class="text-right">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</td>
                </tr>
                
                <tr class="total-row">
                    <td colspan="3" class="text-right">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($reservation->service->price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if($reservation->notes)
        <div style="margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-left: 3px solid #000;">
            <strong>Notes:</strong><br>
            {{ $reservation->notes }}
        </div>
        @endif

        <div class="footer">
            <p>Thank you for trusting <strong>Tangwin Cut Studio</strong>.</p>
            <p>This is a computer-generated document. No signature is required.</p>
        </div>

    </div>

</body>
</html>