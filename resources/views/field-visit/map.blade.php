<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Field Visit Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        #map {
            height: 100vh;
            width: 100%;
        }
        
    </style>
</head>
<body>

<div id="map"></div>

<!-- DATA -->
<script type="application/json" id="locations-data">
{!! json_encode($visits, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const locations = JSON.parse(
        document.getElementById('locations-data').textContent
    );

    const map = L.map('map').setView([11.1271, 78.6569], 7);

    // OpenStreetMap tiles (FREE)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    locations.forEach(loc => {

        if (!loc.latitude || !loc.longitude) return;

        const marker = L.marker([
            Number(loc.latitude),
            Number(loc.longitude)
        ]).addTo(map);

        marker.bindPopup(`
            <strong>${loc.emp_name}</strong><br>
            ${loc.outlet_name}<br>
            ${loc.address}<br>
            <small>
                Visited: ${loc.visited_at}<br>
                Accuracy: ${loc.location_accuracy} m
            </small>
        `);
    }); 

    // Auto fit bounds if locations exist
    if (locations.length) {
        const bounds = locations
            .filter(l => l.latitude && l.longitude)
            .map(l => [Number(l.latitude), Number(l.longitude)]);
        map.fitBounds(bounds);
    }
</script>

</body>
</html>
