// highwayResize.js
// Measure all highway slider images and set --car-w to the widest rendered width
document.addEventListener('DOMContentLoaded', () => {
    const imgs = Array.from(document.querySelectorAll('#infinite .highway-car img'));
    if (!imgs.length) return;

    // Wait for all images to be loaded (or already complete)
    const loadPromises = imgs.map(img => {
        if (img.complete && img.naturalWidth) return Promise.resolve();
        return new Promise(resolve => { img.addEventListener('load', resolve); img.addEventListener('error', resolve); });
    });

    Promise.all(loadPromises).then(() => {
        // target height used by CSS (falls back to 56)
        const rootStyle = getComputedStyle(document.documentElement);
        const carH = parseFloat(rootStyle.getPropertyValue('--car-h')) || 56;

        let maxW = 0;
        imgs.forEach(img => {
            const nw = img.naturalWidth || img.width;
            const nh = img.naturalHeight || (nw > 0 ? nw : 1);
            const renderedW = Math.round((nw / nh) * carH);
            if (renderedW > maxW) maxW = renderedW;
        });

        // Provide a minimum to avoid too-small boxes
        if (maxW < 40) maxW = 40;

        // Set CSS variable on root so layout/animation recalculates
        document.documentElement.style.setProperty('--car-w', maxW + 'px');
    });
});
