// Back-to-top button
(function () {
    var btn = document.getElementById('back-to-top');
    if (!btn) return;
    var toggle = function () {
        var show = window.scrollY > 500;
        btn.classList.toggle('opacity-0', !show);
        btn.classList.toggle('translate-y-2', !show);
        btn.classList.toggle('pointer-events-none', !show);
    };
    window.addEventListener('scroll', toggle, { passive: true });
    toggle();
    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
    });
})();

// Carousels: arrows, loop back to the start, gentle autoplay that pauses on hover/focus
document.querySelectorAll('[data-carousel]').forEach(function (root) {
    var track = root.querySelector('[data-track]');
    if (!track) return;
    var step = function () { return Math.max(track.clientWidth * 0.9, 200); };
    var atEnd = function () { return track.scrollLeft + track.clientWidth >= track.scrollWidth - 4; };
    var go = function (dir) {
        if (dir > 0 && atEnd()) { track.scrollTo({ left: 0 }); return; }
        if (dir < 0 && track.scrollLeft <= 4) { track.scrollTo({ left: track.scrollWidth }); return; }
        track.scrollBy({ left: dir * step() });
    };
    var prev = root.querySelector('[data-prev]'), next = root.querySelector('[data-next]');
    if (prev) prev.addEventListener('click', function () { go(-1); });
    if (next) next.addEventListener('click', function () { go(1); });

    if (root.dataset.autoplay === '1' && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        var paused = false, timer = setInterval(function () { if (!paused && !document.hidden) go(1); }, 5000);
        ['mouseenter', 'focusin', 'touchstart'].forEach(function (e) { root.addEventListener(e, function () { paused = true; }, { passive: true }); });
        ['mouseleave', 'focusout'].forEach(function (e) { root.addEventListener(e, function () { paused = false; }); });
    }
});
