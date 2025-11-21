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
        
        /* Custom Scrollbar untuk area konten */
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: #111; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 2px; }

        /* Input Date Customization */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.5;
            cursor: pointer;
        }
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
            
            <div class="md:hidden p-6 border-b border-white/10 flex justify-between items-center">
                <span class="text-[#C6A87C] text-xs font-bold uppercase">Step <span x-text="currentStep"></span>/4</span>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-white">&times; Close</a>
            </div>

            <div class="flex-1 p-6 md:p-12 overflow-y-auto custom-scroll relative ">
                
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Choose Service</h2>
                    <p class="text-gray-500 mb-8">Pilih perawatan yang Anda butuhkan.</p>

                    <div class="grid grid-cols-1 gap-4">
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
                            
                            <div x-show="selectedService && selectedService.id === {{ $service->id_service }}" 
                                 class="absolute top-4 right-4 text-[#C6A87C]">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                    <div x-show="currentStep === 2" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                        <h2 class="text-4xl font-display text-white mb-2">Select Stylist</h2>
                        <p class="text-gray-500 mb-8">Siapa yang akan menangani gaya Anda hari ini?</p>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <div @click="selectCapster(null, 'Siapa Saja (Available)')"
                                class="cursor-pointer group text-center">
                                <div class="aspect-[3/4] border border-white/10 rounded-lg mb-3 flex items-center justify-center bg-[#111] transition-all group-hover:border-[#C6A87C]"
                                    :class="selectedCapster === null && selectedCapsterName === 'Siapa Saja (Available)' ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                    <span class="text-4xl text-gray-600 group-hover:text-[#C6A87C]">?</span>
                                </div>
                                <h3 class="text-white font-bold text-sm">Bebas / Siapa Saja</h3>
                            </div>

                            @foreach($capsters as $capster)
                            <div @click="selectCapster({{ $capster->id_employee }}, '{{ $capster->employee_name }}')"
                                class="cursor-pointer group text-center">
                                <div class="relative aspect-[3/4] overflow-hidden rounded-lg mb-3 border border-transparent transition-all group-hover:border-[#C6A87C]"
                                    :class="selectedCapster && selectedCapster.id === {{ $capster->id_employee }} ? 'border-[#C6A87C] ring-1 ring-[#C6A87C]' : ''">
                                    <img src="{{ $capster->photo_path ? asset('storage/' . $capster->photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($capster->employee_name).'&background=1a1a1a&color=fff' }}" 
                                        class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                </div>
                                <h3 class="text-white font-bold text-sm">{{ $capster->employee_name }}</h3>
                            </div>
                            @endforeach
                        </div>
                    </div>

                <div x-show="currentStep === 3" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Date & Time</h2>
                    <p class="text-gray-500 mb-8">Pilih waktu yang sesuai untuk Anda.</p>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Select Date</label>
                            <input type="date" x-model="date" 
                                   class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none"
                                   min="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Available Slots</label>
                            <div class="grid grid-cols-3 gap-3" x-show="date">
                                <template x-for="time in ['10:00', '11:00', '13:00', '14:00', '16:00', '19:00', '20:00']">
                                    <button @click="timeSlot = time"
                                            class="py-3 text-sm border rounded transition-all duration-200"
                                            :class="timeSlot === time ? 'bg-[#C6A87C] text-black border-[#C6A87C] font-bold' : 'border-white/10 text-gray-400 hover:border-white hover:text-white'">
                                        <span x-text="time"></span>
                                    </button>
                                </template>
                            </div>
                            <p x-show="!date" class="text-sm text-gray-600 italic">Silakan pilih tanggal terlebih dahulu.</p>
                        </div>
                    </div>
                </div>

                <div x-show="currentStep === 4" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-4xl font-display text-white mb-2">Confirmation</h2>
                    <p class="text-gray-500 mb-8">Lengkapi data diri Anda untuk konfirmasi booking.</p>

                    <form id="bookingForm" class="space-y-6">
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                            <input type="text" x-model="customerName" placeholder="Masukan Nama Anda"
                                   class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none placeholder-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Phone Number (WhatsApp)</label>
                            <input type="tel" x-model="customerPhone" placeholder="Contoh: 08123456789"
                                   class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none placeholder-gray-700">
                        </div>
                        
                        <div>
                            <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Notes (Optional)</label>
                            <textarea x-model="notes" rows="3" placeholder="Ada permintaan khusus?"
                                      class="w-full bg-[#111] border border-white/10 rounded-lg p-4 text-white focus:border-[#C6A87C] focus:ring-0 transition outline-none placeholder-gray-700"></textarea>
                        </div>

                        <div class="bg-[#1a1a1a] p-4 rounded border border-dashed border-white/10 mt-6">
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">Booking Summary</p>
                            <p class="text-white text-sm"><span class="text-[#C6A87C]">Service:</span> <span x-text="selectedService?.name"></span></p>
                            <p class="text-white text-sm"><span class="text-[#C6A87C]">Time:</span> <span x-text="date"></span> at <span x-text="timeSlot"></span></p>
                        </div>
                    </form>
                </div>

            </div>

            <div class="p-6 md:p-8 border-t border-white/10 bg-[#0a0a0a] flex justify-between items-center">
                <button @click="prevStep()" x-show="currentStep > 1"
                        class="text-gray-500 hover:text-white text-sm uppercase tracking-widest transition font-bold">
                    &larr; Back
                </button>
                <div x-show="currentStep === 1"></div> <button @click="nextStep()" 
                        class="px-8 py-3 bg-[#C6A87C] hover:bg-white text-black font-bold uppercase tracking-widest text-sm transition-all duration-300 shadow-[0_0_15px_rgba(198,168,124,0.3)] disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="!canProceed()">
                    <span x-text="currentStep === 4 ? 'Confirm Booking' : 'Next Step &rarr;'"></span>
                </button>
            </div>

        </div>
    </div>

    <script>
        function bookingWizard() {
            return {
                currentStep: 1,
                steps: ['Service', 'Stylist', 'Time', 'Confirm'],
                
                // Data Models
                selectedService: null, // { id, name, price }
                selectedCapster: null, // { id, name }
                selectedCapsterName: '', // Untuk tampilan 'Siapa Saja'
                date: '',
                timeSlot: '',
                customerName: '',
                customerPhone: '',
                notes: '',

                // Actions
                selectService(id, name, price) {
                    this.selectedService = { id, name, price };
                },
                
                selectCapster(id, name) {
                    this.selectedCapster = id ? { id, name } : null;
                    this.selectedCapsterName = name;
                },

                // Format Currency Rupiah
                formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
                },

                // Format Date UI
                formatDate(dateStr) {
                    if(!dateStr) return '';
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    return new Date(dateStr).toLocaleDateString('id-ID', options);
                },

                // Navigation Logic
                nextStep() {
                    if (this.currentStep < 4) {
                        this.currentStep++;
                    } else {
                        // Submit Logic (Nanti kita hubungkan ke Backend)
                        alert('Booking Logic akan diproses di langkah berikutnya!');
                    }
                },

                prevStep() {
                    if (this.currentStep > 1) this.currentStep--;
                },

                // Validation
                canProceed() {
                    if (this.currentStep === 1) return this.selectedService !== null;
                    if (this.currentStep === 2) return this.selectedCapsterName !== ''; // Capster boleh null (siapa saja), tapi harus dipilih opsinya
                    if (this.currentStep === 3) return this.date !== '' && this.timeSlot !== '';
                    if (this.currentStep === 4) return this.customerName !== '' && this.customerPhone !== '';
                    return false;
                }
            }
        }
    </script>
</body>
</html>