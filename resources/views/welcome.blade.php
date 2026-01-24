<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flux PaaS - Ready to Deploy</title>
    
    {{-- PANGGILAN CSS/JS VITE YANG KRUSIAL --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center">
    
    {{-- Kartu Utama di Tengah Layar --}}
    <div class="max-w-md w-full mx-auto">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            
            {{-- Header Kartu dengan Gradien Modern --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-8 text-center">
                <h1 class="text-4xl font-extrabold text-white tracking-tight">
                    FLUX <span class="text-indigo-200">PaaS</span>
                </h1>
                <p class="text-indigo-100 mt-2 text-sm font-medium">
                    Deployment Tanpa CLI. Semudah Klik.
                </p>
            </div>

            {{-- Badan Kartu --}}
            <div class="p-8 space-y-6">
                
                {{-- Status Badge --}}
                <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg border border-green-100">
                    <div class="flex items-center space-x-3">
                        {{-- Ikon Berkedip --}}
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-green-800 font-semibold">System Status</span>
                    </div>
                    <span class="px-3 py-1 text-xs font-bold text-green-700 uppercase bg-green-200 rounded-full">
                        OPERATIONAL
                    </span>
                </div>
                
                {{-- Informasi Stack --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg border text-center">
                        <div class="text-2xl font-bold text-gray-800">PHP 8.4</div>
                        <div class="text-xs text-gray-500 uppercase font-medium tracking-wider">Engine</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border text-center">
                        <div class="text-2xl font-bold text-gray-800">Laravel 12</div>
                        <div class="text-xs text-gray-500 uppercase font-medium tracking-wider">Framework</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border text-center">
                        <div class="text-2xl font-bold text-gray-800">PostgreSQL 18</div>
                        <div class="text-xs text-gray-500 uppercase font-medium tracking-wider">Database</div>
                    </div>
                     <div class="bg-gray-50 p-4 rounded-lg border text-center">
                        <div class="text-2xl font-bold text-gray-800">Octane/Franken</div>
                        <div class="text-xs text-gray-500 uppercase font-medium tracking-wider">Server</div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div>
                    <button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Mulai Buat Project Baru
                    </button>
                </div>

            </div>
             {{-- Footer Kartu --}}
            <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Running on Docker • {{ now()->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    </div>

</body>
</html>