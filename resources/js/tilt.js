/**
 * Pointer-driven 3D tilt.
 *
 * Any element with a data-tilt attribute leans gently toward the pointer.
 * The element's CSS reads the angles from the --rx / --ry custom properties
 * (see .card-3d and .iso-model in app.css), so all the visual work stays in
 * CSS and this file only supplies two numbers.
 *
 * Skipped entirely on touch screens and for visitors who prefer reduced motion.
 */
const MAX_TILT = 6; // degrees

export function initTilt() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (reduced || !finePointer) return;

    document.querySelectorAll('[data-tilt]').forEach((el) => {
        const strength = Number(el.dataset.tilt) || MAX_TILT;
        // The hero model listens on its whole section so the effect is
        // noticeable from anywhere in it; cards listen on themselves.
        const target = el.closest('[data-tilt-area]') ?? el;

        target.addEventListener('pointermove', (e) => {
            const r = target.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;   // -0.5 … 0.5
            const y = (e.clientY - r.top) / r.height - 0.5;
            el.style.setProperty('--rx', `${(-y * strength).toFixed(2)}deg`);
            el.style.setProperty('--ry', `${(x * strength).toFixed(2)}deg`);
        });

        target.addEventListener('pointerleave', () => {
            el.style.setProperty('--rx', '0deg');
            el.style.setProperty('--ry', '0deg');
        });
    });
}
