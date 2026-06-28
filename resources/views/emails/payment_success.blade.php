<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Berhasil</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .email-container { background: #ffffff; padding: 40px; border-radius: 8px; max-width: 600px; margin: 0 auto; border-top: 4px solid #C6A87C; }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo img { max-height: 80px; width: auto; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 14px; margin: 16px 0; }
        .detail-table td { padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-label { color: #666; width: 30%; }
        .detail-value { font-weight: bold; color: #111; }
    </style>
</head>
<body>
    <div class="email-container">
        
        <h2 style="color: #111; margin-bottom: 8px; font-size: 22px;">Hi, {{ $reservation->customer_name }}! 👋</h2>
        @if($reservation->payment_type == 'cash')
            <p>Booking Anda di <strong>Tangwin Cut Studio</strong> telah terkonfirmasi. Silakan lakukan pembayaran langsung di tempat (Cash).</p>
        @else
            <p>Terima kasih telah melakukan pembayaran. Booking Anda di <strong>Tangwin Cut Studio</strong> telah terkonfirmasi.</p>
        @endif
        
        <p><strong>Detail Booking:</strong></p>
        <div style="background: #f9f9f9; padding: 16px 20px; border-radius: 6px; border: 1px solid #eaeaea; margin-bottom: 20px;">
            <table class="detail-table">
                <tr>
                    <td class="detail-label">Layanan</td>
                    <td class="detail-value">{{ $reservation->service->service_name }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Kapster</td>
                    <td class="detail-value">{{ $reservation->employee->employee_name ?? 'Any Stylist' }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Waktu</td>
                    <td class="detail-value">{{ date('d M Y', strtotime($reservation->booking_date)) }} jam {{ date('H:i', strtotime($reservation->booking_time)) }}</td>
                </tr>
            </table>
        </div>

        <p>Invoice bukti pembayaran telah kami lampirkan dalam email ini (PDF).</p>
        
        <p>Sampai jumpa di studio!</p>
        <br>
        <p><small>Salam Well,<br>Tangwin Cut Team</small></p>
    </div>
</body>
</html>