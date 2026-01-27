@extends("layouts.guest")
@section("title", "Request Access")

@section("content")
    <div class="h-screen flex flex-col lg:flex-row overflow-hidden bg-white">

        {{-- LEFT SIDE: Infrastructure Request Flow --}}
        <div class="hidden lg:flex lg:w-[42%] bg-slate-950 p-12 flex-col justify-between border-r border-white/5 relative h-full">
            {{-- Background Effects --}}
            <div class="absolute inset-0 bg-aurora opacity-30"></div>
            <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>

            {{-- 1. Brand Section --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <div class="space-y-0.5">
                    <span class="text-2xl font-black text-white tracking-tighter leading-none">FLUX<span class="text-indigo-500">_</span></span>
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-mono text-indigo-400 uppercase tracking-[0.3em]">Infrastructure Core</span>
                    </div>
                </div>
            </div>

            {{-- 2. Infographic Flow --}}
            <div class="relative z-10">
                <h1 class="text-2xl font-bold text-white mb-10 tracking-tight">Access Pipeline</h1>

                <div class="space-y-0">
                    {{-- Step 1 --}}
                    <div class="flex gap-6 pb-8 relative">
                        <div class="absolute left-[17px] top-10 bottom-0 w-px border-l border-dashed border-indigo-500/30"></div>
                        <div class="relative z-20 w-9 h-9 rounded-full bg-indigo-600 border-4 border-slate-950 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-white">01</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-widest">Submission</h3>
                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Identity and justification are logged for audit.</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-6 pb-8 relative">
                        <div class="absolute left-[17px] top-10 bottom-0 w-px border-l border-dashed border-slate-800"></div>
                        <div class="relative z-20 w-9 h-9 rounded-full bg-slate-900 border-4 border-slate-950 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-slate-500">02</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-widest">Verification</h3>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Administrator validates access requirements.</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-6">
                        <div class="relative z-20 w-9 h-9 rounded-full bg-slate-900 border-4 border-slate-950 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-slate-500">03</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <h3 class="text-xs font-bold text-slate-300 uppercase tracking-widest">Provisioning</h3>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Console nodes activated and credentials granted.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Bottom Info --}}
            <div class="relative z-10 pt-6 border-t border-white/5">
                <p class="text-[10px] font-mono text-slate-600 uppercase tracking-widest">
                    Infrastructure Unit // 2026
                </p>
            </div>
        </div>

        {{-- RIGHT SIDE: Form Section --}}
        <div class="flex-1 flex flex-col justify-center px-8 lg:px-24 bg-white h-full relative">

            {{-- Mobile Logo --}}
            <div class="lg:hidden absolute top-8 left-8 flex items-center gap-2">
                <div class="w-6 h-6 rounded bg-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2.5" />
                    </svg>
                </div>
                <span class="font-bold text-slate-900 tracking-tighter">FLUX</span>
            </div>

            <div class="max-w-[480px] w-full mx-auto">
                <header class="mb-8">
                    <h2 class="text-2xl font-black text-slate-950 tracking-tight">Request Access</h2>
                    <p class="text-slate-500 text-sm mt-1">Complete the form to gain access to the Flux Core Console.</p>
                </header>

                <form action="{{ route("register") }}" class="space-y-4" id="requestForm" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">First Name</label>
                            <input autofocus class="input-field @error("first_name") border-rose-500 bg-rose-50/50 @enderror" name="first_name" placeholder="John" required type="text" value="{{ old("first_name") }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Last Name</label>
                            <input class="input-field @error("last_name") border-rose-500 bg-rose-50/50 @enderror" name="last_name" placeholder="Doe" required type="text" value="{{ old("last_name") }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Username</label>
                            <input class="input-field @error("username") border-rose-500 bg-rose-50/50 @enderror" name="username" placeholder="johndoe" required type="text" value="{{ old("username") }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Email</label>
                            <input class="input-field @error("email") border-rose-500 bg-rose-50/50 @enderror" name="email" placeholder="john@flux.com" required type="email" value="{{ old("email") }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Department</label>
                            <input class="input-field @error("department") border-rose-500 bg-rose-50/50 @enderror" name="department" placeholder="IT Infrastructure" required type="text" value="{{ old("department") }}">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Role</label>
                            <div class="relative">
                                <select class="input-field appearance-none cursor-pointer pr-10" name="role" required>
                                    <option disabled selected value="">Select Role</option>
                                    <option value="Developer">Developer</option>
                                    <option value="Quality Assurance">Quality Assurance</option>
                                    <option value="System Administrator">System Administrator</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Justification</label>
                        <textarea class="input-field @error("justification") border-rose-500 bg-rose-50/50 @enderror h-16 py-2 resize-none text-[13px]" name="justification" placeholder="Brief purpose for requesting access..." required>{{ old("justification") }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Password</label>
                            <input class="input-field @error("password") border-rose-500 bg-rose-50/50 @enderror" name="password" placeholder="••••••••" required type="password">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Confirm</label>
                            <input class="input-field @error("password_confirmation") border-rose-500 bg-rose-50/50 @enderror" name="password_confirmation" placeholder="••••••••" required type="password">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button class="btn-primary" id="submitBtn" type="submit">
                            <span id="btnText">Request Access Control</span>
                            <div class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" id="btnLoading"></div>
                        </button>
                    </div>
                </form>

                <footer class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-center gap-2">
                    <span class="text-xs text-slate-500">Already have an account?</span>
                    <a class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route("login") }}">Login to Console</a>
                </footer>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        document.getElementById('requestForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').classList.add('hidden');
            document.getElementById('btnLoading').classList.remove('hidden');
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: '{{ $errors->first() }}',
                background: '#fff',
                color: '#0f172a',
                iconColor: '#ef4444'
            });
        @endif

        @if (session("success"))
            Toast.fire({
                icon: 'success',
                title: 'Request Sent',
                text: '{{ session("success") }}',
                background: '#fff',
                color: '#0f172a',
                iconColor: '#10b981'
            });
        @endif
    </script>
@endpush
