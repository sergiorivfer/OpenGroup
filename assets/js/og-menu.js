/* ============================================================
   OG-MENU.JS — Menú principal Open Group
   Sticky header, sidebar, mobile, logo rotation
   Scroll nativo (sin Lenis, sin ScrollSmoother)
   ============================================================ */

(function () {
    "use strict";

    var header, sidebar, overlay, sidebarToggle, sidebarClose, mobileToggle;
    var lastScroll = 0;
    var ticking = false;

    // ─── Esperar a que el DOM tenga el menu ───
    function waitForEl(id, cb) {
        if (document.getElementById(id)) { cb(); return; }
        var t = setInterval(function () {
            if (document.getElementById(id)) { clearInterval(t); cb(); }
        }, 100);
        setTimeout(function () { clearInterval(t); }, 5000);
    }

    waitForEl("ogmHeader", function () {
        header = document.getElementById("ogmHeader");
        sidebar = document.getElementById("ogmSidebar");
        overlay = document.getElementById("ogmOverlay");
        sidebarToggle = document.getElementById("ogmSidebarToggle");
        sidebarClose = document.getElementById("ogmSidebarClose");
        mobileToggle = document.getElementById("ogmMobileToggle");
        // logoRotate eliminado — logo completo sin rotación
        init();
    });

    function init() {
        if (!header) return;

        // ─── Sticky header + logo rotation ───
        function onScroll() {
            var sy = window.scrollY;
            if (sy > 10) { header.classList.add("is-sticky"); }
            else { header.classList.remove("is-sticky"); }

            lastScroll = sy;
            ticking = false;
        }

        window.addEventListener("scroll", function () {
            if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
        }, { passive: true });

        // Estado inicial
        onScroll();

        // ─── Sidebar ───
        function openSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.add("is-open");
            overlay.classList.add("is-visible");
            document.body.classList.add("ogm-sidebar-open");
        }

        function closeSidebar() {
            if (!sidebar || !overlay) return;
            sidebar.classList.remove("is-open");
            overlay.classList.remove("is-visible");
            document.body.classList.remove("ogm-sidebar-open");
        }

        if (sidebarToggle) { sidebarToggle.addEventListener("click", openSidebar); }
        if (sidebarClose) { sidebarClose.addEventListener("click", closeSidebar); }
        if (overlay) { overlay.addEventListener("click", closeSidebar); }
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && sidebar && sidebar.classList.contains("is-open")) closeSidebar();
        });

        // ─── Mobile toggle ───
        if (mobileToggle) {
            mobileToggle.addEventListener("click", function () {
                if (!sidebar || !overlay) return;
                sidebar.classList.toggle("is-open");
                overlay.classList.toggle("is-visible");
                document.body.classList.toggle("ogm-sidebar-open");
            });
        }

        // ─── Dropdowns ───
        document.querySelectorAll(".ogm-nav__dropdown > a, .ogm-sidebar__dropdown > a").forEach(function (link) {
            link.addEventListener("click", function (e) {
                var p = this.parentElement;
                if (!p) return;
                if (window.innerWidth > 991 && !p.closest(".ogm-sidebar")) return;
                e.preventDefault();
                p.classList.toggle("is-expanded");
            });
        });

        // ─── Active page ───
        var path = window.location.pathname.split("/").pop() || "inicio.html";
        document.querySelectorAll(".ogm-nav__list a, .ogm-sidebar__nav a").forEach(function (a) {
            if (a.getAttribute("href") === path) a.classList.add("is-active");
        });

        // ─── Cerrar sidebar al navegar ───
        document.querySelectorAll(".ogm-sidebar__nav a").forEach(function (a) {
            a.addEventListener("click", function () {
                if (this.parentElement && this.parentElement.classList.contains("ogm-sidebar__dropdown")) return;
                closeSidebar();
            });
        });

        // ─── Refresh ScrollTrigger ───
        if (typeof ScrollTrigger !== "undefined") {
            setTimeout(function () { ScrollTrigger.refresh(); }, 300);
        }

        console.log("🐙 OG-Menu: listo");
    }
})();
