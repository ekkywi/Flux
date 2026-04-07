@extends("layouts.app")
@section("title", "Access Pipeline")
@section("page_title", "Access Control")
@section("page_subtitle", "Provisioning identities and security clearance.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 1. CONTROL BAR --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-3 w-3">
                    @if ($pendingRequests->count() > 0)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    @endif
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">Security Gate</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        <span class="font-bold text-zinc-900">{{ $pendingRequests->count() }}</span> pending requests
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 pl-2 overflow-x-auto no-scrollbar">
                <div class="flex items-center gap-3 px-4 py-2 bg-zinc-50/50 border border-zinc-200/50 rounded-xl">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none">Total Active</span>
                        <span class="text-xs font-bold text-zinc-700 mt-0.5">{{ \App\Models\User::where("is_active", true)->count() }} Users</span>
                    </div>
                </div>
                <div class="h-8 w-px bg-zinc-200 mx-1"></div>
                <div class="flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white rounded-xl shadow-sm">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="text-xs font-mono font-bold tracking-wider" id="serverClock">00:00:00</span>
                </div>
            </div>
        </div>

        {{-- 2. PIPELINE LIST --}}
        <div class="space-y-3">
            @forelse($pendingRequests as $request)
                <div class="group relative flex flex-col md:flex-row md:items-center gap-4 rounded-2xl bg-white p-4 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-md transition-all duration-200">

                    {{-- Identity --}}
                    <div class="flex items-center gap-4 md:w-[280px] shrink-0">
                        <div class="h-10 w-10 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-sm font-black text-zinc-700 group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0 shadow-sm">
                            {{ substr($request->user->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-blue-600 truncate transition-colors">
                                {{ $request->user->first_name }} {{ $request->user->last_name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                @php
                                    $badgeColor = match ($request->request_type) {
                                        \App\Enums\ApprovalType::ACCOUNT_REQUEST => "bg-blue-50 text-blue-600 border-blue-100",
                                        \App\Enums\ApprovalType::RESET_PASSWORD => "bg-rose-50 text-rose-600 border-rose-100",
                                        \App\Enums\ApprovalType::SERVER_ACCESS => "bg-amber-50 text-amber-600 border-amber-100",
                                        default => "bg-zinc-50 text-zinc-600 border-zinc-100",
                                    };
                                @endphp
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border {{ $badgeColor }}">
                                    {{ $request->request_type->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Justification --}}
                    <div class="flex-1 md:px-6 md:border-l md:border-zinc-100">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Reason / Context</p>
                        <p class="text-xs text-zinc-600 font-medium italic leading-relaxed line-clamp-2 md:line-clamp-1">
                            "{{ $request->justification ?? "No specific justification provided." }}"
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between md:justify-end gap-4 md:w-auto md:pl-6 md:border-l md:border-zinc-100 mt-4 md:mt-0">
                        <div class="text-right hidden xl:block">
                            <span class="block text-[9px] font-mono text-zinc-400">{{ $request->created_at->format("M d, Y") }}</span>
                            <span class="block text-[9px] font-mono text-zinc-500 font-bold">{{ $request->created_at->format("H:i") }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <form action="{{ route("admin.approvals.reject", $request) }}" class="reject-form" method="POST">
                                @csrf
                                <input class="reject-reason-input" name="reason" type="hidden">
                                <button class="btn-reject p-2 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-all" data-user="{{ $request->user->first_name }}" type="button">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </button>
                            </form>

                            <form action="{{ route("admin.approvals.approve", $request) }}" class="approval-form" method="POST">
                                @csrf
                                <button class="btn-approve flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white hover:bg-blue-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:shadow-blue-500/20 active:scale-95 border border-transparent" data-user="{{ $request->user->first_name }}" type="button">
                                    <span>Approve</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center rounded-3xl border-2 border-dashed border-zinc-200 bg-zinc-50/50">
                    <h3 class="text-zinc-900 font-bold text-lg">All Clear</h3>
                    <p class="text-zinc-500 text-sm mt-1">No pending access requests.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

{{-- KEMBALIKAN KE @PUSH AGAR RAPI --}}
@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. CLOCK
            function updateClock() {
                const now = new Date();
                const clockElement = document.getElementById('serverClock');
                if (clockElement) clockElement.textContent = now.toLocaleTimeString('en-GB', {
                    hour12: false
                });
            }
            setInterval(updateClock, 1000);
            updateClock();

            // 2. SWEETALERT
            const fluxSwal = Swal.mixin({
                customClass: {
                    popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden',
                    title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                    htmlContainer: 'text-zinc-500 text-sm px-6 pb-2',
                    confirmButton: 'bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6',
                    cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                    input: 'bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all mx-6 mb-4 w-auto'
                },
                buttonsStyling: false
            });

            document.querySelectorAll('.btn-approve').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.approval-form');
                    const userName = this.getAttribute('data-user');
                    fluxSwal.fire({
                        title: 'Authorize Access?',
                        html: `Granting system privileges to <b class="text-zinc-900">${userName}</b>.`,
                        icon: 'question',
                        iconColor: '#2563eb',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Authorization',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fluxSwal.fire({
                                title: 'Processing...',
                                showConfirmButton: false,
                                didOpen: () => Swal.showLoading()
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
                    } = await fluxSwal.fire({
                        title: 'Reject Request',
                        html: `Reason for rejecting <b class="text-zinc-900">${userName}</b>.`,
                        input: 'textarea',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Rejection',
                        confirmButtonClass: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 mx-2 mb-6',
                        inputValidator: (value) => {
                            if (!value) return 'Reason is required!'
                        }
                    });
                    if (isConfirmed) {
                        reasonInput.value = text;
                        fluxSwal.fire({
                            title: 'Rejecting...',
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
