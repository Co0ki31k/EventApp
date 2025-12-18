/* add-event-panel.js
   Handles add-event panel interactions: location picker, opening/closing
*/
(function(){
    var btn = document.getElementById('btn-point-on-map');
    var panel = document.getElementById('panel-add-event');
    var latInput = document.getElementById('event-lat');
    var lngInput = document.getElementById('event-lng');

    if(!panel) return; // panel not loaded yet

    function closePanel(){
        // trigger a document click outside sidebar so sidebar.js runs hideAllPanels
        try{
            document.body.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
        }catch(e){
            if(panel){ panel.classList.add('panel-hidden'); panel.setAttribute('aria-hidden','true'); }
        }
    }
    function openPanel(){
        if(panel){ panel.classList.remove('panel-hidden'); panel.setAttribute('aria-hidden','false'); }
    }

    if(btn){
        btn.addEventListener('click', function(){
            btn.blur();  // Remove focus before hiding panel to avoid aria-hidden warning
            closePanel();
                       if(btn) btn.classList.remove('location-selected');  // Reset button color when picking again
            try{ window.dispatchEvent(new CustomEvent('panel:addEvent:pickLocation', { detail: {} })); } catch(e){ console.error(e); }
        });
    }

    window.addEventListener('panel:addEvent:locationSelected', function(e){
        var d = e && e.detail ? e.detail : null;
        if(!d) return;
        if(latInput) latInput.value = d.lat;
        if(lngInput) lngInput.value = d.lng;
            if(btn) btn.classList.add('location-selected');  // Add green color to button
        // remove panel-hidden to show panel
        if(panel) panel.classList.remove('panel-hidden');
        if(panel) panel.setAttribute('aria-hidden', 'false');
        // bring into view and focus first field
        setTimeout(function(){
            try{ panel.scrollIntoView({ behavior: 'smooth', block: 'center' }); }catch(_){}
            var t = document.getElementById('event-title'); if(t) t.focus();
        }, 50);
        try{ window.dispatchEvent(new CustomEvent('panel:addEvent:placeMarker', { detail: { lat: d.lat, lng: d.lng } })); } catch(err){ console.error(err); }
    });
})();
