@extends("layouts.app")
@section("title", "User Directory")
@section("page_title", "Infrastructure Personnel")

@section("content")
    <div class="space-y-8 pb-20 text-slate-900">

        {{-- 1. NOTIFICATION SYSTEM --}}
        @if (session("success") || session("error"))
            <div class="fixed top-4 right-4 z-[60] {{ session("success") ? "bg-emerald-500" : "bg-rose-500" }} text-white px-6 py-3 rounded-2xl shadow-2xl animate-bounce">
                <p class="text-xs font-black uppercase tracking-widest">
                    {{ session("success") ?? session("error") }}
                </p>
            </div>
        @endif

        {{-- 2. STREAMLINED HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-2">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-indigo-600 mb-1">
                    <div class="h-1 w-6 bg-indigo-600 rounded-full"></div>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em]">Security Protocol</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Identity Management</h1>
                <p class="text-xs text-slate-500 font-medium">
                    Managing <span class="text-indigo-600 font-bold">{{ $users->total() }} authorized entities</span> within the console.
                </p>
            </div>

            {{-- Compact Stats & Actions --}}
            <div class="flex items-center gap-4 px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm">
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Total</span>
                    <span class="text-sm font-black text-slate-900">{{ $users->total() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>
                <div class="text-center min-w-[50px]">
                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none">Active</span>
                    <span class="text-sm font-black text-emerald-600">{{ \App\Models\User::where("is_active", true)->count() }}</span>
                </div>
                <div class="w-px h-6 bg-slate-100"></div>

                {{-- PINTU ARCHIVED --}}
                <a class="px-3 py-1.5 bg-slate-50 text-slate-400 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-2 border border-transparent hover:border-rose-100" href="{{ route("admin.users.archived") }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" />
                    </svg>
                    Archived
                </a>

                <button class="px-4 py-1.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-600 transition-all shadow-md" onclick="toggleProvisionModal()">
                    + Provision
                </button>
            </div>
        </div>

        {{-- 3. IDENTITY DIRECTORY TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Personnel</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Classification</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Management</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm font-black border border-slate-200 group-hover:border-indigo-500">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate tracking-tight">{{ $user->first_name }} {{ $user->last_name }}</p>
                                            <p class="text-[10px] font-mono text-slate-400 truncate">{{ $user->username }} // {{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-slate-700 uppercase tracking-wider">{{ $user->department }}</span>
                                            <span class="text-[9px] text-slate-300">•</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $user->role }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 text-[9px] font-black uppercase tracking-widest">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Operational
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-slate-400 text-[9px] font-black uppercase tracking-widest">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                            Deactivated
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors rounded-lg hover:bg-indigo-50" onclick="toggleEditModal({{ $user }})" title="Edit Entity">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" />
                                            </svg>
                                        </button>
                                        {{-- BUTTON REVOKE --}}
                                        <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors rounded-lg hover:bg-rose-50" onclick="toggleRevokeModal({{ $user }})" title="Revoke Access">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- 4. PROVISION MODAL (Identical Style) --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="provisionModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-indigo-600 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Provision Identity</h3>
                <p class="text-xs text-slate-500 font-medium">Issue new system credentials for infrastructure personnel.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl">
                    <ul class="text-[10px] font-black uppercase tracking-wide text-rose-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route("admin.users.store") }}" class="space-y-5" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">First Name</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-medium" name="first_name" placeholder="John" required type="text" value="{{ old("first_name") }}">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Last Name</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-medium" name="last_name" placeholder="Doe" required type="text" value="{{ old("last_name") }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Username</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-bold text-indigo-600" name="username" placeholder="johndoe" required type="text" value="{{ old("username") }}">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Dept</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 appearance-none font-bold" name="department" required>
                            <option disabled selected value="">Assign Dept</option>
                            @foreach (["INC", "ITC", "ITS", "MIS"] as $dept)
                                <option {{ old("department") == $dept ? "selected" : "" }} value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Role</label>
                    <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 appearance-none font-bold text-slate-700" name="role" required>
                        <option disabled selected value="">Assign Role</option>
                        @foreach (["Developer", "Quality Assurance", "System Administrator"] as $role)
                            <option {{ old("role") == $role ? "selected" : "" }} value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Email Address</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 font-medium" name="email" placeholder="johndoe@flux.console" required type="email" value="{{ old("email") }}">
                </div>

                <div class="p-5 bg-indigo-50/50 rounded-[1.5rem] border border-dashed border-indigo-200 group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-2 block">Temporary Password</label>
                    <input class="w-full bg-transparent font-mono text-indigo-600 font-black border-none p-0 focus:ring-0 text-sm" name="temporary_password" readonly type="text" value="{{ Str::random(14) }}">
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleProvisionModal()" type="button">Abort</button>
                    <button class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200" type="submit">Authorize</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4.5 EDIT MODAL --}}
    <div class="fixed inset-0 z-[100] items-center justify-center hidden bg-slate-900/60 backdrop-blur-sm px-4" id="editModal">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] p-10 shadow-2xl border border-slate-200 transform transition-all">
            <div class="mb-8">
                <div class="h-1.5 w-12 bg-amber-500 rounded-full mb-4"></div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Modify Identity</h3>
                <p class="text-xs text-slate-500 font-medium">Update system credentials and access levels for personnel.</p>
            </div>

            <form action="" class="space-y-5" id="editForm" method="POST">
                @csrf
                @method("PATCH")

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">First Name</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" id="edit_first_name" name="first_name" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Last Name</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" id="edit_last_name" name="last_name" required type="text">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Username</label>
                        <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm font-mono bg-slate-50 font-bold text-indigo-600" id="edit_username" name="username" required type="text">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Status</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 appearance-none font-bold" id="edit_is_active" name="is_active">
                            <option value="1">OPERATIONAL</option>
                            <option value="0">DEACTIVATED</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Dept</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 appearance-none font-bold" id="edit_department" name="department" required>
                            @foreach (["INC", "ITC", "ITS", "MIS"] as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Role</label>
                        <select class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 appearance-none font-bold text-slate-700" id="edit_role" name="role" required>
                            @foreach (["Developer", "Quality Assurance", "System Administrator"] as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-1">Email Address</label>
                    <input class="w-full px-5 py-3.5 rounded-2xl border-slate-200 text-sm bg-slate-50 font-medium" id="edit_email" name="email" required type="email">
                </div>

                <div class="flex gap-4 pt-4">
                    <button class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleEditModal()" type="button">Cancel</button>
                    <button class="flex-1 px-6 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200" type="submit">Update Identity</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4.6 CUSTOM REVOKE MODAL (Flux Style) --}}
    <div class="fixed inset-0 z-[110] items-center justify-center hidden bg-slate-900/80 backdrop-blur-md px-4" id="revokeModal">
        <div class="bg-white w-full max-w-sm rounded-[2.5rem] p-10 shadow-2xl border border-rose-100 transform transition-all">
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 bg-rose-50 rounded-full flex items-center justify-center mb-6 border border-rose-100">
                    <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Revoke Access?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    You are about to purge <span class="text-rose-600 font-bold font-mono" id="revoke_target_name"></span> from active directory. This will be logged as a <span class="text-rose-600 font-black underline">CRITICAL EVENT</span>.
                </p>
            </div>

            <form action="" class="space-y-4" id="revokeForm" method="POST">
                @csrf
                @method("DELETE")

                <div class="flex flex-col gap-3">
                    <button class="w-full px-6 py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-200" type="submit">
                        Confirm Revocation
                    </button>
                    <button class="w-full px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all" onclick="toggleRevokeModal()" type="button">
                        Abort Mission
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. SCRIPTS --}}
    @push("scripts")
        <script>
            // MODAL CONTROLS
            function toggleProvisionModal() {
                const modal = document.getElementById('provisionModal');
                modal.classList.toggle('hidden');
                modal.classList.toggle('flex');
            }

            function toggleEditModal(user = null) {
                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                if (user) {
                    form.action = `/admin/users/${user.id}`;
                    document.getElementById('edit_first_name').value = user.first_name;
                    document.getElementById('edit_last_name').value = user.last_name;
                    document.getElementById('edit_username').value = user.username;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_department').value = user.department;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_is_active').value = user.is_active ? 1 : 0;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // CUSTOM REVOKE CONTROL
            function toggleRevokeModal(user = null) {
                const modal = document.getElementById('revokeModal');
                const form = document.getElementById('revokeForm');
                const targetDisplay = document.getElementById('revoke_target_name');

                if (user) {
                    form.action = `/admin/users/${user.id}`;
                    targetDisplay.textContent = user.username.toUpperCase();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }

            // SERVER CLOCK
            function updateClock() {
                const clock = document.getElementById('serverClock');
                if (clock) clock.textContent = new Date().toLocaleTimeString('en-GB');
            }
            setInterval(updateClock, 1000);
            updateClock();

            // TOAST AUTO-HIDE
            setTimeout(() => {
                document.querySelectorAll('.animate-bounce').forEach(t => t.style.display = 'none');
            }, 4000);
        </script>
    @endpush
@endsection
