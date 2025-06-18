
   fetch('pages/sideBar.html')
      .then(res => res.text())
      .then(data => {
        document.getElementById('sidebarContainer').innerHTML = data;
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggle");
        const overlay = document.getElementById("overlay");

        toggleBtn.addEventListener("click", () => {
          sidebar.classList.toggle("show");
          overlay.classList.toggle("d-none");
          document.body.style.overflow = sidebar.classList.contains("show") ? "hidden" : "";
        });

        overlay.addEventListener("click", () => {
          sidebar.classList.remove("show");
          overlay.classList.add("d-none");
          document.body.style.overflow = "";
        });
      });