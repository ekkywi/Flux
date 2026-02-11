// resources/js/flux.js

// 1. Dropdown Logic
// Kita tempel ke window agar bisa dipanggil via onclick="toggleDropdown()"
window.toggleDropdown = function (id) {
  const el = document.getElementById(id);
  const all = ["notif-dropdown", "user-dropdown"];

  all.forEach((oid) => {
    if (oid !== id) {
      const other = document.getElementById(oid);
      if (other && !other.classList.contains("hidden")) {
        other.classList.add("hidden");
      }
    }
  });

  if (el) {
    el.classList.toggle("hidden");
    if (!el.classList.contains("hidden")) {
      el.classList.remove("scale-95", "opacity-0");
    } else {
      el.classList.add("scale-95", "opacity-0");
    }
  }
};

// 2. Submenu Logic
window.toggleSubmenu = function (id) {
  const el = document.getElementById(id);
  if (el) el.classList.toggle("hidden");

  if (id === "cold-storage-submenu") {
    const arrow = document.getElementById("arrow-cold-storage");
    if (arrow) arrow.classList.toggle("rotate-180");
  }
  if (id === "security-submenu") {
    const arrow = document.getElementById("arrow-security");
    if (arrow) arrow.classList.toggle("rotate-180");
  }
};

// 3. Mobile Menu Logic
window.toggleMobileMenu = function () {
  const el = document.getElementById("mobile-sidebar");
  const ov = document.getElementById("mobile-menu-overlay");

  if (el && ov) {
    el.classList.toggle("-translate-x-full");
    ov.classList.toggle("hidden");
    ov.classList.toggle("opacity-0");
  }
};

// 4. Click Outside Listener (Menutup dropdown saat klik di luar)
window.addEventListener("click", function (e) {
  const notifContainer = document.getElementById("notif-container");
  const userContainer = document.getElementById("user-container");

  if (notifContainer && !notifContainer.contains(e.target)) {
    const dd = document.getElementById("notif-dropdown");
    if (dd) dd.classList.add("hidden");
  }

  if (userContainer && !userContainer.contains(e.target)) {
    const dd = document.getElementById("user-dropdown");
    if (dd) dd.classList.add("hidden");
  }
});
