@extends("layouts.app")
@section("title", "Security Protocol")
@section("page_title", "Security Infrastructure")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. STREAMLINED HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Asymmetric Encryption</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Master SSH Key</h1>
                <p class="text-xs text-slate-500 font-medium">
                    Central identity for <span class="text-indigo-600 font-bold">Flux Console Proxy</span> communications.
                </p>
            </div>

            {{-- Encryption Stats --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="text-center min-w-[60px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Algorithm</span>
                    <span class="text-sm font-black text-slate-900 font-mono">RSA</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[60px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Bits</span>
                    <span class="text-sm font-black text-slate-900 font-mono">4096</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[60px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Status</span>
                    <span class="text-sm font-black text-emerald-600 font-mono italic">ACTIVE</span>
                </div>
            </div>
        </div>

        {{-- 2. MASTER KEY INTERFACE --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            {{-- Public Key Display --}}
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-[2.5rem] border border-slate-200 overflow-hidden shadow-sm flex flex-col h-full">
                    <div class="px-10 py-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight">Public Key</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Append this to authorized_keys on your nodes</p>
                        </div>
                        <button class="p-3 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm group" onclick="copyPublicKey()">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" stroke-width="2" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-10 flex-1">
                        <div class="w-full h-full bg-slate-900 rounded-3xl p-8 relative group overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 opacity-10">
                                <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" />
                                </svg>
                            </div>
                            <code class="text-indigo-300 font-mono text-xs leading-relaxed break-all block relative z-10" id="public_key_text">
                                {{ $masterKey?->public_key ?? "CRYPTO_ERROR: Identity not established." }}
                            </code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Key Management & Meta --}}
            <div class="space-y-6">
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-10 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-4 block">Infrastructure Integrity</span>
                        <h3 class="text-xl font-black text-slate-900 mb-6">Key Management</h3>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between py-3 border-b border-slate-50">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Last Rotation</span>
                                <span class="text-xs font-black text-slate-700">
                                    {{ $masterKey?->last_rotated_at ? \Carbon\Carbon::parse($masterKey->last_rotated_at)->format("d M Y") : "NEVER" }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-slate-50">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Fingerprint</span>
                                <span class="text-[10px] font-mono font-bold text-indigo-600">SHA256:{{ substr(md5($masterKey?->public_key), 0, 12) }}...</span>
                            </div>
                        </div>

                        <button class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3" onclick="toggleRotateModal()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2.5" />
                            </svg>
                            Rotate Security Key
                        </button>
                    </div>
                </div>

                {{-- Security Warning Card --}}
                <div class="bg-amber-50 rounded-[2.5rem] border border-amber-100 p-8">
                    <div class="flex gap-4">
                        <div class="h-10 w-10 rounded-xl bg-amber-500 flex-shrink-0 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2.5" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-amber-900 uppercase tracking-tight">Security Advisory</h4>
                            <p class="text-[10px] text-amber-700 mt-1 leading-relaxed">
                                Rotating the Master Key will immediately <span class="font-bold">revoke access</span> to all managed servers until the new public key is deployed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROTATION CONFIRMATION MODAL (Using your Revoke Modal Style) --}}
    <div class="fixed inset-0 z-[110] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4" id="rotateModal">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl border border-rose-100 transform transition-all">
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 bg-rose-50 rounded-full flex items-center justify-center mb-6 border border-rose-100">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Rotate Key?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    You are initiating a <span class="text-rose-600 font-bold underline">CORE IDENTITY ROTATION</span>. This action cannot be undone and requires physical key updates on all nodes.
                </p>
            </div>

            <form action="{{ route("admin.security.master-key.rotate") }}" method="POST">
                @csrf
                <div class="flex flex-col gap-3">
                    <button class="w-full px-6 py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-200" type="submit">
                        Confirm Identity Rotation
                    </button>
                    <button class="w-full px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleRotateModal()" type="button">
                        Abort Protocol
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push("scripts")
        <script>
            function toggleRotateModal() {
                const modal = document.getElementById('rotateModal');
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }

            function copyPublicKey() {
                const keyText = document.getElementById('public_key_text').innerText;
                navigator.clipboard.writeText(keyText).then(() => {
                    Toast.fire({
                        icon: 'success',
                        iconColor: '#10b981',
                        title: 'Identity Copied',
                        html: '<span class="flux-toast-content">Public key has been moved to clipboard.</span>',
                        customClass: {
                            popup: 'flux-toast flux-toast-success',
                            title: 'flux-toast-title'
                        }
                    });
                });
            }
        </script>
    @endpush
@endsection
