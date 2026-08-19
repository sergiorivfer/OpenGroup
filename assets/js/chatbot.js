/* ==========================================
   CHATBOT - Tooltip para Oriana/Dialvox
   Reusable - solo agrega un tooltip
   al botón existente del chat
   ========================================== */

(function() {
  'use strict';

  let tooltip = null;
  let hideTimeout = null;
  let firstVisit = true;
  let orianaBtn = null;
  let lastScrollY = 0;
  let scrollCooldown = false;

  function init() {
    orianaBtn = document.querySelector('.bot-widget-bubble');
    if (!orianaBtn) {
      setTimeout(init, 500);
      return;
    }

    // Crear tooltip
    tooltip = document.createElement('div');
    tooltip.className = 'og-chat-tooltip';
    tooltip.innerHTML = 'Hola soy <strong>Oriana</strong>, ¿en qué puedo ayudarte?';
    document.body.appendChild(tooltip);

    // Posicionar el tooltip
    positionTooltip();
    window.addEventListener('resize', positionTooltip);

    // Hover sobre el botón → mostrar tooltip
    orianaBtn.addEventListener('mouseenter', showTooltip);
    // Se oculta 20s después de salir (mouseleave)
    orianaBtn.addEventListener('mouseleave', () => hideTooltip(20000));

    // Clic en el botón → ocultar tooltip + trackear en GTM
    orianaBtn.addEventListener('click', function() {
      hideTooltip(300);
      if (window.dataLayer) {
        window.dataLayer.push({event: 'chatbot_open'});
      }
    });

    // Scroll: hacia abajo → aparece, hacia arriba → desaparece
    lastScrollY = window.scrollY;
    window.addEventListener('scroll', onScroll, { passive: true });

    // Primera visita: mostrar tooltip a los 3s
    setTimeout(() => {
      if (firstVisit) {
        showTooltip();
        tooltip.classList.add('first-visit');
        firstVisit = false;
        setTimeout(() => {
          tooltip.classList.remove('first-visit', 'show');
        }, 6000);
      }
    }, 3000);

    // Click fuera → ocultar
    document.addEventListener('click', (e) => {
      if (!orianaBtn.contains(e.target) && !tooltip.contains(e.target)) {
        if (tooltip.classList.contains('show') && !tooltip.classList.contains('first-visit')) {
          hideTooltip(0);
        }
      }
    });
  }

  function positionTooltip() {
    if (!tooltip || !orianaBtn) return;
    const rect = orianaBtn.getBoundingClientRect();
    tooltip.style.bottom = (window.innerHeight - rect.top + 12) + 'px';
    tooltip.style.right = (window.innerWidth - rect.right) + 'px';
  }

  function showTooltip() {
    if (hideTimeout) clearTimeout(hideTimeout);
    tooltip.classList.remove('first-visit');
    tooltip.classList.add('show');
    positionTooltip();
  }

  function hideTooltip(delay) {
    if (hideTimeout) clearTimeout(hideTimeout);
    hideTimeout = setTimeout(() => tooltip.classList.remove('show'), delay);
  }

  function onScroll() {
    if (scrollCooldown) return;
    scrollCooldown = true;
    setTimeout(() => { scrollCooldown = false; }, 100);

    var currentScrollY = window.scrollY;
    var delta = currentScrollY - lastScrollY;
    lastScrollY = currentScrollY;

    if (delta > 0) {
      // Scroll hacia abajo → mostrar tooltip (siempre)
      showTooltip();
      if (firstVisit) {
        tooltip.classList.add('first-visit');
        firstVisit = false;
        setTimeout(() => {
          tooltip.classList.remove('first-visit', 'show');
        }, 6000);
      }
    } else if (delta < 0) {
      // Scroll hacia arriba → ocultar tooltip
      hideTooltip(0);
    }
  }

  // Esperar a que el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
