<?php
// File Diagnostik untuk memastikan database sudah di-set ke 'Enabled'
// HANYA UNTUK TUJUAN PENGUJIAN. Hapus file ini setelah selesai.

// --- Bagian ini harus sama persis dengan config.php Anda ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'ogietvco_hotel2');
define('DB_USER', 'ogietvco_uhotel2');
define('DB_PASS', 'Riz260578()'); // Pastikan ini adalah password yang benar!

function init_db_connection_diag() {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        // Jika koneksi gagal, tampilkan pesan error koneksi
        echo "<h1 style='color:red;'>ERROR KRITIS: GAGAL KONEKSI DATABASE</h1>";
        echo "<p>Cek ulang kredensial di file ini atau status database Anda.</p>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        return null;
    }
}
// --- Akhir Bagian Konfigurasi ---

$db = init_db_connection_diag();

echo "<!DOCTYPE html><html><head><title>DB Status Check</title><style>body{font-family:sans-serif; padding: 20px;} h1 {margin-top: 0;}</style></head><body>";

if ($db) {
    try {
        // Coba ambil status global launcher
        $stmt = $db->query("SELECT setting_value FROM global_settings WHERE setting_key = 'launcher_enabled'");
        $status = $stmt->fetchColumn();

        if ($status === false) {
            // Jika datanya tidak ada (tabel ada tapi barisnya belum di-insert)
            echo "<h1 style='color:orange;'>ERROR: DATA STATUS TIDAK DITEMUKAN!</h1>";
            echo "<p>Baris 'launcher_enabled' belum ada di tabel 'global_settings'.</p>";
            echo "<p>Jalankan perintah SQL ini di phpMyAdmin:</p>";
            echo "<pre>INSERT INTO global_settings (setting_key, setting_value) VALUES ('launcher_enabled', '1');</pre>";
        } else {
            $isEnabled = ($status === '1');
            $color = $isEnabled ? 'green' : 'red';
            $text = $isEnabled ? 'AKTIF' : 'NONAKTIF';
            
            echo "<h1 style='color:{$color};'>HASIL: Launcher seharusnya {$text}</h1>";
            echo "<p>Nilai yang dibaca database: <code>{$status}</code></p>";
            
            if (!$isEnabled) {
                echo "<p>Launcher Anda saat ini {$text} di database. Untuk mengaktifkannya, jalankan perintah SQL ini di phpMyAdmin:</p>";
                echo "<pre>UPDATE global_settings SET setting_value = '1' WHERE setting_key = 'launcher_enabled';</pre>";
            } else {
                echo "<p style='color:green;'>Status AKTIF! Silakan refresh halaman INDEX.PHP Anda.</p>";
            }
        }
        
    } catch (PDOException $e) {
        // Jika terjadi error SQL (misal tabel tidak ditemukan)
        echo "<h1 style='color:red;'>ERROR SQL: TABEL TIDAK DITEMUKAN</h1>";
        echo "<p>Harap pastikan Anda sudah mengimpor file <code>database.sql</code> (semua tabel) dengan benar.</p>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
}

echo "</body></html>";
?>
