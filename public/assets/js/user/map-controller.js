/* map-controller.js
   Responsible for initializing the map and exposing functions to load pins
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
            console.log('UserMapController init complete');

            // Dispatch event for other modules (e.g. map-markers.js)
            try {
                window.dispatchEvent(new CustomEvent('map:ready', { detail: { map: this.map } }));
            } catch(e) { console.error(e); }
        },
        // Pick mode state
        _pickMode: false,
        _tempMarker: null,
        _selectedMarker: null,
        _onMoveHandler: null,
        _onClickHandler: null,
        // Get green icon from centralized MapIcons module
        _getGreenIcon: function(){
            return window.MapIcons ? window.MapIcons.getGreenIcon() : null;
        },
        // Start interactive pick mode: marker follows cursor, click to select
        startPickLocation: function(){
            if(!this.map) return;
            if(this._pickMode) return;
            this._pickMode = true;
            var self = this;
            if(this.map && this.map.getContainer) this.map.getContainer().style.cursor = 'crosshair';

            this._onMoveHandler = function(e){
                var latlng = e.latlng;
                if(!self._tempMarker){
                    self._tempMarker = L.marker(latlng, { icon: self._getGreenIcon(), interactive: false }).addTo(self.markersLayer);
                } else {
                    self._tempMarker.setLatLng(latlng);
                }
            };

            this._onClickHandler = function(e){
                var latlng = e.latlng;
                if(self._tempMarker){ self.markersLayer.removeLayer(self._tempMarker); self._tempMarker = null; }
                if(self._selectedMarker){ self.markersLayer.removeLayer(self._selectedMarker); self._selectedMarker = null; }
                self._selectedMarker = L.marker(latlng, { icon: self._getGreenIcon(), interactive: false }).addTo(self.markersLayer);

                try{ window.dispatchEvent(new CustomEvent('panel:addEvent:locationSelected', { detail: { lat: latlng.lat, lng: latlng.lng } })); } catch(err){ console.log(err); }

                self.stopPickLocation();
            };

            this.map.on('mousemove', this._onMoveHandler);
            this.map.on('click', this._onClickHandler);
        },
        // Stop pick mode programmatically
        stopPickLocation: function(){
            if(!this._pickMode) return;
            this._pickMode = false;
            if(this.map && this.map.getContainer) this.map.getContainer().style.cursor = '';
            if(this._onMoveHandler) this.map.off('mousemove', this._onMoveHandler);
            if(this._onClickHandler) this.map.off('click', this._onClickHandler);
            this._onMoveHandler = null;
            this._onClickHandler = null;
            if(this._tempMarker){ this.markersLayer.removeLayer(this._tempMarker); this._tempMarker = null; }
        },
            // Place or move the selected marker programmatically
        placeMarkerAt: function(lat, lng){
            if(!this.map) return;
            var latlng = L.latLng(lat, lng);
            if(this._selectedMarker){ this.markersLayer.removeLayer(this._selectedMarker); this._selectedMarker = null; }
            this._selectedMarker = L.marker(latlng, { icon: this._getGreenIcon(), interactive: false }).addTo(this.markersLayer);
            this.map.setView(latlng, this.map.getZoom());
        },
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

    // Attach panel event listeners only after initialization to ensure map exists
    function attachPanelListeners(){
        if(window.UserMapController._eventsAttached) return;
        window.addEventListener('panel:addEvent:pickLocation', function(){
            try{ window.UserMapController.startPickLocation(); } catch(err){ console.error(err); }
        });
        window.addEventListener('panel:addEvent:placeMarker', function(e){
            var d = e && e.detail ? e.detail : null;
            if(d && d.lat !== undefined && d.lng !== undefined){
                try{ window.UserMapController.placeMarkerAt(d.lat, d.lng); } catch(err){ console.error(err); }
            }
        });
        // allow external cancel
        window.addEventListener('panel:addEvent:cancelPick', function(){
            try{ window.UserMapController.stopPickLocation(); } catch(err){ console.error(err); }
        });
        window.UserMapController._eventsAttached = true;
    }

    // Try to attach listeners once map is ready
    var attachRetries = 10;
    (function waitForMap(){
        if(window.UserMapController && window.UserMapController._initialized){
            attachPanelListeners();
            return;
        }
        if(attachRetries-- <= 0){ attachPanelListeners(); return; }
        setTimeout(waitForMap, 200);
    })();
})();
