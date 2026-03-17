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

        .status-refund_requested {
            background-color: rgba(168, 85, 247, 0.2);
            border: 1px solid rgba(168, 85, 247, 0.5);
            color: #a855f7;
        }
    </style>
</head>

<body class="text-gray-300 min-h-screen bg-[#050505] py-12 px-4 md:px-0">

    <div class="max-w-3xl mx-auto" x-data="{ showRefundModal: false }">
        <div class="text-center mb-10">
            <a href="{{ route('home') }}" class="inline-block hover:opacity-80 transition">
                <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo" class="h-16 mx-auto w-auto">
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

            <div class="p-6 md:p-10 border-b border-white/10 flex flex-col md:flex-row justify-between items-start md:items-center bg-[#111]">
                <div>
                    <h2 class="text-3xl font-display text-white">Detail Reservasi</h2>
                    <p class="text-xs text-gray-500 uppercase tracking-widest mt-2 hover:text-white transition">ID: #{{ str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="status-badge status-{{ strtolower($reservation->status) }}">
                        @if($reservation->status == 'pending') Menunggu Pembayaran
                        @elseif($reservation->status == 'approved') Dibayar & Disetujui
                        @elseif($reservation->status == 'canceled') Dibatalkan
                        @elseif($reservation->status == 'done') Selesai
                        @elseif($reservation->status == 'refund_requested') Refund Diproses
                        @else {{ ucfirst($reservation->status) }} @endif
                    </span>
                </div>
            </div>

            <div class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="space-y-6">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Informasi Pelanggan</p>
                        <p class="text-white font-bold text-lg">{{ $reservation->customer_name }}</p>
                        <p class="text-gray-400 text-sm">{{ $reservation->customer_phone }}</p>
                        @if($reservation->customer_email)
                        <p class="text-gray-400 text-sm">{{ $reservation->customer_email }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Tanggal & Waktu</p>
                        <p class="text-white font-bold text-lg">{{ \Carbon\Carbon::parse($reservation->booking_date)->translatedFormat('l, d F Y') }}</p>
                        <p class="text-[#C6A87C] text-lg">{{ date('H:i', strtotime($reservation->booking_time)) }} WIB</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-[#111] p-5 rounded-lg border border-white/5">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">Rincian Layanan</p>

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
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Lokasi (Cabang)</p>
                        <p class="text-white">{{ $reservation->store->store_name ?? 'Tangwin Cut Studio' }}</p>
                        <p class="text-gray-400 text-sm">{{ $reservation->store->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-white/10 bg-[#050505] flex justify-between items-center flex-col sm:flex-row gap-4">
                <a href="{{ route('booking.check.form') }}" class="text-gray-500 hover:text-white text-xs uppercase tracking-widest transition">&larr; Pencarian Lain</a>

                @if($reservation->status == 'approved' && \Carbon\Carbon::parse($reservation->booking_date)->startOfDay()->gt(now()->startOfDay()))
                <!-- Tombol Batal & Refund -->
                <button @click="showRefundModal = true" class="px-6 py-3 bg-red-900/50 hover:bg-red-900/80 text-red-200 text-xs font-bold uppercase tracking-widest rounded transition border border-red-500/20 shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                    Batalkan & Ajukan Refund
                </button>
                <p class="text-[10px] text-gray-600 sm:hidden w-full text-center mt-2">Maksimal pembatalan H-1.</p>
                @elseif($reservation->status == 'approved')
                <p class="text-xs border border-yellow-500/20 bg-yellow-500/10 text-yellow-500 py-2 px-4 rounded">Batas waktu pembatalan telah usai (Maks H-1).</p>
                @endif
            </div>

        </div>

        <!-- Modal Refund -->
        <div x-show="showRefundModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4" x-transition.opacity>
            <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="showRefundModal = false"></div>

            <div class="relative w-full max-w-lg bg-[#0a0a0a] border border-[#C6A87C]/30 rounded-xl shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]" x-transition>

                <div class="p-6 border-b border-white/10 flex justify-between items-center bg-[#111] flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-display text-white">Pengajuan Refund</h3>
                        <p class="text-xs text-gray-500 mt-1">Isi data rekening tujuan refund</p>
                    </div>
                    <button @click="showRefundModal = false" class="text-gray-500 hover:text-white text-2xl">&times;</button>
                </div>

                <form action="{{ route('booking.check.cancel') }}" method="POST" class="flex-1 overflow-y-auto custom-scroll p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="id_reservation" value="{{ $reservation->id_reservation }}">

                    <div class="bg-red-500/10 border border-red-500/20 p-4 rounded text-sm text-red-300 mb-4">
                        <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan. Jadwal Anda akan segera dihapus dan dana akan dikembalikan penuh (100%).
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Nama Bank / E-Wallet</label>
                        <input type="text" name="bank_name" required placeholder="Contoh: BCA / GoPay" class="w-full bg-[#111] border border-white/10 rounded-lg p-3 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Nomor Rekening / No. HP</label>
                        <input type="text" name="account_number" required placeholder="Masukkan nomor" class="w-full bg-[#111] border border-white/10 rounded-lg p-3 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Atas Nama (Sesuai Rekening)</label>
                        <input type="text" name="account_name" required placeholder="Nama pemilik rekening" class="w-full bg-[#111] border border-white/10 rounded-lg p-3 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none text-sm">
                    </div>

                    <div>
                        <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Alasan Pembatalan</label>
                        <textarea name="cancel_reason" required rows="2" placeholder="Tulis alasan singkat..." class="w-full bg-[#111] border border-white/10 rounded-lg p-3 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none text-xs"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-white/10 mt-6 pb-2">
                        <button type="button" @click="showRefundModal = false" class="px-5 py-2.5 text-sm text-gray-400 hover:text-white transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-red-800 hover:bg-red-700 text-white font-bold uppercase tracking-widest text-xs rounded transition shadow-lg shadow-red-900/50">Konfirmasi Pembatalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>