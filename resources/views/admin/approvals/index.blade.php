@extends("layouts.app")
@section("title", "Approval Center")
@section("page_title", "Access Pipeline")

@section("content")
    {{-- Container diubah menjadi space-y-8 tanpa max-w agar lebar konten sama dengan Dashboard --}}
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. STREAMLINED HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Security Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Access Control</h1>
                <p class="text-xs text-slate-500 font-medium">Provisioning identities for <span class="text-indigo-600 font-bold">{{ $pendingRequests->count() }} pending requests</span>.</p>
            </div>

            {{-- Compact Stats --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Active</span>
                    <span class="text-sm font-black text-slate-900">{{ \App\Models\User::where("is_active", true)->count() }}</span>
                </div>

                <div class="w-px h-6 bg-slate-100"></div>

                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Pending</span>
                    <span class="text-sm font-black {{ $pendingRequests->count() > 0 ? "text-rose-600 animate-pulse" : "text-slate-900" }}">
                        {{ $pendingRequests->count() }}
                    </span>
                </div>

                <div class="w-px h-6 bg-slate-100"></div>

                <div class="text-center min-w-[80px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Server Time</span>
                    <span class="text-xs font-mono font-bold text-indigo-600" id="serverClock">00:00:00</span>
                </div>
            </div>
        </div>

        {{-- 2. COMPACT PIPELINE LIST --}}
        <div class="grid grid-cols-1 gap-4">
            @forelse($pendingRequests as $request)
                <div class="group bg-white border border-slate-200 rounded-[1.5rem] p-2 transition-all duration-300 hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-500/5">

                    <div class="flex flex-col lg:flex-row lg:items-center">

                        {{-- SECTION A: USER IDENTITY & REQUEST TYPE (280px) --}}
                        <div class="lg:w-[280px] p-4 flex flex-col bg-slate-50/50 rounded-[1.2rem] border border-transparent group-hover:bg-indigo-50/30 transition-all duration-300">
                            {{-- Request Type Badge --}}
                            <div class="mb-3">
                                @php
                                    $typeClasses = match ($request->request_type) {
                                        \App\Enums\ApprovalType::ACCOUNT_REQUEST => "bg-indigo-100 text-indigo-700 border-indigo-200",
                                        \App\Enums\ApprovalType::RESET_PASSWORD => "bg-rose-100 text-rose-700 border-rose-200",
                                        \App\Enums\ApprovalType::SERVER_ACCESS => "bg-amber-100 text-amber-700 border-amber-200",
                                        default => "bg-slate-100 text-slate-700 border-slate-200",
                                    };
                                @endphp
                                <span class="px-2 py-0.5 border {{ $typeClasses }} text-[8px] font-black uppercase tracking-widest rounded-md">
                                    {{ $request->request_type->label() }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="relative flex-shrink-0">
                                    <div class="h-10 w-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-300 shadow-sm group-hover:border-indigo-200 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold truncate tracking-tight text-sm text-slate-900">{{ $request->user->first_name }} {{ $request->user->last_name }}</h3>
                                    <p class="text-[10px] font-mono text-slate-400 truncate">{{ $request->user->email }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION B: JUSTIFICATION (Flexible) --}}
                        <div class="flex-1 px-8 py-4 lg:py-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Reason / Justification</span>
                                <div class="h-px flex-1 bg-slate-100"></div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium italic line-clamp-2 pr-6 border-l-2 border-slate-100 pl-4">
                                "{{ $request->justification ?? "No justification provided." }}"
                            </p>
                        </div>

                        {{-- SECTION C: ACTION PANEL (320px) --}}
                        <div class="lg:w-[320px] flex items-center gap-4 pl-6 lg:border-l border-slate-100">

                            {{-- Meta Info --}}
                            <div class="hidden xl:flex flex-col text-right min-w-[100px]">
                                <span class="text-[8px] font-bold text-slate-300 uppercase tracking-widest leading-none">Received</span>
                                <span class="text-[10px] font-mono font-bold text-slate-500 mt-1">{{ $request->created_at->format("d.m.y // H:i") }}</span>
                            </div>

                            {{-- Proportional Action Buttons --}}
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                {{-- Reject Form --}}
                                <form action="{{ route("admin.approvals.reject", $request) }}" class="reject-form w-full" method="POST">
                                    @csrf
                                    <input class="reject-reason-input" name="reason" type="hidden">
                                    <button class="btn-reject w-full flex items-center justify-center gap-1.5 h-10 px-3 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all duration-300 group/reject overflow-hidden" data-user="{{ $request->user->first_name }} {{ $request->user->last_name }}" type="button">
                                        {{-- Tambah Ikon Reject agar simetris --}}
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 group-hover/reject:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
                                        </svg>
                                        <span class="text-[9px] font-black uppercase tracking-wider">Reject</span>
                                    </button>
                                </form>

                                {{-- Approve Form --}}
                                <form action="{{ route("admin.approvals.approve", $request) }}" class="approval-form w-full" method="POST">
                                    @csrf
                                    <button class="btn-approve w-full h-10 px-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-300 shadow-sm hover:shadow-indigo-500/20 flex items-center justify-center gap-1.5 group/btn overflow-hidden" data-user="{{ $request->user->first_name }} {{ $request->user->last_name }}" type="button">
                                        <span class="text-[9px] font-black uppercase tracking-wider">Authorize</span>
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13 5l7 7-7 7M5 12h14" stroke-width="2.5" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2rem] p-24 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M5 13l4 4L19 7" stroke-width="3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none">Pipeline Clear</h3>
                    <p class="text-slate-500 text-xs mt-2">All identities have been authorized.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // 1. Clock Sync
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-GB', {
                hour12: false
            });
            const clockElement = document.getElementById('serverClock');
            if (clockElement) clockElement.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. SweetAlert (English UI)
        document.querySelectorAll('.btn-approve').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.approval-form');
                const userName = this.getAttribute('data-user');

                Swal.fire({
                    title: 'Grant System Access?',
                    html: `<div class="flux-content text-slate-600 text-sm">You are granting full system privileges to <b>${userName}</b>. This action will be logged.</div>`,
                    icon: 'warning',
                    iconColor: '#4f46e5',
                    showCancelButton: true,
                    confirmButtonText: 'Authorize Now',
                    cancelButtonText: 'Abort',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'flux-popup',
                        title: 'flux-title',
                        confirmButton: 'flux-confirm-btn',
                        cancelButton: 'flux-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Authorizing...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            customClass: {
                                popup: 'flux-popup',
                                title: 'flux-title'
                            }
                        });
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.btn-reject').forEach(button => {
            button.addEventListener('click', async function() {
                const form = this.closest('.reject-form');
                const reasonInput = form.querySelector('.reject-reason-input');
                const userName = this.getAttribute('data-user');

                const {
                    value: text,
                    isConfirmed
                } = await Swal.fire({
                    title: 'Reject Request?',
                    html: `<div class="flux-content text-slate-600 text-sm">Please provide a reason for rejecting <b>${userName}</b>'s request.</div>`,
                    input: 'textarea',
                    inputPlaceholder: 'Type your reason here...',
                    inputAttributes: {
                        'aria-label': 'Type your reason here'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Confirm Rejection',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#e11d48', // rose-600
                    customClass: {
                        popup: 'flux-popup',
                        title: 'flux-title',
                        confirmButton: 'flux-confirm-btn bg-rose-600',
                        cancelButton: 'flux-cancel-btn'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write a reason!'
                        }
                        if (value.length < 5) {
                            return 'Reason must be at least 5 characters.'
                        }
                    }
                });

                if (isConfirmed) {
                    reasonInput.value = text;
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });
    </script>
@endpush
