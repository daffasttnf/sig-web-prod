@extends('app')

@section('title', 'Air Page')

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
        function getColor(ika) {
            return ika > 85 ? '#4b9cd3' : // Biru terang untuk IKA sangat tinggi
                ika > 70 ? '#67a9d0' : // Biru muda
                ika > 65 ? '#7fb3c2' : // Biru kebiruan
                ika > 45 ? '#99b7b3' : // Biru kehijauan
                ika > 40 ? '#e88a6d' : // Merah muda keoranyean
                '#d66e6e'; // Merah terang untuk IKA rendah
        }

        // Gaya untuk setiap fitur GeoJSON
        function style(feature) {
            return {
                fillColor: getColor(feature.properties.ika),
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
                `<b>${feature.properties.name}</b><br>IKA: ${feature.properties.ika}<br>Sungai: ${feature.properties.main_river}<br>Status: ${feature.properties.water_quality}`
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
            this._div.innerHTML = '<h4>Indeks Kualitas Air Sungai di Jawa</h4>' + (props ?
                '<b>' + props.name + '</b><br />' + props.ika :
                'Hover over a province');
        };

        info.addTo(map);

        // Control legenda untuk populasi
        var legend = L.control({
            position: 'bottomright'
        });

        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'info legend'),
                ikaGrades = [0, 40, 45, 65, 70, 85]; // Menyesuaikan rentang IKA

            // Loop untuk membuat label dan kotak warna untuk setiap rentang IKA
            for (var i = 0; i < ikaGrades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getColor(ikaGrades[i] + 1) + '"></i> ' +
                    // Menggunakan getColor dengan nilai lebih dari grade[i]
                    ikaGrades[i] + (ikaGrades[i + 1] ? '&ndash;' + ikaGrades[i + 1] + '<br>' : '+');
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
