@extends("layouts.app")
@section("title", "User Directory")
@section("page_title", "Personnel")
@section("page_subtitle", "Manage authorized identities and access levels.")

@section("content")
    <div class="space-y-6 pb-20">

        {{-- 2. CONTROL BAR (Stats & Actions) --}}
        <div class="sticky top-0 z-30 flex flex-col gap-4 rounded-2xl bg-white/80 p-2 backdrop-blur-xl border border-zinc-200 shadow-sm md:flex-row md:items-center md:justify-between transition-all">

            {{-- Left: Status Indicator --}}
            <div class="flex items-center gap-3 px-4">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 leading-none">Active Directory</h2>
                    <p class="text-[10px] font-medium text-zinc-500 mt-0.5">
                        <span class="font-bold text-zinc-900">{{ $users->total() }}</span> authorized personnel
                    </p>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 pl-2 overflow-x-auto no-scrollbar">

                {{-- Stats Active --}}
                <div class="flex items-center gap-3 px-4 py-2 bg-zinc-50/50 border border-zinc-200/50 rounded-xl hidden md:flex">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none">Operational</span>
                        <span class="text-xs font-bold text-emerald-600 mt-0.5">{{ \App\Models\User::where("is_active", true)->count() }} Users</span>
                    </div>
                </div>

                <div class="h-8 w-px bg-zinc-200 mx-1 hidden md:block"></div>

                {{-- Archived Button --}}
                <a class="flex items-center gap-2 px-3 py-2 bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:border-zinc-300" href="{{ route("admin.users.archived") }}">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Archives</span>
                </a>

                {{-- Provision Button --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white hover:bg-blue-600 rounded-xl text-[11px] font-bold uppercase tracking-wide transition-all shadow-sm hover:shadow-blue-500/20 active:scale-95 border border-transparent" onclick="openProvisionModal()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span>Provision</span>
                </button>
            </div>
        </div>

        {{-- 3. USER LIST (Grid/Card Style for better mobile view) --}}
        <div class="grid grid-cols-1 gap-3">
            @foreach ($users as $user)
                <div class="group relative flex flex-col md:flex-row md:items-center gap-4 rounded-2xl bg-white p-4 shadow-sm border border-zinc-200 hover:border-blue-500/30 hover:shadow-md transition-all duration-200">

                    {{-- Identity --}}
                    <div class="flex items-center gap-4 md:w-[280px] shrink-0">
                        <div class="h-10 w-10 rounded-xl bg-zinc-50 border border-zinc-100 flex items-center justify-center text-sm font-black text-zinc-700 group-hover:bg-blue-600 group-hover:text-white transition-colors shrink-0 shadow-sm">
                            {{ substr($user->first_name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-blue-600 truncate transition-colors">
                                {{ $user->first_name }} {{ $user->last_name }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] font-mono text-zinc-400 truncate">{{ $user->username }}</span>
                                <span class="text-zinc-300">•</span>
                                <span class="text-[10px] font-mono text-zinc-400 truncate">{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dept & Role --}}
                    <div class="flex-1 flex flex-col md:flex-row md:items-center gap-2 md:gap-6 md:px-6 md:border-l md:border-zinc-100">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Department</span>
                            <span class="text-xs font-bold text-zinc-700 mt-0.5">{{ $user->department }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Role</span>
                            <span class="text-xs font-medium text-zinc-600 mt-0.5">{{ $user->role }}</span>
                        </div>
                    </div>

                    {{-- Status & Actions --}}
                    <div class="flex items-center justify-between md:justify-end gap-4 md:w-auto md:pl-6 md:border-l md:border-zinc-100 mt-4 md:mt-0">

                        {{-- Status Badge --}}
                        @if ($user->is_active)
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-100">
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                </span>
                                <span class="text-[9px] font-bold text-emerald-700 uppercase tracking-wide">Active</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-zinc-100 border border-zinc-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-zinc-400"></span>
                                <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-wide">Inactive</span>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1">
                            <button class="p-2 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" onclick="openEditModal({{ $user }})" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                            <button class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" onclick="confirmRevoke({{ $user }})" title="Revoke Access">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        // ==========================================
        // 1. CONFIGURATION (GLOBAL)
        // ==========================================

        // Cek CSRF Token dulu
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        // Config SweetAlert
        const fluxSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans',
                title: 'text-zinc-900 text-lg font-bold pt-6 px-6',
                htmlContainer: 'text-zinc-500 text-sm px-6 pb-6',
                confirmButton: 'bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6',
                cancelButton: 'bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6',
                input: 'bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all mx-6 mb-4 w-auto'
            },
            buttonsStyling: false
        });

        // ==========================================
        // 2. HELPER: SUBMIT FORM (PANTING!)
        // ==========================================
        // Fungsi ini kita taruh di window agar bisa diakses dari mana saja
        window.submitForm = function(action, method, data = {}) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;
            form.style.display = 'none'; // Sembunyikan form

            // Input CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Input Method Spoofing (PATCH/DELETE)
            if (method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
            }

            // Input Data Lain
            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
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
        // HELPER: RANDOM PASSWORD GENERATOR
        // ==========================================
        function generatePassword(length = 12) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let retVal = "";
            for (let i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        }

        // ==========================================
        // 3. ACTIONS (PROVISION, EDIT, DELETE)
        // ==========================================

        // --- PROVISION MODAL ---
        window.openProvisionModal = async function() {
            // Generate password otomatis
            const tempPassword = generatePassword(14);

            const {
                value: formValues
            } = await fluxSwal.fire({
                title: 'Provision Identity',
                html: `
                    <div class="flex flex-col gap-3 text-left">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">First Name</label>
                                <input id="swal-first" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" placeholder="John">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Last Name</label>
                                <input id="swal-last" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" placeholder="Doe">
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Username</label>
                            <input id="swal-user" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm font-mono text-blue-600 font-bold outline-none focus:border-blue-500 transition-colors" placeholder="johndoe">
                        </div>
                        
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Email</label>
                            <input id="swal-email" type="email" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" placeholder="john@flux.console">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Dept</label>
                                <select id="swal-dept" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">
                                    <option value="INC">INC</option>
                                    <option value="ITC">ITC</option>
                                    <option value="ITS">ITS</option>
                                    <option value="MIS">MIS</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Role</label>
                                <select id="swal-role" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">
                                    <option value="Developer">Developer</option>
                                    <option value="Quality Assurance">QA</option>
                                    <option value="System Administrator">SysAdmin</option>
                                </select>
                            </div>
                        </div>

                        {{-- PASSWORD DISPLAY --}}
                        <div class="p-3 bg-blue-50/50 rounded-xl border border-dashed border-blue-200 mt-2">
                            <label class="text-[9px] font-black uppercase tracking-widest text-blue-400 mb-1 block">Temporary Password</label>
                            <input id="swal-pass" type="text" readonly value="${tempPassword}" class="w-full bg-transparent font-mono text-blue-600 font-black border-none p-0 focus:ring-0 text-sm tracking-wider select-all">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Authorize Identity',
                focusConfirm: false,
                preConfirm: () => {
                    const first = document.getElementById('swal-first').value;
                    const email = document.getElementById('swal-email').value;

                    if (!first || !email) {
                        Swal.showValidationMessage('First Name and Email are required');
                        return false;
                    }

                    // 🔥 PERBAIKAN UTAMA DI SINI 🔥
                    return {
                        first_name: first,
                        last_name: document.getElementById('swal-last').value,
                        username: document.getElementById('swal-user').value,
                        email: email,
                        department: document.getElementById('swal-dept').value,
                        role: document.getElementById('swal-role').value,

                        // Ganti 'password' jadi 'temporary_password' sesuai PHP Action
                        temporary_password: document.getElementById('swal-pass').value,
                    }
                }
            });

            if (formValues) {
                // Submit ke backend
                window.submitForm('{{ route("admin.users.store") }}', 'POST', formValues);
            }
        };

        // --- EDIT MODAL ---
        window.openEditModal = async function(user) {
            // Render options manual
            const depts = ['INC', 'ITC', 'ITS', 'MIS'];
            const roles = ['Developer', 'Quality Assurance', 'System Administrator'];

            let deptOptions = depts.map(d => `<option value="${d}" ${user.department === d ? 'selected' : ''}>${d}</option>`).join('');
            let roleOptions = roles.map(r => `<option value="${r}" ${user.role === r ? 'selected' : ''}>${r}</option>`).join('');

            const {
                value: formValues
            } = await fluxSwal.fire({
                title: 'Modify Identity',
                html: `
                    <div class="flex flex-col gap-3 text-left">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">First Name</label>
                                <input id="edit-first" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" value="${user.first_name}">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Last Name</label>
                                <input id="edit-last" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" value="${user.last_name}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Username</label>
                                <input id="edit-user" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm font-mono font-bold outline-none focus:border-blue-500 transition-colors" value="${user.username}">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Status</label>
                                <select id="edit-active" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">
                                    <option value="1" ${user.is_active ? 'selected' : ''}>Active</option>
                                    <option value="0" ${!user.is_active ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Email</label>
                            <input id="edit-email" type="email" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors" value="${user.email}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Dept</label>
                                <select id="edit-dept" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">${deptOptions}</select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-400 uppercase">Role</label>
                                <select id="edit-role" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors">${roleOptions}</select>
                            </div>
                        </div>

                        {{-- NEW: PASSWORD RESET FIELD --}}
                        <div class="mt-2 pt-4 border-t border-zinc-100">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase flex justify-between">
                                Reset Password 
                                <span class="text-zinc-300 font-normal normal-case">(Optional: Leave blank to keep current)</span>
                            </label>
                            <div class="relative mt-1">
                                <input id="edit-password" type="text" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors shadow-sm placeholder:text-zinc-300" placeholder="Set new password...">
                                <button type="button" onclick="document.getElementById('edit-password').value = Math.random().toString(36).slice(-10)" class="absolute right-2 top-1.5 text-[9px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded hover:bg-blue-100">
                                    GENERATE
                                </button>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update Identity',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        first_name: document.getElementById('edit-first').value,
                        last_name: document.getElementById('edit-last').value,
                        username: document.getElementById('edit-user').value,
                        email: document.getElementById('edit-email').value,
                        is_active: document.getElementById('edit-active').value,
                        department: document.getElementById('edit-dept').value,
                        role: document.getElementById('edit-role').value,

                        // Kirim password (bisa kosong)
                        password: document.getElementById('edit-password').value
                    }
                }
            });

            if (formValues) {
                // Backend akan terima field 'password'. 
                // Jika kosong (null/empty string), Backend JANGAN update passwordnya.
                window.submitForm(`/admin/users/${user.id}`, 'PATCH', formValues);
            }
        };

        // --- DELETE (REVOKE) ACTION ---
        window.confirmRevoke = function(user) {
            fluxSwal.fire({
                title: 'Revoke Access?',
                html: `Are you sure you want to remove <b class="text-zinc-900">${user.username}</b>? <br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Revoke Access',
                confirmButtonClass: 'bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-rose-700 mx-2 mb-6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ambil ID dari object user
                    window.submitForm(`/admin/users/${user.id}`, 'DELETE');
                }
            });
        };
    </script>
@endpush
