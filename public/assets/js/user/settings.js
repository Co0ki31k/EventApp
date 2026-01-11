/* settings.js
   Module for managing notification preferences in the settings panel (localStorage version)
*/
(function(){
    window.SettingsPanel = {
        _initialized: false,
        form: null,
        resetBtn: null,
        messageDiv: null,

        init: function(){
            if(this._initialized) return;
            
            this.form = document.getElementById('notification-settings-form');
            if(!this.form) {
                return;
            }
            
            this.resetBtn = document.getElementById('reset-settings-btn');
            this.messageDiv = document.getElementById('settings-message');
            
            this._initialized = true;
            this.loadPreferences();
            this.form.addEventListener('submit', this.handleSubmit.bind(this));
            if(this.resetBtn){
                this.resetBtn.addEventListener('click', this.resetToDefaults.bind(this));
            }
        },

        loadPreferences: function(){
            var savedPrefs = localStorage.getItem('notificationPreferences');
            if(savedPrefs){
                try {
                    var prefs = JSON.parse(savedPrefs);
                    var prefElements = [
                        {id: 'pref-friend-request-sent', key: 'friend_request_sent'},
                        {id: 'pref-friend-request-received', key: 'friend_request_received'},
                        {id: 'pref-event-joined', key: 'event_joined'},
                        {id: 'pref-event-created', key: 'event_created'},
                        {id: 'pref-event-starting-soon', key: 'event_starting_soon'},
                        {id: 'pref-event-ongoing', key: 'event_ongoing'}
                    ];
                    
                    prefElements.forEach(function(item){
                        var elem = document.getElementById(item.id);
                        if(elem){
                            elem.checked = prefs[item.key] !== false;
                        }
                    });
                } catch(e){
                    console.error('Błąd podczas ładowania preferencji:', e);
                }
            }
        },

        handleSubmit: function(e){
            e.preventDefault();
            this.savePreferences();
        },

        savePreferences: function(){
            var prefElements = [
                {id: 'pref-friend-request-sent', key: 'friend_request_sent'},
                {id: 'pref-friend-request-received', key: 'friend_request_received'},
                {id: 'pref-event-joined', key: 'event_joined'},
                {id: 'pref-event-created', key: 'event_created'},
                {id: 'pref-event-starting-soon', key: 'event_starting_soon'},
                {id: 'pref-event-ongoing', key: 'event_ongoing'}
            ];
            
            var preferences = {};
            prefElements.forEach(function(item){
                var elem = document.getElementById(item.id);
                preferences[item.key] = elem ? elem.checked : true;
            });
            
            try {
                localStorage.setItem('notificationPreferences', JSON.stringify(preferences));
                this.showMessage('Ustawienia zostały zapisane', 'success');
                
                // Odśwież powiadomienia jeśli moduł jest dostępny
                if(window.NotificationsManager && typeof window.NotificationsManager.loadNotifications === 'function'){
                    window.NotificationsManager.loadNotifications();
                }
            } catch(e){
                console.error('Błąd podczas zapisywania preferencji:', e);
                this.showMessage('Wystąpił błąd podczas zapisywania ustawień', 'error');
            }
        },

        getPreferences: function(){
            var savedPrefs = localStorage.getItem('notificationPreferences');
            if(savedPrefs){
                try {
                    return JSON.parse(savedPrefs);
                } catch(e){
                    console.error('Błąd podczas odczytu preferencji:', e);
                }
            }
            return {
                friend_request_sent: true,
                friend_request_received: true,
                event_joined: true,
                event_created: true,
                event_starting_soon: true,
                event_ongoing: true
            };
        },

        resetToDefaults: function(){
            var checkboxes = document.querySelectorAll('#notification-settings-form input[type="checkbox"]');
            checkboxes.forEach(function(checkbox){
                checkbox.checked = true;
            });
            this.showMessage('Ustawienia zostały zresetowane do domyślnych. Kliknij "Zapisz ustawienia" aby zatwierdzić.', 'info');
        },

        showMessage: function(message, type){
            if(!this.messageDiv) return;
            
            this.messageDiv.textContent = message;
            this.messageDiv.className = 'settings-message ' + (type || 'info');
            this.messageDiv.style.display = 'block';
            
            var self = this;
            setTimeout(function(){
                if(self.messageDiv){
                    self.messageDiv.style.display = 'none';
                }
            }, 5000);
        }
    };

    // Umożliw globalny dostęp do preferencji dla notifications.js
    window.getNotificationPreferences = function(){
        return window.SettingsPanel.getPreferences();
    };

    // Inicjalizacja po załadowaniu DOM
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){
            window.SettingsPanel.init();
        });
    } else {
        window.SettingsPanel.init();
    }
})();
