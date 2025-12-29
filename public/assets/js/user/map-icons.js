/* map-icons.js
   Centralized map icon definitions for Leaflet markers.
   Provides getGreenIcon(), getRedIcon() and getBlueIcon() functions.
*/
(function(){
    var _greenIcon = null;
    var _redIcon = null;
    var _blueIcon = null;

    window.MapIcons = {
        // Green pin icon (for picking location, temporary markers)
        getGreenIcon: function(){
            if(!_greenIcon && typeof L !== 'undefined'){
                _greenIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
            }
            return _greenIcon;
        },

        // Red pin icon (for event markers)
        getRedIcon: function(){
            if(!_redIcon && typeof L !== 'undefined'){
                _redIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
            }
            return _redIcon;
        },

        // Blue pin icon (for friends, special markers, etc.)
        getBlueIcon: function(){
            if(!_blueIcon && typeof L !== 'undefined'){
                _blueIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
            }
            return _blueIcon;
        }
    };
})();
