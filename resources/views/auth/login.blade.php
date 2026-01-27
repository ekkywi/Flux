@extends("layouts.guest")
@section("title", "Console Login")

@push("styles")
    <style>
        .typing {
            width: 0;
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid transparent;
            animation: typing 1.5s steps(30, end) forwards;
        }

        .line-1 {
            animation-delay: 0.5s;
        }

        .line-2 {
            animation-delay: 2.2s;
            opacity: 0;
            animation: fadeIn 0.5s forwards 2.2s;
        }

        .line-3 {
            animation-delay: 3.0s;
            opacity: 0;
            animation: fadeIn 0.5s forwards 3.0s;
        }

        .line-4 {
            animation-delay: 3.8s;
            opacity: 0;
            animation: fadeIn 0.5s forwards 3.8s;
        }

        .line-5 {
            animation-delay: 4.6s;
            opacity: 0;
            animation: fadeIn 0.5s forwards 4.6s;
        }

        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .terminal-cursor {
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {

            0%,
            100% {
                border-color: transparent
            }

            50% {
                border-color: #6366f1
            }
        }
    </style>
@endpush

@section("content")
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- LEFT SIDE --}}
        <div class="hidden lg:flex lg:w-[48%] bg-slate-950 p-16 flex-col justify-between border-r border-white/5 relative overflow-hidden">

            <div class="absolute inset-0 bg-aurora opacity-40"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0); background-size: 24px 24px;"></div>
            <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06)); background-size: 100% 2px, 3px 100%;"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center backdrop-blur-md shadow-2xl">
                        <svg class="w-7 h-7 text-indigo-500 fill-current" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="space-y-0.5">
                        <span class="text-2xl font-black text-white tracking-tighter leading-none">FLUX<span class="text-indigo-500">_</span></span>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] font-mono text-indigo-400 uppercase tracking-[0.3em]">Infrastructure Core</span>
                        </div>
                    </div>
                </div>
                <div class="px-3 py-1 rounded border border-white/10 bg-white/5 backdrop-blur-sm">
                    <span class="text-[10px] font-mono text-slate-400 tracking-widest uppercase">Ver. 1.0.0</span>
                </div>
            </div>

            <div class="relative z-10">
                <div class="max-w-md">
                    <h1 class="text-6xl font-black text-white tracking-tight leading-[0.9] mb-6">
                        Build.<br>
                        <span class="text-indigo-600">Deploy.</span><br>
                        Scale.
                    </h1>

                    <div class="glass-card rounded-3xl overflow-hidden border-white/5 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)]">
                        <div class="relative bg-slate-900/40 backdrop-blur-xl border border-white/5 p-6 rounded-2xl max-w-md overflow-hidden ring-1 ring-indigo-500/20 shadow-2xl">
                            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2 opacity-50">
                                    <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                </div>
                                <span class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">flux-session — tty1</span>
                            </div>

                            <div class="font-mono text-sm leading-relaxed space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-400">$</span>
                                    <p class="text-cyan-300 typing line-1">flux init --cluster-core</p>
                                </div>

                                <div class="line-2 flex gap-2">
                                    <span class="text-slate-500">[sys]</span>
                                    <p class="text-slate-300">establishing secure handshake...</p>
                                </div>

                                <div class="line-3 flex gap-2">
                                    <span class="text-emerald-400">✓</span>
                                    <p class="text-slate-300">node pool: <span class="text-indigo-400">12 active nodes</span> online</p>
                                </div>

                                <div class="line-4 flex gap-2">
                                    <span class="text-emerald-400">✓</span>
                                    <p class="text-slate-300">security mesh: <span class="text-indigo-400">mTLS enforced</span></p>
                                </div>

                                <div class="line-5 flex items-center gap-2 pt-2 border-t border-white/5 mt-2">
                                    <span class="text-indigo-400">$</span>
                                    <p class="text-indigo-400">ready for commands<span class="terminal-cursor border-l-2 ml-1">&nbsp;</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 flex items-center justify-between border-t border-white/5 pt-8">
                <div class="font-mono text-[10px] text-slate-500 flex items-center gap-8 uppercase tracking-widest">
                    <div class="group cursor-default">
                        <span class="group-hover:text-indigo-400 transition-colors">Development Edition</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-mono text-indigo-500/50 uppercase tracking-tighter">Flux Infrastructure Core — 2026</span>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="flex-1 flex items-center justify-center p-8 bg-white">
            <div class="max-w-[360px] w-full">
                <header class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-950">Console Login</h2>
                    <p class="text-slate-500 text-sm mt-1">Authenticate to access Flux Core Console.</p>
                </header>

                <form action="{{ route("login") }}" class="space-y-5" id="loginForm" method="POST">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5 tracking-wider">Email or Username</label>
                        <input autofocus class="input-field" name="login" placeholder="identity@flux.com" required type="text" value="{{ old("login") }}">
                    </div>

                    <div>
                        <div class="flex justify-between mb-1.5">
                            <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Password</label>
                            <a class="text-[11px] font-bold text-indigo-600 hover:underline" href="#">Forgot?</a>
                        </div>
                        <div class="relative">
                            <input class="input-field" id="password" name="password" placeholder="••••••••" required type="password">
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400" onclick="togglePassword()" type="button">
                                <svg class="w-4 h-4" fill="none" id="eyeIcon" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input class="w-4 h-4 text-indigo-600 rounded border-slate-300" id="remember" name="remember" type="checkbox">
                        <label class="ml-2 text-sm font-medium text-slate-600" for="remember">Remember session</label>
                        <a class="text-[11px] font-bold text-indigo-600 hover:underline" href="#">Forgot?</a>
                    </div>

                    <button class="btn-primary" id="submitBtn" type="submit">
                        <span id="btnText">Sign In</span>
                        <div class="hidden w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" id="btnLoading"></div>
                    </button>
                </form>

                <p class="mt-8 text-sm text-slate-500 border-t border-slate-100 pt-6 text-center font-medium">
                    Need access? <a class="text-indigo-600 font-bold" href="{{ route("register") }}">Request account</a>
                </p>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        function togglePassword() {
            const p = document.getElementById('password');
            p.type = p.type === 'password' ? 'text' : 'password';
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('btnText').textContent = 'Authenticating...';
            document.getElementById('btnLoading').classList.remove('hidden');
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session("success"))
            Toast.fire({
                icon: 'success',
                title: 'Access Granted',
                text: '{{ session("success") }}',
                background: '#ffffff',
                color: '#0f172a',
                iconColor: '#10b981'
            });
        @endif

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: 'Authentication Failed',
                text: '{{ $errors->first() }}',
                background: '#ffffff',
                color: '#0f172a',
                iconColor: '#ef4444'
            });
        @endif
    </script>
@endpush
