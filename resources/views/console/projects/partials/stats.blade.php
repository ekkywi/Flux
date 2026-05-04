<div class="bg-[#0B1120] rounded-[2rem] p-6 text-white relative overflow-hidden shadow-xl shadow-blue-900/10 border border-blue-900/30">
    <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600 rounded-full blur-[60px] opacity-20 -mr-10 -mt-10"></div>
    <div class="relative z-10">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full {{ $project->health_percent >= 90 ? "bg-emerald-500" : ($project->health_percent >= 50 ? "bg-yellow-500" : "bg-red-500") }} animate-pulse"></div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400">System Health</h3>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="p-3 rounded-xl bg-white/5 border border-white/5 flex flex-col justify-center">
                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Total Nodes</p>
                <p class="text-2xl font-black text-white">{{ $project->total_nodes }}</p>
            </div>

            <div class="p-3 rounded-xl bg-white/5 border border-white/5 relative flex flex-col justify-center">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Active / Health</p>
                    <span class="text-[10px] font-black {{ $project->health_color }} bg-white/5 px-2 py-0.5 rounded-md">
                        {{ $project->health_percent }}%
                    </span>
                </div>
                <p class="text-2xl font-black {{ $project->health_color }}">
                    {{ $project->active_nodes }} <span class="text-sm font-medium text-zinc-500">Running</span>
                </p>
            </div>
        </div>
    </div>
</div>
