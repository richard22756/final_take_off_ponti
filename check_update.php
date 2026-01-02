<?php
// Ambil versi aplikasi yang dikirimkan oleh aplikasi
$current_version_code = $_GET['version_code'] ?? 1;  // Dapatkan versi aplikasi dari permintaan

// Membaca file JSON untuk mendapatkan versi terbaru
$version_info = json_decode(file_get_contents('version_info.json'), true);

// Ambil latest_version_code dari file JSON
$latest_version_code = $version_info['latest_version_code'];
$apkUrl = $version_info['apkUrl'];

// Memeriksa apakah ada pembaruan
if ($current_version_code < $latest_version_code) {
    $response = [
        'hasUpdate' => true,
        'apkUrl' => $apkUrl  // URL APK terbaru
    ];
} else {
    $response = [
        'hasUpdate' => false,
        'apkUrl' => null
    ];
}

// Mengembalikan respons dalam format JSON
echo json_encode($response);
?>
