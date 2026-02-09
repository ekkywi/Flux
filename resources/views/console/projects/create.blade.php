@extends("layouts.app")
@section("title", "Initialize Project")
@section("page_title", "Repository Setup")

@section("content")
    {{-- WRAPPER UTAMA: Flex Center & H-Full agar pas di tengah layar tanpa scroll --}}
    <div class="h-full flex flex-col justify-center items-center p-6">

        <form action="{{ route("console.projects.store") }}" class="w-full max-w-5xl" id="wizardForm" method="POST">
            @csrf

            {{-- CARD WIZARD CONTAINER --}}
            <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-200 overflow-hidden flex flex-col md:flex-row min-h-[420px]">

                {{-- PANEL KIRI: INFO & PROGRESS (Dark Mode) --}}
                <div class="bg-slate-900 p-8 md:w-1/3 flex flex-col justify-between relative overflow-hidden">
                    {{-- Decorative Blurs --}}
                    <div class="absolute top-0 right-0 -mr-12 -mt-12 w-40 h-40 rounded-full bg-indigo-500 blur-3xl opacity-20"></div>
                    <div class="absolute bottom-0 left-0 -ml-12 -mb-12 w-40 h-40 rounded-full bg-rose-500 blur-3xl opacity-20"></div>

                    {{-- Header Section --}}
                    <div class="relative z-10 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white/10 border border-white/5 backdrop-blur-md">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Wizard Mode</span>
                        </div>

                        <div>
                            <h1 class="text-3xl font-black text-white tracking-tight leading-none mb-2">Initialize<br>Protocol</h1>
                            <p class="text-xs font-medium text-slate-400 leading-relaxed" id="stepDescription">
                                Step 1: Configure project identity.
                            </p>
                        </div>
                    </div>

                    {{-- Progress Steps Visual --}}
                    <div class="relative z-10 space-y-4">
                        {{-- Step 1 Indicator --}}
                        <div class="flex items-center gap-4 transition-all duration-300 opacity-100" id="indicator-1">
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-[10px] font-bold text-white border border-indigo-400 ring-4 ring-indigo-500/20" id="badge-1">01</div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-white">Identity</span>
                        </div>
                        {{-- Step 2 Indicator --}}
                        <div class="flex items-center gap-4 transition-all duration-300 opacity-40" id="indicator-2">
                            <div class="h-8 w-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-400 border border-slate-700" id="badge-2">02</div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Source Control</span>
                        </div>
                        {{-- Step 3 Indicator --}}
                        <div class="flex items-center gap-4 transition-all duration-300 opacity-40" id="indicator-3">
                            <div class="h-8 w-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-400 border border-slate-700" id="badge-3">03</div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Confirmation</span>
                        </div>
                    </div>
                </div>

                {{-- PANEL KANAN: FORM INPUTS (White Mode) --}}
                <div class="p-8 md:w-2/3 bg-white flex flex-col relative">

                    {{-- STEP 1: IDENTITY --}}
                    <div class="step-content h-full flex flex-col justify-center space-y-6" id="step-1">
                        <div class="space-y-4">
                            <div class="group">
                                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Project Identifier</label>

                                {{-- Input Name --}}
                                <input autofocus class="block w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all outline-none" id="inputName" name="name" oninput="clearError('name')" placeholder="FLUX_APP_V1" required type="text" value="{{ old("name") }}">

                                {{-- Error Message Container --}}
                                <p class="hidden mt-2 text-[9px] font-black text-rose-500 uppercase tracking-widest ml-1 animate-pulse" id="error-name"></p>
                            </div>

                            <div class="group">
                                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Description <span class="font-normal text-slate-300 normal-case">(Optional)</span></label>
                                <input class="block w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-medium text-slate-600 placeholder-slate-300 focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all outline-none" name="description" placeholder="Brief deployment notes..." type="text" value="{{ old("description") }}">
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: SOURCE --}}
                    <div class="step-content h-full flex flex-col justify-center space-y-6 hidden" id="step-2">
                        <div class="group">
                            <label class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Git Repository URL</label>

                            {{-- Input Repo --}}
                            <input class="block w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-mono font-medium text-indigo-600 placeholder-slate-300 focus:bg-white focus:border-indigo-600 focus:ring-0 transition-all outline-none" id="inputRepo" name="repository_url" oninput="clearError('repo')" placeholder="https://github.com/..." type="url" value="{{ old("repository_url") }}">

                            {{-- Error Message Container --}}
                            <p class="hidden mt-2 text-[9px] font-black text-rose-500 uppercase tracking-widest ml-1 animate-pulse" id="error-repo"></p>

                            <p class="mt-3 text-[10px] text-slate-400 font-medium ml-1">
                                Ensure the repository is public or your SSH keys are configured in Settings.
                            </p>
                        </div>
                    </div>

                    {{-- STEP 3: CONFIRMATION --}}
                    <div class="step-content h-full flex flex-col justify-center space-y-6 hidden" id="step-3">
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 text-center space-y-4">
                            <div class="h-16 w-16 mx-auto bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight">Ready to Initialize</h3>
                                <p class="text-xs text-slate-500 font-medium mt-1">Review your configuration before proceeding.</p>
                            </div>

                            {{-- Summary Box --}}
                            <div class="text-left bg-white p-4 rounded-xl border border-slate-200 space-y-2 shadow-sm">
                                <div class="flex justify-between border-b border-slate-50 pb-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Project</span>
                                    <span class="text-[10px] font-black text-slate-800 uppercase" id="summaryName">...</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Repo</span>
                                    <span class="text-[10px] font-mono font-bold text-indigo-600 truncate max-w-[200px]" id="summaryRepo">...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NAVIGATION BUTTONS --}}
                    <div class="pt-6 mt-auto border-t border-slate-100 flex items-center justify-between">
                        {{-- Back Button --}}
                        <button class="hidden px-5 py-3 rounded-xl text-slate-400 font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 hover:text-slate-700 transition-all" id="btnBack" onclick="prevStep()" type="button">
                            &larr; Back
                        </button>
                        {{-- Cancel Button (Only on Step 1) --}}
                        <a class="px-5 py-3 rounded-xl text-slate-400 font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 hover:text-slate-700 transition-all" href="{{ route("console.projects.index") }}" id="btnCancel">
                            Cancel
                        </a>

                        {{-- Next Button --}}
                        <button class="px-8 py-3 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center gap-2" id="btnNext" onclick="nextStep()" type="button">
                            Next Step &rarr;
                        </button>

                        {{-- Submit Button --}}
                        <button class="hidden px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-200 transform hover:-translate-y-0.5 transition-all flex items-center gap-2" id="btnSubmit" onclick="submitWizard()" type="button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            </svg>
                            Engage Protocol
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    @push("scripts")
        <script>
            let currentStep = 1;
            const totalSteps = 3;

            // --- 1. UI RENDERER ---
            function updateUI() {
                // Toggle Step Visibility
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
                document.getElementById(`step-${currentStep}`).classList.remove('hidden');

                // Update Indicators
                for (let i = 1; i <= totalSteps; i++) {
                    const indicator = document.getElementById(`indicator-${i}`);
                    const badge = document.getElementById(`badge-${i}`);
                    const text = indicator.querySelector('span');

                    if (i === currentStep) { // Active
                        indicator.className = "flex items-center gap-4 transition-all duration-300 opacity-100";
                        badge.className = "h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-[10px] font-bold text-white border border-indigo-400 ring-4 ring-indigo-500/20 transition-all";
                        badge.innerHTML = '0' + i;
                        text.className = "text-[10px] font-black uppercase tracking-widest text-white transition-all";
                    } else if (i < currentStep) { // Completed
                        indicator.className = "flex items-center gap-4 transition-all duration-300 opacity-60";
                        badge.className = "h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-[10px] font-bold text-white border border-emerald-400 transition-all";
                        badge.innerHTML = '✓';
                        text.className = "text-[10px] font-black uppercase tracking-widest text-slate-300 transition-all";
                    } else { // Inactive
                        indicator.className = "flex items-center gap-4 transition-all duration-300 opacity-40";
                        badge.className = "h-8 w-8 rounded-full bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-400 border border-slate-700 transition-all";
                        badge.innerHTML = '0' + i;
                        text.className = "text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all";
                    }
                }

                // Description
                const desc = document.getElementById('stepDescription');
                if (currentStep === 1) desc.innerText = "Step 1: Configure project identity.";
                if (currentStep === 2) desc.innerText = "Step 2: Connect source control.";
                if (currentStep === 3) desc.innerText = "Step 3: Final review.";

                // Buttons Visibility
                const btnBack = document.getElementById('btnBack');
                const btnCancel = document.getElementById('btnCancel');
                const btnNext = document.getElementById('btnNext');
                const btnSubmit = document.getElementById('btnSubmit');

                if (currentStep === 1) {
                    btnBack.classList.add('hidden');
                    btnCancel.classList.remove('hidden');
                } else {
                    btnBack.classList.remove('hidden');
                    btnCancel.classList.add('hidden');
                }

                if (currentStep === totalSteps) {
                    btnNext.classList.add('hidden');
                    btnSubmit.classList.remove('hidden');

                    // Populate Summary
                    document.getElementById('summaryName').innerText = document.getElementById('inputName').value;
                    document.getElementById('summaryRepo').innerText = document.getElementById('inputRepo').value;
                } else {
                    btnNext.classList.remove('hidden');
                    btnSubmit.classList.add('hidden');
                }
            }

            // --- 2. VALIDATION LOGIC ---
            function showError(field, message) {
                const inputID = field === 'name' ? 'inputName' : 'inputRepo';
                const input = document.getElementById(inputID);
                const errorText = document.getElementById(`error-${field}`);

                input.classList.remove('border-slate-100', 'focus:border-indigo-600');
                input.classList.add('border-rose-500', 'focus:border-rose-500', 'bg-rose-50');
                errorText.innerText = message;
                errorText.classList.remove('hidden');
                input.classList.add('animate-pulse');
                setTimeout(() => input.classList.remove('animate-pulse'), 500);
            }

            function clearError(field) {
                const inputID = field === 'name' ? 'inputName' : 'inputRepo';
                const input = document.getElementById(inputID);
                const errorText = document.getElementById(`error-${field}`);
                input.classList.remove('border-rose-500', 'focus:border-rose-500', 'bg-rose-50');
                input.classList.add('border-slate-100', 'focus:border-indigo-600');
                errorText.classList.add('hidden');
            }

            // --- 3. NAVIGATION & SUBMISSION ---
            function nextStep() {
                if (currentStep === 1) {
                    const nameVal = document.getElementById('inputName').value.trim();
                    if (!nameVal) {
                        showError('name', 'PROJECT IDENTIFIER IS REQUIRED');
                        return;
                    }
                    if (nameVal.length < 3) {
                        showError('name', 'IDENTIFIER MUST BE > 3 CHARACTERS');
                        return;
                    }
                }
                if (currentStep === 2) {
                    const repoVal = document.getElementById('inputRepo').value.trim();
                    if (!repoVal) {
                        showError('repo', 'REPOSITORY URL IS REQUIRED');
                        return;
                    }
                }

                if (currentStep < totalSteps) {
                    currentStep++;
                    updateUI();
                }
            }

            function prevStep() {
                if (currentStep > 1) {
                    currentStep--;
                    updateUI();
                }
            }

            // FUNGSI BARU: Manual Submit
            function submitWizard() {
                const btn = document.getElementById('btnSubmit');

                // UX: Disable button & Show Loading
                btn.disabled = true;
                btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            INITIALIZING...
        `;
                btn.classList.add('opacity-75', 'cursor-not-allowed');

                // Submit Form
                document.getElementById('wizardForm').submit();
            }

            // Handle Enter Key
            document.getElementById('wizardForm').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Mencegah submit default browser

                    if (currentStep < totalSteps) {
                        nextStep(); // Jika step 1 atau 2, lanjut next
                    } else {
                        submitWizard(); // Jika step 3, jalankan submit manual
                    }
                }
            });

            // Init
            updateUI();
        </script>
    @endpush
@endsection
