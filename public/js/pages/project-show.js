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
                <div>
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Name</label>
                    <input id="edit-name" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold" value="${config.name}">
                </div>
                
                <div>
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Repo</label>
                    <div class="flex gap-2">
                        <input id="edit-repo" class="flex-1 px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-mono" value="${config.repository_url}">
                        <button type="button" id="btn-check-repo" class="px-4 py-2 bg-zinc-900 text-white rounded-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Branch</label>
                        <select id="edit-branch" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-mono"><option value="${config.branch}" selected>${config.branch}</option></select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Status</label>
                        <select id="edit-status" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold">
                            <option value="active" ${config.status === "active" ? "selected" : ""}>Active</option>
                            <option value="maintenance" ${config.status === "maintenance" ? "selected" : ""}>Maintenance</option>
                            <option value="archived" ${config.status === "archived" ? "selected" : ""}>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">Project Stack</label>
                        <select id="edit-stack" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold" onchange="toggleEditPhpVersion()">
                            <option value="laravel" ${config.stack === "laravel" || config.stack === "php" ? "selected" : ""}>Laravel / PHP</option>
                            <option value="nodejs" ${config.stack === "nodejs" ? "selected" : ""}>Node.js</option>
                            <option value="html" ${config.stack === "html" ? "selected" : ""}>Static HTML</option>
                        </select>
                    </div>
                    <div id="edit-php-container" style="${config.stack === "laravel" || config.stack === "php" || !config.stack ? "display:block;" : "display:none;"}">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase">PHP Version</label>
                        <select id="edit-php-version" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm font-bold">
                            <option value="8.4" ${config.php_version === "8.4" ? "selected" : ""}>PHP 8.4 (Latest)</option>
                            <option value="8.3" ${config.php_version === "8.3" ? "selected" : ""}>PHP 8.3</option>
                            <option value="8.2" ${config.php_version === "8.2" ? "selected" : ""}>PHP 8.2</option>
                            <option value="8.1" ${config.php_version === "8.1" ? "selected" : ""}>PHP 8.1</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-zinc-400 uppercase">Desc</label>
                    <textarea id="edit-desc" rows="3" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-sm">${config.description}</textarea>
                </div>
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
    preConfirm: () => {
      // Tangkap data dari form modal SweetAlert
      const name = document.getElementById("edit-name").value;
      const repository_url = document.getElementById("edit-repo").value;
      const branch = document.getElementById("edit-branch").value;
      const status = document.getElementById("edit-status").value;
      const description = document.getElementById("edit-desc").value;
      const stack = document.getElementById("edit-stack").value;
      const php_version = document.getElementById("edit-php-version").value;

      return {
        name,
        repository_url,
        branch,
        status,
        description,
        stack, // 🔥 Kirim ke Laravel
        php_version:
          stack === "laravel" || stack === "php" ? php_version : null, // Kirim PHP version jika stack sesuai
      };
    },
  });

  if (formValues) submitForm(config.routes.update, "PATCH", formValues);
};

window.toggleEditPhpVersion = function () {
  const stackSelect = document.getElementById("edit-stack");
  const phpContainer = document.getElementById("edit-php-container");

  if (stackSelect && phpContainer) {
    const selectedStack = stackSelect.value.toLowerCase();
    if (selectedStack === "laravel" || selectedStack === "php") {
      phpContainer.style.display = "block";
    } else {
      phpContainer.style.display = "none";
    }
  }
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
    // Fetch Daftar Server
    const response = await fetch("/internal/servers-list");
    const servers = await response.json();

    if (servers.length === 0) {
      fluxSwal.fire(
        "Error",
        "No active servers found. Please contact System Admin.",
        "error",
      );
      return;
    }

    let serverOptions = servers
      .map(
        (s) =>
          `<option value="${s.id}">[${s.description || "Server"}] ${s.name} (${s.ip_address})</option>`,
      )
      .join("");

    // Fallback aman untuk database_type
    const dbType = config.database_type || "sqlite";
    const dbNameDisplay =
      dbType === "pgsql" ? "POSTGRESQL" : dbType.toUpperCase();

    // Logika HTML DB Server
    let dbServerHtml = "";
    if (dbType !== "sqlite") {
      dbServerHtml = `
        <div class="mt-5 pt-4 border-t border-zinc-100">
            <label class="text-[10px] font-bold text-zinc-400 uppercase">Target Database Server</label>
            <div class="relative mt-1">
                <select id="new-env-db-server" class="w-full px-3 py-2.5 border border-blue-200 rounded-xl text-sm font-mono appearance-none bg-blue-50 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled selected>-- Select Database Server --</option>
                    ${serverOptions}
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <p class="text-[9px] font-bold text-blue-500 uppercase mt-1.5 flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                ${dbNameDisplay} container will be deployed here.
            </p>
        </div>
        `;
    }

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
                <label class="text-[10px] font-bold text-zinc-400 uppercase">Target App Server</label>
                <div class="relative mt-1">
                    <select id="new-env-server" class="w-full px-3 py-2.5 border rounded-xl text-sm font-mono appearance-none bg-white">
                        <option value="" disabled selected>-- Select Application Server --</option>
                        ${serverOptions}
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            ${dbServerHtml}

            <div class="mt-2 p-4 rounded-xl border border-zinc-200 bg-zinc-50 text-left">
                <label class="flex items-center cursor-pointer gap-3">
                    <div class="relative shrink-0">
                        <input type="checkbox" name="install_ioncube" id="install_ioncube" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-zinc-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-zinc-900">Enable ionCube Loader</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5 leading-tight">Install PHP extension to run encrypted source code (zend_extension).</div>
                    </div>
                </label>
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
        const s = document.getElementById("new-env-server").value;

        // 🔥 Tangkap nilai checkbox ionCube
        const ioncubeCheckbox = document.getElementById("install_ioncube");
        const installIoncube = ioncubeCheckbox
          ? ioncubeCheckbox.checked
          : false;

        // Cek target DB secara aman
        const dbSelect = document.getElementById("new-env-db-server");
        const db_s = dbSelect ? dbSelect.value : null;

        if (!n) Swal.showValidationMessage("Name required");
        else if (!s)
          Swal.showValidationMessage("Please select an Application server");
        else if (dbType !== "sqlite" && !db_s)
          Swal.showValidationMessage("Please select a Database server");

        return {
          name: n,
          server_id: s,
          db_server_id: db_s,
          type: document.querySelector('input[name="env_type"]:checked').value,
          branch: document.getElementById("new-env-branch").value,
          install_ioncube: installIoncube ? 1 : 0, // 🔥 Kirim data ini ke Laravel Controller
        };
      },
    });

    // Jika form disubmit dan lolos validasi, kirim ke Backend
    if (value) submitForm(config.routes.envStore, "POST", value);
  } catch (error) {
    console.error(error);
    fluxSwal.fire(
      "Error",
      "Failed to load provision form. Check console.",
      "error",
    );
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

window.openEnvSettings = async function (envId, envName, base64Script) {
  const currentScript = base64Script ? atob(base64Script) : "";

  const { value } = await fluxSwal.fire({
    title: `${envName} Settings`,
    width: "600px",
    html: `
        <div class="flex flex-col gap-4 text-left">
            <div>
                <label class="text-[10px] font-bold text-zinc-400 uppercase flex items-center gap-2 mb-2">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Post-Deploy Script
                </label>
                <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl mb-3 text-[11px] text-blue-700 leading-relaxed">
                    This script runs <b>inside the application container</b> automatically every time you click Deploy/Redeploy, right after the container is built and running.
                </div>
                <textarea id="edit-deploy-script" rows="8" class="w-full px-4 py-3 bg-zinc-950 text-emerald-400 border border-zinc-800 rounded-xl text-xs font-mono focus:ring-emerald-500 focus:border-emerald-500 shadow-inner" spellcheck="false">${currentScript}</textarea>
            </div>
        </div>
    `,
    showCancelButton: true,
    confirmButtonText: "Save Configuration",
    preConfirm: () => {
      return {
        deploy_script: document.getElementById("edit-deploy-script").value,
      };
    },
  });

  if (value) {
    const updateUrl = config.routes.envDestroy.replace(":envId", envId);
    submitForm(updateUrl, "PATCH", value);
  }
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
        // setTimeout(() => {
        //   window.location.reload();
        // }, 1500);
      }
    });
};

window.startConfirm = (envId, envName) => {
  fluxSwal
    .fire({
      title: "Start Environment?",
      html: `Are you sure you want to start <b>${envName}</b>?<br><br><span class='text-xs text-zinc-500 block text-left mt-2'>This will boot up the existing containers without pulling new code or resetting the database.</span>`,
      icon: "info",
      showCancelButton: true,
      confirmButtonText: "Yes, Start it!",
      confirmButtonColor: "#10b981",
      cancelButtonText: "Cancel",
      reverseButtons: true,
      showLoaderOnConfirm: true,
      preConfirm: async () => {
        try {
          const startUrl = config.routes.envDeploy
            .replace(":envId", envId)
            .replace("/deploy", "/start");

          const response = await fetch(startUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken,
              Accept: "application/json",
            },
          });

          if (response.redirected) {
            return { message: "Start signal sent successfully" };
          }

          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.message || "Failed to send start signal");
          }

          return data;
        } catch (error) {
          submitForm(
            config.routes.envDeploy
              .replace(":envId", envId)
              .replace("/deploy", "/start"),
            "POST",
          );
          return false;
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    })
    .then((result) => {
      if (result.isConfirmed && result.value) {
        showNativeToast(`Start signal sent to ${envName}`, "success");
      }
    });
};

window.stopConfirm = (envId, envName) => {
  fluxSwal
    .fire({
      title: "Stop Environment?",
      html: `Are you sure you want to shut down <b>${envName}</b>?<br><br><span class='text-xs text-rose-500 font-bold bg-rose-50 px-3 py-2 rounded-lg border border-rose-100 block text-left'>Warning: All containers on the Application Server and Database will be forcibly terminated.</span>`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, Turn it off",
      confirmButtonColor: "#e11d48",
      cancelButtonText: "Cancel",
      reverseButtons: true,
      showLoaderOnConfirm: true,
      preConfirm: async () => {
        try {
          const stopUrl = config.routes.envDeploy
            .replace(":envId", envId)
            .replace("/deploy", "/stop");

          const response = await fetch(stopUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": csrfToken,
              Accept: "application/json",
            },
          });

          if (response.redirected) {
            return { message: "Stop signal sent successfully" };
          }

          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.message || "Failed to send stop signal");
          }

          return data;
        } catch (error) {
          submitForm(
            config.routes.envDeploy
              .replace(":envId", envId)
              .replace("/deploy", "/stop"),
            "POST",
          );
          return false;
        }
      },
      allowOutsideClick: () => !Swal.isLoading(),
    })
    .then((result) => {
      if (result.isConfirmed && result.value) {
        showNativeToast(`Stop signal sent to ${envName}`, "success");

        // setTimeout(() => {
        //   window.location.reload();
        // }, 1500);
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

// ==========================================
// 6. REAL-TIME ENGINE (SILENT POLLING)
// ==========================================

window.startEnvironmentPolling = function () {
  const envList = document.getElementById("environments-list");
  if (!envList) return;

  setInterval(async () => {
    try {
      const res = await fetch(window.location.href, {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Cache-Control": "no-cache",
        },
      });

      if (!res.ok) return;
      const html = await res.text();

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");

      const newList = doc.getElementById("environments-list");

      if (newList && envList.innerHTML !== newList.innerHTML) {
        envList.innerHTML = newList.innerHTML;
      }
    } catch (e) {}
  }, 3000);
};

document.addEventListener("DOMContentLoaded", window.startEnvironmentPolling);

// ==========================================
// 7. LIVE TERMINAL (LOGS VIEWER)
// ==========================================

window.terminalInterval = null;

window.openTerminal = function (envId, envName) {
  fluxSwal.fire({
    title: `Terminal: ${envName}`,
    html: `
            <div class="bg-zinc-950 rounded-xl p-4 mt-2 h-96 overflow-y-auto text-left font-mono text-xs text-emerald-400 shadow-inner flex flex-col" id="terminal-screen-${envId}">
                <div class="text-zinc-500 mb-2">Connecting to build server...</div>
                <div id="terminal-output-${envId}"></div>
                <div id="terminal-spinner-${envId}" class="mt-2 text-zinc-400 animate-pulse">_</div>
            </div>
        `,
    width: "800px",
    showConfirmButton: false,
    showCloseButton: true,
    allowOutsideClick: false,
    didClose: () => {
      if (window.terminalInterval) clearInterval(window.terminalInterval);
    },
  });

  const outputDiv = document.getElementById(`terminal-output-${envId}`);
  const screenDiv = document.getElementById(`terminal-screen-${envId}`);
  const spinnerDiv = document.getElementById(`terminal-spinner-${envId}`);

  const fetchLogs = async () => {
    try {
      const logUrl = config.routes.envDeploy
        .replace(":envId", envId)
        .replace("/deploy", "/logs");

      const res = await fetch(logUrl);
      const data = await res.json();

      if (data.logs && data.logs.length > 0) {
        let logHtml = data.logs
          .map((line) => {
            if (
              line.includes("ERROR") ||
              line.includes("FAIL") ||
              line.includes("FATAL")
            ) {
              return `<div class="text-rose-500">${line}</div>`;
            }
            if (line.includes("Initializing") || line.includes("completed")) {
              return `<div class="text-blue-400">${line}</div>`;
            }
            return `<div>${line}</div>`;
          })
          .join("");

        if (outputDiv.innerHTML !== logHtml) {
          outputDiv.innerHTML = logHtml;
          screenDiv.scrollTop = screenDiv.scrollHeight;
        }
      }

      if (data.status === "completed" || data.status === "failed") {
        spinnerDiv.style.display = "none";
        if (window.terminalInterval) clearInterval(window.terminalInterval);
      } else {
        spinnerDiv.style.display = "block";
      }
    } catch (e) {
      console.error("Gagal menarik log", e);
    }
  };

  fetchLogs();
  if (window.terminalInterval) clearInterval(window.terminalInterval);
  window.terminalInterval = setInterval(fetchLogs, 2000);
};

// ==========================================
// 8. AD-HOC WEB TERMINAL (CLI)
// ==========================================

// Helper untuk mencegah XSS jika output Linux mengandung tag HTML
window.escapeHtmlCLI = function (text) {
  if (!text) return "";
  return text
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
};

window.openWebCLI = function (envId, envName, commandUrl) {
  fluxSwal.fire({
    title: `CLI: ${envName}`,
    width: "800px",
    showConfirmButton: false,
    showCloseButton: true,
    html: `
            <div class="bg-zinc-950 rounded-xl p-4 h-[400px] flex flex-col font-mono text-xs text-left shadow-inner mt-2">
                <div class="text-zinc-500 mb-2 border-b border-zinc-800 pb-2">
                    Connected to container app. Type a command (e.g., <span class="text-emerald-500">php artisan about</span>) and press Enter.
                </div>
                
                <div id="cli-output-${envId}" class="flex-1 overflow-y-auto whitespace-pre-wrap break-all text-zinc-300 pb-2 leading-relaxed"></div>
                
                <div class="flex items-center gap-2 text-emerald-400 pt-2 shrink-0">
                    <span class="font-bold">$&gt;</span>
                    <input type="text" id="cli-input-${envId}" class="flex-1 bg-transparent border-none outline-none text-emerald-400 focus:ring-0 p-0 font-mono text-xs placeholder-zinc-700" autocomplete="off" spellcheck="false" placeholder="Enter command...">
                </div>
            </div>
        `,
    didOpen: () => {
      const inputField = document.getElementById(`cli-input-${envId}`);
      const outputArea = document.getElementById(`cli-output-${envId}`);

      // Auto-focus kursor saat modal terbuka
      inputField.focus();

      // Dengarkan tombol Enter
      inputField.addEventListener("keydown", async (e) => {
        if (e.key === "Enter") {
          const cmd = inputField.value.trim();
          if (!cmd) return;

          // 1. Tampilkan perintah yang diketik user ke layar (Warna Abu-abu)
          outputArea.innerHTML += `<div><span class="text-zinc-500">$&gt; ${escapeHtmlCLI(cmd)}</span></div>`;

          // 2. Kosongkan input & disable sementara
          inputField.value = "";
          inputField.disabled = true;

          // 3. Tampilkan animasi loading
          const loadingId = `cli-loading-${Date.now()}`;
          outputArea.innerHTML += `<div id="${loadingId}" class="text-zinc-600 animate-pulse">Running...</div>`;
          outputArea.scrollTop = outputArea.scrollHeight; // Scroll ke bawah

          try {
            // 4. Kirim perintah ke Laravel Controller
            const response = await fetch(commandUrl, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
              },
              body: JSON.stringify({ command: cmd }),
            });

            const data = await response.json();

            // Hapus animasi loading
            document.getElementById(loadingId).remove();

            // 5. Cetak balasan dari server
            if (data.status === "success") {
              outputArea.innerHTML += `<div class="text-emerald-300 mb-2">${escapeHtmlCLI(data.output)}</div>`;
            } else {
              outputArea.innerHTML += `<div class="text-rose-400 mb-2">${escapeHtmlCLI(data.output)}</div>`;
            }
          } catch (error) {
            document.getElementById(loadingId).remove();
            outputArea.innerHTML += `<div class="text-rose-500 mb-2">Network Error: Request failed.</div>`;
          }

          // 6. Kembalikan kursor input
          inputField.disabled = false;
          inputField.focus();
          outputArea.scrollTop = outputArea.scrollHeight;
        }
      });

      // Fokuskan kembali input jika user mengklik area hitam
      document
        .getElementById(`cli-output-${envId}`)
        .addEventListener("click", () => {
          inputField.focus();
        });
    },
  });
};
