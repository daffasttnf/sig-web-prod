<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Jawa Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        /* Global Styling */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, rgba(162, 194, 225, 0.8), rgba(225, 247, 213, 0.8)); /* Latar belakang glass effect */
            color: #333;
            height: 100vh;
            overflow: hidden;
        }

        h1 {
            text-align: center;
            font-family: 'Open Sans', sans-serif;
            color: #2c3e50; /* Darker Color for Header */
            padding: 20px 0;
            margin: 0;
            font-size: 36px;
        }

        #map {
            width: 100%;
            height: 80vh; /* Peta lebih menonjol */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Custom Info Control Styling with Glassmorphism */
        .info {
            padding: 20px;
            font: 14px/20px 'Open Sans', sans-serif;
            background: rgba(255, 255, 255, 0.2); /* Transparan */
            backdrop-filter: blur(10px); /* Efek blur di belakang */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin: 10px;
            transition: opacity 0.3s ease;
        }

        .info h4 {
            margin: 0;
            font-size: 18px;
            color: #34495e;
        }

        /* Custom Legend Control Styling with Glassmorphism */
        .legend {
            line-height: 24px;
            font-family: 'Open Sans', sans-serif;
            font-size: 14px;
            color: #34495e;
            background-color: rgba(255, 255, 255, 0.2); /* Transparan */
            backdrop-filter: blur(8px); /* Efek blur di belakang */
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .legend i {
            width: 20px;
            height: 20px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }

        /* Hover Effect on Provinces */
        .leaflet-interactive {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .leaflet-interactive:hover {
            transform: scale(1.05);
            opacity: 1 !important; /* Terang saat di-hover */
        }

        /* Efek transparansi provinsi non-hover */
        .leaflet-interactive.non-hover {
            opacity: 0.4;
        }

        /* Custom Map Controls Styling */
        .leaflet-control-attribution {
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            background-color: rgba(255, 255, 255, 0.5); /* Transparan */
            border-radius: 5px;
            padding: 5px 10px;
        }

        /* Styling untuk tombol redirect ke Admin */
        .redirect-button {
            position: fixed;
            left: 20px;
            z-index: 9999;
            bottom: 20px;
            padding: 15px 30px;
            background: linear-gradient(135deg, #ff7e5f, #feb47b); /* Warna tombol yang menarik */
            color: white;
            font-size: 16px;
            font-weight: 500;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* Efek hover pada tombol */
        .redirect-button:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f); /* Warna berubah saat hover */
            transform: translateY(-4px);
        }

        .redirect-button:active {
            transform: translateY(2px); /* Efek saat tombol ditekan */
        }

    </style>
</head>

<body>
    <h1> Map of Java </h1>
    <div id="map"></div>

    <!-- Tombol untuk redirect ke admin -->
    <button class="redirect-button" onclick="window.location.href = 'http://127.0.0.1:8000/admin';">Go to Admin</button>

    <script>
        // Inisialisasi peta dengan titik fokus Pulau Jawa
        const map = L.map('map').setView([-7.5, 110], 7); // Fokus pada Pulau Jawa

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fungsi untuk menentukan warna berdasarkan populasi
        function getColor(population) {
            return population > 60000 ? '#9b1b30' :
                   population > 40000 ? '#d36f72' :
                   population > 20000 ? '#e7713e' :
                   population > 10000 ? '#ffb84c' :
                   population > 5000 ? '#ffeb67' :
                   '#a0d084'; // Warna default jika tidak masuk dalam rentang
        }

        // Gaya untuk setiap fitur GeoJSON
        function style(feature) {
            return {
                fillColor: getColor(feature.properties.population),
                weight: 2,
                opacity: 0.7,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

        // Interaksi hover
        function highlightFeature(e) {
            const layer = e.target;

            layer.setStyle({
                weight: 5,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.9
            });

            layer.bringToFront();

            // Update info control saat hover
            info.update(layer.feature.properties);

            // Tambahkan kelas non-hover ke provinsi lainnya
            geojson.eachLayer(function (layer) {
                if (layer !== e.target) {
                    layer.getElement().classList.add('non-hover');
                }
            });
        }

        function resetHighlight(e) {
            geojson.resetStyle(e.target);
            info.update();

            // Hapus kelas non-hover
            geojson.eachLayer(function (layer) {
                layer.getElement().classList.remove('non-hover');
            });
        }

        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        // Menambahkan popup untuk setiap provinsi
        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });

            layer.bindPopup(`<b>${feature.properties.name}</b><br>Population: ${feature.properties.population}`);
        }

        let geojson;

        // Control untuk menampilkan informasi custom saat hover
        var info = L.control();

        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };

        // Method untuk memperbarui control info berdasarkan data yang di-hover
        info.update = function (props) {
            this._div.innerHTML = '<h4>Population of Java</h4>' + (props ?
                '<b>' + props.name + '</b><br />' + props.population + ' people'
                : 'Hover over a province');
        };

        info.addTo(map);

        // Control legenda untuk populasi
        var legend = L.control({ position: 'bottomright' });

        legend.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'info legend'),
                grades = [0, 5000, 10000, 20000, 40000, 60000],
                labels = [];

            // Loop untuk membuat label dan kotak warna untuk setiap rentang populasi
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                    grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
            }

            return div;
        };

        legend.addTo(map);

        // Fetch data GeoJSON dari API dan tambahkan ke peta
        fetch('/provinces')
            .then(response => response.json())
            .then(data => {
                console.log('GeoJSON Data:', data); // Debugging data GeoJSON
                geojson = L.geoJson(data, {
                    style: style,
                    onEachFeature: onEachFeature
                }).addTo(map);
            })
            .catch(error => {
                console.error('Error fetching GeoJSON:', error);
            });
    </script>
</body>

</html>
