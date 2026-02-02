@extends("layouts.app")
@section("title", "Archived Nodes")
@section("page_title", "Infrastructure Trash")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">
        {{-- 1. HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-rose-600 mb-1">
                    <div class="h-1 w-6 bg-rose-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Decommissioned Entities</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Archived Nodes</h1>
                <p class="text-xs text-slate-500 font-medium">Review technical specifications before <span class="text-indigo-600 font-bold">re-integrating</span> nodes to the core.</p>
            </div>

            <a class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 flex items-center gap-2" href="{{ route("admin.servers.index") }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2.5" />
                </svg>
                Back to Inventory
            </a>
        </div>

        {{-- 2. DETAILED ARCHIVE TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Node Name</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Point</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Environment</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Archived Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Operation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($servers as $server)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                {{-- Node Name & ID --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-black border border-slate-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-500 truncate tracking-tight">{{ $server->name }}</p>
                                            <p class="text-[9px] font-mono text-slate-400 truncate uppercase">ID: {{ substr($server->id, 0, 8) }}...</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Technical Access --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-600 font-mono">{{ $server->ip_address }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $server->ssh_user }} @ Port {{ $server->ssh_port }}</span>
                                    </div>
                                </td>

                                {{-- Environment Badge (Dampened version) --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-0.5 rounded-md border border-slate-200 bg-slate-50 text-slate-400 text-[8px] font-black uppercase tracking-widest">
                                        {{ $server->environment }}
                                    </span>
                                </td>

                                {{-- Decommission Timestamp --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-rose-600">{{ $server->deleted_at->format("d M Y") }}</span>
                                        <span class="text-[10px] font-mono text-slate-400 uppercase">{{ $server->deleted_at->format("H:i:s T") }}</span>
                                    </div>
                                </td>

                                {{-- Restore Action --}}
                                <td class="px-6 py-4 text-right">
                                    <button class="px-4 py-2 bg-white border border-indigo-200 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm" onclick="openRestoreModal('{{ $server->id }}', '{{ $server->name }}')" type="button">
                                        Restore Node
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-20 text-center" colspan="5">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-200">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Infrastructure archive is empty</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $servers->links() }}
            </div>
        </div>
    </div>

    {{-- RESTORE CONFIRMATION MODAL --}}
    <div class="fixed inset-0 z-[110] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4" id="restoreModal">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl border border-indigo-100 transform transition-all">
            <div class="mb-8 text-center">
                {{-- Icon Re-Integrate --}}
                <div class="mx-auto h-16 w-16 bg-indigo-50 rounded-full flex items-center justify-center mb-6 border border-indigo-100">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Re-integrate?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Server <span class="text-indigo-600 font-bold" id="restoreServerNameDisplay"></span> will be returned to active inventory.
                </p>
            </div>

            <form id="restoreForm" method="POST">
                @csrf
                @method("PATCH")
                <div class="flex flex-col gap-3">
                    <button class="w-full px-6 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200" type="submit">
                        Confirm Restoration
                    </button>
                    <button class="w-full px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleRestoreModal()" type="button">
                        Abort
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        function toggleRestoreModal() {
            const modal = document.getElementById('restoreModal');
            modal.classList.toggle('hidden');
            modal.classList.toggle('flex');
        }

        function openRestoreModal(id, name) {
            const form = document.getElementById('restoreForm');
            form.action = `/admin/servers/${id}/restore`;

            document.getElementById('restoreServerNameDisplay').textContent = name;

            toggleRestoreModal();
        }
    </script>
@endpush
