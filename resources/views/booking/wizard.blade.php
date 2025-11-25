<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Tangwin Cut</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Manrope', sans-serif; background-color: #050505; }
        h1, h2, h3, .font-display { font-family: 'Italiana', serif; }
        
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #111; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1); opacity: 0.5; cursor: pointer;
        }

        /* --- ANIMASI CEKLIS EMAS (WOW EFFECT) --- */
        .checkmark { width: 80px; height: 80px; border-radius: 50%; display: block; stroke-width: 2; stroke: #C6A87C; stroke-miterlimit: 10; margin: 10% auto; box-shadow: inset 0px 0px 0px #C6A87C; animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
        .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 2; stroke-miterlimit: 10; stroke: #C6A87C; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 50px #C6A87C; stroke: #000; } }
    </style>
</head>
<body class="text-gray-300 h-screen w-screen overflow-hidden flex items-center justify-center bg-[url('https://images.unsplash.com/photo-1634480496840-b3654a5253e8?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center">

    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm z-0"></div>

    <div class="relative z-10 w-full max-w-5xl h-full md:h-[85vh] bg-[#0a0a0a] border border-white/10 shadow-2xl flex flex-col md:flex-row overflow-hidden animate-fade-in-up"
         x-data="bookingWizard()">

        <div class="w-full md:w-1/3 bg-[#111] border-r border-white/5 p-8 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] pointer-events-none"></div>
            
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="text-2xl font-display text-white block mb-12 tracking-widest">
                    TANGWIN<span class="text-[#C6A87C]">.</span>
                </a>

                <div class="space-y-6">
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

            <div class="relative z-10 mt-auto pt-8 border-t border-white/5">
                <h4 class="text-[#C6A87C] text-xs uppercase tracking-widest mb-4">Your Selection</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Service</span>
                        <span class="text-white text-right truncate w-32" x-text="selectedService ? selectedService.name : '-'"></span>
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
                        <span class="text-[#C6A87C] font-bold" x-text="selectedService ? formatRupiah(selectedService.price) : '-'"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full md:w-2/3 bg-[#0a0a0a] relative flex flex-col h-full md:overflow-hidden">
            
            <div class="md:hidden p-6 border-b border-white/10 flex justify-between items-center flex-shrink-0">
                <span class="text-[#C6A87C] text-xs font-bold uppercase">Step <span x-text="currentStep"></span>/4</span>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-white">&times; Close</a>
            </div>

            <div class="flex-1 p-6 md:p-12 overflow-y-auto custom-scroll relative min-h-0">
                
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Choose Service</h2>
                    <p class="text-gray-500 mb-8">Pilih perawatan yang Anda butuhkan.</p>

                    <div class="grid grid-cols-1 gap-4 pb-4">
                        @foreach($services as $service)
                        <div @click="selectService({{ $service->id_service }}, '{{ $service->service_name }}', {{ $service->price }})"
                             class="group relative p-6 border rounded-lg cursor-pointer transition-all duration-300 hover:bg-white/5"
                             :class="selectedService && selectedService.id === {{ $service->id_service }} ? 'border-[#C6A87C] bg-white/5' : 'border-white/10'">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-xl font-display text-white group-hover:text-[#C6A87C] transition-colors">{{ $service->service_name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $service->description }}</p>
                                </div>
                                <span class="text-lg font-bold text-white">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            </div>
                            <div x-show="selectedService && selectedService.id === {{ $service->id_service }}" class="absolute top-4 right-4 text-[#C6A87C]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="currentStep === 2" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Select Stylist</h2>
                    <p class="text-gray-500 mb-8">Siapa yang akan menangani gaya Anda hari ini?</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-4">
                        <div @click="selectCapster(null, 'Siapa Saja (Available)')" class="cursor-pointer group text-center">
                            <div class="relative h-40 w-full border border-white/10 rounded-lg mb-2 flex items-center justify-center bg-[#111] transition-all group-hover:border-[#C6A87C]"
                                 :class="selectedCapster === null && selectedCapsterName === 'Siapa Saja (Available)' ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                <span class="text-4xl text-gray-600 group-hover:text-[#C6A87C]">?</span>
                            </div>
                            <h3 class="text-white font-bold text-sm truncate">Bebas / Siapa Saja</h3>
                        </div>
                        @foreach($capsters as $capster)
                        <div @click="selectCapster({{ $capster->id_employee }}, '{{ $capster->employee_name }}')" class="cursor-pointer group text-center">
                            <div class="relative h-40 w-full overflow-hidden rounded-lg mb-2 border border-transparent transition-all group-hover:border-[#C6A87C]"
                                 :class="selectedCapster && selectedCapster.id === {{ $capster->id_employee }} ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                <img src="{{ $capster->photo_path ? asset('storage/' . $capster->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($capster->employee_name).'&background=1a1a1a&color=fff' }}" 
                                     class="w-full h-full object-cover object-top grayscale group-hover:grayscale-0 transition-all duration-500">
                            </div>
                            <h3 class="text-white font-bold text-sm truncate">{{ $capster->employee_name }}</h3>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="currentStep === 3" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Date and Time</h2>
                    <p class="text-gray-500 mb-8">Pilih waktu yang sesuai untuk Anda.</p>

                    <div class="space-y-6 pb-4">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Select Date</label>
                            <input type="date" x-model="date" class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none" min="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Available Slots</label>
                            <div x-show="isLoadingSlots" class="text-sm text-[#C6A87C] animate-pulse mb-2">Mencari jadwal tersedia...</div>
                            <div class="grid grid-cols-3 md:grid-cols-4 gap-3" x-show="date && !isLoadingSlots">
                                <template x-for="slot in availableSlots" :key="slot.id_slot">
                                    <button @click="timeSlot = slot.formatted_time" type="button"
                                            class="py-3 text-sm border rounded transition-all duration-200"
                                            :class="timeSlot === slot.formatted_time ? 'bg-[#C6A87C] text-black border-[#C6A87C] font-bold' : 'border-white/10 text-gray-400 hover:border-white hover:text-white'">
                                        <span x-text="slot.formatted_time"></span>
                                    </button>
                                </template>
                            </div>
                            <p x-show="date && !isLoadingSlots && availableSlots.length === 0" class="text-sm text-red-400 italic mt-2">Maaf, tidak ada jadwal tersedia.</p>
                            <p x-show="!date" class="text-sm text-gray-600 italic">Silakan pilih tanggal terlebih dahulu.</p>
                        </div>
                    </div>
                </div>

                <div x-show="currentStep === 4" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Confirmation</h2>
                    <p class="text-gray-500 mb-8">Lengkapi data diri Anda.</p>

                    <form class="space-y-6 pb-4">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                            <input type="text" x-model="customerName" placeholder="Masukan Nama Anda" class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Phone Number (WhatsApp)</label>
                            <input type="tel" x-model="customerPhone" placeholder="Contoh: 0812..." class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Email Address (Optional)</label>
                            <input type="email" x-model="customerEmail" placeholder="contoh@email.com" class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none">
                            <p class="text-[10px] text-gray-500 mt-1">*Isi jika ingin invoice dikirim ke email.</p>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Notes (Optional)</label>
                            <textarea x-model="notes" rows="3" placeholder="Ada permintaan khusus?" class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none"></textarea>
                        </div>
                        <div class="bg-[#1a1a1a] p-4 rounded border border-dashed border-white/10 mt-6">
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Booking Summary</p>
                            <p class="text-white text-sm"><span class="text-[#C6A87C]">Service:</span> <span x-text="selectedService?.name"></span></p>
                            <p class="text-white text-sm"><span class="text-[#C6A87C]">Time:</span> <span x-text="date"></span> at <span x-text="timeSlot"></span></p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-6 md:p-8 border-t border-white/10 bg-[#0a0a0a] flex justify-between items-center flex-shrink-0">
                <button @click="prevStep()" x-show="currentStep > 1" class="text-gray-500 hover:text-white text-sm uppercase tracking-widest transition font-bold">&larr; Back</button>
                <div x-show="currentStep === 1"></div> 
                <button @click="nextStep()" class="px-8 py-3 bg-[#C6A87C] hover:bg-white text-black font-bold uppercase tracking-widest text-sm transition-all duration-300 shadow-[0_0_15px_rgba(198,168,124,0.3)] disabled:opacity-50 disabled:cursor-not-allowed" :disabled="!canProceed()">
                    <span x-text="currentStep === 4 ? 'Confirm Booking' : 'Next Step &rarr;'"></span>
                </button>
            </div>
        </div>

        <div x-show="isProcessingPayment" class="absolute inset-0 z-50 bg-black/90 flex flex-col items-center justify-center" x-transition>
            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-[#C6A87C] mb-4"></div>
            <p class="text-[#C6A87C] text-lg font-display tracking-widest animate-pulse">Processing Payment...</p>
        </div>

        <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center px-4" x-transition>
            
            <div class="absolute inset-0 bg-black/90" @click="if(!showSuccessAnimation) showPaymentModal = false"></div>

            <div class="relative w-full max-w-2xl bg-[#0a0a0a] border border-[#C6A87C]/30 rounded-xl shadow-2xl overflow-hidden transform transition-all" x-transition>
                
                <div x-show="!showSuccessAnimation">
                    <div class="p-6 border-b border-white/10 flex justify-between items-center bg-[#111]">
                        <div>
                            <h3 class="text-2xl font-display text-white" x-text="paymentResult ? 'Complete Payment' : 'Select Payment'"></h3>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mt-1" x-text="paymentResult ? 'Selesaikan pembayaran Anda' : 'Pilih metode pembayaran'"></p>
                        </div>
                        <button @click="showPaymentModal = false" class="text-gray-500 hover:text-white text-2xl">&times;</button>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6" x-show="!paymentResult">
                        <div @click="paymentMethod = 'qris'" class="cursor-pointer group relative p-6 border rounded-xl transition-all duration-300 flex flex-col items-center text-center hover:bg-white/5" :class="paymentMethod === 'qris' ? 'border-[#C6A87C] bg-[#C6A87C]/5 ring-1 ring-[#C6A87C]' : 'border-white/10'">
                            <div class="absolute top-3 right-3 px-2 py-1 bg-[#C6A87C] text-black text-[10px] font-bold uppercase tracking-wider rounded">Instant</div>
                            <div class="w-16 h-16 mb-4 bg-white rounded-lg flex items-center justify-center">
                                <svg class="w-10 h-10 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            </div>
                            <h4 class="text-lg font-bold text-white mb-1">QRIS</h4>
                            <p class="text-xs text-gray-500">Scan via GoPay, OVO, BCA, dll.</p>
                        </div>

                        <div @click="paymentMethod = 'bank_transfer'" class="cursor-pointer group relative p-6 border rounded-xl transition-all duration-300 flex flex-col items-center text-center hover:bg-white/5" :class="paymentMethod === 'bank_transfer' ? 'border-[#C6A87C] bg-[#C6A87C]/5 ring-1 ring-[#C6A87C]' : 'border-white/10'">
                            <div class="w-16 h-16 mb-4 bg-[#222] border border-white/10 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-white mb-1">Bank Transfer</h4>
                            <p class="text-xs text-gray-500">Virtual Account BCA</p>
                        </div>
                    </div>

                    <div class="p-8 text-center" x-show="paymentResult">
                        <div x-show="paymentResult && paymentResult.payment_type === 'qris'" class="flex flex-col items-center">
                            <p class="text-gray-400 text-sm mb-4">Scan QR code di bawah ini:</p>
                            <div class="bg-white p-4 rounded-xl shadow-2xl shadow-[#C6A87C]/20 mb-6">
                                <img :src="paymentResult ? paymentResult.qr_image_url : ''" alt="QRIS" class="w-64 h-64 object-contain">
                            </div>
                            <p class="text-[#C6A87C] text-xs uppercase tracking-widest animate-pulse">Menunggu Pembayaran Otomatis...</p>
                        </div>

                        <div x-show="paymentResult && paymentResult.payment_type === 'bank_transfer'" class="flex flex-col items-center">
                            <p class="text-gray-400 text-sm mb-6">Transfer ke Virtual Account BCA:</p>
                            <div class="bg-[#111] border border-white/10 p-6 rounded-xl w-full max-w-sm mb-6">
                                <div class="flex items-center justify-center gap-3">
                                    <span class="text-3xl font-display text-white tracking-widest" x-text="paymentResult ? paymentResult.va_number : ''"></span>
                                </div>
                            </div>
                            <p class="text-[#C6A87C] text-xs uppercase tracking-widest animate-pulse">Menunggu Pembayaran Otomatis...</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-white/10 bg-[#050505] flex justify-end" x-show="!paymentResult">
                        <button @click="processPaymentCore()" :disabled="!paymentMethod" class="px-8 py-3 bg-[#C6A87C] text-black font-bold uppercase tracking-widest text-sm transition-all duration-300 hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed">
                            Pay Now
                        </button>
                    </div>
                </div>

                <div x-show="showSuccessAnimation" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="p-12 text-center bg-[#0a0a0a]">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                    <h3 class="text-3xl font-display text-white mt-6 mb-2">Payment Successful!</h3>
                    <p class="text-gray-500 text-sm mb-8">Terima kasih telah mempercayakan gaya Anda kepada kami.</p>
                    <p class="text-[#C6A87C] text-xs uppercase tracking-widest animate-pulse">Redirecting to Home...</p>
                </div>

            </div>
        </div>

    </div>

    <script>
        function bookingWizard() {
            return {
                currentStep: 1,
                steps: ['Service', 'Stylist', 'Time', 'Confirm'],
                showPaymentModal: false,
                showSuccessAnimation: false, // WOW Factor
                paymentMethod: null,
                paymentResult: null, 
                paymentCheckInterval: null, 
                selectedService: null, selectedCapster: null, selectedCapsterName: '', 
                date: '', timeSlot: '', availableSlots: [], isLoadingSlots: false, isProcessingPayment: false,
                customerName: '', customerPhone: '', customerEmail: '', notes: '',

                init() {
                    this.$watch('date', (value) => { if(value) this.fetchSlots(); });
                },

                selectService(id, name, price) { this.selectedService = { id, name, price }; },
                selectCapster(id, name) { 
                    this.selectedCapster = id ? { id, name } : null; 
                    this.selectedCapsterName = name;
                    if(this.date) { this.fetchSlots(); this.timeSlot = ''; }
                },

                fetchSlots() {
                    if (!this.date) return;
                    this.isLoadingSlots = true; this.availableSlots = [];
                    let url = `{{ route('booking.slots') }}?date=${this.date}`;
                    if (this.selectedCapster) url += `&employee_id=${this.selectedCapster.id}`;
                    fetch(url).then(r => r.json()).then(d => { this.availableSlots = d.slots; this.isLoadingSlots = false; }).catch(() => this.isLoadingSlots = false);
                },

                formatRupiah(n) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n); },
                formatDate(d) { if(!d) return ''; return new Date(d).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }); },

                nextStep() {
                    if (this.currentStep < 4) this.currentStep++;
                    else this.showPaymentModal = true;
                },
                prevStep() { if (this.currentStep > 1) this.currentStep--; },
                
                canProceed() {
                    if (this.currentStep === 1) return this.selectedService !== null;
                    if (this.currentStep === 2) return this.selectedCapsterName !== ''; 
                    if (this.currentStep === 3) return this.date !== '' && this.timeSlot !== '';
                    if (this.currentStep === 4) {
                        return this.customerName !== '' && 
                            this.customerPhone !== '';
                    }
                    return false;
                },

                processPaymentCore() {
                    this.isProcessingPayment = true;
                    fetch('{{ route('booking.process') }}', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                        },

                        body: JSON.stringify({
                            service_id: this.selectedService.id,
                            capster_id: this.selectedCapster ? this.selectedCapster.id : null,
                            date: this.date, time: this.timeSlot,
                            customer_name: this.customerName, customer_phone: this.customerPhone, customer_email: this.customerEmail, notes: this.notes,
                            payment_method: this.paymentMethod
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.isProcessingPayment = false;
                        if(data.status === 'success') {
                            this.paymentResult = data;
                            // START AUTO DETECT
                            this.pollPaymentStatus(data.order_id); 
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(e => {
                        this.isProcessingPayment = false;
                        alert('Error Sistem: ' + e);
                    });
                },

                // FUNGSI MATA-MATA (POLLING)
                pollPaymentStatus(orderId) {
                    console.log("Mulai memantau status untuk Order ID:", orderId); // Cek di Console Browser (F12)

                    this.paymentCheckInterval = setInterval(() => {
                        fetch(`{{ route('booking.check') }}?order_id=${orderId}`)
                            .then(r => r.json())
                            .then(res => {
                                console.log("Status Midtrans:", res); 

                                if(res.status === 'paid') {
                                    console.log("PEMBAYARAN SUKSES TERDETEKSI!");
                                    
                                    // STOP Checking
                                    clearInterval(this.paymentCheckInterval);
                                    
                                    // TRIGGER ANIMASI WOW
                                    this.showSuccessAnimation = true; 
                                    
                                    // Redirect ke Home setelah 4 detik
                                    setTimeout(() => {
                                        window.location.href = "{{ route('home') }}";
                                    }, 4000);
                                }
                            })
                            .catch(err => console.error("Polling Error:", err));
                    }, 5000); // Cek setiap 5 detik
                }
            }
        }
    </script>
</body>
</html>