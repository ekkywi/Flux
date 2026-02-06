<!DOCTYPE html>
<html class="h-full bg-slate-50" lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>@yield("title") | Flux Console</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <style>
        .flux-popup {
            border-radius: 24px !important;
            padding: 2.5rem !important;
            border: 1px solid #e2e8f0 !important;
        }

        .flux-title {
            font-weight: 800 !important;
            letter-spacing: -0.025em !important;
            color: #0f172a !important;
            font-size: 1.25rem !important;
        }

        .flux-content {
            font-size: 0.875rem !important;
            color: #64748b !important;
            line-height: 1.6 !important;
        }

        .flux-confirm-btn {
            background-color: #4f46e5 !important;
            color: #fff !important;
            border-radius: 12px !important;
            font-weight: 800 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            padding: 0.8rem 2rem !important;
            margin: 0.5rem !important;
            border: none !important;
        }

        .flux-cancel-btn {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border-radius: 12px !important;
            font-weight: 800 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            padding: 0.8rem 2rem !important;
            margin: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
        }

        .flux-toast {
            border-radius: 16px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 1rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
        }

        .flux-toast-success {
            border-left: 4px solid #10b981 !important;
            background: #fff !important;
        }

        .flux-toast-error {
            border-left: 4px solid #ef4444 !important;
            background: #fff !important;
        }

        .flux-toast-title {
            font-weight: 800 !important;
            font-size: 0.875rem !important;
            color: #0f172a !important;
        }

        .flux-toast-content {
            font-size: 0.75rem !important;
            color: #64748b !important;
        }
    </style>
</head>

<body class="h-full antialiased text-slate-900">
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-72 bg-[#0f172a] text-white flex flex-col flex-shrink-0 border-r border-white/10">
            <div class="p-8 flex items-center gap-4"> {{-- Gap dinaikkan ke 4 untuk nafas desain lebih lega --}}
                {{-- Ikon Box --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>

                {{-- Text Group --}}
                <div class="flex flex-col justify-center">
                    <div class="flex items-center">
                        <span class="font-black text-xl tracking-tighter text-white leading-none">
                            FLUX<span class="text-indigo-500 animate-pulse">_</span>
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-bold font-mono tracking-[0.15em] uppercase mt-1 leading-none">
                        Infrastructure Core
                    </span>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto mt-4">
                {{-- SECTION: MAIN --}}
                <div class="px-4 py-3">
                    <span class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600">Main Control</span>
                </div>

                <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("console.dashboard") ? "bg-indigo-600 text-white shadow-lg shadow-indigo-500/40" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("console.dashboard") }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("console.projects.*") ? "bg-indigo-600 text-white shadow-lg shadow-indigo-500/40" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("console.projects.index") }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2" />
                    </svg>
                    <span>Project Inventory</span>
                </a>

                @if (Auth::user()->role === "System Administrator")
                    {{-- SECTION: PRIVILEGED --}}
                    <div class="px-4 pt-6 pb-2">
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600">Privileged Access</span>
                    </div>

                    {{-- 1. Approvals --}}
                    <a class="flex items-center justify-between px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.approvals.*") ? "bg-indigo-600/10 text-indigo-400" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("admin.approvals.index") }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" />
                            </svg>
                            <span>Approvals</span>
                        </div>
                        @if (isset($pendingCount) && $pendingCount > 0)
                            <span class="flex h-5 min-w-[20px] px-1.5 items-center justify-center rounded-lg bg-rose-500 text-[10px] font-black text-white animate-pulse">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    {{-- 2. User Management --}}
                    <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.users.*") ? "bg-indigo-600/10 text-indigo-400" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("admin.users.index") }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" />
                        </svg>
                        <span>User Management</span>
                    </a>

                    {{-- 3. Server Inventory --}}
                    <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.servers.*") && !request()->routeIs("admin.cold-storage.*") ? "bg-indigo-600 text-white shadow-lg shadow-indigo-500/40" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("admin.servers.index") }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-width="2" />
                        </svg>
                        <span>Server Inventory</span>
                    </a>

                    {{-- 4. Cold Storage (Dropdown) --}}
                    <div class="space-y-1" id="coldStorageMenuWrapper">
                        <button class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.cold-storage.*") ? "bg-white/5 text-white" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" onclick="toggleColdStorageMenu()">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs("admin.cold-storage.*") ? "text-indigo-400" : "" }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>Cold Storage</span>
                            </div>
                            <svg class="w-3.5 h-3.5 transition-transform {{ request()->routeIs("admin.cold-storage.*") ? "rotate-180" : "" }}" fill="none" id="coldStorageArrow" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                        </button>

                        {{-- Dropdown Content dengan Parameter Dinamis --}}
                        <div class="{{ request()->routeIs("admin.cold-storage.*") ? "" : "hidden" }} pl-12 space-y-1 mt-1" id="coldStorageDropdown">
                            @foreach (["infrastructure" => "Infrastructure", "identity" => "Identity Vault", "projects" => "Project Archives"] as $type => $label)
                                <a class="flex items-center gap-3 py-2 text-[11px] font-extrabold uppercase tracking-widest transition-all 
        {{ request()->route("type") == $type ? "text-indigo-400" : "text-slate-500 hover:text-white" }}" href="{{ route("admin.cold-storage.index", $type) }}"> {{-- VARIABEL $type, BUKAN "infrastructure" --}}
                                    <div class="w-1 h-1 rounded-full {{ request()->route("type") == $type ? "bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.8)]" : "bg-slate-700" }}"></div>
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. Security Settings (Dropdown) --}}
                    <div class="space-y-1">
                        <button class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.security.*") ? "bg-white/5 text-white" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" onclick="toggleSecurityMenu()">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ request()->routeIs("admin.security.*") ? "text-indigo-400" : "" }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <span>Security Settings</span>
                            </div>
                            <svg class="w-3.5 h-3.5 transition-transform {{ request()->routeIs("admin.security.*") ? "rotate-180" : "" }}" fill="none" id="securityArrow" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                        </button>
                        <div class="{{ request()->routeIs("admin.security.*") ? "" : "hidden" }} pl-12 space-y-1 mt-1" id="securityDropdown">
                            <a class="flex items-center gap-3 py-2 text-[11px] font-extrabold uppercase tracking-widest transition-all {{ request()->routeIs("admin.security.master-key.*") ? "text-indigo-400" : "text-slate-500 hover:text-white" }}" href="{{ route("admin.security.master-key.index") }}">
                                <div class="w-1 h-1 rounded-full {{ request()->routeIs("admin.security.master-key.*") ? "bg-indigo-500" : "bg-slate-700" }}"></div>
                                Master SSH Key
                            </a>
                        </div>
                    </div>

                    {{-- 6. System Logs --}}
                    <a class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs("admin.logs.*") ? "bg-indigo-600/10 text-indigo-400" : "text-slate-400 hover:bg-white/5 hover:text-white" }}" href="{{ route("admin.logs.index") }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-width="2" />
                        </svg>
                        <span>System Logs</span>
                    </a>
                @endif
            </nav>

            {{-- Sidebar Footer Info --}}
            <div class="p-6 text-center">
                <span class="text-[10px] font-mono text-slate-600 uppercase tracking-widest">Core Console Ver. 1.0.0</span>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-hidden">
            {{-- TOPBAR --}}
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-10 flex-shrink-0 z-40">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest leading-none">Core Console</span>
                    <h2 class="text-lg font-bold text-slate-800 mt-1">@yield("page_title", "Dashboard")</h2>
                </div>
                <div class="flex items-center gap-5">
                    {{-- Notification Dropdown --}}
                    <div class="relative" id="notificationDropdownWrapper">
                        <button class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" id="notificationMenuBtn" onclick="toggleNotificationMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" />
                            </svg>
                            {{-- Indikator titik jika ada notifikasi --}}
                            @if (isset($pendingCount) && $pendingCount > 0)
                                <span class="absolute top-2 right-2.5 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></span>
                            @endif
                        </button>

                        {{-- Dropdown Menu Notifikasi --}}
                        <div class="absolute right-0 mt-3 w-80 bg-white border border-slate-200 rounded-2xl shadow-2xl shadow-slate-200/50 hidden z-50 overflow-hidden" id="notificationDropdown">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-widest">Notifications</span>
                                @if (isset($pendingCount) && $pendingCount > 0)
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[10px] font-bold rounded-full">{{ $pendingCount }} New</span>
                                @endif
                            </div>

                            <div class="max-h-[300px] overflow-y-auto">
                                {{-- Contoh Item Notifikasi: Access Request --}}
                                @if (isset($pendingCount) && $pendingCount > 0)
                                    <a class="flex items-start gap-4 p-4 hover:bg-slate-50 transition-colors border-b border-slate-50" href="{{ route("admin.approvals.index") }}">
                                        <div class="h-8 w-8 rounded-lg bg-indigo-100 flex-shrink-0 flex items-center justify-center text-indigo-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs font-bold text-slate-900">New Access Requests</p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">There are {{ $pendingCount }} users waiting for authorization.</p>
                                            <p class="text-[9px] text-indigo-500 font-bold mt-2 uppercase tracking-tighter italic">Review Required</p>
                                        </div>
                                    </a>
                                @else
                                    {{-- State Jika Kosong --}}
                                    <div class="p-8 text-center">
                                        <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="2" />
                                        </svg>
                                        <p class="text-xs font-medium text-slate-400">All caught up!</p>
                                    </div>
                                @endif
                            </div>

                            <a class="block p-3 text-center text-[10px] font-bold text-slate-400 hover:text-indigo-600 transition-colors border-t border-slate-100 uppercase tracking-widest" href="#">
                                View All Activity
                            </a>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-200 mx-1"></div>

                    {{-- User Profile Dropdown --}}
                    <div class="relative" id="userDropdownWrapper">
                        <button class="flex items-center gap-3 p-1 pr-3 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-200" id="userMenuBtn" onclick="toggleUserMenu()">
                            <div class="h-10 w-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-black shadow-lg">
                                {{ substr(Auth::user()->first_name, 0, 1) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-xs font-bold text-slate-900 leading-none">{{ Auth::user()->first_name }}</p>
                                <p class="text-[10px] text-slate-500 mt-1 font-mono uppercase tracking-tighter">{{ Auth::user()->role }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7" stroke-width="2" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div class="absolute right-0 mt-3 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl shadow-slate-200/50 hidden z-50 overflow-hidden" id="userDropdown">
                            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account ID</p>
                                <p class="text-xs font-mono text-slate-600 truncate mt-1">{{ Auth::user()->id }}</p>
                            </div>
                            <div class="p-2">
                                <a class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-all group" href="#">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" />
                                    </svg>
                                    My Profile
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-all group" href="#">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" />
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                                    </svg>
                                    Settings
                                </a>
                            </div>
                            <div class="p-2 bg-slate-50 border-t border-slate-100">
                                <form action="{{ route("logout") }}" method="POST">
                                    @csrf
                                    <button class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-bold text-rose-500 hover:bg-rose-100 rounded-lg transition-all group">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" />
                                        </svg>
                                        Terminate Session
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content Area --}}
            <section class="flex-1 overflow-y-auto p-10">
                @yield("content")
            </section>
        </main>
    </div>

    <script>
        const userDropdown = document.getElementById('userDropdown');
        const notificationDropdown = document.getElementById('notificationDropdown');

        function toggleUserMenu() {
            // Tutup notifikasi jika sedang terbuka
            notificationDropdown.classList.add('hidden');
            // Toggle user menu
            userDropdown.classList.toggle('hidden');
        }

        function toggleNotificationMenu() {
            // Tutup user menu jika sedang terbuka
            userDropdown.classList.add('hidden');
            // Toggle notification menu
            notificationDropdown.classList.toggle('hidden');
        }

        // Menutup dropdown jika klik di luar area
        window.onclick = function(event) {
            if (!event.target.closest('#userDropdownWrapper')) {
                userDropdown.classList.add('hidden');
            }
            if (!event.target.closest('#notificationDropdownWrapper')) {
                notificationDropdown.classList.add('hidden');
            }
        }

        function toggleColdStorageMenu() {
            const dropdown = document.getElementById('coldStorageDropdown');
            const arrow = document.getElementById('coldStorageArrow');

            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        function toggleSecurityMenu() {
            const dropdown = document.getElementById('securityDropdown');
            const arrow = document.getElementById('securityArrow');

            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    </script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            buttonsStyling: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session("success"))
            Toast.fire({
                icon: 'success',
                iconColor: '#10b981',
                title: 'Action Successful',
                html: '<span class="flux-toast-content">{{ session("success") }}</span>',
                customClass: {
                    popup: 'flux-toast flux-toast-success',
                    title: 'flux-toast-title'
                }
            });
        @endif

        @if (session("error") || $errors->any())
            Toast.fire({
                icon: 'error',
                iconColor: '#ef4444',
                title: 'System Alert',
                html: '<span class="flux-toast-content">{{ session("error") ?? $errors->first() }}</span>',
                customClass: {
                    popup: 'flux-toast flux-toast-error',
                    title: 'flux-toast-title'
                }
            });
        @endif
    </script>
    @stack("scripts")
</body>

</html>
