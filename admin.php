<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';



$page = $_GET['page'] ?? 'dashboard';


if ($page === 'logout') {
    session_destroy();
    header('Location: ?page=login');
    exit;
}


if (!in_array($page, ['login', 'register'])) {
    require_admin_login();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_marquee') {
    $text = trim($_POST['marquee_text'] ?? '');
    $db = init_db_connection();

    if ($db) {
        $stmt = $db->prepare("INSERT INTO system_marquee (id, content) VALUES (1, ?) 
                              ON DUPLICATE KEY UPDATE content = VALUES(content)");
        $ok = $stmt->execute([$text]);

        if ($ok) {
            flash('success', 'Teks berjalan berhasil disimpan.');
        } else {
            flash('error', 'Gagal menyimpan teks berjalan.');
        }
    } else {
        flash('error', 'Koneksi database gagal.');
    }

    header('Location: ?page=running_text');
    exit;
}


$success = flash('success');
$error   = flash('error');

$admin_user = $_SESSION['admin_username'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Hotel IPTV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">

<?php if (is_admin_logged_in()): ?>
    <div class="flex h-screen">

        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col fixed h-screen">
            <div class="p-6 text-center border-b border-gray-700">
                <h2 class="text-2xl font-bold text-yellow-400">TakeOff IPTV</h2>
                <span class="text-sm">Admin Panel</span>
            </div>

            <nav class="flex-grow p-4 space-y-1">
                <a href="?page=dashboard" class="block px-4 py-2 rounded-lg <?=($page==='dashboard')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Dashboard</a>
                <a href="?page=devices" class="block px-4 py-2 rounded-lg <?=($page==='devices')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Perangkat</a>
                

                <a href="?page=checkin" class="block px-4 py-2 rounded-lg <?=($page==='checkin')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Manajemen Check-In</a>
                <a href="?page=send_notification" class="block px-4 py-2 rounded-lg <?=($page==='send_notification')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Notifikasi</a>

                <a href="?page=facilities" class="block px-4 py-2 rounded-lg <?=($page==='facilities')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Facilities</a>
                <a href="?page=amenities" class="block px-4 py-2 rounded-lg <?=($page==='amenities')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Amenities (Barang)</a>
                <a href="?page=information" class="block px-4 py-2 rounded-lg <?=($page==='information')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Information</a>
                <a href="?page=dining" class="block px-4 py-2 rounded-lg <?=($page==='dining')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Dining (Menu)</a>
                
                <hr class="border-gray-700 my-2">
                <span class="px-4 text-xs text-gray-500 uppercase">Pesanan Tamu</span>
                <a href="?page=dining_orders" class="block px-4 py-2 rounded-lg <?=($page==='dining_orders')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Pesanan Dining</a>
                <a href="?page=amenity_requests" class="block px-4 py-2 rounded-lg <?=($page==='amenity_requests')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Permintaan Amenities</a>
                <hr class="border-gray-700 my-2">
                
                <a href="?page=app_control" class="block px-4 py-2 rounded-lg <?=($page==='app_control')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Entertainment Apps</a>
                <a href="?page=running_text" class="block px-4 py-2 rounded-lg <?=($page==='running_text')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Running Text</a>
                <a href="?page=update" class="block px-4 py-2 rounded-lg <?=($page==='update')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">System Update</a>
                <a href="?page=flashscreen" class="block px-4 py-2 rounded-lg <?=($page==='flashscreen')?'bg-yellow-400 text-gray-900':'hover:bg-gray-800'?>">Flashscreen/Background</a>
            </nav>

            <div class="p-4 mt-auto border-t border-gray-700">
                <p class="text-xs mb-2 text-gray-400">Login sebagai: <b><?= htmlspecialchars($admin_user) ?></b></p>
                <a href="?page=logout" class="block text-red-400 hover:text-white hover:bg-red-500 px-4 py-2 rounded-lg text-center">Logout</a>
            </div>
        </aside>

       
        <main class="flex-grow p-10 ml-64 overflow-y-auto">
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php

                $allowed_pages = [
                    'dashboard', 'devices', 'checkin', 
                    'facilities', 'amenities', 'information', 'dining',
                    'dining_orders', 'amenity_requests', 
                    'app_control', 'running_text', 'update', 'flashscreen',
                    'login', 'register', 'send_notification'
                ];
             
                if (in_array($page, $allowed_pages)) {
                    $file = __DIR__ . "/pages/{$page}.php";
                    if (file_exists($file)) {
                        include $file;
                    } else {
                        echo "<p class='text-gray-600'>Halaman <b>{$page}</b> belum dibuat.</p>";
                    }
                } else {
                    echo "<p class='text-gray-600'>Halaman tidak dikenal.</p>";
                }
            ?>
        </main>
    </div>

<?php else: ?>
    <?php
      
        $auth_page = ($page === 'register') ? 'register' : 'login';
        include __DIR__ . "/pages/{$auth_page}.php";
    ?>
<?php endif; ?>

</body>
</html>