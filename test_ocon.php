<?php
// v12.2 - File Tes Diagnostik
// File ini akan membaca database 'system_apps' dan mengecek apakah file ikon ada di server.

include 'config.php';
$db = init_db_connection();
if ($db === null) {
    die("Koneksi database gagal. Periksa 'config.php' dan 'php-error.log'.");
}

echo "<!DOCTYPE html><html lang='id'><head><title>Tes Path Ikon</title><style>
    body { font-family: sans-serif; margin: 20px; background: #f4f4f4; }
    h1 { color: #333; }
    table { border-collapse: collapse; width: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
    th { background-color: #333; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .status-ok { color: green; font-weight: bold; }
    .status-error { color: red; font-weight: bold; }
    code { background: #eee; padding: 2px 5px; border-radius: 4px; }
</style></head><body>";

echo "<h1>Hasil Tes Path Ikon (v12.2)</h1>";
echo "<p>File ini mengecek path ikon yang terdaftar di database <code>system_apps</code> Anda.</p>";

try {
    $stmt = $db->query("SELECT app_name, icon_path FROM system_apps ORDER BY sort_order ASC");
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($apps)) {
        echo "<p class='status-error'>ERROR: Tabel 'system_apps' kosong atau tidak ada. Harap jalankan 'database.sql' (v12.2).</p>";
    } else {
        echo "<table><thead><tr><th>Nama Aplikasi</th><th>Path di Database</th><th>Status Path</th></tr></thead><tbody>";
        
        foreach ($apps as $app) {
            $path_in_db = $app['icon_path'];
            
            // Mengecek path persis seperti yang ada di database
            // __DIR__ adalah folder saat ini (yaitu /home/ogietvco/public_html/AHotel)
            $full_server_path = __DIR__ . '/' . $path_in_db;

            echo "<tr>";
            echo "<td>" . htmlspecialchars($app['app_name']) . "</td>";
            echo "<td><code>" . htmlspecialchars($path_in_db) . "</code></td>";
            
            if (file_exists($full_server_path)) {
                echo "<td class='status-ok'>DITEMUKAN di:<br><code>" . htmlspecialchars($full_server_path) . "</code></td>";
            } else {
                echo "<td class='status-error'>HILANG di:<br><code>" . htmlspecialchars($full_server_path) . "</code></td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    }

} catch (PDOException $e) {
    echo "<p class='status-error'>DATABASE ERROR: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
