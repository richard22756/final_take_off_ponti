<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'phpmyadmin');
define('DB_PASS', 'iptvmma2025');
define('DB_NAME', 'take_off');

define('BASE_URL', 'http://192.168.0.241/AHFix/');

define('VHP_USER', 'vhp_admin');
define('VHP_PASS', 'PassHotelRahasia123!');

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
        error_log("Database Connection Error: " . $e->getMessage());
        return null;
    }
}
?>