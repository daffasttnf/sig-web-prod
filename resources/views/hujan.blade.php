@extends('app')

@section('title', 'Hujan Page')

@section('content')
    <h1>Welcome to the Air Page</h1>
    <div id="air"></div>
@endsection

@section('scripts')

    <script>
        // Inisialisasi peta dengan titik fokus Pulau Jawa
        const map = L.map('map').setView([-7.5, 110], 7); // Fokus pada Pulau Jawa

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fungsi untuk menentukan warna berdasarkan populasi
        function getColor(rainfall) {
            return rainfall > 3000 ? '#003366' : // Biru tua untuk curah hujan sangat tinggi
                rainfall > 2500 ? '#004d7f' : // Biru sedikit lebih terang
                rainfall > 2000 ? '#006699' : // Biru sedang
                rainfall > 1800 ? '#3385b4' : // Biru muda sedikit gelap
                rainfall > 1500 ? '#66a3cc' : // Biru muda
                '#99c2e6'; // Biru sangat terang untuk curah hujan rendah
        }



        // Gaya untuk setiap fitur GeoJSON
        function style(feature) {
            return {
                fillColor: getColor(feature.properties.rainfall),
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
        }

        function resetHighlight(e) {
            geojson.resetStyle(e.target);
            info.update();
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

            layer.bindPopup(
                `<b>${feature.properties.name}</b><br>Rainfall: ${feature.properties.rainfall} mm </br>Category: ${feature.properties.rainfall_category}`
                );
        }

        let geojson;

        // Control untuk menampilkan informasi custom saat hover
        var info = L.control();

        info.onAdd = function(map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };

        // Method untuk memperbarui control info berdasarkan data yang di-hover
        info.update = function(props) {
            this._div.innerHTML = '<h4>Curah Hujan di Jawa</h4>' + (props ?
                '<b>' + props.name + '</b><br />' + props.rainfall + ' mm' :
                'Hover over a province');
        };

        info.addTo(map);

        // Control legenda untuk curah hujan
        var legend = L.control({
            position: 'bottomright'
        });

        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend'),
                rainfallRanges = [0, 1500, 1800, 2000, 2500, 3000]; // Rentang curah hujan

            // Loop untuk membuat label dan kotak warna untuk setiap rentang curah hujan
            for (var i = 0; i < rainfallRanges.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getColor(rainfallRanges[i] + 1) + '"></i> ' +
                    rainfallRanges[i] + (rainfallRanges[i + 1] ? '&ndash;' + rainfallRanges[i + 1] + '<br>' : '+');
            }

            return div;
        };

        legend.addTo(map);


        // Referensi ke overlay elemen
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorOverlay = document.getElementById('error-overlay');

        // Tampilkan overlay
        function showLoading() {
            loadingOverlay.classList.add('active');
        }

        // Sembunyikan overlay
        function hideLoading() {
            loadingOverlay.classList.remove('active');
        }

        // Tampilkan overlay error
        function showError() {
            errorOverlay.classList.add('active');
        }

        // Sembunyikan overlay error
        function hideError() {
            errorOverlay.classList.remove('active');
        }

        // Fungsi untuk mencoba ulang fetch
        function retryFetch() {
            hideError(); // Sembunyikan error saat mencoba lagi
            loadData(); // Panggil fungsi untuk memuat ulang data
        }

        // Fungsi untuk memuat data
        function loadData() {
            showLoading(); // Tampilkan loading overlay

            fetch('/provinces')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok'); // Tangkap error jika respons tidak OK
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('GeoJSON Data:', data); // Debugging data GeoJSON

                    // Tambahkan data GeoJSON ke peta
                    geojson = L.geoJson(data, {
                        style: style,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    // Sembunyikan loading overlay setelah data dimuat
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error fetching GeoJSON:', error);
                    hideLoading(); // Sembunyikan loading overlay
                    showError(); // Tampilkan error overlay
                });
        }

        // Panggil fungsi untuk memuat data saat pertama kali halaman dimuat
        loadData();
    </script>
@endsection
