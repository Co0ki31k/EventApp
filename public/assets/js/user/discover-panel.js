/* discover-panel.js
   Module for loading and displaying top popular events in the discover panel.
   Clicking on an event card will open the same popup as clicking on a map marker.
*/
(function(){
    window.DiscoverPanel = {
        _initialized: false,
        container: null,
        currentUserId: null,

        init: function(){
            if(this._initialized) return;
            
            this.container = document.getElementById('discover-events-container');
            if(!this.container) return;

            // Get current user ID from global variable (set in PHP)
            if(typeof window.currentUserId !== 'undefined'){
                this.currentUserId = window.currentUserId;
            }

            this._initialized = true;
            
            // Load top events when discover panel is opened
            this.setupPanelListener();
        },

        setupPanelListener: function(){
            var self = this;
            var discoverPanel = document.getElementById('panel-discover');
            
            if(!discoverPanel) return;

            // Use MutationObserver to detect when panel becomes visible
            var observer = new MutationObserver(function(mutations){
                mutations.forEach(function(mutation){
                    if(mutation.type === 'attributes' && mutation.attributeName === 'class'){
                        var isPanelVisible = !discoverPanel.classList.contains('panel-hidden');
                        
                        if(isPanelVisible){
                            // Panel just became visible - always refresh data
                            self.loadTopEvents();
                        }
                    }
                });
            });

            observer.observe(discoverPanel, {
                attributes: true,
                attributeFilter: ['class']
            });
        },

        loadTopEvents: function(){
            var self = this;
            
            fetch('/Projekt/public/api/top-events.php', {
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
                console.error('Error loading top events:', err);
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
            card.className = 'discover-event-card';
            card.setAttribute('data-event-id', event.id);
            
            // Build card content
            var headerDiv = document.createElement('div');
            headerDiv.className = 'discover-event-header';
            
            var title = document.createElement('h4');
            title.className = 'discover-event-title';
            title.textContent = event.title || 'Bez tytułu';
            
            var participantsDiv = document.createElement('div');
            participantsDiv.className = 'discover-event-participants';
            
            var icon = document.createElement('span');
            icon.className = 'discover-participants-icon';
            icon.textContent = '👥';
            
            var count = document.createElement('span');
            count.className = 'discover-participants-count';
            count.textContent = event.participants_count || 0;
            
            participantsDiv.appendChild(icon);
            participantsDiv.appendChild(count);
            
            headerDiv.appendChild(title);
            headerDiv.appendChild(participantsDiv);
            
            // Meta information
            var metaDiv = document.createElement('div');
            metaDiv.className = 'discover-event-meta';
            
            if(event.category_name){
                var categoryDiv = document.createElement('div');
                categoryDiv.className = 'discover-event-category';
                
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
                dateDiv.className = 'discover-event-date';
                
                var dateIcon = document.createElement('span');
                dateIcon.className = 'date-icon';
                dateIcon.textContent = '📅';
                
                var dateText = document.createElement('span');
                dateText.textContent = this.formatDateTime(event.start_datetime);
                
                dateDiv.appendChild(dateIcon);
                dateDiv.appendChild(dateText);
                metaDiv.appendChild(dateDiv);
            }
            
            card.appendChild(headerDiv);
            card.appendChild(metaDiv);
            
            // Add click handler to open event popup (same as clicking map marker)
            card.addEventListener('click', function(){
                self.openEventPopup(event);
            });
            
            return card;
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
                '<div class="discover-empty-state">' +
                    '<div class="empty-icon">🔍</div>' +
                    '<p class="empty-message">Brak popularnych wydarzeń</p>' +
                '</div>';
        },

        renderError: function(){
            if(!this.container) return;
            
            this.container.innerHTML = 
                '<div class="discover-empty-state">' +
                    '<div class="empty-icon">⚠️</div>' +
                    '<p class="empty-message">Wystąpił błąd podczas ładowania wydarzeń</p>' +
                '</div>';
        }
    };

    // Initialize when DOM is ready
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){
            window.DiscoverPanel.init();
        });
    } else {
        window.DiscoverPanel.init();
    }
})();
