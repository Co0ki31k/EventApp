(function(){
    var toggle = document.getElementById('yourEventsToggle');
    var menu = document.getElementById('yourEventsMenu');

    if(!toggle || !menu) return;

    function openMenu(){
        menu.classList.add('open');
        menu.setAttribute('aria-hidden','false');
        toggle.setAttribute('aria-expanded','true');
    }
    function closeMenu(){
        menu.classList.remove('open');
        menu.setAttribute('aria-hidden','true');
        toggle.setAttribute('aria-expanded','false');
    }

    toggle.addEventListener('click', function(e){
        e.stopPropagation();
        if(menu.classList.contains('open')) closeMenu(); else openMenu();
    });

    // close when clicking outside
    document.addEventListener('click', function(e){
        if(!menu.contains(e.target) && e.target !== toggle){
            closeMenu();
        }
    });

    // close on escape
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') closeMenu();
    });
})();