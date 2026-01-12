// Dynamiczne ładowanie zapisanych wydarzeń do dropdowna w topbarze
(function(){
    function renderPendingEventsList(events) {
        var placeholder = document.getElementById('pendingEventsPlaceholder');
        if (!placeholder) return;
        if (!events || events.length === 0) {
            placeholder.innerHTML = '<div class="dropdown-item">Brak zapisanych wydarzeń</div>';
            return;
        }
        var html = '<ul class="pending-events-list">';
        events.forEach(function(ev) {
            var title = ev.title ? escapeHtml(ev.title) : 'Bez tytułu';
            var date = ev.start_datetime ? formatDate(ev.start_datetime) : '';
            html += '<li class="pending-event-item">' +
                '<span class="pending-event-title">📍 ' + title + '</span>' +
                '<span class="pending-event-date">' + date + '</span>' +
                '</li>';
        });
        html += '</ul>';
        placeholder.innerHTML = html;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    function formatDate(dateStr) {
        try {
            var d = new Date(dateStr);
            var day = ('0' + d.getDate()).slice(-2);
            var month = ('0' + (d.getMonth() + 1)).slice(-2);
            var year = d.getFullYear();
            var hours = ('0' + d.getHours()).slice(-2);
            var minutes = ('0' + d.getMinutes()).slice(-2);
            return day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
        } catch(e) {
            return dateStr;
        }
    }

    // Funkcja do pobrania szczegółów zapisanych wydarzeń
    function fetchPendingEventsDetails() {
        fetch('/Projekt/public/api/guest-events.php?details=1', {
            credentials: 'same-origin'
        })
        .then(function(res){ return res.json(); })
        .then(function(data){
            if(data.success && Array.isArray(data.details)) {
                renderPendingEventsList(data.details);
            } else {
                renderPendingEventsList([]);
            }
        })
        .catch(function(){
            renderPendingEventsList([]);
        });
    }

    // Odśwież dropdown przy otwarciu menu
    var toggle = document.getElementById('yourEventsToggle');
    if(toggle) {
        toggle.addEventListener('click', function(){
            fetchPendingEventsDetails();
        });
    }

    // fallback: odśwież dropdown po załadowaniu strony (np. jeśli menu otwarte na start)
    document.addEventListener('DOMContentLoaded', fetchPendingEventsDetails);
})();
