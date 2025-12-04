// Inicjalizacja mapy Leaflet
document.addEventListener('DOMContentLoaded', function() {
    // Utwórz mapę z centrum na Łodzi
    const map = L.map('map').setView([51.7592, 19.4560], 13);

    // Dodaj warstwy kafelków OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        minZoom: 3
    }).addTo(map);

    // Przykładowy marker
    const marker = L.marker([51.7592, 19.4560]).addTo(map);
    marker.bindPopup('<b>Łódź</b><br>Centrum miasta').openPopup();

    // Obsługa kliknięcia na mapę (do późniejszego dodawania wydarzeń)
    map.on('click', function(e) {
        console.log('Kliknięto na współrzędne:', e.latlng);
        // Tutaj później dodamy funkcję tworzenia wydarzenia
    });

    // Dostosuj rozmiar mapy po załadowaniu
    setTimeout(function() {
        map.invalidateSize();
    }, 100);
});
