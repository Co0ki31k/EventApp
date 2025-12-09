/* map-controller.js
   Responsible for initializing the map and exposing functions to load pins
   This is a placeholder — actual map lib initialization will be added later.
*/
(function(){
    window.UserMapController = {
        map: null,
        markersLayer: null,
        init: function(opts){
            console.log('UserMapController init', opts);
            if(typeof L === 'undefined'){
                console.error('Leaflet (L) is not loaded. Ensure Leaflet JS is included.');
                return;
            }
            var center = (opts && opts.center) ? opts.center : [52.2297, 21.0122];
            var zoom = (opts && opts.zoom) ? opts.zoom : 13;

            var mapEl = document.getElementById('map');
            if(!mapEl){ console.error('Map element #map not found'); return; }

            this.map = L.map('map').setView(center, zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            this.markersLayer = L.layerGroup().addTo(this.map);

            // example marker at center
            L.marker(center).addTo(this.markersLayer).bindPopup('Przykładowy punkt').openPopup();

            var self = this;
            document.addEventListener('map:modeChange', function(e){
                var mode = e.detail && e.detail.mode;
                console.log('map:modeChange received', mode);
                self.setMode(mode);
            });
        },
        setMode: function(mode){
            // placeholder: change behavior based on mode
            console.log('Set map mode:', mode);
            // e.g. clear markers or load friend markers
            if(!this.map) return;
            // simple demo: set map view when switching modes
            if(mode === 'friends'){
                this.map.setZoom(13);
            } else if(mode === 'now'){
                this.map.setZoom(14);
            } else {
                this.map.setZoom(13);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function(){
        // Auto-init map when DOM ready
        try{ window.UserMapController.init(); }catch(err){ console.error(err); }
    });
})();
