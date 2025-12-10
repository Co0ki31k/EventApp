/* topbar.js
   Handles mode-switch buttons and profile edit trigger
*/
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var modeButtons = document.querySelectorAll('.mode-btn');
        function setActiveButton(btn){
            modeButtons.forEach(function(b){ b.classList.remove('active'); b.setAttribute('aria-pressed','false'); });
            if(btn){ btn.classList.add('active'); btn.setAttribute('aria-pressed','true'); }
        }

        modeButtons.forEach(function(btn){
            btn.addEventListener('click', function(){
                var mode = btn.getAttribute('data-mode');
                // set active styling
                setActiveButton(btn);
                // dispatch custom event
                document.dispatchEvent(new CustomEvent('map:modeChange', { detail: { mode: mode } }));
            });
        });

        // default active first button
        if(modeButtons.length){ setActiveButton(modeButtons[0]); }

        var profileBtn = document.getElementById('profile-edit-btn');
        if(profileBtn){
            profileBtn.addEventListener('click', function(){
                var modal = document.getElementById('profile-edit-modal');
                if(modal){ modal.setAttribute('aria-hidden','false'); modal.classList.add('open'); }
            });
        }
        // modal close
        document.addEventListener('click', function(e){
            if(e.target.classList.contains('modal-close')){
                var modal = e.target.closest('.modal');
                if(modal){ modal.setAttribute('aria-hidden','true'); modal.classList.remove('open'); }
            }
        });
    });
})();
