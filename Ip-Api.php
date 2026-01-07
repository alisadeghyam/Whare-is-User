<?php

$result = false;
$error = false;

if (isset($_POST['RESET'])) {
    $result = false;
    $error = false;
}

if (isset($_POST['submit'])) {

    $ip = $_POST['UserIp'];
    $url = "http://ip-api.com/json/" . $ip;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    $respone = json_decode($resp, true);

    if ($respone['status'] == 'success') {
        $result = true;
        $error = false;
    } else {
        $result = false;
        $error = true;
    }
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جستجوی IP</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- فونت فارسی -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }

        p {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .fade-in {
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        #map {
            height: 300px;
            width: 100%;
            border-radius: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-100 to-blue-300 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-lg">

        <div class="bg-white shadow-2xl rounded-3xl p-8 fade-in text-right">

            <h1 class="text-3xl font-bold text-blue-700 mb-6 text-center">جستجوی اطلاعات IP</h1>

            <!-- پیام خطا -->
            <?php if ($error) { ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6 fade-in" role="alert">
                    <strong class="font-bold">خطا!</strong>
                    <span class="block mt-1">آدرس IP وارد شده معتبر نیست یا اطلاعات آن قابل دریافت نیست.</span>
                </div>
            <?php } ?>

            <!-- فرم جستجو -->
            <?php if (!$result) { ?>
                <form method="POST" class="flex flex-col gap-4">
                    <input type="text" name="UserIp" placeholder="IP کاربر را وارد کنید"
                        class="p-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-700 transition duration-300 text-right">
                    <button name="submit" type="submit"
                        class="bg-blue-500 text-white py-3 rounded-xl font-semibold shadow hover:bg-blue-600 transition duration-300 transform hover:scale-105">جستجو</button>
                </form>
            <?php } ?>

            <!-- نمایش نتیجه -->
            <?php if ($result) { ?>
                <div class="space-y-3 text-gray-700 mt-4 text-right">
                    <p><span class="font-semibold">IP:</span> <?php echo $respone['query']; ?></p>
                    <p><span class="font-semibold">کشور:</span> <?php echo $respone['country'] . " (" . $respone['countryCode'] . ")"; ?></p>
                    <p><span class="font-semibold">استان:</span> <?php echo $respone['regionName'] . " (" . $respone['region'] . ")"; ?></p>
                    <p><span class="font-semibold">شهر:</span> <?php echo $respone['city']; ?></p>
                    <p><span class="font-semibold">مختصات:</span> <?php echo $respone['lat'] . ", " . $respone['lon']; ?></p>
                    <p><span class="font-semibold">منطقه زمانی:</span> <?php echo $respone['timezone']; ?></p>
                </div>

                <!-- Map -->
                <div id="map"></div>

                <!-- دکمه شروع مجدد -->
                <form method="POST" class="mt-6 text-center">
                    <button name="RESET" type="submit"
                        class="inline-block px-8 py-3 bg-blue-500 text-white rounded-2xl shadow hover:bg-blue-600 transition font-semibold">شروع مجدد</button>
                </form>

                <script>
                    const lat = <?php echo $respone['lat']; ?>;
                    const lon = <?php echo $respone['lon']; ?>;
                    const map = L.map('map').setView([lat, lon], 10);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    L.marker([lat, lon]).addTo(map)
                        .bindPopup('<?php echo $respone['city'] . ", " . $respone['country']; ?>')
                        .openPopup();
                </script>
            <?php } ?>

        </div>
    </div>

</body>

</html>
