<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Jawa Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">


    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Open+Sans:wght@300;400;600&display=swap"
        rel="stylesheet">

    <style>
        /* Global Styling */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, rgba(162, 194, 225, 0.8), rgba(225, 247, 213, 0.8));
            /* Latar belakang glass effect */
            color: #333;
            height: 100vh;
            overflow: hidden;
        }

        h1 {
            text-align: center;
            font-family: 'Open Sans', sans-serif;
            color: #2c3e50;
            /* Darker Color for Header */
            padding: 20px 0;
            margin: 0;
            font-size: 36px;
        }

        #map {
            width: 100%;
            height: 80vh;
            /* Peta lebih menonjol */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Custom Info Control Styling with Glassmorphism */
        .info {
            padding: 20px;
            font: 14px/20px 'Open Sans', sans-serif;
            background: rgba(255, 255, 255, 0.2);
            /* Transparan */
            backdrop-filter: blur(10px);
            /* Efek blur di belakang */
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
            background-color: rgba(255, 255, 255, 0.2);
            /* Transparan */
            backdrop-filter: blur(8px);
            /* Efek blur di belakang */
            padding: 15px;
            bottom: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            
        }

        /* Mobile - Sesuaikan posisi legenda */
        @media (max-width: 768px) {
            .legend {
                bottom: 100px;
                /* Tambah jarak dari navbar pada perangkat kecil */
            }
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
            opacity: 1 !important;
            /* Terang saat di-hover */
        }

        /* Efek transparansi provinsi non-hover */
        .leaflet-interactive.non-hover {
            opacity: 0.4;
        }

        /* Custom Map Controls Styling */
        .leaflet-control-attribution {
            font-family: 'Open Sans', sans-serif;
            font-size: 12px;
            background-color: rgba(255, 255, 255, 0.5);
            /* Transparan */
            border-radius: 5px;
            padding: 5px 10px;
        }

        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            /* Gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            /* Selalu di atas */
            visibility: hidden;
            /* Tersembunyi default */
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s;
        }

        #loading-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .loader {
            border: 8px solid rgba(255, 255, 255, 0.3);
            /* Lingkaran transparan */
            border-top: 8px solid white;
            /* Warna animasi */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            /* Animasi */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #error-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            /* Gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            /* Selalu di atas */
            visibility: hidden;
            /* Tersembunyi default */
            opacity: 0;
            transition: visibility 0.3s, opacity 0.3s;
        }

        #error-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .error-content {
            background: white;
            padding: 20px 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .error-content button {
            margin-top: 15px;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background: #ff6b6b;
            /* Warna tombol merah */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .error-content button:hover {
            background: #ff3b3b;
            /* Lebih gelap saat hover */
        }

        .glass-navbar {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            margin: 0 auto;
            max-width: 600px;
            width: 100%;
            display: flex;
            justify-content: space-around;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 15px 15px 0 0;
            transition: transform 0.3s ease-in-out;
            z-index: 99999;
        }

        /* Mobile - Navbar full width */
        @media (max-width: 768px) {
            .glass-navbar {
                width: 100%;
                max-width: 100%;
                left: 0;
                transform: none;
            }
        }


        .glass-navbar a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .glass-navbar a i {
            font-size: 24px;
            /* Ukuran ikon */
            color: #007bff;
            margin-bottom: 5px;
            transition: transform 0.2s, color 0.2s;
        }

        .glass-navbar a:hover i {
            transform: scale(1.2);
            /* Efek zoom */
            color: #0056b3;
            /* Warna saat hover */
        }

        .glass-navbar a span {
            font-size: 14px;
            color: #000;
            margin-top: 5px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <h1> Peta Jawa </h1>
    <div id="map"></div>

    <div class="glass-navbar">
        <a href="{{ route('home') }}">
            <i class="fas fa-users"></i>
            <span>Populasi</span>
        </a>
        <a href="{{ route('air') }}">
            <i class="fas fa-tint"></i>
            <span>Air</span>
        </a>
        <a href="{{ route('tanah') }}">
            <i class="fas fa-seedling"></i>
            <span>Tanah</span>
        </a>
        <a href="{{ route('hujan') }}">
            <i class="fas fa-cloud-showers-heavy"></i>
            <span>Hujan</span>
        </a>
    </div>



    <div id="loading-overlay">
        <div class="loader"></div>
    </div>

    <div id="error-overlay">
        <div class="error-content">
            <p>Failed to fetch data. Please try again.</p>
            <button onclick="retryFetch()">Try Again</button>
        </div>
    </div>

    <script></script>
    @yield('scripts')

</body>

</html>
