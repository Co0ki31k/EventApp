/* map-controller.js
   Responsible for initializing the map and exposing functions to load pins
   This is a placeholder — actual map lib initialization will be added later.
*/
(function(){
    window.UserMapController = {
        map: null,
        markersLayer: null,
        init: function(opts){
            // avoid double initialization
            if(this._initialized){
                console.log('UserMapController already initialized');
                return;
            }

            console.log('UserMapController init', opts);
            if(typeof L === 'undefined'){
                console.error('Leaflet (L) is not loaded. Ensure Leaflet JS is included.');
                return;
            }
            
            // Default center: Łódź
            var center = (opts && opts.center) ? opts.center : [51.7592, 19.4550];
            var zoom = (opts && opts.zoom) ? opts.zoom : 13;

            var mapEl = document.getElementById('map');
            if(!mapEl){ console.error('Map element #map not found'); return; }

            // create map without default zoom controls
            this.map = L.map('map', { zoomControl: false }).setView(center, zoom);

            // use CartoDB Positron (light_all) as a lighter base for gray/desaturated styling
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap & CartoDB'
            }).addTo(this.map);

            this.markersLayer = L.layerGroup().addTo(this.map);

            this._initialized = true;

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

    // Ensure init runs even if this script is loaded after DOMContentLoaded
    function tryInit(retries){
        retries = typeof retries === 'number' ? retries : 5;

        // if already initialized, do nothing
        if(window.UserMapController && (window.UserMapController._initialized || window.UserMapController.map)){
            return;
        }

        if(typeof L === 'undefined'){
            if(retries <= 0){ console.error('Leaflet (L) not available after retries'); return; }
            // wait for Leaflet to load
            return setTimeout(function(){ tryInit(retries-1); }, 150);
        }

        var mapEl = document.getElementById('map');
        if(!mapEl){
            if(retries <= 0){ console.error('Map element #map not found after retries'); return; }
            return setTimeout(function(){ tryInit(retries-1); }, 150);
        }

        try{ window.UserMapController.init(); }catch(err){ console.error(err); }
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){ tryInit(); });
    } else {
        tryInit();
    }
})();
