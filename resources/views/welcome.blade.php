<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racket Arena - Premium Sports Facility</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#4f46e5 0.5px, transparent 0.5px), radial-gradient(#4f46e5 0.5px, #f8fafc 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }
    </style>
</head>
<body class="font-sans text-slate-800 bg-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-blue-600 text-white p-2 rounded-lg">
                    <i class="fa-solid fa-shuttlecock text-xl"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">Racket Arena</span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="#courts" class="hover:text-blue-600 transition">Our Courts</a>
                <a href="#features" class="hover:text-blue-600 transition">Features</a>
                <a href="#pricing" class="hover:text-blue-600 transition">Pricing</a>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/customer-dashboard') }}" class="font-semibold text-slate-600 hover:text-slate-900">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-slate-900">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                Sign Up
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
        <div class="absolute inset-0 hero-pattern z-0"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-6 border border-blue-100">
                🚀 Now open for bookings
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 tracking-tight mb-6">
                Master Your Game <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">On Premier Courts</span>
            </h1>
            <p class="text-lg text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Experience badminton like never before. Real-time availability checks, instant booking confirmation, and professional-grade flooring designed for champions.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-full hover:bg-blue-700 shadow-xl shadow-blue-200/50">
                    Book a Court Now
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
                <a href="#courts" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-slate-700 transition-all duration-200 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300">
                    View Facilities
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-slate-900 py-12">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-white mb-1">12</div>
                <div class="text-slate-400 text-sm font-medium uppercase">Pro Courts</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-1">24/7</div>
                <div class="text-slate-400 text-sm font-medium uppercase">Online Booking</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-1">500+</div>
                <div class="text-slate-400 text-sm font-medium uppercase">Active Players</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-1">4.9</div>
                <div class="text-slate-400 text-sm font-medium uppercase">User Rating</div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Why Choose Racket Arena?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">We provide a seamless experience from the moment you log in to the moment you step off the court.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-6">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Instant Booking</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Say goodbye to phone calls. Check real-time availability and secure your slot in seconds with our live dashboard.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl mb-6">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Premium Flooring</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Play on BWF-certified synthetic mats that offer excellent grip and shock absorption to prevent injuries.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 rounded-2xl p-8 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl mb-6">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Community Events</h3>
                    <p class="text-slate-500 leading-relaxed">
                        Join our monthly tournaments and weekend leagues. Connect with other players and improve your rank.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courts Section -->
    <section id="courts" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Our Courts</h2>
                    <p class="text-slate-500">Choose the surface that fits your playstyle.</p>
                </div>
                <a href="{{ route('login') }}" class="hidden md:flex items-center gap-2 text-blue-600 font-bold hover:underline">
                    View Schedule <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="h-48 bg-emerald-600 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></div>
                        <i class="fa-solid fa-leaf text-white/30 text-8xl transform -rotate-12 translate-y-4"></i>
                        <span class="absolute bottom-4 left-4 bg-white/90 backdrop-blur text-emerald-800 text-xs font-bold px-2 py-1 rounded">Synthetic Grass</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Court A & B</h3>
                        <p class="text-slate-500 text-sm mt-2">Soft surface with medium bounce. Ideal for beginners and endurance training.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="h-48 bg-blue-600 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></div>
                        <i class="fa-solid fa-table text-white/30 text-8xl transform -rotate-12 translate-y-4"></i>
                        <span class="absolute bottom-4 left-4 bg-white/90 backdrop-blur text-blue-800 text-xs font-bold px-2 py-1 rounded">Pro Acrylic</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Court C (Tournament)</h3>
                        <p class="text-slate-500 text-sm mt-2">Professional non-slip acrylic surface. High pace, consistent bounce for matches.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
                    <div class="h-48 bg-orange-500 flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></div>
                        <i class="fa-solid fa-shoe-prints text-white/30 text-8xl transform -rotate-12 translate-y-4"></i>
                        <span class="absolute bottom-4 left-4 bg-white/90 backdrop-blur text-orange-800 text-xs font-bold px-2 py-1 rounded">Wooden Parquet</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Court D (Classic)</h3>
                        <p class="text-slate-500 text-sm mt-2">Traditional wooden flooring. Excellent shock absorption for high-intensity play.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-shuttlecock text-blue-600 text-2xl"></i>
                <span class="font-bold text-xl text-slate-900">Racket Arena</span>
            </div>
            <div class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} Racket Arena. All rights reserved.
            </div>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                    <i class="fa-brands fa-twitter"></i>
                </a>
            </div>
        </div>
    </footer>

</body>
</html>