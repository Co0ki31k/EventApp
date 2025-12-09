/* topbar.js
   Handles mode-switch buttons and profile edit trigger
*/
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.mode-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                var mode = btn.getAttribute('data-mode');
                // dispatch custom event
                document.dispatchEvent(new CustomEvent('map:modeChange', { detail: { mode: mode } }));
            });
        });

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
