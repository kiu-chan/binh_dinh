<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map Example</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map { height: 800px; }
    </style>
</head>
<body>
    <div id="map"></div>
    <script>
        var map = L.map('map').setView([51.505, -0.09], 13); // Set initial coordinates and zoom level
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Define initial coordinates for the polygon
        var polygonCoordinates = [
            [51.51, -0.12],
            [51.51, -0.08],
            [51.503, -0.06],
            [51.497, -0.08],
            [51.49, -0.12],
            [51.51, -0.12]
        ];

        // Create the initial polygon and add it to the map
        var polygon = L.polygon(polygonCoordinates, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5
        }).addTo(map);

        // Add a popup to the polygon
        polygon.bindPopup("This is a polygon.");

        // Event listener for map clicks
        map.on('click', function(e) {
            var newPoint = [e.latlng.lat, e.latlng.lng];
            polygonCoordinates.push(newPoint);

            // Remove the existing polygon
            map.removeLayer(polygon);

            // Calculate convex hull using Graham Scan
            var convexHull = calculateConvexHull(polygonCoordinates);

            // Create a new polygon with the convex hull points
            polygon = L.polygon(convexHull, {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5
            }).addTo(map);

            // Add the popup to the new polygon
            polygon.bindPopup("This is a polygon.");
        });

        // Function to calculate convex hull using Graham Scan
        function calculateConvexHull(points) {
            // Convert points to Leaflet LatLng objects
            var latLngPoints = points.map(function(point) {
                return L.latLng(point[0], point[1]);
            });

            // Sort points lexicographically (by x, then by y)
            latLngPoints.sort(function(a, b) {
                return a.lat !== b.lat ? a.lat - b.lat : a.lng - b.lng;
            });

            // Helper function to determine orientation
            function orientation(p, q, r) {
                var val = (q.lat - p.lat) * (r.lng - q.lng) - (q.lng - p.lng) * (r.lat - q.lat);
                if (val === 0) return 0; // collinear
                return (val > 0) ? 1 : 2; // clock or counterclock wise
            }

            // Build the hull
            var hull = [];

            // Build lower hull
            for (var i = 0; i < latLngPoints.length; i++) {
                while (hull.length >= 2 && orientation(hull[hull.length - 2], hull[hull.length - 1], latLngPoints[i]) !== 2) {
                    hull.pop();
                }
                hull.push(latLngPoints[i]);
            }

            // Build upper hull
            for (var j = latLngPoints.length - 2; j >= 0; j--) {
                while (hull.length >= 2 && orientation(hull[hull.length - 2], hull[hull.length - 1], latLngPoints[j]) !== 2) {
                    hull.pop();
                }
                hull.push(latLngPoints[j]);
            }

            // Remove the last point because it's a duplicate of the first point
            hull.pop();

            // Convert hull back to array of [lat, lng]
            var convexHull = hull.map(function(point) {
                return [point.lat, point.lng];
            });

            return convexHull;
        }
    </script>
</body>
</html>
