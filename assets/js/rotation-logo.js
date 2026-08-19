document.addEventListener("DOMContentLoaded", function () {
    gsap.registerPlugin(ScrollTrigger);

    let lastScroll = 0; // Guardar la posición del scroll anterior
    let rotation = 0;   // Ángulo de rotación inicial
    const logos = document.querySelectorAll("#rotating-logo, #rotating-logo-2");

    window.addEventListener("scroll", () => {
        let scrollTop = window.scrollY;
        let scrollDelta = scrollTop - lastScroll; // Diferencia entre la posición actual y la anterior

        if (scrollDelta > 0) {
            // Scroll hacia abajo (en sentido horario)
            rotation += 2;
        } else if (scrollDelta < 0) {
            // Scroll hacia arriba (en sentido antihorario)
            rotation -= 2;
        }

        logos.forEach(logo => {
            gsap.to(logo, {
                rotation: rotation,
                duration: 0.5,
                ease: "power2.out"
            });
        });

        lastScroll = scrollTop;
    });
});
