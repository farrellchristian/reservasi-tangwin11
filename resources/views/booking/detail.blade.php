<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Tangwin Cut</title>
    <link rel="icon" href="{{ asset('images/logo_tangwin_white.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: #050505;
        }

        h1,
        h2,
        h3,
        .font-display {
            font-family: 'Italiana', serif;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background-color: rgba(234, 179, 8, 0.2);
            border: 1px solid rgba(234, 179, 8, 0.5);
            color: #eab308;
        }

        .status-approved {
            background-color: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #22c55e;
        }

        .status-canceled {
            background-color: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #ef4444;
        }

        .status-done {
            background-color: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #3b82f6;
        }

        .status-rescheduled {
            background-color: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #3b82f6;
        }
    </style>
</head>

<body class="text-gray-300 min-h-screen bg-[#050505] flex items-center justify-center py-6 px-4 md:px-0">

    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-block hover:opacity-80 transition">
                <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo" class="h-12 mx-auto w-auto">
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/50 text-green-500 text-sm rounded-lg p-4 mb-6 text-center animate-fade-in-up">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm rounded-lg p-4 mb-6 text-center animate-fade-in-up">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-[#0a0a0a] border border-white/10 shadow-2xl rounded-xl overflow-hidden animate-fade-in-up">

            <div class="p-5 md:p-8 border-b border-white/10 flex flex-col md:flex-row justify-between items-start md:items-center bg-[#111]">
                <div>
                    <h2 class="text-2xl font-display text-white">Detail Reservasi</h2>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1 hover:text-white transition">ID: #{{ str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="status-badge status-{{ strtolower($reservation->status) }}">
                        @if($reservation->status == 'pending') Menunggu Pembayaran
                        @elseif($reservation->status == 'approved') Dibayar & Disetujui
                        @elseif($reservation->status == 'canceled') Dibatalkan
                        @elseif($reservation->status == 'done') Selesai
                        @elseif($reservation->status == 'rescheduled') Dijadwalkan Ulang
                        @else {{ ucfirst($reservation->status) }} @endif
                    </span>
                </div>
            </div>

            <div class="p-5 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Informasi Pelanggan</p>
                        <p class="text-white font-bold text-base">{{ $reservation->customer_name }}</p>
                        <p class="text-gray-400 text-sm">{{ $reservation->customer_phone }}</p>
                        @if($reservation->customer_email)
                        <p class="text-gray-400 text-sm">{{ $reservation->customer_email }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Tanggal & Waktu</p>
                        <p class="text-white font-bold text-base">{{ \Carbon\Carbon::parse($reservation->booking_date)->translatedFormat('l, d F Y') }}</p>
                        <p class="text-[#C6A87C] text-base">{{ date('H:i', strtotime($reservation->booking_time)) }} WIB</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-[#111] p-5 rounded-lg border border-white/5">
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-2">Rincian Layanan</p>

                        <div class="flex justify-between items-start border-b border-white/10 pb-3 mb-3">
                            <div>
                                <p class="text-white font-bold">{{ $reservation->service->service_name ?? current(explode(' (', $reservation->service->service_name)) }}</p>
                                <p class="text-xs text-gray-500">Stylist: {{ $reservation->employee->employee_name ?? 'Siapa Saja' }}</p>
                            </div>
                            <p class="text-white">Rp {{ number_format($reservation->service->price ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <p class="text-gray-400">Total Pembayaran</p>
                            <p class="text-[#C6A87C] font-bold text-lg">Rp {{ number_format($reservation->service->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Lokasi (Cabang)</p>
                        <p class="text-white">{{ $reservation->store->store_name ?? 'Tangwin Cut Studio' }}</p>
                        <p class="text-gray-400 text-sm">{{ $reservation->store->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-5 border-t border-white/10 bg-[#050505] flex justify-between items-center flex-col sm:flex-row gap-4">
                <a href="{{ route('booking.check.form') }}" class="text-gray-500 hover:text-white text-xs uppercase tracking-widest transition">&larr; Pencarian Lain</a>

                @if($reservation->status == 'approved' && \Carbon\Carbon::parse($reservation->booking_date)->startOfDay()->gt(now()->startOfDay()))
                <!-- Tombol Ubah Jadwal -->
                <a href="{{ route('booking.reschedule', ['id_reservation' => $reservation->id_reservation]) }}"
                   class="inline-block px-6 py-3 bg-[#C6A87C]/10 hover:bg-[#C6A87C]/20 text-[#C6A87C] text-xs font-bold uppercase tracking-widest rounded transition border border-[#C6A87C]/30 shadow-[0_0_15px_rgba(198,168,124,0.1)]">
                    Ubah Jadwal
                </a>
                @elseif($reservation->status == 'approved')
                <p class="text-xs border border-yellow-500/20 bg-yellow-500/10 text-yellow-500 py-2 px-4 rounded">Jadwal sudah melewati hari ini dan tidak dapat diubah.</p>
                @endif
            </div>

        </div>

    </div>
</body>

</html>