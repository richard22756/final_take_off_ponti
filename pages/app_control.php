<?php
// ===========================================================
// APP CONTROL MODULE (v15.1 - Fixed Icons)
// ===========================================================
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$db = init_db_connection();
if (!$db) die("<div class='bg-red-100 border border-red-400 text-red-700 p-4 rounded'>Gagal konek database.</div>");

// === Hapus aplikasi ===
if (isset($_GET['delete'])) {
    $pkg = trim($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM system_apps WHERE android_package=?");
    $stmt->execute([$pkg]);
    flash('success', "Aplikasi dengan package <b>{$pkg}</b> berhasil dihapus.");
    header("Location: admin.php?page=app_control");
    exit;
}

// === Tambah aplikasi baru ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_app'])) {
    $name = trim($_POST['app_name'] ?? '');
    $package = trim($_POST['android_package'] ?? '');
    $sort = (int)($_POST['sort_order'] ?? 99);
    $uploadDir = __DIR__ . '/../uploads/icons/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $iconUrl = '';
    if (!empty($_FILES['icon_file']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['icon_file']['name'], PATHINFO_EXTENSION));
        $filename = 'icon_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['icon_file']['tmp_name'], $uploadDir . $filename);
        
        // Simpan path relatif agar fleksibel
        $iconUrl = "uploads/icons/" . $filename;
    }

    if ($name && $package) {
        $stmt = $db->prepare("INSERT INTO system_apps (app_key, app_name, android_package, icon_path, is_visible, sort_order)
                              VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->execute([
            strtolower(preg_replace('/[^a-z0-9]+/', '_', $name)),
            $name, $package, $iconUrl, $sort
        ]);
        flash('success', "Aplikasi <b>{$name}</b> berhasil ditambahkan!");
    } else {
        flash('error', 'Nama dan package wajib diisi.');
    }

    header("Location: admin.php?page=app_control");
    exit;
}

// === Update ON/OFF ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['app'] ?? [] as $pkg => $val) {
        $status = ($val === 'on') ? 1 : 0;
        $stmt = $db->prepare("UPDATE system_apps SET is_visible=? WHERE android_package=?");
        $stmt->execute([$status, $pkg]);
    }
    $stmt = $db->query("SELECT android_package FROM system_apps");
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $pkg) {
        if (!isset($_POST['app'][$pkg])) {
            $db->prepare("UPDATE system_apps SET is_visible=0 WHERE android_package=?")->execute([$pkg]);
        }
    }
    flash('success', 'Status aplikasi berhasil diperbarui.');
    header("Location: admin.php?page=app_control");
    exit;
}

// === Ambil daftar aplikasi ===
$stmt = $db->query("SELECT id, app_name, android_package, icon_path, is_visible, sort_order 
                    FROM system_apps ORDER BY sort_order ASC, id ASC");
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🎮 Entertainment App Manager</h1>
    <p class="text-gray-600 mb-8">
        Kelola aplikasi hiburan yang muncul di launcher hotel.
    </p>

    <form method="POST">
        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($apps as $app): ?>
                <div class="flex items-center justify-between bg-white rounded-xl shadow-md p-4 border border-gray-200 hover:shadow-lg transition">
                    <div class="flex items-center space-x-3">
                        <img src="<?= htmlspecialchars(get_full_url($app['icon_path'])) ?>" 
                             class="w-12 h-12 rounded-lg border border-gray-300 object-contain" alt="">
                        <div>
                            <h2 class="text-lg font-semibold"><?= htmlspecialchars($app['app_name']) ?></h2>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($app['android_package']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="app[<?= htmlspecialchars($app['android_package']) ?>]" 
                                   <?= $app['is_visible'] ? 'checked' : '' ?> 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-yellow-400 transition relative">
                                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                        <a href="admin.php?page=app_control&delete=<?= urlencode($app['android_package']) ?>"
                           onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($app['app_name']) ?>?')"
                           class="px-2 py-1 text-sm bg-red-500 hover:bg-red-600 text-white rounded-lg shadow">
                           🗑 Hapus
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 flex justify-end">
            <button name="update" class="px-5 py-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg shadow-lg">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>

    <div class="mt-12 bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4">➕ Tambah Aplikasi Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_app" value="1">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Aplikasi</label>
                    <input type="text" name="app_name" required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-400 focus:border-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Package Android</label>
                    <input type="text" name="android_package" required placeholder="contoh: com.netflix.ninja"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-400 focus:border-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampilan</label>
                    <input type="number" name="sort_order" value="99"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-yellow-400 focus:border-yellow-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Icon Aplikasi (PNG/JPG)</label>
                    <input type="file" name="icon_file" accept="image/*"
                           class="block w-full text-sm text-gray-300 bg-gray-800 border border-gray-700 rounded-lg cursor-pointer">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button class="px-5 py-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg shadow-lg">
                    ➕ Tambah Aplikasi
                </button>
            </div>
        </form>
    </div>
</div>