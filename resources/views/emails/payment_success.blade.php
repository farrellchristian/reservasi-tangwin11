<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Berhasil</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .email-container { background: #ffffff; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Hi, {{ $reservation->customer_name }}! 👋</h2>
        @if($reservation->payment_type == 'cash')
            <p>Booking Anda di <strong>Tangwin Cut Studio</strong> telah terkonfirmasi. Silakan lakukan pembayaran langsung di tempat (Cash).</p>
        @else
            <p>Terima kasih telah melakukan pembayaran. Booking Anda di <strong>Tangwin Cut Studio</strong> telah terkonfirmasi.</p>
        @endif
        
        <p><strong>Detail Booking:</strong></p>
        <ul>
            <li>Layanan: {{ $reservation->service->service_name }}</li>
            <li>Kapster: {{ $reservation->employee->employee_name ?? 'Any Stylist' }}</li>
            <li>Waktu: {{ date('d M Y', strtotime($reservation->booking_date)) }} jam {{ date('H:i', strtotime($reservation->booking_time)) }}</li>
        </ul>

        <p>Invoice bukti pembayaran telah kami lampirkan dalam email ini (PDF).</p>
        
        <p>Sampai jumpa di studio!</p>
        <br>
        <p><small>Salam Well,<br>Tangwin Cut Team</small></p>
    </div>
</body>
</html>