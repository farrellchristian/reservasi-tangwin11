<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Diubah - Tangwin Cut</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0; }
        .email-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            border-top: 4px solid #C6A87C;
        }
        .logo { text-align: center; margin-bottom: 28px; }
        .logo-text {
            font-size: 22px;
            letter-spacing: 6px;
            font-weight: 300;
            color: #111;
            text-transform: uppercase;
        }
        .logo-sub { font-size: 10px; letter-spacing: 4px; color: #888; text-transform: uppercase; margin-top: 2px; }
        h2 { color: #111827; margin-bottom: 8px; font-size: 22px; }
        .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 28px; }
        .notice-box {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 14px 18px;
            font-size: 13px;
            color: #92400e;
            margin-bottom: 24px;
        }
        .detail-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }
        .detail-box h3 { margin-top: 0; color: #111827; font-size: 13px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 16px; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .detail-table td { padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-table tr:last-child td { border-bottom: none; }
        .detail-label { color: #6b7280; width: 40%; }
        .detail-value { color: #111827; font-weight: 600; text-align: right; }
        .detail-value.gold { color: #b45309; }
        .old-schedule { text-decoration: line-through; color: #9ca3af; font-weight: normal; }
        .footer { text-align: center; margin-top: 32px; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">
            <img src="{{ asset('images/logo_struk.png') }}" alt="Tangwin Cut" style="max-height: 80px; width: auto;">
        </div>

        <h2>Jadwal Anda Telah Diubah 📅</h2>
        <p class="subtitle">Halo, <strong>{{ $newReservation->customer_name }}</strong>! Permintaan reschedule Anda telah berhasil diproses.</p>

        <div class="notice-box">
            ⚠️ <strong>Perhatian:</strong> Booking lama ({{ $oldReservation->booking_number ?? '#'.str_pad($oldReservation->id_reservation, 5, '0', STR_PAD_LEFT) }}) telah dibatalkan dan digantikan dengan booking baru di bawah ini.
        </div>

        <div class="detail-box">
            <h3>Detail Reservasi Baru</h3>
            <table class="detail-table">
                <tr>
                    <td class="detail-label">Nomor Booking Baru</td>
                    <td class="detail-value">{{ $newReservation->booking_number ?? '#'.str_pad($newReservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Layanan</td>
                    <td class="detail-value">{{ $newReservation->service->service_name }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Stylist</td>
                    <td class="detail-value">{{ $newReservation->employee->employee_name ?? 'Any Stylist' }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Jadwal Lama</td>
                    <td class="detail-value old-schedule">
                        {{ date('d M Y', strtotime($oldReservation->booking_date)) }},
                        {{ date('H:i', strtotime($oldReservation->booking_time)) }} WIB
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">Jadwal Baru</td>
                    <td class="detail-value gold">
                        {{ date('d M Y', strtotime($newReservation->booking_date)) }},
                        {{ date('H:i', strtotime($newReservation->booking_time)) }} WIB
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">Lokasi</td>
                    <td class="detail-value">{{ $newReservation->store->store_name ?? 'Tangwin Cut Studio' }}</td>
                </tr>
                <tr>
                    <td class="detail-label">Total Pembayaran</td>
                    <td class="detail-value gold">Rp {{ number_format($newReservation->service->price ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #4b5563; line-height: 1.6;">
            Kami telah mencatat perubahan jadwal Anda. Tidak diperlukan pembayaran ulang.
            Sampai jumpa di studio!
        </p>

        <div class="footer">
            <p>Salam,<br><strong>Tangwin Cut Team</strong></p>
            <p style="margin-top: 8px;">Email ini dikirim otomatis, mohon jangan balas email ini.</p>
        </div>
    </div>
</body>
</html>
