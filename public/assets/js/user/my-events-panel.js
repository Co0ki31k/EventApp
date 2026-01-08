/* my-events-panel.js
   Module for loading and displaying user's joined events in the my events panel.
   Clicking on an event card will open the same popup as clicking on a map marker.
*/
(function(){
    window.MyEventsPanel = {
        _initialized: false,
        container: null,
        currentUserId: null,

        init: function(){
            if(this._initialized) return;
            
            this.container = document.getElementById('my-events-container');
            if(!this.container) return;

            // Get current user ID from global variable (set in PHP)
            if(typeof window.currentUserId !== 'undefined'){
                this.currentUserId = window.currentUserId;
            }

            this._initialized = true;
            
            // Load my events when panel is opened
            this.setupPanelListener();
        },

        setupPanelListener: function(){
            var self = this;
            var myEventsPanel = document.getElementById('panel-my-events');
            
            if(!myEventsPanel) return;

            // Use MutationObserver to detect when panel becomes visible
            var observer = new MutationObserver(function(mutations){
                mutations.forEach(function(mutation){
                    if(mutation.type === 'attributes' && mutation.attributeName === 'class'){
                        var isPanelVisible = !myEventsPanel.classList.contains('panel-hidden');
                        
                        if(isPanelVisible){
                            // Panel just became visible - always refresh data
                            self.loadMyEvents();
                        }
                    }
                });
            });

            observer.observe(myEventsPanel, {
                attributes: true,
                attributeFilter: ['class']
            });
        },

        loadMyEvents: function(){
            var self = this;
            
            fetch('/Projekt/public/api/my-events.php', {
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(function(res){ 
                if(!res.ok) throw new Error('Network response was not ok');
                return res.json(); 
            })
            .then(function(data){
                if(data.success && data.events && data.events.length > 0){
                    self.renderEvents(data.events);
                } else {
                    self.renderEmptyState();
                }
            })
            .catch(function(err){
                console.error('Error loading my events:', err);
                self.renderError();
            });
        },

        renderEvents: function(events){
            var self = this;
            if(!this.container) return;

            // Clear loading state
            this.container.innerHTML = '';

            events.forEach(function(event){
                var card = self.createEventCard(event);
                self.container.appendChild(card);
            });
        },

        createEventCard: function(event){
            var self = this;
            
            var card = document.createElement('div');
            card.className = 'my-event-card';
            
            // Add active class if event is currently happening
            if(event.is_active){
                card.classList.add('active-event');
                // For active events, load detailed info
                this.loadEventDetails(event.id, card);
                return card;
            }
            
            card.setAttribute('data-event-id', event.id);
            
            // Title
            var title = document.createElement('h4');
            title.className = 'my-event-title';
            title.textContent = event.title || 'Bez tytułu';
            
            card.appendChild(title);
            
            // Meta information
            var metaDiv = document.createElement('div');
            metaDiv.className = 'my-event-meta';
            
            if(event.category_name){
                var categoryDiv = document.createElement('div');
                categoryDiv.className = 'my-event-category';
                
                var catIcon = document.createElement('span');
                catIcon.className = 'category-icon';
                catIcon.textContent = '🏷️';
                
                var catText = document.createElement('span');
                catText.textContent = event.category_name;
                
                categoryDiv.appendChild(catIcon);
                categoryDiv.appendChild(catText);
                metaDiv.appendChild(categoryDiv);
            }
            
            if(event.start_datetime){
                var dateDiv = document.createElement('div');
                dateDiv.className = 'my-event-date';
                
                var dateIcon = document.createElement('span');
                dateIcon.className = 'date-icon';
                dateIcon.textContent = '📅';
                
                var dateText = document.createElement('span');
                dateText.textContent = this.formatDateTime(event.start_datetime);
                
                dateDiv.appendChild(dateIcon);
                dateDiv.appendChild(dateText);
                metaDiv.appendChild(dateDiv);
            }
            
            card.appendChild(metaDiv);
            
            // Add click handler to open event popup (same as clicking map marker)
            card.addEventListener('click', function(){
                self.openEventPopup(event);
            });
            
            return card;
        },

        loadEventDetails: function(eventId, card){
            var self = this;
            
            fetch('/Projekt/public/api/event-details.php?event_id=' + eventId, {
                credentials: 'same-origin',
                cache: 'no-cache'
            })
            .then(function(res){ 
                if(!res.ok) throw new Error('Network response was not ok');
                return res.json(); 
            })
            .then(function(data){
                if(data.success && data.event){
                    self.renderActiveEventCard(data.event, card);
                }
            })
            .catch(function(err){
                console.error('Error loading event details:', err);
            });
        },

        renderActiveEventCard: function(event, card){
            var self = this;
            card.setAttribute('data-event-id', event.id);
            
            // Title
            var title = document.createElement('h4');
            title.className = 'my-event-title';
            title.textContent = event.title || 'Bez tytułu';
            card.appendChild(title);
            
            // Meta information
            var metaDiv = document.createElement('div');
            metaDiv.className = 'my-event-meta';
            
            if(event.category_name){
                var categoryDiv = document.createElement('div');
                categoryDiv.className = 'my-event-category';
                
                var catIcon = document.createElement('span');
                catIcon.className = 'category-icon';
                catIcon.textContent = '🏷️';
                
                var catText = document.createElement('span');
                catText.textContent = event.category_name;
                
                categoryDiv.appendChild(catIcon);
                categoryDiv.appendChild(catText);
                metaDiv.appendChild(categoryDiv);
            }
            
            if(event.start_datetime){
                var dateDiv = document.createElement('div');
                dateDiv.className = 'my-event-date';
                
                var dateIcon = document.createElement('span');
                dateIcon.className = 'date-icon';
                dateIcon.textContent = '📅';
                
                var dateText = document.createElement('span');
                dateText.textContent = this.formatDateTime(event.start_datetime);
                
                dateDiv.appendChild(dateIcon);
                dateDiv.appendChild(dateText);
                metaDiv.appendChild(dateDiv);
            }
            
            card.appendChild(metaDiv);
            
            // Description
            if(event.description){
                var descDiv = document.createElement('div');
                descDiv.className = 'my-event-description';
                descDiv.textContent = event.description;
                card.appendChild(descDiv);
            }
            
            // Participants section
            if(event.participants && event.participants.length > 0){
                var participantsSection = document.createElement('div');
                participantsSection.className = 'my-event-participants-section';
                
                var participantsHeader = document.createElement('h5');
                participantsHeader.className = 'participants-header';
                participantsHeader.textContent = 'Uczestnicy (' + event.participants_count + ')';
                participantsSection.appendChild(participantsHeader);
                
                var participantsList = document.createElement('div');
                participantsList.className = 'participants-list';
                
                event.participants.forEach(function(participant){
                    var participantItem = document.createElement('div');
                    participantItem.className = 'participant-item';
                    
                    if(participant.is_current_user){
                        participantItem.classList.add('current-user');
                    }
                    
                    // Add class based on friend status
                    if(participant.friend_status === 'accepted'){
                        participantItem.classList.add('is-friend');
                    } else if(participant.friend_status === 'pending'){
                        participantItem.classList.add('is-pending');
                    }
                    
                    var participantName = document.createElement('span');
                    participantName.className = 'participant-name';
                    participantName.textContent = participant.username;
                    
                    if(participant.is_current_user){
                        participantName.textContent += ' (Ty)';
                    }
                    
                    participantItem.appendChild(participantName);
                    
                    // Show different badge/button based on friend status
                    if(!participant.is_current_user){
                        if(participant.friend_status === 'accepted'){
                            // Green checkmark for accepted friends
                            var friendBadge = document.createElement('span');
                            friendBadge.className = 'friend-badge friend-accepted';
                            friendBadge.textContent = '✓';
                            friendBadge.title = 'Znajomy';
                            participantItem.appendChild(friendBadge);
                        } else if(participant.friend_status === 'pending'){
                            // Clock icon for pending invitations
                            var pendingBadge = document.createElement('span');
                            pendingBadge.className = 'friend-badge friend-pending';
                            pendingBadge.textContent = '⏳';
                            pendingBadge.title = 'Oczekujące zaproszenie';
                            participantItem.appendChild(pendingBadge);
                        } else {
                            // Add friend button for non-friends
                            var addFriendBtn = document.createElement('button');
                            addFriendBtn.className = 'add-friend-btn';
                            addFriendBtn.innerHTML = '➕';
                            addFriendBtn.title = 'Wyślij zaproszenie';
                            addFriendBtn.setAttribute('data-user-id', participant.user_id);
                            
                            addFriendBtn.addEventListener('click', function(e){
                                e.stopPropagation();
                                self.sendFriendRequest(participant.user_id, addFriendBtn);
                            });
                            
                            participantItem.appendChild(addFriendBtn);
                        }
                    }
                    
                    participantsList.appendChild(participantItem);
                });
                
                participantsSection.appendChild(participantsList);
                card.appendChild(participantsSection);
            }
            
            // Add click handler to open event popup
            card.addEventListener('click', function(){
                self.openEventPopup(event);
            });
        },

        openEventPopup: function(event){
            // Check if the map markers module is available
            if(!window.UserMapMarkers){
                console.error('UserMapMarkers module not available');
                return;
            }

            // Find the marker for this event or create a temporary popup
            var marker = this.findMarkerForEvent(event.id);
            
            if(marker){
                // If marker exists, open its popup and center map on it
                marker.openPopup();
                
                // Center map on marker if map controller is available
                if(window.UserMapController && window.UserMapController.map){
                    var map = window.UserMapController.map;
                    map.setView(marker.getLatLng(), 14, { animate: true });
                }
            } else {
                // If marker doesn't exist, we need to check participation and show popup
                // This can happen if the event is not currently visible on the map
                this.showEventPopupWithParticipation(event);
            }
        },

        findMarkerForEvent: function(eventId){
            if(!window.UserMapMarkers || !window.UserMapMarkers.markers){
                return null;
            }

            var markers = window.UserMapMarkers.markers;
            for(var i = 0; i < markers.length; i++){
                if(markers[i]._eventId === eventId){
                    return markers[i];
                }
            }
            return null;
        },

        showEventPopupWithParticipation: function(event){
            var self = this;
            
            // Check participation status
            if(!window.UserMapMarkers){
                console.error('UserMapMarkers module not available');
                return;
            }

            // We know user is joined since these are their events
            window.UserMapMarkers.checkParticipation(event.id)
                .then(function(isJoined){
                    // Build popup content using the same method as map markers
                    var popupContent = window.UserMapMarkers.buildPopupContent(event, isJoined);
                    
                    // Create a Leaflet popup at the event location
                    if(window.UserMapController && window.UserMapController.map && window.L){
                        var map = window.UserMapController.map;
                        var popup = window.L.popup({
                            maxWidth: 320,
                            closeButton: true,
                            className: 'custom-event-popup'
                        })
                        .setLatLng([parseFloat(event.latitude), parseFloat(event.longitude)])
                        .setContent(popupContent)
                        .openOn(map);

                        // Center map on popup
                        map.setView([parseFloat(event.latitude), parseFloat(event.longitude)], 14, { 
                            animate: true 
                        });

                        // Setup button click handler after popup is opened
                        setTimeout(function(){
                            var btn = document.querySelector('.btn-popup-action[data-event-id="' + event.id + '"]');
                            if(btn){
                                window.UserMapMarkers.setupPopupButtonHandler(btn);
                            }
                        }, 100);
                    }
                })
                .catch(function(err){
                    console.error('Error checking participation:', err);
                });
        },

        sendFriendRequest: function(userId, button){
            // Disable button during request
            button.disabled = true;
            button.innerHTML = '⏳';
            
            fetch('/Projekt/public/api/send-friend-request.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ user_id: userId })
            })
            .then(function(res){ 
                if(!res.ok) throw new Error('Network response was not ok');
                return res.json(); 
            })
            .then(function(data){
                if(data.success){
                    // Change button to pending state
                    button.className = 'friend-badge friend-pending';
                    button.innerHTML = '⏳';
                    button.title = 'Oczekujące zaproszenie';
                    button.disabled = true;
                } else {
                    alert(data.message || 'Nie udało się wysłać zaproszenia');
                    button.disabled = false;
                    button.innerHTML = '➕';
                }
            })
            .catch(function(err){
                console.error('Error sending friend request:', err);
                alert('Wystąpił błąd podczas wysyłania zaproszenia');
                button.disabled = false;
                button.innerHTML = '➕';
            });
        },

        formatDateTime: function(dateStr){
            if(!dateStr) return 'Nie podano';
            try {
                var date = new Date(dateStr);
                if(isNaN(date.getTime())) return dateStr;
                return date.toLocaleDateString('pl-PL', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch(e){
                return dateStr;
            }
        },

        renderEmptyState: function(){
            if(!this.container) return;
            
            this.container.innerHTML = 
                '<div class="my-events-empty-state">' +
                    '<div class="empty-icon">📅</div>' +
                    '<p class="empty-message">Nie jesteś zapisany na żadne wydarzenia</p>' +
                '</div>';
        },

        renderError: function(){
            if(!this.container) return;
            
            this.container.innerHTML = 
                '<div class="my-events-empty-state">' +
                    '<div class="empty-icon">⚠️</div>' +
                    '<p class="empty-message">Wystąpił błąd podczas ładowania wydarzeń</p>' +
                '</div>';
        }
    };

    // Initialize when DOM is ready
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){
            window.MyEventsPanel.init();
        });
    } else {
        window.MyEventsPanel.init();
    }
})();
