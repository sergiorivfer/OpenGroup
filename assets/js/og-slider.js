/* ======================================
   OG-SLIDER.JS — Hero slider vanilla
   Sin dependencias, ~2KB
   ====================================== */
(function () {
  "use strict";

  var slides, tabs, current = 0, interval = 7000, timer, isAnimating = false;

  function init() {
    var track = document.querySelector(".og-slider__track");
    if (!track) return;

    slides = track.querySelectorAll(".og-slider__slide");
    tabs = document.querySelectorAll(".og-slider__tab");
    if (!slides.length) return;

    // Show first slide and activate first tab
    slides[0].classList.add("is-active");
    if (tabs.length) tabs[0].classList.add("is-active");

    // Tab click
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var idx = parseInt(this.getAttribute("data-slide"), 10);
        if (!isNaN(idx) && idx !== current) goTo(idx);
      });
    });

    // Touch / swipe support
    var touchStartX = 0, touchEndX = 0;
    track.addEventListener("touchstart", function (e) {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    track.addEventListener("touchend", function (e) {
      touchEndX = e.changedTouches[0].screenX;
      var diff = touchStartX - touchEndX;
      if (Math.abs(diff) > 50) {
        diff > 0 ? goTo(current + 1) : goTo(current - 1);
      }
    }, { passive: true });

    // Start auto-play
    startTimer();
  }

  function goTo(idx) {
    if (isAnimating) return;
    if (idx < 0) idx = slides.length - 1;
    if (idx >= slides.length) idx = 0;
    if (idx === current) return;

    isAnimating = true;

    // Remove active from all
    slides.forEach(function (s) { s.classList.remove("is-active"); });
    tabs.forEach(function (t) { t.classList.remove("is-active"); });

    current = idx;

    // Activate current
    // Force reflow for animation restart
    void slides[current].offsetWidth;
    slides[current].classList.add("is-active");
    if (tabs[current]) tabs[current].classList.add("is-active");

    setTimeout(function () { isAnimating = false; }, 1200);

    restartTimer();
  }

  function startTimer() {
    stopTimer();
    timer = setInterval(function () { goTo(current + 1); }, interval);
  }

  function stopTimer() {
    if (timer) { clearInterval(timer); timer = null; }
  }

  function restartTimer() {
    stopTimer();
    startTimer();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
