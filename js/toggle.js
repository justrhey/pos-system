
function toggleDropdown(e) {
  const parent = e.target.closest(".dropdown");
  parent.classList.toggle("open");
}

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("hidden");
}
