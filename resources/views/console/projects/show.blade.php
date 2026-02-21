@extends("layouts.app")

@section("title", $project->name)
@section("page_title", "Mission Control")
@section("page_subtitle", "Overview and operational status.")

@section("content")
    <div class="space-y-8 max-w-7xl mx-auto">

        {{-- 0. SYSADMIN BANNER --}}
        @if (auth()->user()->role === "System Administrator")
            <div class="bg-indigo-900 text-indigo-100 px-6 py-3 rounded-xl flex items-center justify-between shadow-lg shadow-indigo-900/20 border border-indigo-700">
                <div class="flex items-center gap-3">
                    <span class="bg-white/10 p-1.5 rounded-lg"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg></span>
                    <p class="text-sm font-medium">You are viewing this project with <b>System Administrator</b> privileges.</p>
                </div>
                @if (!$project->members->contains(auth()->id()))
                    <span class="text-[10px] uppercase font-bold tracking-wider bg-black/30 px-2 py-1 rounded text-white/70">Ghost Mode</span>
                @endif
            </div>
        @endif

        {{-- 1. HEADER (Identity & Actions) --}}
        @include("console.projects.partials.header")

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 space-y-6">
                {{-- 2. LEFT COLUMN (Stats & Team) --}}
                @include("console.projects.partials.stats")
                @include("console.projects.partials.team")
            </div>

            <div class="lg:col-span-2 space-y-6">
                {{-- 3. RIGHT COLUMN (Environments) --}}
                @include("console.projects.partials.environments")
            </div>
        </div>
    </div>

    @push("scripts")
        {{-- 🔥 DATA BRIDGE: Mengirim Data PHP ke JavaScript Eksternal --}}
        <script>
            window.ProjectConfig = {
                // ✅ WAJIB PAKAI TANDA KUTIP ("...") UNTUK UUID
                id: "{{ $project->id }}",
                name: "{{ $project->name }}",
                repository_url: "{{ $project->repository_url }}",
                branch: "{{ $project->branch }}",
                status: "{{ $project->status }}",

                // Escape description agar aman dari enter/kutip dalam teks
                description: `{{ str_replace(["\r", "\n"], ["", "\\n"], addslashes($project->description)) }}`,

                csrfToken: "{{ csrf_token() }}",

                currentUser: {
                    // ✅ FIX: USER ID JUGA HARUS DIKUTIP (Jaga-jaga jika User ID anda juga UUID)
                    id: "{{ auth()->id() }}",

                    // Logic Role (Pastikan kutipnya rapi)
                    role: "{{ auth()->user()->role === "System Administrator" ? "sysadmin" : $project->members->firstWhere("id", auth()->id())?->pivot->role ?? "member" }}"
                },

                // Definisi Route untuk dipanggil JS
                routes: {
                    fetchBranches: "{{ route("console.projects.fetch-branches") }}",
                    update: "{{ route("console.projects.update", $project->id) }}",
                    destroy: "{{ route("console.projects.destroy", $project->id) }}",

                    // Route Environments
                    envStore: "{{ route("console.projects.environments.store", $project->id) }}",
                    envDestroy: "{{ route("console.projects.environments.destroy", [$project->id, ":envId"]) }}",
                    envDeploy: "{{ route("console.projects.environments.deploy", [$project->id, ":envId"]) }}",

                    // Route Members (Pastikan route ini ada di web.php)
                    // Jika error 404, tambahkan 'console.' di depan nama route ini
                    memberSearch: "{{ route("projects.members.search", $project->id) }}",
                    memberStore: "{{ route("projects.members.store", $project->id) }}",
                    memberUpdate: "{{ route("projects.members.update", [$project->id, ":uid"]) }}",
                    memberDestroy: "{{ route("projects.members.destroy", [$project->id, ":uid"]) }}"
                }
            };
        </script>

        {{-- Panggil File JS Eksternal --}}
        <script src="{{ asset("js/pages/project-show.js") }}"></script>
    @endpush
@endsection
