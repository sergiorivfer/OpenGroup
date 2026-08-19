document.addEventListener('DOMContentLoaded', () => {
  // Todos los formularios AJAX comparten esta clase
  const forms = document.querySelectorAll('form.js-ajax-form');
  if (!forms.length) return;

  forms.forEach(form => {
    const endpoint       = form.dataset.endpoint || form.getAttribute('action') || 'mail.php';
    const statusTargetId = form.dataset.statusTarget || null;
    const statusBox      = statusTargetId ? document.getElementById(statusTargetId) : null;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      if (statusBox) statusBox.textContent = 'Enviando...';

      // Validar política de datos si existe en este form
      const politica = form.querySelector('[name="politicaDatos"]');
      if (politica && !politica.checked) {
        if (statusBox) statusBox.textContent = 'Debes aceptar la Política de Datos.';
        return;
      }

      const fd = new FormData(form);

      // Campos comunes extra
      if (politica) {
        fd.set('politicaDatos', politica.checked ? '1' : '');
      }

      const datosInfo = form.querySelector('[name="datosInfo"]');
      if (datosInfo) {
        fd.set('datosInfo', datosInfo.checked ? '1' : '');
      }

      fd.set('page', location.href);
      fd.set('userAgent', navigator.userAgent);

      // Asegurar formName
      const formNameAttr = form.dataset.formName || form.id || '';
      if (formNameAttr && !fd.has('formName')) {
        fd.set('formName', formNameAttr);
      }

      try {
        const res  = await fetch(endpoint, {
          method: 'POST',
          body: fd
        });

        // Leemos como texto SIEMPRE (para debug) y luego intentamos JSON
        const text = await res.text();
        console.log('Respuesta cruda de', endpoint, ':', text);

        let json = {};
        try {
          json = JSON.parse(text);
        } catch (err) {
          console.error('Respuesta NO JSON desde', endpoint, err);
          if (statusBox) {
            statusBox.textContent = 'No se pudo enviar el formulario. Respuesta inesperada del servidor.';
          }
          return;
        }

        if (json.ok) {
          if (statusBox) {
            statusBox.textContent = json.message || '¡Gracias! Tu mensaje fue enviado.';
          }
          form.reset();

          // Resetear reCAPTCHA si existe en este form
          if (typeof grecaptcha !== 'undefined') {
            const captchaInForm = form.querySelector('.g-recaptcha');
            if (captchaInForm) {
              try { grecaptcha.reset(); } catch (e) {}
            }
          }
        } else {
          if (statusBox) {
            statusBox.textContent = json.error || 'No se pudo enviar el formulario. Inténtalo de nuevo.';
          }
        }
      } catch (err) {
        console.error('Error de red o servidor en', endpoint, err);
        if (statusBox) {
          statusBox.textContent = 'Error de red o servidor. Intenta de nuevo.';
        }
      }
    });
  });
});
