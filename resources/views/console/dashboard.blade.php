@extends("layouts.app")
@section("title", "Overview")

@section("content")
    <div class="space-y-8">
        <header>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Welcome back, {{ $user->first_name }}!</h1>
            <p class="text-slate-500 text-sm font-medium">Your infrastructure is currently stable.</p>
        </header>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach ([
            "Active Nodes" => [$stats["nodes"], "text-indigo-600"],
            "System Uptime" => [$stats["uptime"], "text-emerald-600"],
            "Total Deploys" => [$stats["deployments"], "text-slate-900"],
            "Active Incidents" => [$stats["errors"], "text-rose-600"],
        ] as $label => $data)
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $label }}</p>
                    <p class="text-2xl font-black {{ $data[1] }}">{{ $data[0] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Placeholder for Node Management --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Active Nodes</h3>
                <button class="text-xs font-bold text-indigo-600 hover:underline">Provision New Node</button>
            </div>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-slate-300">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-width="2" />
                    </svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">No nodes provisioned yet. Start by adding your first server.</p>
            </div>
        </div>
    </div>
@endsection
