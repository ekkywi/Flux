@extends("layouts.app")

@section("title", "Initialize Project")
@section("page_title", "New Deployment")
@section("page_subtitle", "Configure repository and build environment.")

{{-- Action Button --}}
@section("actions")
    <a class="flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-500 hover:text-zinc-800 hover:border-zinc-300 text-xs font-bold uppercase tracking-widest rounded-xl transition-all shadow-sm" href="{{ route("console.projects.index") }}">
        Cancel
    </a>
@endsection

@section("content")
    <div class="max-w-7xl mx-auto">
        {{-- Menampilkan Error Global jika ada (misal DB Transaction gagal) --}}
        @if (session("error"))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                {{ session("error") }}
            </div>
        @endif

        <form action="{{ route("console.projects.store") }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8" method="POST">
            @csrf

            {{-- KOLOM KIRI: INPUT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. Identity --}}
                <div class="bg-white p-8 rounded-3xl border border-zinc-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900">Project Identity</h3>
                    </div>

                    <div class="space-y-5">
                        {{-- Name Input --}}
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Project Name <span class="text-red-500">*</span></label>
                            <input class="w-full bg-zinc-50 border @error("name") border-red-300 bg-red-50 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 font-medium placeholder:text-zinc-400 transition-colors" name="name" oninput="updatePreview('preview-name', this.value)" placeholder="e.g. Flux Core API" required type="text" value="{{ old("name") }}">

                            @error("name")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description Input --}}
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Description</label>
                            <textarea class="w-full bg-zinc-50 border border-zinc-200 text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 font-medium placeholder:text-zinc-400" name="description" placeholder="Brief description of the service..." rows="3">{{ old("description") }}</textarea>
                        </div>

                        <div class="pt-4 border-t border-zinc-100">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Project Stack <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select class="w-full bg-zinc-50 border @error("stack") border-red-300 bg-red-50 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 font-bold appearance-none transition-colors" id="stack" name="stack" onchange="togglePhpVersion()" required>
                                    <option disabled selected value="">-- Select Stack --</option>
                                    <option {{ old("stack") == "laravel" ? "selected" : "" }} value="laravel">Laravel / PHP</option>
                                    <option {{ old("stack") == "nodejs" ? "selected" : "" }} value="nodejs">Node.js</option>
                                    <option {{ old("stack") == "html" ? "selected" : "" }} value="html">Static HTML</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>
                            @error("stack")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="php_version_container" style="display: none;">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">PHP Version</label>
                            <div class="relative">
                                <select class="w-full bg-zinc-50 border @error("php_version") border-red-300 bg-red-50 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 font-bold appearance-none transition-colors" id="php_version" name="php_version">
                                    <option {{ old("php_version", "8.4") == "8.4" ? "selected" : "" }} value="8.4">PHP 8.4 (Latest)</option>
                                    <option {{ old("php_version") == "8.3" ? "selected" : "" }} value="8.3">PHP 8.3</option>
                                    <option {{ old("php_version") == "8.2" ? "selected" : "" }} value="8.2">PHP 8.2</option>
                                    <option {{ old("php_version") == "8.1" ? "selected" : "" }} value="8.1">PHP 8.1</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>
                            @error("php_version")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 🔥 PERBAIKAN: DATABASE ENGINE DITAMBAH MARIADB 🔥 --}}
                        <div class="pt-4 border-t border-zinc-100 mt-4">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Database Engine <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                <label class="cursor-pointer relative">
                                    <input {{ old("database_type", "sqlite") == "sqlite" ? "checked" : "" }} class="peer sr-only" name="database_type" onchange="toggleDbVersion()" type="radio" value="sqlite">
                                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 peer-checked:bg-blue-50 peer-checked:border-blue-300 peer-checked:ring-1 peer-checked:ring-blue-300 transition-all text-center">
                                        <p class="text-sm font-bold text-zinc-900">SQLite</p>
                                        <p class="text-[9px] text-zinc-500 uppercase tracking-wider mt-1">Single Node</p>
                                    </div>
                                    <div class="absolute top-2 right-2 hidden peer-checked:block text-blue-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>

                                <label class="cursor-pointer relative">
                                    <input {{ old("database_type") == "mysql" ? "checked" : "" }} class="peer sr-only" name="database_type" onchange="toggleDbVersion()" type="radio" value="mysql">
                                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 peer-checked:bg-blue-50 peer-checked:border-blue-300 peer-checked:ring-1 peer-checked:ring-blue-300 transition-all text-center">
                                        <p class="text-sm font-bold text-zinc-900">MySQL</p>
                                        <p class="text-[9px] text-zinc-500 uppercase tracking-wider mt-1">Multi Node</p>
                                    </div>
                                    <div class="absolute top-2 right-2 hidden peer-checked:block text-blue-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>

                                <label class="cursor-pointer relative">
                                    <input {{ old("database_type") == "mariadb" ? "checked" : "" }} class="peer sr-only" name="database_type" onchange="toggleDbVersion()" type="radio" value="mariadb">
                                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 peer-checked:bg-blue-50 peer-checked:border-blue-300 peer-checked:ring-1 peer-checked:ring-blue-300 transition-all text-center">
                                        <p class="text-sm font-bold text-zinc-900">MariaDB</p>
                                        <p class="text-[9px] text-zinc-500 uppercase tracking-wider mt-1">Multi Node</p>
                                    </div>
                                    <div class="absolute top-2 right-2 hidden peer-checked:block text-blue-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>

                                <label class="cursor-pointer relative">
                                    <input {{ old("database_type") == "pgsql" ? "checked" : "" }} class="peer sr-only" name="database_type" onchange="toggleDbVersion()" type="radio" value="pgsql">
                                    <div class="p-3.5 rounded-xl border border-zinc-200 bg-zinc-50 hover:bg-zinc-100 peer-checked:bg-blue-50 peer-checked:border-blue-300 peer-checked:ring-1 peer-checked:ring-blue-300 transition-all text-center">
                                        <p class="text-sm font-bold text-zinc-900">PostgreSQL</p>
                                        <p class="text-[9px] text-zinc-500 uppercase tracking-wider mt-1">Multi Node</p>
                                    </div>
                                    <div class="absolute top-2 right-2 hidden peer-checked:block text-blue-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            @error("database_type")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 🔥 FITUR BARU: DROPDOWN DATABASE VERSION 🔥 --}}
                        <div id="db_version_container" style="display: none;">
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 mt-4">Database Version</label>
                            <div class="relative">
                                <select class="w-full bg-zinc-50 border @error("database_version") border-red-300 bg-red-50 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3.5 font-bold appearance-none transition-colors" id="database_version" name="database_version">
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>
                            @error("database_version")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- 2. Source Control (Smart Fetch) --}}
                <div class="bg-white p-8 rounded-3xl border border-zinc-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/5 to-transparent rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-10 w-10 rounded-xl bg-zinc-100 text-zinc-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900">Source Control</h3>
                    </div>

                    <div class="space-y-5">
                        {{-- Repo URL + Check Button --}}
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Repository URL <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <svg class="w-4 h-4 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                        </svg>
                                    </div>
                                    <input class="bg-zinc-50 border @error("repository_url") border-red-300 bg-red-50 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-3.5 font-mono placeholder:text-zinc-400 transition-colors" id="repo_url" name="repository_url" oninput="updatePreview('preview-repo', this.value)" placeholder="https://github.com/user/repo.git" required type="url" value="{{ old("repository_url") }}">
                                </div>

                                {{-- Button Fetch --}}
                                <button class="px-4 py-2 bg-zinc-100 border border-zinc-200 text-zinc-600 hover:bg-white hover:text-blue-600 hover:border-blue-200 rounded-xl text-xs font-bold uppercase tracking-wide transition-all shadow-sm flex items-center gap-2 whitespace-nowrap" id="btn-fetch" onclick="fetchBranches()" type="button">
                                    <span id="icon-fetch">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                        </svg>
                                    </span>
                                    <span class="hidden" id="spinner-fetch">
                                        <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor"></circle>
                                            <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                                        </svg>
                                    </span>
                                    <span id="text-fetch">Check</span>
                                </button>
                            </div>

                            {{-- Error Message for Repo --}}
                            @error("repository_url")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror

                            <p class="text-[10px] text-zinc-400 mt-1.5 ml-1 hidden" id="fetch-msg">Verifying access...</p>
                        </div>

                        {{-- Branch Dropdown --}}
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Target Branch <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select class="bg-white border @error("branch") border-red-300 @else border-zinc-200 @enderror text-zinc-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 appearance-none font-bold disabled:bg-zinc-100 disabled:text-zinc-400 transition-colors" disabled id="branch-select" name="branch" onchange="updatePreview('preview-branch', this.value)" required>
                                    <option disabled selected value="">Verify repository URL first...</option>
                                </select>

                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                </div>
                            </div>

                            @error("branch")
                                <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                            @enderror

                            <p class="text-[10px] text-blue-500 mt-2 hidden" id="branch-success">
                                <span class="font-bold">✓ Connected.</span> Found <span id="branch-count">0</span> branches.
                            </p>
                        </div>
                    </div>
                </div>

                <button class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-xl shadow-blue-500/20 hover:shadow-blue-600/40 hover:-translate-y-1" type="submit">Initialize Repository</button>
            </div>

            {{-- KOLOM KANAN: PREVIEW (Sticky) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-[#0B1120] rounded-[2rem] p-6 text-white shadow-2xl shadow-blue-900/20 border border-blue-900/30 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600 rounded-full blur-[80px] opacity-20 pointer-events-none"></div>

                        <h3 class="text-xs font-black text-zinc-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Configuration Preview
                        </h3>

                        <div class="bg-[#0f172a] rounded-xl border border-white/5 p-4 font-mono text-xs leading-relaxed mb-6 relative group">
                            <p class="text-zinc-500 mb-2"># System Initialization</p>
                            <p>
                                <span class="text-blue-400">flux</span> create project \<br>
                                &nbsp;&nbsp;--name="<span class="text-emerald-400" id="preview-name">Untitled</span>" \<br>
                                &nbsp;&nbsp;--repo="<span class="text-yellow-400 truncate max-w-[150px] inline-block align-bottom" id="preview-repo">...</span>" \<br>
                                &nbsp;&nbsp;--branch="<span class="text-pink-400" id="preview-branch">...</span>"
                            </p>
                            <p class="mt-4 text-zinc-500 animate-pulse">_</p>
                        </div>

                        {{-- Additional Info --}}
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/5">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                                <div>
                                    <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-wide">Environment</p>
                                    <p class="text-[10px] text-zinc-500 mt-0.5">Initial environment will be set to <strong class="text-white">Development</strong>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // 🔥 FITUR BARU: Konfigurasi Versi Database Dinamis 🔥
        const dbVersions = {
            mysql: [{
                    value: 'latest',
                    label: 'MySQL Latest (Recommended)'
                },
                {
                    value: '9.6',
                    label: 'MySQL 9.6 (Innovation Release)'
                },
                {
                    value: '9.5',
                    label: 'MySQL 9.5 (Innovation Release)'
                },
                {
                    value: '9.4',
                    label: 'MySQL 9.4 (Innovation Release)'
                },
                {
                    value: '9.3',
                    label: 'MySQL 9,3 (Innovation Release)'
                },
                {
                    value: '9.0',
                    label: 'MySQL 9.0 (Innovation Release)'
                },
                {
                    value: '8.4',
                    label: 'MySQL 8.4 (Long Term Support)',
                },
                {
                    value: '8.0',
                    label: 'MySQL 8.0 (Legacy Long Term Support)'
                },
                {
                    value: '5.7',
                    label: 'MySQL 5.7 (Older, not recommended)'
                },
            ],
            pgsql: [{
                    value: 'latest',
                    label: 'PostgreSQL Latest (Recommended)'
                },
                {
                    value: '18-alpine',
                    label: 'PostgreSQL 18'
                },
                {
                    value: '17-alpine',
                    label: 'PostgreSQL 17'
                },
                {
                    value: '16-alpine',
                    label: 'PostgreSQL 16'
                },
                {
                    value: '15-alpine',
                    label: 'PostgreSQL 15'
                },
                {
                    value: '14-alpine',
                    label: 'PostgreSQL 14'
                }
            ],
            mariadb: [{
                    value: 'latest',
                    label: 'MariaDB Latest (Recommended)'
                },
                {
                    value: '11.2',
                    label: 'MariaDB 11.2'
                },
                {
                    value: '10.11',
                    label: 'MariaDB 10.11 (Long Term Support)'
                },
                {
                    value: '10.6',
                    label: 'MariaDB 10.6 (Long Term Support)'
                }
            ]
        };

        function toggleDbVersion() {
            const dbType = document.querySelector('input[name="database_type"]:checked').value;
            const container = document.getElementById('db_version_container');
            const select = document.getElementById('database_version');
            const oldDbVersion = "{{ old("database_version") }}";

            if (dbType === 'sqlite') {
                container.style.display = 'none';
                select.innerHTML = ''; // Kosongkan
            } else {
                container.style.display = 'block';
                select.innerHTML = '';

                // Isi dropdown sesuai engine yang dipilih
                dbVersions[dbType].forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.value;
                    opt.text = v.label;

                    // Pertahankan state lama jika form error
                    if (v.value === oldDbVersion) {
                        opt.selected = true;
                    }

                    select.appendChild(opt);
                });
            }
        }

        function togglePhpVersion() {
            const stack = document.getElementById('stack').value;
            const phpContainer = document.getElementById('php_version_container');

            if (stack === 'laravel' || stack === 'php') {
                phpContainer.style.display = 'block';
            } else {
                phpContainer.style.display = 'none';
            }
        }

        function updatePreview(id, val) {
            const el = document.getElementById(id);
            el.innerText = val.trim() === '' ? '...' : val;
        }

        async function fetchBranches() {
            const repo = document.getElementById('repo_url').value;
            if (!repo) return alert("Please enter a URL");

            // UI Loading
            const btnText = document.getElementById('text-fetch'),
                icon = document.getElementById('icon-fetch'),
                spinner = document.getElementById('spinner-fetch'),
                msg = document.getElementById('fetch-msg'),
                select = document.getElementById('branch-select');

            btnText.innerText = "Checking...";
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            msg.classList.remove('hidden');
            msg.innerText = "Contacting git remote...";
            msg.className = "text-[10px] mt-1.5 ml-1 text-blue-500 animate-pulse";
            select.disabled = true;
            select.innerHTML = '<option>Loading...</option>';

            try {
                const token = document.querySelector('input[name="_token"]').value;

                const res = await fetch("{{ route("console.projects.fetch-branches") }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    },
                    body: JSON.stringify({
                        repository_url: repo
                    })
                });

                if (!res.ok) throw new Error("Failed");
                const data = await res.json();

                select.innerHTML = '';

                if (data.branches.length === 0) {
                    let opt = document.createElement('option');
                    opt.text = "No branches found (Empty Repo?)";
                    select.appendChild(opt);
                } else {
                    data.branches.forEach(b => {
                        let opt = document.createElement('option');
                        opt.value = b;
                        opt.innerText = b;
                        if (b === 'main' || b === 'master') opt.selected = true;
                        select.appendChild(opt);
                    });
                }

                // UI Success
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');
                icon.innerHTML = '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

                btnText.innerText = "Verified";
                btnText.classList.add('text-emerald-600');
                msg.classList.add('hidden');

                select.disabled = false;
                select.classList.remove('bg-zinc-50');
                select.classList.add('bg-white', 'ring-2', 'ring-blue-100');

                document.getElementById('branch-success').classList.remove('hidden');
                document.getElementById('branch-count').innerText = data.branches.length;

                if (data.branches.length > 0) updatePreview('preview-branch', select.value);

            } catch (e) {
                // UI Error
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');
                icon.innerHTML = '<svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

                btnText.innerText = "Error";
                btnText.classList.add('text-red-600');

                msg.innerText = "Connection failed. Check URL/Visibility.";
                msg.className = "text-[10px] mt-1.5 ml-1 text-red-500 font-bold";

                select.innerHTML = '<option disabled selected>Connection Failed</option>';
            }
        }

        window.onload = function() {
            const oldName = "{{ old("name") }}";
            const oldRepo = "{{ old("repository_url") }}";
            if (oldName) updatePreview('preview-name', oldName);
            if (oldRepo) updatePreview('preview-repo', oldRepo);

            togglePhpVersion();
            toggleDbVersion(); // 🔥 Panggil saat awal loading agar versi sesuai dengan yg dipilih
        }
    </script>
@endsection
