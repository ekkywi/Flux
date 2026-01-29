@extends("layouts.app")
@section("title", "Archived Personnel")
@section("page_title", "Security Vault")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-rose-500 mb-1">
                    <div class="h-1 w-6 bg-rose-500 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Archived Directory</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Archived Personnel</h1>
                <p class="text-xs text-slate-500 font-medium">Viewing <span class="text-rose-600 font-bold">{{ $archivedUsers->total() }} revoked entities</span> in the secure vault.</p>
            </div>

            <div class="flex items-center gap-4">
                <a class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200" href="{{ route("admin.users.index") }}">
                    ← Return to Directory
                </a>
            </div>
        </div>

        {{-- 2. ARCHIVED TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse grayscale-[0.5] hover:grayscale-0 transition-all">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identity</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Revocation Date</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Classification</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($archivedUsers as $user)
                            <tr class="hover:bg-rose-50/20 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-black border border-slate-200">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-400 line-through decoration-slate-300">{{ $user->first_name }} {{ $user->last_name }}</p>
                                            <p class="text-[10px] font-mono text-slate-300">{{ $user->username }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-rose-500">{{ $user->deleted_at->format("d M Y") }}</span>
                                        <span class="text-[10px] font-mono text-slate-400">{{ $user->deleted_at->format("H:i:s") }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{ $user->department }} // {{ $user->role }}</span>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <button class="px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all" onclick="toggleRestoreModal({{ $user }})">
                                        Restore Access
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-20 text-center" colspan="4">
                                    <p class="text-slate-400 text-xs font-medium uppercase tracking-widest italic">The vault is empty. No archived personnel found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RESTORE CONFIRMATION MODAL --}}
    <div class="fixed inset-0 z-[110] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4" id="restoreModal">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl border border-emerald-100">
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 bg-emerald-50 rounded-full flex items-center justify-center mb-6 border border-emerald-100">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Restore Access?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    You are re-authorizing <span class="text-emerald-600 font-bold font-mono" id="restore_target_name"></span>. This identity will be returned to the active directory.
                </p>
            </div>

            <form action="" class="space-y-4" id="restoreForm" method="POST">
                @csrf
                @method("PATCH")
                <div class="flex flex-col gap-3">
                    <button class="w-full px-6 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-200" type="submit">
                        Confirm Restoration
                    </button>
                    <button class="w-full px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleRestoreModal()" type="button">
                        Abort
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push("scripts")
        <script>
            function toggleRestoreModal(user = null) {
                const modal = document.getElementById('restoreModal');
                const form = document.getElementById('restoreForm');
                const target = document.getElementById('restore_target_name');

                if (user) {
                    form.action = `/admin/users/${user.id}/restore`;
                    target.textContent = user.username.toUpperCase();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }
        </script>
    @endpush
@endsection
