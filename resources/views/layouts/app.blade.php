<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace("_", "-", app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>@yield("title", "Flux Console") - Flux</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    {{-- Scripts & Styles --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(["resources/css/app.css", "resources/js/app.js", "resources/css/flux.css", "resources/js/flux.js"])
</head>

<body class="h-full overflow-hidden relative">

    {{-- APP CONTAINER --}}
    <div class="flex h-full w-full relative z-10 lg:p-4 gap-4" id="app-container">

        {{-- ================= SIDEBAR (Midnight Blue) ================= --}}
        <aside class="hidden lg:flex w-72 flex-col justify-between rounded-[2rem] bg-[#0B1120] text-white p-6 shadow-2xl shadow-blue-900/20 relative overflow-hidden shrink-0 border border-blue-900/30">
            {{-- Inner Glow (Static) --}}
            <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-transparent pointer-events-none"></div>

            <div class="relative z-10 flex flex-col h-full">
                {{-- Brand --}}
                <div class="flex items-center gap-3 mb-8 px-2 flex-shrink-0">
                    <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg shadow-blue-500/40">
                        <svg class="h-5 w-5 text-white" fill="none" stroke-width="2.5" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-white leading-none">Flux</h1>
                        <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mt-0.5">Enterprise</p>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="space-y-1.5 overflow-y-auto flex-1 pr-2 custom-scrollbar">

                    {{-- 1. CONSOLE AREA --}}
                    <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-3 mt-2">Console</p>

                    {{-- Dashboard --}}
                    <a class="relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-300 group overflow-hidden {{ request()->routeIs("console.dashboard") ? "bg-blue-500/20 text-white shadow-inner border border-blue-400/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("console.dashboard") }}">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <svg class="h-5 w-5 relative z-10 transition-transform group-hover:scale-110 {{ request()->routeIs("console.dashboard") ? "text-blue-300" : "" }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span class="relative z-10">Dashboard</span>
                    </a>

                    {{-- Projects --}}
                    <a class="relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all duration-300 group overflow-hidden {{ request()->routeIs("console.projects.*") ? "bg-blue-500/20 text-white shadow-inner border border-blue-400/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("console.projects.index") }}">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <svg class="h-5 w-5 relative z-10 transition-transform group-hover:scale-110 {{ request()->routeIs("console.projects.*") ? "text-blue-300" : "" }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        <span class="relative z-10">Projects</span>
                    </a>

                    {{-- 2. ADMIN AREA --}}
                    @if (Auth::user()->role === "System Administrator")
                        <div class="pt-8 pb-3 px-3 border-t border-white/5 mt-4">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">System Admin</span>
                        </div>

                        {{-- Approvals --}}
                        <a class="relative flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.approvals.*") ? "bg-blue-500/10 text-white border border-blue-500/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("admin.approvals.index") }}">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>Approvals</span>
                            </div>
                            @if (isset($pendingCount) && $pendingCount > 0)
                                <span class="flex h-5 min-w-[20px] px-1.5 items-center justify-center rounded bg-red-500 text-[10px] font-black text-white animate-pulse">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        {{-- Identity (User Management) --}}
                        <a class="relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.users.*") ? "bg-blue-500/10 text-white border border-blue-500/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("admin.users.index") }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span>Identity</span>
                        </a>

                        {{-- Server Inventory --}}
                        <a class="relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.servers.*") ? "bg-blue-500/10 text-white border border-blue-500/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("admin.servers.index") }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span>Servers</span>
                        </a>

                        {{-- Cold Storage (Dropdown) --}}
                        <div class="space-y-1">
                            <button class="w-full relative flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.cold-storage.*") ? "text-white" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" onclick="toggleSubmenu('cold-storage-submenu')">
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <span>Cold Storage</span>
                                </div>
                                <svg class="w-3 h-3 transition-transform {{ request()->routeIs("admin.cold-storage.*") ? "rotate-180" : "" }}" fill="none" id="arrow-cold-storage" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                            <div class="{{ request()->routeIs("admin.cold-storage.*") ? "" : "hidden" }} space-y-1 pl-4 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-blue-900/50" id="cold-storage-submenu">
                                @foreach (["infrastructure" => "Infrastructure", "identity" => "Identity Vault", "projects" => "Project Archives"] as $type => $label)
                                    <a class="block pl-8 py-2 text-xs font-bold uppercase tracking-widest transition-all {{ request()->route("type") == $type ? "text-blue-400" : "text-zinc-500 hover:text-zinc-300" }}" href="{{ route("admin.cold-storage.index", ["type" => $type]) }}">
                                        {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Security (Submenu) --}}
                        <div class="space-y-1">
                            <button class="w-full relative flex items-center justify-between gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.security.*") ? "text-white" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" onclick="toggleSubmenu('security-submenu')">
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <span>Security</span>
                                </div>
                                <svg class="w-3 h-3 transition-transform {{ request()->routeIs("admin.security.*") ? "rotate-180" : "" }}" fill="none" id="arrow-security" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                            <div class="{{ request()->routeIs("admin.security.*") ? "" : "hidden" }} space-y-1 pl-4 relative before:absolute before:left-6 before:top-0 before:bottom-0 before:w-px before:bg-blue-900/50" id="security-submenu">
                                <a class="block pl-8 py-2 text-xs font-bold uppercase tracking-widest transition-all {{ request()->routeIs("admin.security.master-key.*") ? "text-blue-400" : "text-zinc-500 hover:text-zinc-300" }}" href="{{ route("admin.security.master-key.index") }}">
                                    Master SSH Key
                                </a>
                            </div>
                        </div>

                        {{-- System Logs --}}
                        <a class="relative flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition-all {{ request()->routeIs("admin.logs.*") ? "bg-blue-500/10 text-white border border-blue-500/20" : "text-zinc-400 hover:text-white hover:bg-white/5" }}" href="{{ route("admin.logs.index") }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span>System Logs</span>
                        </a>
                    @endif
                </nav>
            </div>

            <div class="relative z-10 border-t border-white/5 pt-6 text-center flex-shrink-0">
                <span class="text-[10px] font-mono text-zinc-600 uppercase tracking-widest">v2.4.0 Stable</span>
            </div>
        </aside>

        {{-- ================= MAIN CONTENT (Lightweight Mode) ================= --}}
        {{-- Optimasi: bg-white/95 (bukan /70), hapus backdrop-blur-xl --}}
        <main class="flex-1 overflow-hidden flex flex-col bg-white/95 border-x border-white/60 shadow-2xl shadow-zinc-200/50 lg:rounded-[2rem] lg:border-y relative ring-1 ring-zinc-200/50">

            {{-- Mobile Header (Solid BG) --}}
            <div class="lg:hidden flex items-center justify-between p-4 border-b border-zinc-200 bg-white sticky top-0 z-30">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                        </svg>
                    </div>
                    <span class="font-bold text-zinc-900 text-lg">Flux</span>
                </div>
                <button class="p-2 text-zinc-600 hover:bg-zinc-100 rounded-lg transition-colors" onclick="toggleMobileMenu()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16m-7 6h7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </button>
            </div>

            {{-- Main Content Scrollable --}}
            <div class="flex-1 overflow-y-auto p-4 lg:p-10 scroll-smooth relative" id="main-scroll">

                <header class="mb-8 flex flex-col gap-6 md:flex-row md:items-start md:justify-between animate-fade-in-up relative z-40">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 leading-tight">@yield("page_title", "Dashboard")</h2>
                        <p class="mt-1 text-sm font-medium text-zinc-500">@yield("page_subtitle", "System Overview")</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="mr-2">@yield("actions")</div>

                        <div class="h-8 w-px bg-zinc-200 mx-1 hidden md:block"></div>

                        {{-- Notifications --}}
                        <div class="relative" id="notif-container">
                            <button class="relative p-2.5 rounded-full bg-white border border-zinc-200 text-zinc-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/10 transition-all group active:scale-95" onclick="toggleDropdown('notif-dropdown')">
                                <svg class="h-6 w-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                @if (isset($pendingCount) && $pendingCount > 0)
                                    <span class="absolute top-1.5 right-2 flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                    </span>
                                @endif
                            </button>
                            <div class="hidden absolute top-full right-0 mt-3 w-80 rounded-2xl bg-white border border-zinc-200 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden transition-all origin-top-right transform scale-95 opacity-0 z-[100]" id="notif-dropdown">
                                <div class="px-5 py-4 border-b border-zinc-100 flex justify-between items-center bg-zinc-50/80 backdrop-blur">
                                    <span class="text-sm font-bold text-zinc-900">Inbox</span>
                                    @if (isset($pendingCount) && $pendingCount > 0)
                                        <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase">{{ $pendingCount }} New</span>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if (isset($pendingCount) && $pendingCount > 0)
                                        <a class="block px-5 py-4 hover:bg-zinc-50 border-b border-zinc-50 transition-colors" href="{{ route("admin.approvals.index") }}">
                                            <div class="flex gap-3">
                                                <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                                    </svg></div>
                                                <div>
                                                    <p class="text-xs font-bold text-zinc-900">Access Requests</p>
                                                    <p class="text-[10px] text-zinc-500 mt-1">{{ $pendingCount }} users waiting for authorization.</p>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <div class="p-8 text-center">
                                            <p class="text-xs font-bold text-zinc-400">All caught up!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- User Profile --}}
                        <div class="relative" id="user-container">
                            <button class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-full bg-white border border-zinc-200 hover:border-blue-300 hover:shadow-md transition-all active:scale-95 group" onclick="toggleDropdown('user-dropdown')">
                                <img alt="" class="h-8 w-8 rounded-full border border-zinc-200" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? "Admin" }}&background=random&color=2563eb&background=EBF4FF">
                                <div class="hidden md:block text-left mr-1">
                                    <p class="text-xs font-bold text-zinc-700 group-hover:text-blue-600 transition-colors">{{ Auth::user()->first_name }}</p>
                                </div>
                                <svg class="h-4 w-4 text-zinc-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                            <div class="hidden absolute top-full right-0 mt-3 w-56 rounded-2xl bg-white border border-zinc-200 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] overflow-hidden transition-all origin-top-right transform scale-95 opacity-0 z-[100]" id="user-dropdown">
                                <div class="px-5 py-4 border-b border-zinc-100 bg-zinc-50/80">
                                    <p class="text-xs font-bold text-zinc-900">{{ Auth::user()->first_name }}</p>
                                    <p class="text-[10px] text-zinc-500 truncate">{{ Auth::user()->role }}</p>
                                </div>
                                <div class="p-2">
                                    <form action="{{ route("logout") }}" method="POST">
                                        @csrf
                                        <button class="w-full text-left px-3 py-2 text-xs font-bold text-red-500 rounded-lg hover:bg-red-50 transition-colors" type="submit">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </header>

                <div class="animate-fade-in-up delay-100 pb-20 relative z-0">
                    @yield("content")
                </div>
            </div>
        </main>
    </div>

    {{-- Mobile Sidebar --}}
    <div class="fixed inset-0 bg-[#0B1120]/80 backdrop-blur-sm z-40 hidden transition-opacity opacity-0" id="mobile-menu-overlay" onclick="toggleMobileMenu()"></div>
    <aside class="fixed top-0 left-0 bottom-0 w-72 bg-[#0B1120] text-white z-50 transform -translate-x-full transition-transform duration-300 ease-out shadow-2xl p-6 flex flex-col border-r border-blue-900/30" id="mobile-sidebar">
        <button class="absolute top-4 right-4 text-zinc-400" onclick="toggleMobileMenu()"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg></button>
        <div>
            <h1 class="text-xl font-bold mb-8">Flux Core</h1>
            <nav class="space-y-2">
                <a class="block text-sm font-bold text-white" href="{{ route("console.dashboard") }}">Dashboard</a>
                <a class="block text-sm font-bold text-white" href="{{ route("console.projects.index") }}">Projects</a>
            </nav>
        </div>
    </aside>

    {{-- 
    =========================================
    FLUX TOAST NOTIFICATION SYSTEM
    =========================================
    --}}

    <div @notify.window="notify($event.detail.message, $event.detail.type)" class="fixed top-6 right-6 z-[150] w-full max-w-sm" style="display: none;" x-data="{
        show: false,
        message: '',
        type: 'success', // success, error, warning
        timer: null,
        progress: 100,
    
        init() {
            // 1. Cek apakah ada Flash Message dari PHP (Laravel Session)
            @if (session("success")) this.notify('{{ session("success") }}', 'success');
            @elseif (session("error")) 
                this.notify('{{ session("error") }}', 'error');
            @elseif (session("warning")) 
                this.notify('{{ session("warning") }}', 'warning'); @endif
        },
    
        // Fungsi Utama untuk memunculkan Toast
        notify(msg, type = 'success') {
            this.message = msg;
            this.type = type;
            this.show = true;
            this.progress = 100;
            this.startTimer();
        },
    
        startTimer() {
            if (this.timer) clearInterval(this.timer);
            this.timer = setInterval(() => {
                this.progress -= 1;
                if (this.progress <= 0) this.close();
            }, 50); // 5 detik total
        },
    
        close() {
            this.show = false;
            clearInterval(this.timer);
        },
        pause() { clearInterval(this.timer); },
        resume() { this.startTimer(); }
    }" x-show="show" x-transition:enter-end="opacity-100 translate-y-0 translate-x-0 scale-100" x-transition:enter-start="opacity-0 translate-y-2 translate-x-2 scale-95" x-transition:enter="transition ease-out duration-300" x-transition:leave-end="opacity-0 translate-y-2 scale-95" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200">

        {{-- CONTAINER UTAMA --}}
        <div class="relative overflow-hidden rounded-2xl bg-white/90 backdrop-blur-xl border border-zinc-200 shadow-2xl shadow-zinc-200/50 p-4 pr-12">

            {{-- 1. PROGRESS BAR --}}
            <div class="absolute bottom-0 left-0 h-1 bg-zinc-100 w-full">
                <div :class="{
                    'bg-blue-600': type === 'success',
                    'bg-rose-500': type === 'error',
                    'bg-amber-500': type === 'warning'
                }" :style="'width: ' + progress + '%'" class="h-full transition-all duration-75 ease-linear">
                </div>
            </div>

            <div class="flex items-start gap-4">
                {{-- 2. ICON --}}
                <div class="relative flex-shrink-0">
                    <div :class="{
                        'bg-blue-400': type === 'success',
                        'bg-rose-400': type === 'error',
                        'bg-amber-400': type === 'warning'
                    }" class="absolute inset-0 rounded-full blur-lg opacity-40"></div>

                    <div :class="{
                        'bg-blue-50 border-blue-100 text-blue-600': type === 'success',
                        'bg-rose-50 border-rose-100 text-rose-600': type === 'error',
                        'bg-amber-50 border-amber-100 text-amber-600': type === 'warning'
                    }" class="relative h-10 w-10 rounded-xl flex items-center justify-center border">

                        {{-- Success Icon --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="type === 'success'">
                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                        </svg>

                        {{-- Error Icon --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="type === 'error'">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>

                        {{-- Warning Icon --}}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="type === 'warning'">
                            <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                </div>

                {{-- 3. TEXT CONTENT --}}
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="text-sm font-black uppercase tracking-wide text-zinc-900" x-text="type === 'error' ? 'System Alert' : (type === 'warning' ? 'Attention' : 'Success')"></h3>
                    <p class="text-xs font-medium text-zinc-500 mt-1 leading-relaxed" x-text="message"></p>
                </div>
            </div>

            {{-- 4. CLOSE BUTTON --}}
            <button @click="close()" class="absolute top-2 right-2 p-2 text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>
        </div>
    </div>

    @stack("scripts")
</body>

</html>
