@extends("layouts.app")
@section("title", "Archived Nodes")
@section("page_title", "Decommissioned Entities")
@section("page_subtitle", "Repository of retired infrastructure nodes.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Title --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 border border-zinc-200 text-zinc-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">Node Archive</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        Viewing <span class="font-bold text-zinc-900">{{ $servers->total() }}</span> decommissioned nodes
                    </p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 pl-2">
                <a class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:border-zinc-300" href="{{ route("admin.servers.index") }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Back to Inventory</span>
                </a>
            </div>
        </div>

        {{-- 2. ARCHIVED LIST --}}
        <div class="grid grid-cols-1 gap-3">
            @forelse ($servers as $server)
                <div class="group relative flex flex-col md:flex-row md:items-center gap-4 rounded-2xl bg-zinc-50/50 p-4 shadow-sm border border-zinc-200 hover:bg-white hover:border-indigo-200 hover:shadow-indigo-500/10 transition-all duration-200">

                    {{-- Identity (Grayscale until hover) --}}
                    <div class="flex items-center gap-4 md:w-[300px] shrink-0 opacity-75 group-hover:opacity-100 transition-opacity">
                        <div class="h-10 w-10 rounded-xl bg-zinc-200 border border-zinc-300 flex items-center justify-center text-sm font-black text-zinc-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm font-bold text-zinc-600 group-hover:text-zinc-900 truncate transition-colors decoration-zinc-400 line-through group-hover:no-underline">
                                {{ $server->name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-[10px] font-mono font-bold text-zinc-500 tracking-tight decoration-zinc-400 line-through group-hover:no-underline group-hover:text-zinc-700">
                                    {{ $server->ip_address }}
                                </span>
                                <span class="text-zinc-300 text-[10px] shrink-0">•</span>
                                <span class="text-[10px] font-mono text-zinc-400 truncate min-w-0">ID: {{ substr($server->id, 0, 8) }}...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Decommission Info --}}
                    <div class="flex-1 flex flex-col md:flex-row md:items-center gap-2 md:gap-8 md:px-6 md:border-l md:border-zinc-200/50">
                        <div class="flex flex-col min-w-[120px]">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Archived Date</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">
                                    {{ $server->deleted_at->format("d M Y") }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Last Config</span>
                            <span class="text-xs font-medium text-zinc-500 mt-0.5">{{ $server->environment }} // {{ $server->ssh_user }}@p:{{ $server->ssh_port }}</span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="flex items-center justify-end md:pl-6 md:border-l md:border-zinc-200/50 mt-4 md:mt-0">
                        <button class="flex items-center gap-2 px-3 py-2 bg-white border border-zinc-200 text-zinc-400 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm group-hover:border-zinc-300" onclick="confirmRestore('{{ $server->id }}', '{{ $server->name }}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            <span>Restore</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                    <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center text-zinc-300 mb-4 shadow-sm border border-zinc-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="text-zinc-900 font-bold text-lg">Empty Archive</h3>
                    <p class="text-zinc-500 text-sm mt-1">No decommissioned nodes found in the records.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $servers->links() }}
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // ==========================================
        // 1. CONFIGURATION
        // ==========================================
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        const fluxSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                htmlContainer: 'text-zinc-500 text-sm px-6 pb-6',
                confirmButton: 'bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-indigo-700 transition-colors shadow-sm mx-2 mb-6',
                cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
            },
            buttonsStyling: false
        });

        // ==========================================
        // 2. HELPER: SUBMIT FORM
        // ==========================================
        window.submitForm = function(action, method) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            form.style.display = 'none';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            if (method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
            }

            document.body.appendChild(form);
            fluxSwal.fire({
                title: 'Processing...',
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            form.submit();
        };

        // ==========================================
        // 3. RESTORE ACTION
        // ==========================================
        window.confirmRestore = function(id, name) {
            fluxSwal.fire({
                title: 'Re-integrate Node?',
                html: `Server <b class="text-zinc-900">${name}</b> will be moved back to active inventory.`,
                icon: 'question',
                iconColor: '#4f46e5', // indigo-600
                showCancelButton: true,
                confirmButtonText: 'Confirm Restoration',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.submitForm(`/admin/servers/${id}/restore`, 'PATCH');
                }
            });
        };
    </script>
@endpush
