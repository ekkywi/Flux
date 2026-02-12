@extends("layouts.app")
@section("title", "Security Protocol")
@section("page_title", "Master Identity")
@section("page_subtitle", "Central cryptographic identity for console proxy.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Status --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">SSH Master Key</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        Algorithm: <span class="font-mono font-bold text-zinc-900">RSA-4096</span>
                    </p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 pl-2">
                {{-- Status Badge --}}
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg mr-2">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Active Identity</span>
                </div>

                {{-- Copy Button --}}
                <button class="flex items-center gap-2 px-3 py-2 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:border-zinc-300" onclick="copyPublicKey()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Copy Key</span>
                </button>

                {{-- Rotate Button --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white hover:bg-rose-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:shadow-rose-500/20 border border-transparent active:scale-95" onclick="confirmRotation()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Rotate</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- 2. PUBLIC KEY TERMINAL --}}
            <div class="xl:col-span-2">
                <div class="group relative flex flex-col h-full rounded-2xl bg-zinc-900 border border-zinc-800 shadow-xl overflow-hidden">
                    {{-- Terminal Header --}}
                    <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-800 bg-zinc-900/50">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-rose-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500/50"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/50"></div>
                            </div>
                            <span class="ml-2 text-[10px] font-mono text-zinc-500">id_rsa.pub</span>
                        </div>
                        <span class="text-[9px] font-bold text-zinc-600 uppercase tracking-widest">Read Only</span>
                    </div>

                    {{-- Key Content --}}
                    <div class="relative flex-1 p-6 overflow-hidden">
                        {{-- Watermark --}}
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                            <svg class="w-64 h-64 text-zinc-800/20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" />
                            </svg>
                        </div>

                        <pre class="relative z-10 font-mono text-xs text-indigo-300 leading-relaxed break-all whitespace-pre-wrap selection:bg-indigo-500/30 selection:text-white" id="publicKeyRaw">{{ $masterKey?->public_key ?? "ERROR: Identity not provisioned." }}</pre>
                    </div>

                    {{-- Bottom Bar --}}
                    <div class="px-4 py-2 bg-zinc-800/50 border-t border-zinc-800 flex items-center justify-between">
                        <span class="text-[9px] font-mono text-zinc-500">Fingerprint: SHA256:{{ substr(md5($masterKey?->public_key), 0, 16) }}...</span>
                        <span class="text-[9px] font-mono text-emerald-500">-- END KEY --</span>
                    </div>
                </div>
            </div>

            {{-- 3. META INFORMATION --}}
            <div class="space-y-6">
                {{-- Info Card --}}
                <div class="rounded-2xl bg-white border border-zinc-200 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 mb-4">Key Lifecycle</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-50">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide">Last Rotation</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-700">
                                    {{ $masterKey?->last_rotated_at ? \Carbon\Carbon::parse($masterKey->last_rotated_at)->format("d M Y") : "Never" }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-zinc-50">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide">Usage</span>
                            <span class="text-xs font-bold text-zinc-700">Console Proxy Auth</span>
                        </div>

                        <div class="bg-zinc-50 rounded-xl p-3 border border-zinc-100">
                            <div class="flex gap-2">
                                <svg class="w-4 h-4 text-zinc-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <p class="text-[10px] text-zinc-500 leading-relaxed">
                                    This public key must be present in the <code class="bg-zinc-200 px-1 rounded text-zinc-700 font-bold">~/.ssh/authorized_keys</code> file of every managed node to allow Flux connectivity.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warning Card --}}
                <div class="rounded-2xl bg-amber-50 border border-amber-100 p-5">
                    <div class="flex gap-3">
                        <div class="shrink-0 text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-amber-800 uppercase tracking-wide mb-1">Rotation Impact</h4>
                            <p class="text-[10px] text-amber-700/80 leading-relaxed">
                                Rotating this key will immediately revoke access to all servers. You must manually update the new key on all nodes to restore connectivity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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

            // Jika method bukan POST (misal PUT/DELETE), Laravel butuh _method field
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
        // 3. COPY TO CLIPBOARD
        // ==========================================
        window.copyPublicKey = function() {
            const keyText = document.getElementById('publicKeyRaw').innerText;
            navigator.clipboard.writeText(keyText).then(() => {
                fluxSwal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    icon: 'success',
                    title: 'Copied to Clipboard',
                    html: '<span class="text-xs text-zinc-500">Public key is ready to paste.</span>',
                    iconColor: '#10b981'
                });
            });
        };

        // ==========================================
        // 4. ROTATE ACTION
        // ==========================================
        window.confirmRotation = function() {
            fluxSwal.fire({
                title: 'Rotate Identity?',
                html: `
                    <p class="mb-2">This will generate a new <b>RSA-4096</b> keypair.</p>
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 text-left">
                        <p class="text-xs text-rose-700 font-bold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Warning: Connection Loss
                        </p>
                        <p class="text-[10px] text-rose-600/80 mt-1">
                            All managed servers will become unreachable until you manually update their authorized_keys with the new identity.
                        </p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Rotate Identity',
                confirmButtonClass: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 mx-2 mb-6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.submitForm('{{ route("admin.security.master-key.rotate") }}', 'POST');
                }
            });
        };
    </script>
@endpush
