/* sidebar.js
   Handles sidebar icon clicks and panel toggling
*/
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var sidebar = document.querySelector('.user-sidebar');
        var icons = document.querySelectorAll('.sidebar-icon');
        var panels = document.querySelectorAll('.panel');
        var panelsContainer = document.querySelector('.sidebar-panels');
        var activePanel = null;

        function hideAllPanels(){
            panels.forEach(function(p){
                p.classList.add('panel-hidden');
                p.setAttribute('aria-hidden','true');
            });
            if(sidebar) sidebar.classList.remove('expanded');
            activePanel = null;
        }

        icons.forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.stopPropagation();
                var panel = btn.getAttribute('data-panel');
                // re-query panels in case DOM changed
                panels = document.querySelectorAll('.panel');
                var target = document.getElementById('panel-' + panel);
                console.debug('[sidebar] icon click, panel=', panel, 'target=', !!target);

                // if panel doesn't exist, just toggle nothing
                if(!target){
                    console.warn('[sidebar] no panel element found for', panel);
                    hideAllPanels();
                    return;
                }

                // toggle same panel
                if(activePanel === panel){
                    hideAllPanels();
                    return;
                }

                // open and show target
                panels.forEach(function(p){ p.classList.add('panel-hidden'); p.setAttribute('aria-hidden','true'); });
                target.classList.remove('panel-hidden');
                target.setAttribute('aria-hidden','false');
                // also set a data attribute for easier inspection
                target.dataset.open = 'true';
                activePanel = panel;
                if(sidebar) sidebar.classList.add('expanded');
                console.debug('[sidebar] opened panel', panel, 'classes=', target.className, 'aria-hidden=', target.getAttribute('aria-hidden'));
            });
        });

        // click outside to close panels — ignore clicks inside visible panel
        document.addEventListener('click', function(e){
            if(!sidebar) return;
            // click inside sidebar -> keep open
            if(sidebar.contains(e.target)) return;

            // click inside any visible panel -> keep open
            var panelAncestor = e.target.closest('.panel');
            if(panelAncestor && panelAncestor.getAttribute && panelAncestor.getAttribute('aria-hidden') === 'false'){
                return;
            }

            // otherwise close
            if(activePanel){
                hideAllPanels();
                // remove inline positioning
                if(panelsContainer){ panelsContainer.style.position=''; panelsContainer.style.left=''; panelsContainer.style.top=''; panelsContainer.style.height=''; panelsContainer.style.zIndex=''; }
            }
        });

        // close on Escape
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape' || e.key === 'Esc'){
                hideAllPanels();
            }
        });
    });
})();
