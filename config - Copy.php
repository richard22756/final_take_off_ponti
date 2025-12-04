<?php
// ============================================================
// CONFIGURATION FILE (Core System v15.0)
// ============================================================

// --- 1. PENGATURAN DATABASE ---
// Sesuaikan dengan kredensial database Anda
define('DB_HOST', 'localhost');
define('DB_USER', 'U_Takeoff'); // Ganti dengan user database Anda
define('DB_PASS', 'TakeOff2025');     // Ganti dengan password database Anda
define('DB_NAME', 'takeoff');

// --- 2. PENGATURAN BASE URL (PENTING!) ---
// Ganti URL ini sesuai alamat server Anda.
// Akhiri dengan garis miring '/'
// Contoh Hosting: 'https://ogietv.com/AHotel/'
// Contoh Lokal:   'http://192.168.1.100/AHotel/'
define('BASE_URL', 'http://192.168.1.7/AHFix/');

// --- 3. PENGATURAN INTEGRASI VHP (PMS) ---
// Username & Password ini harus diberikan ke tim IT VHP
// untuk otentikasi Basic Auth saat mereka mengirim data tamu.
define('VHP_USER', 'vhp_admin');
define('VHP_PASS', 'PassHotelRahasia123!');

// ============================================================
// FUNGSI KONEKSI DATABASE
// ============================================================
function init_db_connection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Log error ke file php_error.log, jangan tampilkan ke user agar tidak error di layar
        error_log("Database Connection Error: " . $e->getMessage());
        return null;
    }
}
?>