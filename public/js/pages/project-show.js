/**
 * Project Show Page Logic
 * Requires 'window.ProjectConfig' to be defined in Blade.
 */

const config = window.ProjectConfig;
const csrfToken = config.csrfToken;

// ==========================================
// 1. UI HELPER & MODULES
// ==========================================

/**
 * 🔥 NATIVE TOAST HELPER
 * Mengirim event ke Alpine.js di Layout (app.blade.php)
 * agar notifikasi tampil konsisten & cantik.
 */
function showNativeToast(message, type = "success") {
  window.dispatchEvent(
    new CustomEvent("notify", {
      detail: { message: message, type: type },
    }),
  );
}

// Modal Dialog (Tetap menggunakan SweetAlert untuk Interaksi Modal)
const fluxSwal =
  window.fluxSwal ||
  Swal.mixin({
    customClass: {
      popup:
        "rounded-2xl border border-zinc-200 shadow-2xl p-0 overflow-hidden font-sans",
      title: "text-zinc-900 text-lg font-bold pt-6 px-6",
      htmlContainer: "text-zinc-500 text-sm px-6 pb-6",
      confirmButton:
        "bg-zinc-900 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-800 transition-colors shadow-sm mx-2 mb-6",
      cancelButton:
        "bg-white text-zinc-600 border border-zinc-200 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-zinc-50 transition-colors mx-2 mb-6",
    },
    buttonsStyling: false,
  });

// Helper: Form Submit (Membuat form HTML tersembunyi untuk kirim data ke Laravel)
window.submitForm = function (action, method, data = {}) {
  const form = document.createElement("form");
  form.method = "POST";
  form.action = action;
  form.style.display = "none";

  const csrfInput = document.createElement("input");
  csrfInput.type = "hidden";
  csrfInput.name = "_token";
  csrfInput.value = csrfToken;
  form.appendChild(csrfInput);

  if (method !== "POST") {
    const m = document.createElement("input");
    m.type = "hidden";
    m.name = "_method";
    m.value = method;
    form.appendChild(m);
  }

  for (const [k, v] of Object.entries(data)) {
    const i = document.createElement("input");
    i.type = "hidden";
    i.name = k;
    i.value = v;
    form.appendChild(i);
  }

  document.body.appendChild(form);

  // Tampilkan loading saat form dikirim
  fluxSwal.fire({
    title: "Processing...",
    showConfirmButton: false,
    didOpen: () => Swal.showLoading(),
  });

  form.submit();
};

// Helper: Logic Role Dropdown
function getRoleOptions(currentSelected = "member") {
  let options = `<option value="member" ${currentSelected === "member" ? "selected" : ""}>Member</option>`;
  const myRole = config.currentUser.role;

  // Hanya Owner & SysAdmin yang bisa mengangkat Manager/Owner lain
  if (myRole === "sysadmin" || myRole === "owner") {
    options += `<option value="manager" ${currentSelected === "manager" ? "selected" : ""}>Manager</option>`;
    options += `<option value="owner" ${currentSelected === "owner" ? "selected" : ""}>Owner</option>`;
  }
  return options;
}

// ==========================================
// 2. FEATURE: EDIT PROJECT SETTINGS
// ==========================================

window.fetchEditBranches = async function () {
  const repoInput = document.getElementById("edit-repo");
  const branchSelect = document.getElementById("edit-branch");
  const btnCheck = document.getElementById("btn-check-repo");
  const repoUrl = repoInput.value;

  if (!repoUrl) {
    repoInput.classList.add("border-red-500");
    return;
  }
  repoInput.classList.remove("border-red-500");

  const originalBtn = btnCheck.innerHTML;
  btnCheck.disabled = true;
  btnCheck.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

  branchSelect.innerHTML = "<option>Loading...</option>";
  branchSelect.disabled = true;

  try {
    const res = await fetch(config.routes.fetchBranches, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify({ repository_url: repoUrl }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message);

    branchSelect.innerHTML = "";
    data.branches.forEach(
      (branch) =>
        (branchSelect.innerHTML += `<option value="${branch}" ${branch === config.branch ? "selected" : ""}>${branch}</option>`),
    );
    branchSelect.disabled = false;

    btnCheck.classList.remove("bg-zinc-900");
    btnCheck.classList.add("bg-emerald-500");
    btnCheck.innerHTML = `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
  } catch (err) {
    branchSelect.innerHTML = `<option value="${config.branch}" selected>${config.branch}</option>`;
    branchSelect.disabled = false;
    btnCheck.classList.remove("bg-zinc-900");
    btnCheck.classList.add("bg-rose-500");
    btnCheck.innerHTML = `<svg class="w-4 h-4 text-white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
  } finally {
    setTimeout(() => {
      btnCheck.innerHTML = originalBtn;
      btnCheck.classList.remove("bg-emerald-500", "bg-rose-500");
      btnCheck.classList.add("bg-zinc-900");
      btnCheck.disabled = false;
    }, 1500);
  }
};

window.openEditProjectModal = async function () {
  const { value: formValues } = await fluxSwal.fire({
    title: "Project Settings",
    width: "600px",
    html: `
            <div class="flex flex-col gap-5 text-left">
                <div><label class="text-[10px] font-bold text-zinc-400 uppercase">Name</label><input id="edit-name" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold" value="${config.name}"></div>
                <div><label class="text-[10px] font-bold text-zinc-400 uppercase">Repo</label><div class="flex gap-2"><input id="edit-repo" class="flex-1 px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-mono" value="${config.repository_url}"><button type="button" id="btn-check-repo" class="px-4 py-2 bg-zinc-900 text-white rounded-xl"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></button></div></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="text-[10px] font-bold text-zinc-400 uppercase">Branch</label><select id="edit-branch" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-mono"><option value="${config.branch}" selected>${config.branch}</option></select></div>
                    <div><label class="text-[10px] font-bold text-zinc-400 uppercase">Status</label><select id="edit-status" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold"><option value="active" ${config.status === "active" ? "selected" : ""}>Active</option><option value="maintenance" ${config.status === "maintenance" ? "selected" : ""}>Maintenance</option><option value="archived" ${config.status === "archived" ? "selected" : ""}>Archived</option></select></div>
                </div>
                <div><label class="text-[10px] font-bold text-zinc-400 uppercase">Desc</label><textarea id="edit-desc" rows="3" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm">${config.description}</textarea></div>
            </div>
        `,
    showCancelButton: true,
    confirmButtonText: "Save Changes",
    didOpen: () => {
      document
        .getElementById("btn-check-repo")
        .addEventListener("click", (e) => {
          e.preventDefault();
          fetchEditBranches();
        });
    },
    preConfirm: () => ({
      name: document.getElementById("edit-name").value,
      repository_url: document.getElementById("edit-repo").value,
      branch: document.getElementById("edit-branch").value,
      status: document.getElementById("edit-status").value,
      description: document.getElementById("edit-desc").value,
    }),
  });
  if (formValues) submitForm(config.routes.update, "PATCH", formValues);
};

// ==========================================
// 3. FEATURE: MANAGE MEMBERS
// ==========================================

window.openAddMemberModal = async function () {
  fluxSwal.fire({ title: "Loading...", didOpen: () => Swal.showLoading() });
  try {
    const res = await fetch(config.routes.memberSearch);
    const users = await res.json();
    let opts = "";
    users.forEach(
      (u) =>
        (opts += `<option value="${u.email}">${u.first_name ? u.first_name + " " + u.last_name : u.name}</option>`),
    );

    const { value } = await fluxSwal.fire({
      title: "Add Personnel",
      html: `<div class="flex flex-col gap-4 text-left"><div><label class="text-[10px] font-bold text-zinc-400 uppercase">User</label><input list="users" id="mem-email" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm"><datalist id="users">${opts}</datalist></div><div><label class="text-[10px] font-bold text-zinc-400 uppercase">Role</label><select id="mem-role" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm">${getRoleOptions()}</select></div></div>`,
      showCancelButton: true,
      confirmButtonText: "Invite",
      preConfirm: () => {
        const e = document.getElementById("mem-email").value;
        if (!e) Swal.showValidationMessage("Required");
        return { email: e, role: document.getElementById("mem-role").value };
      },
    });
    if (value) submitForm(config.routes.memberStore, "POST", value);
  } catch (e) {
    fluxSwal.fire("Error", "Failed to load users", "error");
  }
};

window.openEditMemberModal = async function (uid, name, role) {
  const { value } = await fluxSwal.fire({
    title: "Update Role",
    html: `<p class="text-xs text-zinc-500 mb-4 text-left">For <b>${name}</b></p><div class="text-left"><label class="text-[10px] font-bold text-zinc-400 uppercase">Role</label><select id="edit-mem-role" class="w-full px-3 py-2 border rounded-lg text-sm">${getRoleOptions(role)}</select></div>`,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => document.getElementById("edit-mem-role").value,
  });
  if (value)
    submitForm(config.routes.memberUpdate.replace(":uid", uid), "PATCH", {
      role: value,
    });
};

window.removeMember = function (uid, name) {
  fluxSwal
    .fire({
      title: "Remove User?",
      text: `Remove ${name}?`,
      showCancelButton: true,
      confirmButtonText: "Yes",
      confirmButtonClass:
        "bg-rose-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-rose-700",
    })
    .then((r) => {
      if (r.isConfirmed)
        submitForm(config.routes.memberDestroy.replace(":uid", uid), "DELETE");
    });
};

// ==========================================
// 4. FEATURE: MANAGE ENVIRONMENTS
// ==========================================

window.fetchEnvBranches = async function () {
  const s = document.getElementById("new-env-branch");
  const b = document.getElementById("btn-refresh-env-branch");
  const orig = b.innerHTML;

  b.disabled = true;
  b.innerHTML = `<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
  s.innerHTML = "<option>Fetching...</option>";
  s.disabled = true;

  try {
    const res = await fetch(config.routes.fetchBranches, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
      },
      body: JSON.stringify({ repository_url: config.repository_url }),
    });
    const d = await res.json();

    s.innerHTML = "";
    d.branches.forEach(
      (br) =>
        (s.innerHTML += `<option value="${br}" ${["main", "master", "develop"].includes(br) ? "selected" : ""}>${br}</option>`),
    );
    s.disabled = false;
    b.innerHTML = `<svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
  } catch {
    s.innerHTML = '<option value="">Manual Input</option>';
    b.innerHTML = "❌";
  }
  setTimeout(() => {
    b.disabled = false;
    b.innerHTML = orig;
  }, 2000);
};

window.openAddEnvModal = async function () {
  fluxSwal.fire({
    title: "Loading Infrastructure...",
    didOpen: () => Swal.showLoading(),
  });

  try {
    // 2. Fetch Daftar Server
    const response = await fetch("/internal/servers-list"); // Sesuaikan route langkah 1
    const servers = await response.json();

    // Jika tidak ada server tersedia
    if (servers.length === 0) {
      fluxSwal.fire(
        "Error",
        "No active servers found. Please contact System Admin.",
        "error",
      );
      return;
    }

    // 3. Buat HTML Options untuk Dropdown
    let serverOptions = servers
      .map(
        (s) =>
          `<option value="${s.id}">[${s.description || "Server"}] ${s.name} (${s.ip_address})</option>`,
      )
      .join("");

    // 4. Tampilkan Modal Form dengan Input Server
    const { value } = await fluxSwal.fire({
      title: "Provision Node",
      width: "500px",
      html: `
                <div class="flex flex-col gap-5 text-left">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Name</label>
                        <input id="new-env-name" class="w-full px-3 py-2.5 border rounded-xl text-sm font-bold" placeholder="e.g. Staging">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Type</label>
                        <div class="grid grid-cols-3 gap-3 mt-1">
                            <label class="cursor-pointer"><input type="radio" name="env_type" value="development" class="peer sr-only" checked><div class="py-2.5 rounded-xl border text-center text-xs font-bold text-zinc-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-200 hover:bg-zinc-50">DEV</div></label>
                            <label class="cursor-pointer"><input type="radio" name="env_type" value="staging" class="peer sr-only"><div class="py-2.5 rounded-xl border text-center text-xs font-bold text-zinc-500 peer-checked:bg-amber-50 peer-checked:text-amber-600 peer-checked:border-amber-200 hover:bg-zinc-50">STAGING</div></label>
                            <label class="cursor-pointer"><input type="radio" name="env_type" value="production" class="peer sr-only"><div class="py-2.5 rounded-xl border text-center text-xs font-bold text-zinc-500 peer-checked:bg-rose-50 peer-checked:text-rose-600 peer-checked:border-rose-200 hover:bg-zinc-50">PROD</div></label>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Target Server</label>
                        <div class="relative">
                            <select id="new-env-server" class="w-full px-3 py-2.5 border rounded-xl text-sm font-mono appearance-none bg-white">
                                ${serverOptions}
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-zinc-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Branch</label>
                            <button type="button" id="btn-refresh-env-branch" class="p-1 border rounded bg-white hover:bg-zinc-50"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg></button>
                        </div>
                        <div class="relative">
                            <select id="new-env-branch" class="w-full px-3 py-2.5 border rounded-xl text-sm font-mono">
                                <option value="${config.branch}">${config.branch} (Default)</option>
                            </select>
                        </div>
                    </div>
                </div>
            `,
      showCancelButton: true,
      confirmButtonText: "Provision",
      didOpen: () => {
        document
          .getElementById("btn-refresh-env-branch")
          .addEventListener("click", (e) => {
            e.preventDefault();
            fetchEnvBranches();
          });
      },
      preConfirm: () => {
        const n = document.getElementById("new-env-name").value;
        const s = document.getElementById("new-env-server").value; // Ambil nilai Server ID

        if (!n) Swal.showValidationMessage("Name required");
        if (!s) Swal.showValidationMessage("Please select a server");

        return {
          name: n,
          server_id: s, // 🔥 Kirim ke Backend
          type: document.querySelector('input[name="env_type"]:checked').value,
          branch: document.getElementById("new-env-branch").value,
        };
      },
    });

    if (value) submitForm(config.routes.envStore, "POST", value);
  } catch (error) {
    console.error(error);
    fluxSwal.fire("Error", "Failed to fetch server list.", "error");
  }
};

window.confirmDeleteEnv = function (id, name, type) {
  const isProd = name.toLowerCase().includes("prod") || type === "production";
  fluxSwal
    .fire({
      title: isProd ? "🚨 DELETE PRODUCTION?" : "Teardown Node?",
      html: `<div class="text-left"><p class="text-sm text-zinc-500 mb-3">Delete <b>${name}</b>?</p><div class="${isProd ? "bg-rose-50 text-rose-700" : "bg-zinc-50 text-zinc-600"} p-3 rounded-lg text-xs flex gap-2 border"><span>${isProd ? "EXTREME DANGER: Live site will go offline." : "Permanent action."}</span></div></div>`,
      showCancelButton: true,
      confirmButtonText: isProd ? "I UNDERSTAND" : "Yes, Teardown",
      confirmButtonColor: isProd ? "#dc2626" : "#18181b",
    })
    .then((r) => {
      if (r.isConfirmed)
        submitForm(config.routes.envDestroy.replace(":envId", id), "DELETE");
    });
};

// ==========================================
// 5. UTILS & ACTIONS
// ==========================================

window.copyToClipboard = (t, m) => {
  navigator.clipboard.writeText(t).then(() => {
    showNativeToast(m, "success"); // Memanggil Alpine Toast di Layout
  });
};

window.deployConfirm = (envId, envName) => {
  fluxSwal
    .fire({
      title: "Deploy Infrastructure?",
      text: `Initialize deployment sequence for ${envName}?`,
      icon: "info",
      showCancelButton: true,
      confirmButtonText: "Yes, Deploy Now",
      confirmButtonColor: "#2563eb", // blue-600
      showLoaderOnConfirm: true, // Menambahkan efek loading pada tombol
      preConfirm: async () => {
        try {
          // Tembak URL deployment yang ada di config
          const deployUrl = config.routes.envDeploy.replace(":envId", envId);

          const response = await fetch(deployUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken,
              Accept: "application/json",
            },
          });

          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.message || "Deployment failed to initialize");
          }

          return data;
        } catch (error) {
          Swal.showValidationMessage(`Request failed: ${error.message}`);
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    })
    .then((result) => {
      if (result.isConfirmed) {
        // Tampilkan Notifikasi Sukses
        showNativeToast(`Deployment queued for ${envName}`, "success");

        // Opsional: Reload halaman agar UI memperbarui status (jika Anda belum punya Livewire/Websockets)
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      }
    });
};

window.confirmTermination = () => {
  fluxSwal
    .fire({
      title: "Terminate?",
      html: "Delete project?",
      showCancelButton: true,
      confirmButtonText: "Yes, Terminate",
      confirmButtonColor: "#dc2626",
    })
    .then((r) => {
      if (r.isConfirmed) submitForm(config.routes.destroy, "DELETE");
    });
};
