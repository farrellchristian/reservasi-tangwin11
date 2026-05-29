<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tangwin Cut - The Gentleman's Hairstudio</title>
    <link rel="icon" href="{{ asset('images/logo_tangwin_white.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Manrope:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* CUSTOM CSS UTILITIES */
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: #050505;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .font-display {
            font-family: 'Italiana', serif;
        }

        /* Noise Texture Overlay (Rahasia tampilan mahal) */
        .bg-noise {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        /* Smooth Image Zoom Animation */
        @keyframes slowZoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }

        .animate-slow-zoom {
            animation: slowZoom 20s infinite alternate;
        }

        /* Custom Selection Color */
        ::selection {
            background-color: #C6A87C;
            /* Gold muted */
            color: #000;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #C6A87C;
        }
    </style>
</head>

<body class="text-gray-300 antialiased" x-data="{ scrolled: false, mobileMenu: false }"
    @scroll.window="scrolled = (window.pageYOffset > 50)">

    {{-- ── ANNOUNCEMENT MODAL ───────────────────── --}}
    @if($announcement)
    <div x-data="{
            showAnnouncement: true,
            closeModal() {
                this.showAnnouncement = false;
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
         }"
         x-init="
            // Lock scroll on open
            document.body.style.paddingRight = (window.innerWidth - document.documentElement.clientWidth) + 'px';
            document.body.style.overflow = 'hidden';
         "
         x-show="showAnnouncement" x-cloak
         class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal()"></div>

        {{-- Modal Card --}}
        <div class="relative w-full max-w-md bg-[#0e0e0e] border border-white/10 rounded-2xl shadow-2xl overflow-hidden"
             x-show="showAnnouncement"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">

            {{-- Close Button --}}
            <button @click="closeModal()"
                    class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors group">
                <svg class="w-4 h-4 text-white/60 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Image --}}
            @if($announcement->image)
            <div class="w-full max-h-[300px] overflow-hidden bg-black flex items-center justify-center">
                <img src="data:image/jpeg;base64,{{ $announcement->image }}"
                     alt="{{ $announcement->title }}"
                     class="max-w-full max-h-[300px] object-contain">
            </div>
            @endif

            {{-- Content --}}
            <div class="p-6">
                {{-- Gold accent line --}}
                <div class="h-[2px] w-10 bg-[#C6A87C] mb-4"></div>

                <h3 class="text-xl font-display text-white mb-3">{{ $announcement->title }}</h3>

                @if($announcement->content)
                <p class="text-sm text-gray-400 leading-relaxed whitespace-pre-line">{{ $announcement->content }}</p>
                @endif

                {{-- Close action --}}
                <button @click="closeModal()"
                        class="mt-5 w-full py-3 bg-white/5 border border-white/10 rounded-lg text-xs uppercase tracking-widest text-white/70 hover:text-white hover:bg-white/10 hover:border-[#C6A87C]/40 transition-all duration-300">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif


    <div class="bg-noise"></div>

    <nav :class="scrolled ? 'bg-[#050505]/80 backdrop-blur-md py-4 border-b border-white/5' : 'bg-transparent py-8'"
        class="fixed w-full z-50 transition-all duration-500 ease-in-out">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="flex justify-between items-center">
                <a href="#" class="z-50 relative group">
                    <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo"
                        class="h-16 md:h-20 w-auto">
                </a>

                <div class="hidden md:flex items-center space-x-12">
                    <a href="#home"
                        class="text-xs uppercase tracking-[0.2em] hover:text-[#C6A87C] transition-colors duration-300 text-white/80">Studio</a>
                    <a href="#services"
                        class="text-xs uppercase tracking-[0.2em] hover:text-[#C6A87C] transition-colors duration-300 text-white/80">Services</a>
                    <a href="#capsters"
                        class="text-xs uppercase tracking-[0.2em] hover:text-[#C6A87C] transition-colors duration-300 text-white/80">Team</a>
                    <a href="{{ route('booking.check.form') }}"
                        class="text-xs uppercase tracking-[0.2em] hover:text-[#C6A87C] transition-colors duration-300 text-white/80">Cek
                        Pesanan</a>

                    <a href="{{ route('booking.form') }}"
                        class="group relative px-8 py-3 bg-white/5 border border-white/10 overflow-hidden transition-all hover:border-[#C6A87C]/50">
                        <div
                            class="absolute inset-0 w-0 bg-[#C6A87C] transition-all duration-[250ms] ease-out group-hover:w-full opacity-10">
                        </div>
                        <span
                            class="relative text-xs font-bold uppercase tracking-widest text-white group-hover:text-[#C6A87C]">Book
                            Now</span>
                    </a>
                </div>

                <button @click="mobileMenu = !mobileMenu" class="md:hidden z-50 text-white focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full"
        class="fixed inset-0 z-40 bg-[#0a0a0a] flex items-center justify-center md:hidden">
        <div class="flex flex-col space-y-8 text-center">
            <a @click="mobileMenu = false" href="#home"
                class="text-3xl font-display text-white hover:text-[#C6A87C]">Studio</a>
            <a @click="mobileMenu = false" href="#services"
                class="text-3xl font-display text-white hover:text-[#C6A87C]">Menu</a>
            <a @click="mobileMenu = false" href="#capsters"
                class="text-3xl font-display text-white hover:text-[#C6A87C]">Stylists</a>
            <a href="{{ route('booking.check.form') }}"
                class="text-3xl font-display text-white hover:text-[#C6A87C]">Cek Pesanan</a>
            <a href="{{ route('booking.form') }}"
                class="px-8 py-4 bg-[#C6A87C] text-black font-bold uppercase tracking-widest mt-8">Book Now</a>
        </div>
    </div>

    <section id="home" class="relative h-screen w-full overflow-hidden flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-[#050505] via-[#050505]/70 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#050505] via-transparent to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1503951914875-452162b7f30a?q=80&w=2070&auto=format&fit=crop"
                class="w-full h-full object-cover animate-slow-zoom opacity-60" alt="">
        </div>

        <div class="relative z-20 w-full max-w-[1400px] mx-auto px-6 lg:px-12 pt-20">
            <div class="max-w-3xl">
                <div class="overflow-hidden mb-6">
                    <p class="text-[#C6A87C] uppercase tracking-[0.4em] text-xs font-bold" data-aos="fade-up"
                        data-aos-duration="1000">Established 2024</p>
                </div>

                <h1 class="text-6xl md:text-8xl lg:text-9xl text-white leading-[0.9] mb-8" data-aos="fade-up"
                    data-aos-delay="200" data-aos-duration="1000">
                    House of <br>
                    <span class="italic font-light text-gray-400 ml-4 md:ml-16">Handsome.</span>
                </h1>

                <p class="text-lg md:text-xl text-gray-400 font-light max-w-lg leading-relaxed mb-12 border-l border-white/20 pl-6"
                    data-aos="fade-up" data-aos-delay="400">
                    Pengalaman grooming eksklusif di mana detail adalah segalanya.
                    Potongan rambut bukan sekadar rutinitas, tapi pernyataan diri.
                </p>

                <div class="flex flex-col sm:flex-row gap-6" data-aos="fade-up" data-aos-delay="600">
                    <a href="{{ route('booking.form') }}"
                        class="group relative px-10 py-5 bg-[#C6A87C] overflow-hidden">
                        <div
                            class="absolute inset-0 w-full h-full bg-white/20 scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-500">
                        </div>
                        <span class="relative text-black font-bold uppercase tracking-[0.2em] text-sm">Make
                            Appointment</span>
                    </a>
                    <a href="#location"
                        class="group flex items-center gap-3 px-6 py-4 text-white hover:text-[#C6A87C] transition-colors">
                        <span class="uppercase tracking-[0.2em] text-sm">View Location</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-2 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute bottom-12 right-12 hidden md:flex flex-col items-center gap-4 z-20 mix-blend-difference">
            <span
                class="text-[10px] uppercase tracking-widest text-white rotate-90 origin-right translate-x-2">Scroll</span>
            <div class="w-[1px] h-24 bg-white/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1/2 bg-white animate-moveDown"></div>
            </div>
        </div>
    </section>

    <div class="bg-[#C6A87C] py-3 overflow-hidden whitespace-nowrap relative z-30 transform -rotate-1 scale-105">
        <div class="inline-block animate-marquee">
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• PREMIUM HAIRCUT</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• HOT TOWEL SHAVE</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• BEARD TRIM</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• GENTLEMAN'S GROOMING</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• PREMIUM HAIRCUT</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• HOT TOWEL SHAVE</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• BEARD TRIM</span>
            <span class="text-black font-bold uppercase tracking-widest mx-8 text-sm">• GENTLEMAN'S GROOMING</span>
        </div>
    </div>

    <section id="services" class="py-32 bg-[#050505] relative">
        <div class="max-w-[1200px] mx-auto px-6 lg:px-12">
            <div class="flex flex-col md:flex-row items-end justify-between mb-24" data-aos="fade-up">
                <div>
                    <h2 class="text-5xl md:text-7xl text-white font-display mb-4">Our Services</h2>
                    <div class="h-[1px] w-24 bg-[#C6A87C]"></div>
                </div>
                <p class="text-gray-500 max-w-sm mt-6 md:mt-0 text-right leading-relaxed">
                    Setiap layanan mencakup konsultasi gaya, pencucian rambut, pijat kepala ringan, dan styling dengan
                    produk premium.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <div class="lg:col-span-7 space-y-0">
                    @forelse($stores as $store)
                        {{-- Store Header --}}
                        <div class="pt-6 pb-2" data-aos="fade-up">
                            <div class="flex items-center gap-4">
                                <span
                                    class="text-[10px] uppercase tracking-[0.4em] text-[#C6A87C] font-bold opacity-70">Studio</span>
                                <div class="h-[1px] flex-1 bg-white/5"></div>
                            </div>
                            <h4 class="text-3xl md:text-4xl font-display text-white/90 mt-2 mb-0">{{ $store->store_name }}
                            </h4>
                        </div>

                        {{-- Services for this store --}}
                        @foreach($store->services as $index => $service)
                            <div class="group border-b border-white/10 py-7 pl-4 hover:border-white/30 transition-colors cursor-default hover:bg-white/[0.02]"
                                data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                                <div class="flex justify-between items-baseline mb-2">
                                    <h3
                                        class="text-xl md:text-2xl text-white/90 font-display group-hover:pl-3 transition-all duration-500">
                                        {{ $service->service_name }}</h3>
                                    <span class="text-lg text-[#C6A87C] font-manrope ml-4 shrink-0">Rp
                                        {{ number_format($service->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <p class="text-xs text-gray-600 group-hover:text-gray-500 transition-colors max-w-md">
                                        {{ $service->description ?? 'Perawatan detail untuk hasil maksimal.' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                    @empty
                        <div class="py-8 text-gray-500">Belum ada data layanan.</div>
                    @endforelse
                </div>

                <div class="hidden lg:block lg:col-span-5 relative">
                    <div class="sticky top-32">
                        <div class="relative h-[600px] w-full overflow-hidden rounded-sm">
                            <div class="absolute inset-0 bg-black/20 z-10"></div>
                            <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?q=80&w=2070&auto=format&fit=crop"
                                class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700"
                                alt="Service Mood">

                            <div class="absolute bottom-8 left-8 z-20">
                                <p class="text-white text-xs uppercase tracking-widest mb-2">Featured Product</p>
                                <p class="text-2xl font-display text-white">Premium Pomade</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="capsters" class="py-32 bg-[#0a0a0a] border-t border-white/5">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="text-center mb-20" data-aos="fade-up">
                <span class="text-[#C6A87C] uppercase tracking-[0.3em] text-xs font-bold">The Artisans</span>
                <h2 class="text-5xl md:text-6xl text-white font-display mt-4">Master Stylists</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-8 lg:gap-12">
                @forelse($capsters as $capster)
                    <div class="group relative w-full md:w-[350px]" data-aos="fade-up">
                        <div class="relative h-[450px] overflow-hidden bg-[#111]">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80 z-10">
                            </div>

                            <img src="{{ $capster->photo ? 'data:image/jpeg;base64,' . $capster->photo : ($capster->photo_path ? asset('storage/' . $capster->photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($capster->employee_name) . '&background=1a1a1a&color=fff&size=800') }}"
                                alt="{{ $capster->employee_name }}"
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 grayscale group-hover:grayscale-0">

                            <div
                                class="absolute bottom-0 left-0 w-full p-8 z-20 translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <h3 class="text-3xl font-display text-white mb-1">{{ $capster->employee_name }}</h3>
                                
                                {{-- Instagram Username --}}
                                @if($capster->instagram_username)
                                    <p class="text-white/60 text-[11px] font-medium tracking-wider mb-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-75">
                                        <span class="text-tang-gold">@</span>{{ $capster->instagram_username }}
                                    </p>
                                @endif

                                <p
                                    class="text-[#C6A87C] text-xs uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                                    {{ $capster->position ?? 'Senior Barber' }}</p>
                            </div>
                        </div>
                        <div
                            class="absolute inset-0 border border-white/10 pointer-events-none group-hover:border-[#C6A87C]/50 transition-colors duration-500">
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Data stylist sedang dimuat.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="relative py-40 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1593702295094-aea22597af65?q=80&w=2070&auto=format&fit=crop"
                class="w-full h-full object-cover opacity-30 grayscale">
            <div class="absolute inset-0 bg-black/60"></div>
        </div>

        <div class="relative z-10 text-center px-6" data-aos="zoom-in">
            <h2 class="text-5xl md:text-8xl font-display text-white mb-8">Ready to look sharp?</h2>
            <p class="text-gray-400 mb-10 max-w-xl mx-auto">Jadwal kami terisi dengan cepat. Amankan waktumu sekarang
                dan rasakan perbedaannya.</p>

            <a href="{{ route('booking.form') }}"
                class="inline-block px-12 py-5 bg-[#C6A87C] text-black font-bold uppercase tracking-widest hover:bg-white transition-colors duration-300 transform hover:-translate-y-1">
                Book Appointment
            </a>
        </div>
    </section>

    <footer id="location" class="bg-[#050505] pt-24 border-t border-white/10">
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16 pb-20">

                <div class="space-y-6">
                    <img src="{{ asset('images/logo_tangwin_white.png') }}" alt="Tangwin Logo"
                        class="h-24 md:h-32 w-auto">
                    <p class="text-gray-500 text-sm leading-relaxed max-w-xs">
                        Destinasi premier untuk pria modern. Kami menggabungkan teknik klasik dengan gaya kontemporer
                        untuk menciptakan tampilan terbaik Anda.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/tangwin_cut/" target="_blank"
                            class="w-10 h-10 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-[#C6A87C] hover:text-black transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="https://api.whatsapp.com/send/?phone=6288216626044&text&type=phone_number&app_absent=0"
                            target="_blank"
                            class="w-10 h-10 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-[#C6A87C] hover:text-black transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .01 5.403.006 12.039a11.82 11.82 0 001.576 5.861L0 24l6.101-1.6c1.889 1.031 4.041 1.575 6.23 1.576h.006c6.637 0 12.042-5.405 12.046-12.041a11.8 11.8 0 00-3.522-8.514"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 lg:col-span-2">
                    <div>
                        <h4 class="text-white uppercase tracking-widest text-xs font-bold mb-6">Location</h4>
                        <p class="text-gray-400 mb-2">Jl. Tlogo Poso, Palebon</p>
                        <p class="text-gray-400 mb-4">Semarang, Jawa Tengah</p>
                        <a href="https://maps.google.com" target="_blank"
                            class="text-[#C6A87C] text-xs uppercase tracking-widest hover:text-white transition-colors border-b border-[#C6A87C] pb-1">Get
                            Directions</a>
                    </div>
                    <div>
                        <h4 class="text-white uppercase tracking-widest text-xs font-bold mb-6">Hours</h4>
                        <ul class="space-y-2 text-gray-400 text-sm">
                            <li class="flex justify-between"><span class="w-24">Mon - Fri</span> <span
                                    class="text-white">10:00 - 21:00</span></li>
                            <li class="flex justify-between"><span class="w-24">Saturday</span> <span
                                    class="text-white">10:00 - 22:00</span></li>
                            <li class="flex justify-between"><span class="w-24">Sunday</span> <span
                                    class="text-[#C6A87C]">Closed</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div
                class="border-t border-white/5 py-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-600 uppercase tracking-widest">
                <p>&copy; 2024 Tangwin Cut Studio.</p>
                <p>Designed with Precision.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true, // animates only once on scroll
            offset: 100, // offset (in px) from the original trigger point
            duration: 800, // duration of animation
            easing: 'ease-out-cubic', // easing function
        });
    </script>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            animation: marquee 20s linear infinite;
        }

        /* Line Vertical Animation */
        @keyframes moveDown {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(100%);
            }
        }

        .animate-moveDown {
            animation: moveDown 2s cubic-bezier(0.77, 0, 0.175, 1) infinite;
        }
    </style>

</body>

</html>