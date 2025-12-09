/* sidebar.js
   Handles sidebar icon clicks and panel toggling
*/
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var sidebar = document.querySelector('.user-sidebar');
        var icons = document.querySelectorAll('.sidebar-icon');
        var activePanel = null;

        icons.forEach(function(btn){
            btn.addEventListener('click', function(){
                var panel = btn.getAttribute('data-panel');
                var target = document.getElementById('panel-' + panel);

                // toggle same panel
                if(sidebar.classList.contains('expanded') && activePanel === panel){
                    // collapse
                    sidebar.classList.remove('expanded');
                    if(target){ target.classList.add('panel-hidden'); target.setAttribute('aria-hidden','true'); }
                    activePanel = null;
                    return;
                }

                // open and show target
                sidebar.classList.add('expanded');
                document.querySelectorAll('.panel').forEach(function(p){
                    p.classList.add('panel-hidden');
                    p.setAttribute('aria-hidden','true');
                });
                if(target){
                    target.classList.remove('panel-hidden');
                    target.setAttribute('aria-hidden','false');
                    activePanel = panel;
                }
            });
        });

        // click outside to close panels
        document.addEventListener('click', function(e){
            if(!sidebar) return;
            if(sidebar.contains(e.target)) return; // inside sidebar
            if(sidebar.classList.contains('expanded')){
                sidebar.classList.remove('expanded');
                document.querySelectorAll('.panel').forEach(function(p){ p.classList.add('panel-hidden'); p.setAttribute('aria-hidden','true'); });
                activePanel = null;
            }
        });
    });
})();
