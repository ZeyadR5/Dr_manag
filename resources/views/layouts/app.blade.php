<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'عيادة د. أحمد نفادي') - نظام إدارة العيادة</title>

    <!-- Google Fonts: Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind Play Standalone -->
    <script src="{{ asset('tailwind.js') }}"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        clinic: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            dark: '#0f172a',
                        },
                        tealmed: {
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Card Transitions */
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            border-radius: 1rem;
        }

        .stat-card-gradient-1 {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }
        .stat-card-gradient-2 {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        }
        .stat-card-gradient-3 {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        }
        .stat-card-gradient-4 {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .sidebar, .topbar {
                display: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 flex flex-col antialiased selection:bg-clinic-500 selection:text-white">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobileBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <div class="flex flex-1 min-h-screen">
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="sidebar fixed md:sticky top-0 right-0 h-screen w-72 bg-slate-900 text-slate-200 z-50 flex flex-col shadow-2xl transition-transform duration-300 ease-in-out translate-x-full md:translate-x-0 border-l border-slate-800">
            <!-- Sidebar Header / Branding -->
            <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-clinic-600 to-tealmed-500 flex items-center justify-center text-white shadow-lg shadow-clinic-500/20 ring-2 ring-white/10">
                        <i class="bi bi-hospital text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="font-black text-lg text-white leading-tight">د. أحمد نفادي</h1>
                        <p class="text-xs text-clinic-300 font-medium">نظام إدارة العيادة والأشعة</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- Quick Doctor Badge -->
            <div class="mx-4 my-3 p-3 rounded-xl bg-slate-800/60 border border-slate-700/50 flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-clinic-900 border border-clinic-600 flex items-center justify-center text-clinic-200 font-bold">
                        <i class="bi bi-person-fill text-lg"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs text-slate-400">الطبيب المعالج</p>
                    <p class="text-sm font-bold text-white truncate">د. أحمد نفادي</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                <div class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">القائمة الرئيسية</div>

                @php
                    $currentUri = request()->path();
                @endphp

                <a href="{{ url('reserv.php') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ request()->is('reserv.php') ? 'bg-gradient-to-r from-clinic-600 to-clinic-700 text-white shadow-lg shadow-clinic-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="bi bi-calendar2-check text-lg {{ request()->is('reserv.php') ? 'text-white' : 'text-clinic-400' }}"></i>
                    <span>جدول الحجوزات والمواعيد</span>
                </a>

                <a href="{{ url('new_appoint.php') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ request()->is('new_appoint.php') ? 'bg-gradient-to-r from-clinic-600 to-clinic-700 text-white shadow-lg shadow-clinic-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="bi bi-calendar-plus text-lg {{ request()->is('new_appoint.php') ? 'text-white' : 'text-teal-400' }}"></i>
                    <span>حجز موعد جديد</span>
                    <span class="mr-auto text-[10px] bg-teal-500/20 text-teal-300 font-bold px-2 py-0.5 rounded-full border border-teal-500/30">جديد</span>
                </a>

                <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">قسم الأشعة والتصوير</div>

                <a href="{{ url('index.php') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ request()->is('index.php') || request()->is('/') ? 'bg-gradient-to-r from-clinic-600 to-clinic-700 text-white shadow-lg shadow-clinic-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <i class="bi bi-search-heart text-lg {{ request()->is('index.php') || request()->is('/') ? 'text-white' : 'text-sky-400' }}"></i>
                    <span>بحث فحوصات الأشعة (PACS)</span>
                </a>

                <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">إجراءات سريعة</div>

                <a href="{{ url('export_excel2.php') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm text-slate-300 hover:bg-slate-800/80 hover:text-white transition-all">
                    <i class="bi bi-file-earmark-spreadsheet text-lg text-emerald-400"></i>
                    <span>تصدير الحجوزات (Excel)</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 text-xs text-slate-400">
                <div class="flex items-center justify-between mb-1">
                    <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        الخادم متصل
                    </span>
                    <span class="text-[11px] text-slate-400">v2.5</span>
                </div>
                <p class="text-[11px] text-slate-400 truncate">Orthanc PACS & Clinic DB</p>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            <!-- Topbar Header -->
            <header class="topbar sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm px-4 lg:px-8 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                    <div>
                        <h2 class="text-lg md:text-xl font-black text-slate-800 flex items-center gap-2">
                            @yield('page-title', 'لوحة التحكم')
                        </h2>
                        <p class="text-xs text-slate-500 hidden sm:block">@yield('page-subtitle', 'مرحباً بك في نظام إدارة عيادة الدكتور أحمد نفادي')</p>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Live Time & Date Badge -->
                    <div class="hidden lg:flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                        <i class="bi bi-clock-history text-clinic-600"></i>
                        <span id="liveClock">--:--:--</span>
                        <span class="text-slate-300">|</span>
                        <i class="bi bi-calendar3 text-clinic-600"></i>
                        <span>{{ now('Africa/Cairo')->translatedFormat('d F Y') }}</span>
                    </div>

                    <!-- Quick Add Appointment Button -->
                    <a href="{{ url('new_appoint.php') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-clinic-600 to-tealmed-600 hover:from-clinic-700 hover:to-tealmed-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-clinic-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>حجز كشف جديد</span>
                    </a>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="main-content flex-1 p-4 md:p-6 lg:p-8 max-w-7xl mx-auto w-full">
                <!-- Session Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold">
                                <i class="bi bi-check-lg text-lg"></i>
                            </div>
                            <div class="font-bold text-sm sm:text-base">{{ session('success') }}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @if(session('error') || (isset($errorMsg) && $errorMsg))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold">
                                <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                            </div>
                            <div class="font-bold text-sm sm:text-base">{{ session('error') ?? $errorMsg }}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 p-1">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="no-print mt-auto py-4 px-6 border-t border-slate-200/80 bg-white text-center text-xs text-slate-400">
                <p>© {{ date('Y') }} عيادة د. أحمد نفادي — جميع الحقوق محفوظة | نظام إدارة العيادات الطبية</p>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle Sidebar on Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Live Clock
        function updateLiveClock() {
            const clockEl = document.getElementById('liveClock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('ar-EG', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                });
            }
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();
    </script>
    @stack('scripts')
</body>
</html>
