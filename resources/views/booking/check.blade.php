<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Pesanan - Tangwin Cut</title>
    <link rel="icon" href="{{ asset('images/logo_tangwin_white.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <style>
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
    </style>
</head>

<body class="text-gray-300 h-screen w-screen flex items-center justify-center bg-[url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=2074&auto=format&fit=crop')] bg-cover bg-center">

    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm z-0"></div>

    <div class="relative z-10 w-full max-w-md bg-[#0a0a0a] border border-white/10 shadow-2xl p-8 rounded-xl animate-fade-in-up">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block mb-4 hover:opacity-80 transition">
                <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo" class="h-14 mx-auto w-auto">
            </a>
            <h2 class="text-3xl font-display text-white">Cek Pesanan</h2>
            <p class="text-gray-500 text-sm mt-2">Masukkan detail reservasi Anda.</p>
        </div>

        @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm rounded-lg p-4 mb-6 text-center">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('booking.check.process') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Order ID / No. Reservasi</label>
                <input type="text" name="order_id" value="{{ old('order_id') }}" placeholder="Contoh: INV-00123 atau 123" required class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none">
                <p class="text-[10px] text-gray-600 mt-1">Cek Order ID / No Invoice pada email Anda.</p>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">No. WhatsApp / Email</label>
                <input type="text" name="contact" value="{{ old('contact') }}" placeholder="0812... atau email@anda.com" required class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-1 focus:ring-[#C6A87C] transition outline-none">
            </div>

            <button type="submit" class="w-full py-4 bg-[#C6A87C] hover:bg-white text-black font-bold uppercase tracking-widest text-sm transition-all duration-300 shadow-[0_0_15px_rgba(198,168,124,0.2)] rounded-lg mt-4">
                Telusuri
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-white text-xs uppercase tracking-widest transition">&larr; Kembali ke Beranda</a>
        </div>
    </div>

</body>

</html>