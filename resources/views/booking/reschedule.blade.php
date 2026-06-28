<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Jadwal - Tangwin Cut</title>
    <meta name="description" content="Ubah jadwal reservasi Anda di Tangwin Cut Hair Studio.">
    <link rel="icon" href="{{ asset('images/logo_tangwin_white.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: #050505;
        }

        h1, h2, h3, .font-display {
            font-family: 'Italiana', serif;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }

        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #111; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease both; }

        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(198,168,124,0); }
            50%       { box-shadow: 0 0 12px 4px rgba(198,168,124,0.25); }
        }
        .pulse-gold { animation: pulse-gold 2s infinite; }

        /* Slot grid */
        .slot-btn {
            position: relative;
            overflow: hidden;
            transition: all .2s;
        }
        .slot-btn.selected {
            background: #C6A87C;
            color: #000;
            border-color: #C6A87C;
            font-weight: 700;
        }
        .slot-btn.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #111;
        }
        .slot-strike {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="text-gray-300 min-h-screen bg-[#050505] flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-2xl mx-auto animate-fade-in-up" x-data="rescheduleApp()">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block hover:opacity-80 transition">
                <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo" class="h-12 mx-auto w-auto">
            </a>
        </div>

        {{-- Alerts --}}
        @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/50 text-red-400 text-sm rounded-xl p-4 mb-6 text-center">
            {{ session('error') }}
        </div>
        @endif

        {{-- Info card lama --}}
        <div class="bg-[#0a0a0a] border border-white/10 rounded-xl p-6 mb-6">
            <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-3">Reservasi yang Akan Diubah</p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-1">Nomor Booking</p>
                    <p class="text-white font-bold">{{ $reservation->booking_number ?? '#'.str_pad($reservation->id_reservation, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-1">Pelanggan</p>
                    <p class="text-white font-bold">{{ $reservation->customer_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-1">Layanan</p>
                    <p class="text-white">{{ $reservation->service->service_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-[10px] uppercase tracking-widest mb-1">Jadwal Lama</p>
                    <p class="text-[#C6A87C] font-bold">
                        {{ \Carbon\Carbon::parse($reservation->booking_date)->translatedFormat('d M Y') }},
                        {{ date('H:i', strtotime($reservation->booking_time)) }} WIB
                    </p>
                </div>
            </div>
        </div>

        {{-- Card utama reschedule --}}
        <div class="bg-[#0a0a0a] border border-white/10 rounded-xl overflow-hidden">

            {{-- Header --}}
            <div class="p-6 border-b border-white/10 bg-[#111]">
                <h1 class="text-3xl font-display text-white">Ubah Jadwal</h1>
                <p class="text-xs text-gray-500 mt-1">Pilih tanggal & waktu baru untuk reservasi Anda.</p>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6">

                {{-- Pilih Tanggal --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Pilih Tanggal Baru</label>
                    <input
                        type="date"
                        x-model="date"
                        min="{{ date('Y-m-d') }}"
                        id="reschedule-date"
                        class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none"
                    >
                </div>

                {{-- Slot --}}
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-3">Pilih Waktu</label>

                    {{-- Loading --}}
                    <div x-show="isLoading" class="text-sm text-[#C6A87C] animate-pulse">Memuat slot tersedia...</div>

                    {{-- Slot grid --}}
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3" x-show="date && !isLoading">
                        <template x-for="slot in slots" :key="slot.id_slot">
                            <button
                                type="button"
                                @click="if(!slot.is_past && !slot.is_full) selectedTime = slot.formatted_time"
                                class="slot-btn py-3 text-sm border rounded"
                                :class="{
                                    'selected pulse-gold': selectedTime === slot.formatted_time,
                                    'disabled border-white/5 text-gray-600': slot.is_past || slot.is_full,
                                    'border-white/10 text-gray-400 hover:border-white hover:text-white': !slot.is_past && !slot.is_full && selectedTime !== slot.formatted_time
                                }"
                                :disabled="slot.is_past || slot.is_full"
                            >
                                <span x-text="slot.formatted_time"></span>
                                <svg x-show="slot.is_past || slot.is_full" class="slot-strike text-gray-600/40" preserveAspectRatio="none" viewBox="0 0 100 100">
                                    <line x1="0" y1="100" x2="100" y2="0" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                        </template>
                    </div>

                    <p x-show="date && !isLoading && slots.length === 0" class="text-sm text-red-400 italic">
                        Maaf, tidak ada slot tersedia pada tanggal ini.
                    </p>
                    <p x-show="!date" class="text-sm text-gray-600 italic">
                        Silakan pilih tanggal terlebih dahulu.
                    </p>
                </div>

                {{-- Peringatan --}}
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4 text-sm text-amber-300">
                    <strong>Perhatian:</strong> Setelah reschedule dikonfirmasi, booking lama akan dibatalkan dan booking baru akan dibuat. Email konfirmasi akan dikirim ke <strong>{{ $reservation->customer_email }}</strong>.
                </div>

                {{-- Summary --}}
                <div x-show="selectedTime" class="bg-[#111] border border-[#C6A87C]/20 rounded-lg p-4 space-y-2 text-sm" x-transition>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-2">Jadwal Baru</p>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tanggal</span>
                        <span class="text-white font-bold" x-text="formatDate(date)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Waktu</span>
                        <span class="text-[#C6A87C] font-bold" x-text="selectedTime + ' WIB'"></span>
                    </div>
                </div>

            </div>

            {{-- Footer / Action --}}
            <div class="p-6 border-t border-white/10 bg-[#050505] flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-white text-xs uppercase tracking-widest transition">
                    &larr; Kembali
                </a>

                <form action="{{ route('booking.reschedule.process') }}" method="POST" x-ref="rescheduleForm">
                    @csrf
                    <input type="hidden" name="id_reservation" value="{{ $reservation->id_reservation }}">
                    <input type="hidden" name="new_date" x-bind:value="date">
                    <input type="hidden" name="new_time" x-bind:value="selectedTime">

                    <button
                        type="button"
                        @click="submit()"
                        :disabled="!date || !selectedTime || isSubmitting"
                        class="px-8 py-3 bg-[#C6A87C] hover:bg-white text-black font-bold uppercase tracking-widest text-xs rounded transition-all duration-300 shadow-[0_0_15px_rgba(198,168,124,0.2)] disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <span x-show="!isSubmitting">Konfirmasi Reschedule</span>
                        <span x-show="isSubmitting">Memproses...</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <script>
        function rescheduleApp() {
            return {
                date: '',
                selectedTime: '',
                slots: [],
                isLoading: false,
                isSubmitting: false,

                init() {
                    this.$watch('date', (val) => {
                        if (val) this.fetchSlots();
                        this.selectedTime = '';
                    });
                },

                fetchSlots() {
                    this.isLoading = true;
                    this.slots = [];
                    const storeId = {{ $reservation->id_store }};
                    const employeeId = {{ $reservation->id_employee ?? 'null' }};
                    let url = `{{ route('booking.slots') }}?date=${this.date}&store_id=${storeId}`;
                    if (employeeId) url += `&employee_id=${employeeId}`;

                    fetch(url)
                        .then(r => r.json())
                        .then(d => {
                            this.slots = Array.isArray(d.slots) ? d.slots : [];
                            this.isLoading = false;
                        })
                        .catch(() => { this.isLoading = false; });
                },

                formatDate(d) {
                    if (!d) return '';
                    return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });
                },

                submit() {
                    if (!this.date || !this.selectedTime) return;
                    this.isSubmitting = true;
                    this.$refs.rescheduleForm.submit();
                }
            }
        }
    </script>

</body>
</html>
