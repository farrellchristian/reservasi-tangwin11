<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permintaan Refund Diterima</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px;">
    <div style="max-w: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ asset('images/logo_tangwin_black.png') }}" alt="Tangwin Cut" style="height: 60px;">
        </div>

        <h2 style="color: #111827; margin-bottom: 20px;">Permintaan Pembatalan & Refund Diterima</h2>

        <p style="color: #4b5563; line-height: 1.6;">Halo <strong>{{ $reservation->customer_name }}</strong>,</p>

        <p style="color: #4b5563; line-height: 1.6;">
            Kami telah menerima permintaan pembatalan jadwal Anda dengan Nomor Reservasi <strong>#{{ str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</strong>. Jadwal Anda telah dibatalkan.
        </p>

        <div style="background-color: #f9fafb; padding: 20px; border-radius: 6px; margin: 20px 0; border: 1px solid #e5e7eb;">
            <h3 style="margin-top: 0; color: #111827; font-size: 16px;">Detail Refund Anda:</h3>
            <ul style="list-style-type: none; padding: 0; margin: 0; color: #4b5563;">
                <li style="margin-bottom: 10px;"><strong>Layanan:</strong> {{ $reservation->service->service_name }}</li>
                <li style="margin-bottom: 10px;"><strong>Tanggal Batal:</strong> {{ date('d M Y') }}</li>
                <li style="margin-bottom: 10px;"><strong>Jumlah Refund:</strong> Rp {{ number_format($refund->amount, 0, ',', '.') }}</li>
                <li style="margin-bottom: 10px;"><strong>Bank/E-Wallet:</strong> {{ $refund->bank_name }}</li>
                <li style="margin-bottom: 10px;"><strong>No Rek/HP:</strong> {{ $refund->account_number }} (a/n {{ $refund->account_name }})</li>
            </ul>
        </div>

        <p style="color: #4b5563; line-height: 1.6;">
            Admin kami akan segera memproses pengembalian dana (refund) ke rekening / nomor yang telah Anda berikan di atas. Mohon kesediaannya menunggu maksimal 1x24 jam kerja.
        </p>

        <p style="color: #4b5563; line-height: 1.6; margin-top: 30px;">
            Terima kasih,<br>
            <strong>Tangwin Cut Studio</strong>
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="text-align: center; color: #9ca3af; font-size: 12px;">
            Email ini dibuat secara otomatis. Harap tidak membalas email ini.
        </p>
    </div>
</body>

</html>