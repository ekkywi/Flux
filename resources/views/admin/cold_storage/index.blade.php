@extends("layouts.app")

@section("title", ucfirst($type) . " Vault")
@section("page_title", ucfirst($type) . " Data Vault")
@section("page_subtitle", "Long-term archival storage for deleted " . $type . " entities.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Context Info --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-800 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none capitalize">{{ $type }} Vault</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        <span class="font-bold text-zinc-900" id="countDisplay">{{ count($archives) }}</span> archives found
                    </p>
                </div>
            </div>

            {{-- Right: Actions & Search --}}
            <div class="flex flex-col md:flex-row gap-2 pl-2 w-full md:w-auto">

                {{-- Live Search --}}
                <div class="relative group w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-indigo-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <input class="w-full pl-9 pr-4 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-[11px] font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none placeholder:text-zinc-400" id="vaultSearch" placeholder="Search filename, ID, or name..." type="text">
                </div>

                <div class="h-8 w-px bg-zinc-200 mx-1 hidden md:block"></div>

                {{-- Return Button --}}
                @php
                    $route = match ($type) {
                        "infrastructure" => route("admin.servers.index"),
                        "identity" => route("admin.users.index"),
                        default => route("console.dashboard"),
                    };
                @endphp
                <a class="flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:border-zinc-300 whitespace-nowrap" href="{{ $route }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Back</span>
                </a>
            </div>
        </div>

        {{-- 2. ARCHIVE GRID (Card Layout) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="archiveGrid">
            @forelse($archives as $item)
                @php
                    // Helper color based on env
                    $envColor = match ($item["env"] ?? "N/A") {
                        "production" => "text-rose-600 bg-rose-50 border-rose-100",
                        "staging" => "text-amber-600 bg-amber-50 border-amber-100",
                        "development" => "text-blue-600 bg-blue-50 border-blue-100",
                        default => "text-zinc-600 bg-zinc-100 border-zinc-200",
                    };
                @endphp

                <div class="archive-item group relative flex flex-col bg-white rounded-2xl border border-zinc-200 shadow-sm hover:border-indigo-300 hover:shadow-indigo-500/10 transition-all duration-200 p-5">

                    {{-- Header: Icon & Name --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="h-10 w-10 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-400 group-hover:text-indigo-600 group-hover:bg-indigo-50 transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-zinc-900 truncate group-hover:text-indigo-700 transition-colors archive-name" title="{{ $item["name"] }}">{{ $item["name"] }}</h3>
                                <p class="text-[10px] font-mono text-zinc-400 truncate archive-file">{{ $item["filename"] }}</p>
                            </div>
                        </div>

                        {{-- Environment Badge --}}
                        <span class="shrink-0 px-2 py-0.5 rounded border text-[9px] font-black uppercase tracking-widest {{ $envColor }}">
                            {{ $item["env"] }}
                        </span>
                    </div>

                    {{-- Content: Meta Info --}}
                    <div class="flex-1 space-y-3 border-t border-zinc-50 pt-3">
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-400 font-medium">Identifier</span>
                            <span class="font-mono font-bold text-zinc-600 archive-id">{{ \Illuminate\Support\Str::limit($item["identifier"], 20) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-400 font-medium">Log Events</span>
                            <span class="font-bold text-zinc-900 bg-zinc-100 px-1.5 rounded">{{ $item["logs_count"] }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-zinc-400 font-medium">Archived At</span>
                            <span class="font-mono text-zinc-500">{{ \Carbon\Carbon::parse($item["timestamp"])->format("d M Y H:i") }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-5 pt-3 border-t border-zinc-100 flex items-center gap-2">
                        <button class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-zinc-50 hover:bg-indigo-50 border border-zinc-200 hover:border-indigo-200 text-zinc-600 hover:text-indigo-700 rounded-xl text-[10px] font-bold uppercase tracking-wide transition-all" onclick="viewDetail('{{ addslashes(json_encode($item["raw_data"])) }}', '{{ $item["filename"] }}')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                            Inspect
                        </button>

                        {{-- Restore Shortcut --}}
                        <button class="p-2 text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 rounded-xl transition-all" onclick="quickRestore('{{ $item["filename"] }}', '{{ $item["name"] }}')" title="Quick Restore">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                    <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center text-zinc-300 mb-4 shadow-sm border border-zinc-100">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="text-zinc-900 font-bold text-lg">Vault Empty</h3>
                    <p class="text-zinc-500 text-sm mt-1">No archived {{ $type }} data found.</p>
                </div>
            @endforelse
        </div>

        {{-- No Results State (Hidden by default) --}}
        <div class="hidden py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50" id="noResults">
            <p class="text-zinc-400 font-bold text-sm uppercase tracking-widest">No matching archives found</p>
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
                confirmButton: 'bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6',
                cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                actions: 'gap-3'
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

            document.body.appendChild(form);
            fluxSwal.fire({
                title: 'Processing...',
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            form.submit();
        };

        // ==========================================
        // 3. LIVE SEARCH LOGIC
        // ==========================================
        document.getElementById('vaultSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.archive-item');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.querySelector('.archive-name').textContent.toLowerCase();
                const file = item.querySelector('.archive-file').textContent.toLowerCase();
                const id = item.querySelector('.archive-id').textContent.toLowerCase();

                if (name.includes(query) || file.includes(query) || id.includes(query)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update Counter
            document.getElementById('countDisplay').textContent = visibleCount;

            // Show/Hide No Results
            const noRes = document.getElementById('noResults');
            if (visibleCount === 0 && items.length > 0) {
                noRes.classList.remove('hidden');
            } else {
                noRes.classList.add('hidden');
            }
        });

        // ==========================================
        // 4. INSPECT / RESTORE LOGIC (Using FluxSwal)
        // ==========================================
        window.viewDetail = function(rawData, filename) {
            try {
                const data = JSON.parse(rawData);
                const identity = data.identity;
                const type = "{{ $type }}";

                // Construct Identity HTML
                let identityHtml = '';
                if (type === 'infrastructure') {
                    identityHtml = `
                        <div class="grid grid-cols-2 gap-2 text-left bg-zinc-50 p-3 rounded-xl border border-zinc-100">
                            <div><span class="text-[9px] uppercase text-zinc-400 font-bold">IP Address</span><div class="font-mono text-xs font-bold text-zinc-700">${identity.ip_address}</div></div>
                            <div><span class="text-[9px] uppercase text-zinc-400 font-bold">SSH</span><div class="font-mono text-xs text-zinc-600">${identity.ssh_user}@p:${identity.ssh_port}</div></div>
                        </div>`;
                } else if (type === 'identity') {
                    identityHtml = `
                        <div class="text-left bg-zinc-50 p-3 rounded-xl border border-zinc-100">
                            <span class="text-[9px] uppercase text-zinc-400 font-bold">Email</span>
                            <div class="font-bold text-xs text-zinc-700">${identity.email}</div>
                            <span class="text-[9px] uppercase text-zinc-400 font-bold mt-2 block">Role</span>
                            <div class="text-xs text-zinc-600">${identity.role}</div>
                        </div>`;
                }

                // Construct Logs HTML
                let logsHtml = '<div class="max-h-40 overflow-y-auto space-y-1 pr-1 custom-scrollbar">';
                if (data.audit_trail && data.audit_trail.length > 0) {
                    data.audit_trail.forEach(log => {
                        logsHtml += `
                            <div class="flex items-center justify-between text-[10px] py-1 border-b border-zinc-100 last:border-0">
                                <span class="font-bold text-indigo-600 uppercase w-24 truncate">${log.action.replace(/_/g, ' ')}</span>
                                <span class="font-mono text-zinc-400">${new Date(log.created_at).toLocaleDateString()}</span>
                            </div>`;
                    });
                } else {
                    logsHtml += '<div class="text-center text-zinc-300 italic py-2 text-xs">No audit trails recorded</div>';
                }
                logsHtml += '</div>';

                // Restore URL
                const restoreUrl = "{{ route("admin.cold-storage.restore", [$type, ":filename"]) }}".replace(':filename', filename);
                const downloadUrl = "{{ route("admin.cold-storage.download", [$type, ":filename"]) }}".replace(':filename', filename);

                // Show Swal
                fluxSwal.fire({
                    title: 'Archive Inspection',
                    html: `
                        <div class="flex flex-col gap-4">
                            <div class="text-center">
                                <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-zinc-900">${identity.name || identity.username}</h3>
                                <p class="text-xs text-zinc-500 font-mono">${filename}</p>
                            </div>

                            ${identityHtml}

                            <div class="text-left">
                                <span class="text-[9px] font-black text-zinc-300 uppercase tracking-widest block mb-1">Audit Snapshot</span>
                                ${logsHtml}
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <a href="${downloadUrl}" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-zinc-200 text-zinc-600 rounded-xl text-xs font-bold uppercase hover:bg-zinc-50 transition-colors">
                                    Download JSON
                                </a>
                                <button onclick="window.submitForm('${restoreUrl}', 'POST')" class="flex items-center justify-center gap-2 px-3 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase hover:bg-indigo-700 transition-colors">
                                    Restore Data
                                </button>
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true
                });

            } catch (e) {
                console.error(e);
                fluxSwal.fire('Error', 'Invalid Archive Data', 'error');
            }
        };

        window.quickRestore = function(filename, name) {
            const restoreUrl = "{{ route("admin.cold-storage.restore", [$type, ":filename"]) }}".replace(':filename', filename);

            fluxSwal.fire({
                title: 'Quick Restore?',
                html: `Restore <b>${name}</b> to active inventory?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Restore',
                confirmButtonClass: 'bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-indigo-700 mx-2 mb-6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.submitForm(restoreUrl, 'POST');
                }
            });
        }
    </script>
@endpush
