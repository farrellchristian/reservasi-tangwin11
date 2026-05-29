<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Tangwin Cut</title>
    <link rel="icon" href="{{ asset('images/logo_tangwin_white.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Midtrans Snap.js -->
    <script
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

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

        .countdown-glow {
            text-shadow: 0 0 15px rgba(198, 168, 124, 0.4);
        }

        /* Custom Toast Notification */
        .toast-enter {
            animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .toast-leave {
            animation: toastSlideOut 0.3s ease-in forwards;
        }

        @keyframes toastSlideIn {
            from {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes toastSlideOut {
            from {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            to {
                transform: translateY(-20px) scale(0.95);
                opacity: 0;
            }
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #111;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 2px;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }

        /* ============================================
           ANIMASI KONFIRMASI BOOKING - WOW EFFECT
           ============================================ */

        /* Particle rain */
        @keyframes particleFall {
            0%   { transform: translateY(-10px) rotate(0deg);   opacity: 1; }
            100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }
        .confirm-particle {
            position: fixed;
            top: -10px;
            border-radius: 50%;
            pointer-events: none;
            animation: particleFall linear forwards;
        }

        /* Overlay entrance */
        @keyframes overlayIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .confirm-overlay {
            animation: overlayIn 0.5s ease forwards;
        }

        /* Card pop */
        @keyframes cardPop {
            0%   { transform: scale(0.7) translateY(40px); opacity: 0; }
            70%  { transform: scale(1.04) translateY(-6px); }
            100% { transform: scale(1)   translateY(0);    opacity: 1; }
        }
        .confirm-card {
            animation: cardPop 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }

        /* Pulsing gold ring */
        @keyframes ringPulse {
            0%   { transform: scale(1);   opacity: 0.6; }
            100% { transform: scale(2.2); opacity: 0;   }
        }
        .confirm-ring {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 2px solid #C6A87C;
            animation: ringPulse 1.8s ease-out infinite;
        }
        .confirm-ring-2 {
            animation-delay: 0.6s;
        }
        .confirm-ring-3 {
            animation-delay: 1.2s;
        }

        /* SVG checkmark */
        .confirm-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: strokeDraw 0.7s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
        }
        .confirm-check {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: strokeDraw 0.4s cubic-bezier(0.65, 0, 0.45, 1) 1.3s forwards;
        }
        @keyframes strokeDraw {
            100% { stroke-dashoffset: 0; }
        }

        /* Checkmark fill glow */
        @keyframes checkFill {
            0%   { box-shadow: inset 0 0 0 0px #C6A87C; }
            100% { box-shadow: inset 0 0 0 55px #C6A87C; stroke: #050505; }
        }
        .confirm-svg {
            animation: checkFill 0.5s ease-in-out 1.4s forwards,
                       checkBounce 0.4s ease-in-out 1.8s both;
        }
        @keyframes checkBounce {
            0%,100% { transform: scale(1); }
            50%      { transform: scale(1.12); }
        }

        /* Text shimmer */
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        .confirm-title {
            background: linear-gradient(90deg,
                #C6A87C 0%, #f0d9a8 40%, #C6A87C 60%, #9a7a52 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s linear infinite, fadeSlideUp 0.6s ease 1.6s both;
        }

        /* Staggered detail rows */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .confirm-row-1 { animation: fadeSlideUp 0.5s ease 2.0s both; }
        .confirm-row-2 { animation: fadeSlideUp 0.5s ease 2.2s both; }
        .confirm-row-3 { animation: fadeSlideUp 0.5s ease 2.4s both; }
        .confirm-row-4 { animation: fadeSlideUp 0.5s ease 2.6s both; }
        .confirm-row-5 { animation: fadeSlideUp 0.5s ease 2.8s both; }
        .confirm-cta   { animation: fadeSlideUp 0.5s ease 3.0s both; }

        /* Glow line under title */
        @keyframes lineExpand {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
        .confirm-line {
            transform-origin: center;
            animation: lineExpand 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) 1.9s both;
        }

        /* Countdown bar */
        @keyframes countdownBar {
            from { width: 100%; }
            to   { width: 0%; }
        }
        .confirm-bar {
            animation: countdownBar 4.5s linear 3.2s both;
        }
    </style>
</head>

<body
    class="text-gray-300 min-h-screen w-screen flex items-center justify-center bg-[url('https://images.unsplash.com/photo-1634480496840-b3654a5253e8?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center">

    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm z-0"></div>

    <div class="relative z-10 w-full max-w-5xl h-screen md:h-[85vh] bg-[#0a0a0a] border border-white/10 shadow-2xl flex flex-col md:flex-row md:overflow-hidden animate-fade-in-up"
        x-data="bookingWizard()">

        <div
            class="w-full md:w-1/3 bg-[#111] border-b md:border-b-0 md:border-r border-white/5 p-4 md:p-8 flex flex-col md:justify-between relative flex-shrink-0">
            <div
                class="absolute top-0 left-0 w-full h-full opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] pointer-events-none">
            </div>

            <!-- Logo -->
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="block mb-4 md:mb-8">
                    <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo"
                        class="h-12 md:h-16 w-auto">
                </a>
            </div>

            <!-- Steps list: hanya tampil di desktop -->
            <div class="relative z-10 flex-1 hidden md:flex md:flex-col md:justify-center">
                <div class="space-y-5">
                    <template x-for="(label, index) in steps" :key="index">
                        <div class="flex items-center space-x-4 group transition-all duration-300"
                            :class="currentStep > index + 1 ? 'text-[#C6A87C]' : (currentStep === index + 1 ? 'text-white' : 'text-gray-600')">
                            <div class="w-8 h-8 rounded-full border flex items-center justify-center text-xs font-bold transition-all duration-300"
                                :class="currentStep > index + 1 ? 'bg-[#C6A87C] border-[#C6A87C] text-black' : (currentStep === index + 1 ? 'border-white text-white' : 'border-gray-700 text-gray-700')">
                                <span x-text="index + 1"></span>
                            </div>
                            <span class="uppercase tracking-widest text-xs font-bold" x-text="label"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Your Selection: hanya tampil di desktop (di bawah step list) -->
            <div class="relative z-10 mt-4 pt-4 border-t border-white/5 flex-shrink-0 hidden md:block">
                <h4 class="text-[#C6A87C] text-xs uppercase tracking-widest mb-3">Your Selection</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Store</span>
                        <span class="text-white text-right truncate w-32"
                            x-text="selectedStore ? selectedStore.name : '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Service</span>
                        <span class="text-white text-right truncate w-32"
                            x-text="selectedService ? selectedService.name : '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stylist</span>
                        <span class="text-white" x-text="selectedCapster ? selectedCapster.name : '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date</span>
                        <span class="text-white" x-text="date ? formatDate(date) : '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total</span>
                        <span class="text-[#C6A87C] font-bold"
                            x-text="selectedService ? formatRupiah(selectedService.price) : '-'"></span>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="w-full md:w-2/3 bg-[#0a0a0a] relative flex flex-col min-h-0 md:h-full md:overflow-hidden flex-shrink-0">

            <div
                class="md:hidden p-4 px-6 border-b border-white/10 flex justify-between items-center flex-shrink-0 transition-all">
                <span class="text-[#C6A87C] text-xs font-bold uppercase">Step <span
                        x-text="currentStep"></span>/5</span>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-white">&times; Close</a>
            </div>

            <!-- Your Selection: hanya mobile, di bawah step bar, di atas konten -->
            <div class="md:hidden px-6 py-3 border-b border-white/5 bg-[#111] flex-shrink-0">
                <h4 class="text-[#C6A87C] text-[10px] uppercase tracking-widest mb-2">Your Selection</h4>
                <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Store</span>
                        <span class="text-white font-medium" x-text="selectedStore ? selectedStore.name : '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Service</span>
                        <span class="text-white font-medium" x-text="selectedService ? selectedService.name : '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Stylist</span>
                        <span class="text-white font-medium" x-text="selectedCapster ? selectedCapster.name : '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Date</span>
                        <span class="text-white font-medium" x-text="date ? formatDate(date) : '—'"></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-gray-600">Total</span>
                        <span class="text-[#C6A87C] font-bold" x-text="selectedService ? formatRupiah(selectedService.price) : '—'"></span>
                    </div>
                </div>
            </div>

            <div class="flex-1 p-6 md:p-12 overflow-y-auto custom-scroll relative min-h-0 pb-32 md:pb-6">

                <!-- Step 1: STORE -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Choose Store</h2>
                    <p class="text-gray-500 mb-8">Pilih lokasi cabang kami yang terdekat dari Anda.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4">
                        @forelse($stores as $store)
                            <div @click="selectStore({{ $store->id_store }}, '{{ addslashes($store->store_name) }}')"
                                class="group relative p-6 border rounded-lg cursor-pointer transition-all duration-300 hover:bg-white/5"
                                :class="selectedStore && selectedStore.id == {{ $store->id_store }} ? 'border-[#C6A87C] bg-white/5 ring-1 ring-[#C6A87C]' : 'border-white/10'">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3
                                            class="text-xl font-display text-white group-hover:text-[#C6A87C] transition-colors gap-2 flex items-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $store->store_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-2 flex items-start gap-2 max-w-[200px]">
                                            <span>{{ $store->address ?? 'Alamat belum tersedia' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div x-show="selectedStore && selectedStore.id == {{ $store->id_store }}"
                                    class="absolute -top-3 -right-3 bg-[#0a0a0a] rounded-full p-1 text-[#C6A87C]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full flex flex-col items-center justify-center p-12 border border-dashed border-white/10 rounded-lg text-center bg-[#111] mt-2">
                                <div
                                    class="w-16 h-16 rounded-full bg-[#1a1a1a] border border-white/5 flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-[#C6A87C]/70" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="1.2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl text-white font-display tracking-wide mb-2">No Stores Available</h3>
                                <p class="text-gray-500 text-sm max-w-md">Saat ini belum ada cabang yang dapat melayani
                                    reservasi. Silakan kembali lagi nanti atau hubungi kami untuk informasi lebih lanjut.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Step 2: SERVICE -->
                <div x-show="currentStep === 2" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Choose Service</h2>
                    <p class="text-gray-500 mb-8">Pilih perawatan yang Anda butuhkan.</p>

                    <div class="grid grid-cols-1 gap-4 pb-4">
                        <!-- Filtering service based on store ID using Alpine template iteration or blade forelse depending on structure, mixed here -->
                        @foreach($services as $service)
                            <div x-show="selectedStore && selectedStore.id == {{ $service->id_store }}"
                                @click="selectService({{ $service->id_service }}, '{{ addslashes($service->service_name) }}', {{ $service->price }})"
                                class="group relative p-6 border rounded-lg cursor-pointer transition-all duration-300 hover:bg-white/5"
                                :class="selectedService && selectedService.id == {{ $service->id_service }} ? 'border-[#C6A87C] bg-white/5' : 'border-white/10'">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3
                                            class="text-xl font-display text-white group-hover:text-[#C6A87C] transition-colors">
                                            {{ $service->service_name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $service->description }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-white">Rp
                                        {{ number_format($service->price, 0, ',', '.') }}</span>
                                </div>
                                <div x-show="selectedService && selectedService.id == {{ $service->id_service }}"
                                    class="absolute -top-3 -right-3 bg-[#0a0a0a] rounded-full p-1 text-[#C6A87C]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                        <!-- Peringatan jika belum ada Service di toko tsb -->
                        <div x-show="!hasServicesForSelectedStore"
                            class="flex flex-col items-center justify-center p-8 border border-white/10 rounded-lg text-center bg-[#111] mt-2">
                            <div
                                class="w-12 h-12 rounded-full bg-[#1a1a1a] border border-white/5 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg text-white font-display mb-1">Layanan Belum Tersedia</h3>
                            <p class="text-gray-500 text-sm max-w-sm">Belum ada layanan di cabang yang Anda pilih.
                                Silakan tekan tombol &larr; Back dan pilih cabang lain.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3: STYLIST -->
                <div x-show="currentStep === 3" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Select Stylist</h2>
                    <p class="text-gray-500 mb-8">Siapa yang akan menangani gaya Anda hari ini?</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-4">
                        <div x-cloak x-show="hasMultipleCapstersForSelectedStore"
                            @click="selectCapster(null, 'Siapa Saja (Available)')"
                            class="cursor-pointer group text-center">
                            <div class="relative h-40 w-full border border-white/10 rounded-lg mb-2 flex items-center justify-center bg-[#111] transition-all group-hover:border-[#C6A87C]"
                                :class="selectedCapster == null && selectedCapsterName == 'Siapa Saja (Available)' ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                <svg class="w-12 h-12 text-gray-600 group-hover:text-[#C6A87C] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <path d="M16 8h.01"/>
                                    <path d="M8 8h.01"/>
                                    <path d="M8 16h.01"/>
                                    <path d="M16 16h.01"/>
                                    <path d="M12 12h.01"/>
                                </svg>
                            </div>
                            <h3 class="text-white font-bold text-sm truncate">Bebas / Siapa Saja</h3>
                        </div>
                        @foreach($capsters as $capster)
                            <div x-show="selectedStore && selectedStore.id == {{ $capster->id_store }}"
                                @click="selectCapster({{ $capster->id_employee }}, '{{ addslashes($capster->employee_name) }}')"
                                class="cursor-pointer group text-center">
                                <div class="relative h-40 w-full overflow-hidden rounded-lg mb-2 border border-transparent transition-all group-hover:border-[#C6A87C]"
                                    :class="selectedCapster && selectedCapster.id == {{ $capster->id_employee }} ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                    <img src="{{ $capster->photo ? 'data:image/jpeg;base64,' . $capster->photo : ($capster->photo_path ? asset('storage/' . $capster->photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($capster->employee_name) . '&background=1a1a1a&color=fff') }}"
                                        class="w-full h-full object-cover object-top grayscale group-hover:grayscale-0 transition-all duration-500">
                                </div>
                                <h3 class="text-white font-bold text-sm truncate">{{ $capster->employee_name }}</h3>
                            </div>
                        @endforeach
                        <!-- Peringatan jika belum ada Capster di toko tsb -->
                        <div x-cloak x-show="!hasCapstersForSelectedStore"
                            class="col-span-full flex flex-col items-center justify-center p-8 border border-white/10 rounded-lg text-center bg-[#111] mt-2">
                            <div
                                class="w-12 h-12 rounded-full bg-[#1a1a1a] border border-white/5 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <h3 class="text-lg text-white font-display mb-1">Stylist Belum Tersedia</h3>
                            <p class="text-gray-500 text-sm max-w-sm">Belum ada stylist di cabang yang Anda pilih.
                                Silakan tekan tombol &larr; Back dan pilih cabang lain.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: DATE & TIME -->
                <div x-show="currentStep === 4" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Date and Time</h2>
                    <p class="text-gray-500 mb-8">Pilih waktu yang sesuai untuk Anda.</p>

                    <div class="space-y-6 pb-4">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Select
                                Date</label>
                            <input type="date" x-model="date"
                                class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none"
                                min="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Available
                                Slots</label>
                            <div x-show="isLoadingSlots" class="text-sm text-[#C6A87C] animate-pulse mb-2">Mencari
                                jadwal tersedia...</div>
                            <div class="grid grid-cols-3 md:grid-cols-4 gap-3" x-show="date && !isLoadingSlots">
                                <template x-for="slot in availableSlots" :key="slot.id_slot">
                                    <button @click="if(!slot.is_past && !slot.is_full) timeSlot = slot.formatted_time"
                                        type="button"
                                        class="py-3 text-sm border rounded transition-all duration-200 relative overflow-hidden"
                                        :class="(slot.is_past || slot.is_full) ? 'border-white/5 text-gray-600 cursor-not-allowed bg-[#111] opacity-50' : (timeSlot === slot.formatted_time ? 'bg-[#C6A87C] text-black border-[#C6A87C] font-bold' : 'border-white/10 text-gray-400 hover:border-white hover:text-white')"
                                        :disabled="slot.is_past || slot.is_full">
                                        <span x-text="slot.formatted_time"></span>
                                        <!-- Diagonal cross-out line -->
                                        <svg x-show="slot.is_past || slot.is_full"
                                            class="absolute inset-0 w-full h-full text-gray-600/40"
                                            preserveAspectRatio="none" viewBox="0 0 100 100">
                                            <line x1="0" y1="100" x2="100" y2="0" stroke="currentColor"
                                                stroke-width="2" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p x-show="date && !isLoadingSlots && availableSlots.length === 0"
                                class="text-sm text-red-400 italic mt-2">Maaf, tidak ada jadwal tersedia.</p>
                            <p x-show="!date" class="text-sm text-gray-600 italic">Silakan pilih tanggal terlebih
                                dahulu.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 5: DETAILS -->
                <div x-show="currentStep === 5" x-cloak x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Confirmation</h2>
                    <p class="text-gray-500 mb-8">Lengkapi data diri Anda.</p>

                    <form class="space-y-6 pb-4">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Nama
                                (Wajib)</label>
                            <input type="text" x-model="customerName" maxlength="50" placeholder="Masukan Nama Anda"
                                class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Nomor Telepon /
                                WhatsApp (Wajib)</label>
                            <input type="tel" x-model="customerPhone" @input="customerPhone = customerPhone.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '')" maxlength="15" placeholder="Contoh: 0812..."
                                class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none">
                            <p class="text-[10px] text-gray-500 mt-1">*Nomor ini akan digunakan untuk menghubungi Anda.
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Email Address
                                (Wajib)</label>
                            <div class="relative">
                                <input type="email" x-model="customerEmail" placeholder="contoh@gmail.com"
                                    class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none pr-12">
                                
                                <!-- Checking Spinner -->
                                <div x-show="emailStatus === 'checking'" x-cloak class="absolute right-4 top-4 transition-opacity duration-300">
                                    <div class="w-5 h-5 border-2 border-[#C6A87C] border-t-transparent rounded-full animate-spin"></div>
                                </div>
                                
                                <!-- Valid Green Check -->
                                <div x-show="emailStatus === 'valid'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="absolute right-4 top-4 text-green-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                
                                <!-- Invalid Red Cross -->
                                <div x-show="emailStatus === 'invalid'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100" class="absolute right-4 top-4 text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1" x-show="!isDisposableEmail">*Kami akan mengirimkan detail reservasi ke email ini.</p>
                            <p class="text-[10px] text-red-500 mt-1" x-show="isDisposableEmail" x-cloak>*Email sementara tidak diizinkan demi keamanan.</p>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Catatan
                                (Opsional)</label>
                            <textarea x-model="notes" rows="3" maxlength="255" placeholder="Ada permintaan khusus?"
                                class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none"></textarea>
                        </div>

                    </form>
                </div>
            </div>

            <div
                class="p-4 md:p-8 border-t border-white/10 bg-[#0a0a0a] flex justify-between items-center flex-shrink-0 fixed bottom-0 left-0 right-0 z-20 md:static md:relative">
                <button @click="prevStep()" x-show="currentStep > 1"
                    class="text-gray-500 hover:text-white text-sm uppercase tracking-widest transition font-bold">&larr;
                    Back</button>
                <div x-show="currentStep === 1"></div>
                <button @click="nextStep()"
                    class="px-8 py-3 bg-[#C6A87C] hover:bg-white text-black font-bold uppercase tracking-widest text-sm transition-all duration-300 shadow-[0_0_15px_rgba(198,168,124,0.3)] disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!canProceed() || isProcessingPayment">
                    <span x-text="currentStep === 5 ? 'Confirm Booking' : 'Next Step &rarr;'"></span>
                </button>
            </div>
        </div>

        <div x-show="isProcessingPayment"
            class="absolute inset-0 z-50 bg-black/90 flex flex-col items-center justify-center" x-transition>
            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-[#C6A87C] mb-4"></div>
            <p class="text-[#C6A87C] text-lg font-display tracking-widest animate-pulse">Processing Payment...</p>
        </div>

        <!-- ... payment modal part ... -->
        <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4"
            x-transition>
            <div class="absolute inset-0 bg-black/90"
                @click="if(!showSuccessAnimation && !isProcessingPayment) showPaymentModal = false"></div>
            <div class="relative w-full max-w-2xl bg-[#0a0a0a] border border-[#C6A87C]/30 rounded-xl shadow-2xl overflow-hidden transform transition-all"
                x-transition>
                <div x-show="!showSuccessAnimation" class="flex flex-col max-h-[92vh]">
                    <div
                        class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center bg-[#111] flex-shrink-0">
                        <div>
                            <h3 class="text-xl md:text-2xl font-display text-white"
                                x-text="paymentResult ? 'Complete Payment' : 'Select Payment'"></h3>
                            <p class="text-[10px] md:text-xs text-gray-500 uppercase tracking-widest mt-1"
                                x-text="paymentResult ? 'Selesaikan pembayaran Anda' : 'Pilih metode pembayaran'"></p>
                        </div>
                        <button @click="showPaymentModal = false" :disabled="isProcessingPayment"
                            class="text-gray-500 hover:text-white text-xl md:text-2xl disabled:opacity-30">&times;</button>
                    </div>

                    <div class="overflow-y-auto custom-scroll flex-1">
                        <div class="p-4 md:p-8 grid grid-cols-2 gap-2 md:gap-6" x-show="!paymentResult">
                            <div @click="paymentMethod = 'qris'"
                                class="cursor-pointer group relative p-3 md:p-6 border rounded-xl transition-all duration-300 flex flex-col items-center text-center hover:bg-white/5"
                                :class="paymentMethod === 'qris' ? 'border-[#C6A87C] bg-[#C6A87C]/5 ring-1 ring-[#C6A87C]' : 'border-white/10'">
                                <div
                                    class="absolute top-2 right-2 px-1.5 py-0.5 bg-[#C6A87C] text-black text-[8px] md:text-[10px] font-bold uppercase tracking-wider rounded">
                                    Instant</div>
                                <div
                                    class="w-10 h-10 md:w-16 md:h-16 mb-2 md:mb-4 bg-white rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 md:w-10 md:h-10 text-black" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                </div>
                                <h4 class="text-sm md:text-lg font-bold text-white mb-0.5 md:mb-1">QRIS</h4>
                                <p class="text-[9px] md:text-xs text-gray-500">Scan via All Apps</p>
                            </div>


                            <div @click="paymentMethod = 'cash'"
                                class="cursor-pointer group relative p-3 md:p-6 border rounded-xl transition-all duration-300 flex flex-col items-center text-center hover:bg-white/5"
                                :class="paymentMethod === 'cash' ? 'border-[#C6A87C] bg-[#C6A87C]/5 ring-1 ring-[#C6A87C]' : 'border-white/10'">
                                <div
                                    class="w-10 h-10 md:w-16 md:h-16 mb-2 md:mb-4 bg-[#222] border border-white/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 md:w-8 md:h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                        </path>
                                    </svg>
                                </div>
                                <h4 class="text-sm md:text-lg font-bold text-white mb-0.5 md:mb-1">Cash</h4>
                                <p class="text-[9px] md:text-xs text-gray-500">Bayar di Tempat</p>
                            </div>
                        </div>

                        <div class="p-4 md:p-8 text-center" x-show="paymentResult">
                            <!-- UI for QRIS and Bank Transfer has been removed because Midtrans Snap handles this directly via popup -->

                            <div x-show="paymentResult && paymentResult.payment_type === 'cash'"
                                class="flex flex-col items-center">
                                <div
                                    class="w-16 h-16 md:w-20 md:h-20 bg-[#C6A87C]/10 border border-[#C6A87C]/30 rounded-full flex items-center justify-center mb-4 md:mb-6">
                                    <svg class="w-8 h-8 md:w-10 md:h-10 text-[#C6A87C]" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg md:text-2xl font-display text-white mb-2">Reservasi Berhasil!</h4>
                                <p class="text-gray-400 text-xs md:text-sm mb-6 text-center max-w-sm">Reservasi Anda
                                    telah tercatat. Silakan lakukan pembayaran secara tunai saat Anda tiba di lokasi.
                                </p>
                                <div
                                    class="bg-[#111] border border-white/10 p-4 rounded-xl w-full max-w-sm text-left space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 text-xs">Total Pembayaran</span>
                                        <span class="text-[#C6A87C] font-bold text-sm"
                                            x-text="formatRupiah(paymentResult.amount)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500 text-xs">Metode</span>
                                        <span class="text-white text-sm">Cash di Tempat</span>
                                    </div>
                                </div>
                                <p
                                    class="text-[#C6A87C] text-[8px] md:text-xs uppercase tracking-widest animate-pulse mt-6">
                                    Redirecting to Home...</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:p-6 border-t border-white/10 bg-[#050505] flex justify-end flex-shrink-0"
                        x-show="!paymentResult">
                        <button @click="processPaymentCore()" :disabled="!paymentMethod || isProcessingPayment"
                            class="w-full md:w-auto px-8 py-2 md:py-3 bg-[#C6A87C] text-black font-bold uppercase tracking-widest text-[10px] md:text-sm transition-all duration-300 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isProcessingPayment">Pay Now</span>
                            <span x-show="isProcessingPayment">Processing...</span>
                        </button>
                    </div>
                </div>

                {{-- PLACEHOLDER: old success animation replaced by fullscreen overlay below --}}

            </div>
        </div>


        <!-- ========================================================
             FULLSCREEN BOOKING CONFIRMATION ANIMATION OVERLAY
             ======================================================== -->
        <div x-show="showSuccessAnimation" x-cloak
             id="confirmOverlay"
             class="confirm-overlay fixed inset-0 z-[99999] flex items-center justify-center px-4"
             style="background: radial-gradient(ellipse at center, #111008 0%, #050505 70%)">

            <!-- Noise texture -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                 style="background-image:url('data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23n)\'/%3E%3C/svg%3E')"></div>

            <!-- Background ambient glow -->
            <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(circle at 50% 40%, rgba(198,168,124,0.08) 0%, transparent 65%);"></div>

            <!-- Particle container (JS fills this) -->
            <div id="confirmParticleContainer" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

            <!-- Main card -->
            <div class="confirm-card relative w-full max-w-md text-center">

                <!-- Checkmark area -->
                <div class="relative flex items-center justify-center mb-8">
                    <!-- Pulsing rings -->
                    <div class="relative w-28 h-28">
                        <div class="confirm-ring"></div>
                        <div class="confirm-ring confirm-ring-2"></div>
                        <div class="confirm-ring confirm-ring-3"></div>

                        <!-- SVG Checkmark -->
                        <svg class="confirm-svg w-28 h-28 block rounded-full"
                             style="stroke: #C6A87C; stroke-width:2; stroke-miterlimit:10; fill:none;"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="confirm-circle" cx="26" cy="26" r="25" fill="none"
                                    style="stroke:#C6A87C; stroke-width:2; stroke-miterlimit:10;"/>
                            <path  class="confirm-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"
                                   style="stroke:#050505; stroke-width:2;"/>
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h2 class="confirm-title font-display text-4xl md:text-5xl mb-2">Booking Confirmed!</h2>

                <!-- Gold line -->
                <div class="confirm-line mx-auto mb-6" style="height:1px; width:80px; background: linear-gradient(90deg, transparent, #C6A87C, transparent);"></div>

                <!-- Sub headline -->
                <p class="confirm-row-1 text-gray-400 text-sm mb-6">Terima kasih, <span class="text-white font-semibold" x-text="customerName"></span>.<br>Kami siap menyambut Anda.</p>

                <!-- Booking detail card -->
                <div class="mx-auto max-w-sm rounded-xl border border-white/10 bg-white/[0.04] backdrop-blur-sm divide-y divide-white/10 overflow-hidden">

                    <div class="confirm-row-2 flex items-center gap-3 px-5 py-3 text-left">
                        <svg class="w-4 h-4 text-[#C6A87C] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64M5.25 21V9.75m13.5 11.25V9.75m-13.5 0A2.25 2.25 0 017.5 7.5h9a2.25 2.25 0 012.25 2.25"/>
                        </svg>
                        <span class="text-gray-500 text-xs w-20">Studio</span>
                        <span class="text-white text-xs font-medium" x-text="selectedStore ? selectedStore.name : ''"></span>
                    </div>

                    <div class="confirm-row-3 flex items-center gap-3 px-5 py-3 text-left">
                        <svg class="w-4 h-4 text-[#C6A87C] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714a2.25 2.25 0 001.357 2.059l.527.18M14.25 3.104c.251.023.501.05.75.082M19.5 10.5c0 2.485-1.567 4.626-3.75 5.498M9.75 3.104A24.3 24.3 0 0012 3c.75 0 1.504.036 2.25.104"/>
                        </svg>
                        <span class="text-gray-500 text-xs w-20">Layanan</span>
                        <span class="text-white text-xs font-medium" x-text="selectedService ? selectedService.name : ''"></span>
                    </div>

                    <div class="confirm-row-4 flex items-center gap-3 px-5 py-3 text-left">
                        <svg class="w-4 h-4 text-[#C6A87C] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/>
                        </svg>
                        <span class="text-gray-500 text-xs w-20">Stylist</span>
                        <span class="text-white text-xs font-medium" x-text="selectedCapsterName"></span>
                    </div>

                    <div class="confirm-row-5 flex items-center gap-3 px-5 py-3 text-left">
                        <svg class="w-4 h-4 text-[#C6A87C] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        <span class="text-gray-500 text-xs w-20">Waktu</span>
                        <span class="text-white text-xs font-medium" x-text="(date ? formatDate(date) : '') + (timeSlot ? ' · ' + timeSlot : '')"></span>
                    </div>
                </div>

                <!-- CTA & countdown -->
                <div class="confirm-cta mt-8">
                    <p class="text-gray-600 text-[10px] uppercase tracking-widest mb-3">Mengarahkan ke halaman utama...</p>
                    <div class="w-48 mx-auto h-[2px] bg-white/10 rounded-full overflow-hidden">
                        <div class="confirm-bar h-full rounded-full" style="background: linear-gradient(90deg, #9a7a52, #C6A87C, #f0d9a8);"></div>
                    </div>
                </div>

            </div><!-- /confirm-card -->
        </div>
        <!-- End Confirmation Overlay -->

        <!-- Custom Toast Notification -->
        <div x-show="toastVisible" x-cloak x-transition:enter="toast-enter" x-transition:leave="toast-leave"
            class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] w-[90%] max-w-md">
            <div class="rounded-xl border px-5 py-4 shadow-2xl backdrop-blur-xl flex items-start gap-3" :class="toastType === 'warning'
                    ? 'bg-yellow-900/80 border-yellow-600/40 shadow-yellow-900/30'
                    : 'bg-red-950/80 border-red-600/30 shadow-red-900/30'">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-0.5">
                    <svg x-show="toastType === 'error'" class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <svg x-show="toastType === 'warning'" class="w-5 h-5 text-yellow-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <!-- Message -->
                <p class="text-sm font-medium leading-relaxed"
                    :class="toastType === 'warning' ? 'text-yellow-100' : 'text-red-100'" x-text="toastMessage"></p>
                <!-- Close Button -->
                <button @click="toastVisible = false"
                    class="flex-shrink-0 ml-auto -mr-1 -mt-1 p-1 rounded-lg transition-colors"
                    :class="toastType === 'warning' ? 'hover:bg-yellow-800/50 text-yellow-400' : 'hover:bg-red-800/50 text-red-400'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

    </div>

    <script>
        function bookingWizard() {
            // Prep data from PHP to calculate dynamic states in frontend
            const servicesData = @json($services);
            const capstersData = @json($capsters);

            return {
                currentStep: 1,
                steps: ['Store', 'Service', 'Stylist', 'Time', 'Confirm'],
                showPaymentModal: false,
                showSuccessAnimation: false,
                _successAnimationWatcher: null,
                paymentMethod: null,
                paymentResult: null,
                paymentCheckInterval: null,
                selectedStore: null,
                selectedService: null,
                selectedCapster: null,
                selectedCapsterName: '',
                date: '',
                timeSlot: '',
                availableSlots: [],
                isLoadingSlots: false,
                isProcessingPayment: false,
                customerName: '',
                customerPhone: '',
                customerEmail: '',
                isDisposableEmail: false,
                isEmailChecking: false,
                disposableDomainsList: [],
                notes: '',
                timeLeft: 0,
                countdownInterval: null,
                paymentExpired: false,
                toastVisible: false,
                toastMessage: '',
                toastType: 'error',
                toastTimeout: null,

                get emailStatus() {
                    const email = this.customerEmail.trim();
                    if (email === '') return 'empty';
                    if (this.isEmailChecking) return 'checking';
                    
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) return 'invalid';
                    if (this.isDisposableEmail) return 'invalid';
                    
                    return 'valid';
                },

                get formattedTimeLeft() {
                    const minutes = Math.floor(this.timeLeft / 60);
                    const seconds = this.timeLeft % 60;
                    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
                },

                init() {
                    // Spawn gold particles whenever success animation becomes visible
                    this.$watch('showSuccessAnimation', (val) => {
                        if (val) {
                            this.$nextTick(() => this._spawnParticles());
                        }
                    });

                    this.$watch('date', (value) => {
                        if (value) this.fetchSlots();
                    });

                    // Fetch dua database global raksasa untuk digabungkan secara otomatis
                    Promise.all([
                        fetch('https://raw.githubusercontent.com/ivolo/disposable-email-domains/master/index.json').then(r => r.json()),
                        fetch('https://raw.githubusercontent.com/FGRibreau/mailchecker/master/list.txt').then(r => r.text())
                    ]).then(([list1, text2]) => {
                        const list2 = text2.split('\n').map(d => d.trim()).filter(d => d);
                        this.disposableDomainsList = [...new Set([...list1, ...list2])];
                    }).catch(() => console.log('Gagal memuat daftar domain temp.'));

                    let emailTimeout;
                    this.$watch('customerEmail', (value) => {
                        this.isDisposableEmail = false;
                        if (!value || !value.includes('@')) return;
                        
                        clearTimeout(emailTimeout);
                        emailTimeout = setTimeout(() => {
                            const domain = value.split('@')[1].toLowerCase();
                            if (!domain || !domain.includes('.')) return;
                            
                            this.isEmailChecking = true;
                            
                            // Pengecekan 1: Cek dari gabungan database lokal otomatis (>10.000 domain)
                            if (this.disposableDomainsList.includes(domain)) {
                                this.isDisposableEmail = true;
                                this.isEmailChecking = false;
                                return;
                            }
                            
                            // Pengecekan 2 & 3: Multi-API Fallback (Concurrent Check)
                            Promise.allSettled([
                                fetch(`https://disposable.debounce.io/?email=${value}`).then(r => r.json()),
                                fetch(`https://disposable-emails-detector.vegastack.com/?domain=${domain}`).then(r => r.json())
                            ]).then(results => {
                                const debounceRes = results[0].status === 'fulfilled' ? results[0].value : null;
                                const vegaRes = results[1].status === 'fulfilled' ? results[1].value : null;
                                
                                if ((debounceRes && debounceRes.disposable === "true") || 
                                    (vegaRes && (vegaRes.isDisposable === true || vegaRes.is_disposable === true))) {
                                    this.isDisposableEmail = true;
                                }
                                this.isEmailChecking = false;
                            }).catch(() => {
                                this.isEmailChecking = false;
                            });
                        }, 600);
                    });
                },

                showToast(message, type = 'error') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.toastVisible = true;
                    if (this.toastTimeout) clearTimeout(this.toastTimeout);
                    this.toastTimeout = setTimeout(() => {
                        this.toastVisible = false;
                    }, 4000);
                },

                get hasServicesForSelectedStore() {
                    if (!this.selectedStore) return false;
                    return servicesData.some(s => s.id_store == this.selectedStore.id);
                },

                get hasMultipleCapstersForSelectedStore() {
                    if (!this.selectedStore) return false;
                    return capstersData.filter(c => c.id_store == this.selectedStore.id).length > 1;
                },

                get hasCapstersForSelectedStore() {
                    if (!this.selectedStore) return false;
                    return capstersData.some(c => c.id_store == this.selectedStore.id);
                },

                selectStore(id, name) {
                    if (this.selectedStore && this.selectedStore.id !== id) {
                        // Reset nested items if store changed
                        this.selectedService = null;
                        this.selectedCapster = null;
                        this.selectedCapsterName = '';
                        this.date = '';
                        this.timeSlot = '';
                        this.availableSlots = [];
                    }
                    this.selectedStore = {
                        id,
                        name
                    };
                },
                selectService(id, name, price) {
                    this.selectedService = {
                        id,
                        name,
                        price
                    };
                },
                selectCapster(id, name) {
                    this.selectedCapster = id ? {
                        id,
                        name
                    } : null;
                    this.selectedCapsterName = name;
                    if (this.date) {
                        this.fetchSlots();
                        this.timeSlot = '';
                    }
                },

                /* Spawn floating gold + warm particles */
                _spawnParticles() {
                    const container = document.getElementById('confirmParticleContainer');
                    if (!container) return;
                    container.innerHTML = '';
                    const colors = [
                        '#C6A87C','#f0d9a8','#9a7a52','#e8c98a',
                        '#fff8e7','#d4a853','#a8875a','#f5e6c8'
                    ];
                    const shapes = ['50%','4px','2px'];
                    for (let i = 0; i < 80; i++) {
                        const el = document.createElement('div');
                        const size = Math.random() * 8 + 4;
                        const color = colors[Math.floor(Math.random() * colors.length)];
                        const shape = shapes[Math.floor(Math.random() * shapes.length)];
                        el.className = 'confirm-particle';
                        el.style.cssText = [
                            `width:${size}px`, `height:${size}px`,
                            `left:${Math.random() * 100}%`,
                            `background:${color}`,
                            `border-radius:${shape}`,
                            `opacity:${Math.random() * 0.6 + 0.4}`,
                            `animation-duration:${Math.random() * 3 + 2}s`,
                            `animation-delay:${Math.random() * 2}s`
                        ].join(';');
                        container.appendChild(el);
                    }
                },

                fetchSlots() {
                    if (!this.date || !this.selectedStore) return;
                    this.isLoadingSlots = true;
                    this.availableSlots = [];
                    let url = `{{ route('booking.slots') }}?date=${this.date}&store_id=${this.selectedStore.id}`;
                    if (this.selectedCapster) url += `&employee_id=${this.selectedCapster.id}`;
                    fetch(url).then(r => r.json()).then(d => {
                        if (d.slots && Array.isArray(d.slots)) {
                            this.availableSlots = d.slots;
                        } else {
                            this.availableSlots = [];
                        }
                        this.isLoadingSlots = false;
                    }).catch(() => this.isLoadingSlots = false);
                },

                formatRupiah(n) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(n);
                },
                formatDate(d) {
                    if (!d) return '';
                    return new Date(d).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },

                nextStep() {
                    if (this.currentStep < 5) this.currentStep++;
                    else this.showPaymentModal = true;
                },
                prevStep() {
                    if (this.currentStep > 1) this.currentStep--;
                },

                canProceed() {
                    if (this.currentStep === 1) return this.selectedStore !== null;
                    if (this.currentStep === 2) return this.selectedService !== null;
                    if (this.currentStep === 3) return this.selectedCapsterName !== '';
                    if (this.currentStep === 4) return this.date !== '' && this.timeSlot !== '';
                    if (this.currentStep === 5) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return this.customerName.trim() !== '' &&
                            this.customerPhone.trim() !== '' &&
                            emailRegex.test(this.customerEmail.trim()) &&
                            !this.isDisposableEmail &&
                            !this.isEmailChecking;
                    }
                    return false;
                },

                processPaymentCore() {
                    this.isProcessingPayment = true;
                    fetch('{{ route("booking.process") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },

                        body: JSON.stringify({
                            store_id: this.selectedStore.id,
                            service_id: this.selectedService.id,
                            capster_id: this.selectedCapster ? this.selectedCapster.id : null,
                            date: this.date,
                            time: this.timeSlot,
                            customer_name: this.customerName,
                            customer_phone: this.customerPhone,
                            customer_email: this.customerEmail,
                            notes: this.notes,
                            payment_method: this.paymentMethod
                        })
                    })
                        .then(r => {
                            // Handle HTTP error responses
                            if (r.status === 429) {
                                throw new Error('RATE_LIMIT');
                            }
                            if (r.status === 409) {
                                return r.json().then(d => { throw new Error(d.message || 'Slot tidak tersedia.'); });
                            }
                            if (!r.ok) {
                                return r.json().then(d => { throw new Error(d.message || 'Terjadi kesalahan.'); });
                            }
                            return r.json();
                        })
                        .then(data => {
                            this.isProcessingPayment = false;
                            if (data.status === 'success') {
                                if (data.payment_type === 'cash') {
                                    this.paymentResult = data;
                                    // Cash: langsung tampilkan info, lalu redirect
                                    setTimeout(() => {
                                        this.showSuccessAnimation = true;
                                        setTimeout(() => {
                                            window.location.href = "{{ route('home') }}";
                                        }, 2000);
                                    }, 3000);
                                } else {
                                    // Online payment: Buka pop-up Snap
                                    this.showPaymentModal = false; // tutup modal utama

                                    // Start Auto-Detect Polling di Background (berjaga-jaga user bayar lewat hp, tp popup di web di close)
                                    this.pollPaymentStatus(data.order_id);

                                    window.snap.pay(data.snap_token, {
                                        onSuccess: (result) => {
                                            this.showSuccessAnimation = true;
                                            setTimeout(() => {
                                                window.location.href = "{{ route('home') }}";
                                            }, 2000);
                                        },
                                        onPending: (result) => {
                                            this.showToast('Menunggu Anda menyelesaikan pembayaran...', 'info');
                                        },
                                        onError: (result) => {
                                            this.showToast('Pembayaran Gagal.', 'error');
                                        },
                                        onClose: () => {
                                            this.showToast('Anda menutup pop-up sebelum menyelesaikan pembayaran.', 'warning');
                                        }
                                    });
                                }
                            } else {
                                this.showToast(data.message, 'error');
                            }
                        })
                        .catch(e => {
                            this.isProcessingPayment = false;
                            if (e.message === 'RATE_LIMIT') {
                                this.showToast('Terlalu banyak percobaan. Silakan tunggu 1 menit sebelum mencoba lagi.', 'warning');
                            } else {
                                this.showToast(e.message, 'error');
                            }
                        });
                },

                startCountdown(duration) {
                    this.timeLeft = duration;
                    this.paymentExpired = false;
                    if (this.countdownInterval) clearInterval(this.countdownInterval);

                    this.countdownInterval = setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                        } else {
                            this.paymentExpired = true;
                            clearInterval(this.countdownInterval);
                            clearInterval(this.paymentCheckInterval);
                        }
                    }, 1000);
                },

                // FUNGSI MATA-MATA (POLLING)
                pollPaymentStatus(orderId) {
                    console.log("Mulai memantau status untuk Order ID:", orderId); // Cek di Console Browser (F12)

                    this.paymentCheckInterval = setInterval(() => {
                        fetch(`{{ route('booking.check') }}?order_id=${orderId}`)
                            .then(r => r.json())
                            .then(res => {
                                console.log("Status Midtrans:", res);

                                if (res.status === 'paid') {
                                    console.log("PEMBAYARAN SUKSES TERDETEKSI!");

                                    // STOP Checking & Countdown
                                    clearInterval(this.paymentCheckInterval);
                                    if (this.countdownInterval) clearInterval(this.countdownInterval);

                                    // TRIGGER ANIMASI WOW
                                    this.showSuccessAnimation = true;

                                    // Redirect ke Home setelah 2 detik
                                    setTimeout(() => {
                                        window.location.href = "{{ route('home') }}";
                                    }, 2000); // <-- Dipercepat jadi 2 detik
                                }
                            })
                            .catch(err => console.error("Polling Error:", err));
                    }, 2000); // <-- Dipercepat ngecek tiap 2 detik
                }
            }
        }
    </script>
</body>

</html>