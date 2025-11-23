// navCenterScroll.js
// Smoothly scroll target sections so they appear centered in the viewport
document.addEventListener('DOMContentLoaded', () => {
    // If the page was reloaded while the URL had #business, clear it so the browser
    // doesn't keep the viewport scrolled to that section after refresh.
    if (window.location.hash === '#business') {
        // scroll to top and remove the hash from the URL without adding history entry
        window.scrollTo(0, 0);
        try { history.replaceState(null, '', window.location.pathname + window.location.search); } catch (e) { /* noop */ }
    }
    document.querySelectorAll('.nav-links a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const hash = this.getAttribute('href');
            if (!hash || hash === '#') return;
            const target = document.querySelector(hash);
            if (!target) return;
            e.preventDefault();
            const rect = target.getBoundingClientRect();
            const targetTop = window.scrollY + rect.top;
            const scrollTo = Math.max(0, Math.round(targetTop - (window.innerHeight - rect.height) / 2));
            window.scrollTo({ top: scrollTo, behavior: 'smooth' });
        });
    });
});
