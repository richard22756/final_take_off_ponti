<?php


if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$db = init_db_connection();
if (!$db) {
    echo "<h2 style='color:red;text-align:center;margin-top:20vh;'>❌ Database tidak dapat terhubung.<br>Periksa file config.php</h2>";
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_device') {
            $device_id   = trim($_POST['device_id'] ?? '');
            $device_name = trim($_POST['device_name'] ?? '');
            $room_number = trim($_POST['room_number'] ?? '');

            if ($device_id === '' || $room_number === '') {
                $error = 'Device ID dan Nomor Kamar wajib diisi.';
            } else {
                $stmt = $db->prepare("
                    INSERT INTO managed_devices (device_id, device_name, room_number, registered_at)
                    VALUES (?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE
                        device_name = VALUES(device_name),
                        room_number = VALUES(room_number),
                        registered_at = NOW()
                ");
                $stmt->execute([$device_id, $device_name, $room_number]);
                $success = 'Perangkat berhasil disimpan.';
            }
        }

        if ($action === 'delete_device') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $db->prepare("DELETE FROM managed_devices WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Perangkat berhasil dihapus.';
            }
        }
    } catch (Exception $e) {
        $error = 'Terjadi kesalahan database: ' . $e->getMessage();
    }
}


try {
    $stmt = $db->query("SELECT * FROM managed_devices ORDER BY room_number ASC");
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $devices = [];
    error_log("Fetch Devices Error: " . $e->getMessage());
}
?>

<div class="bg-gray-100 min-h-screen">
  <div class="bg-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Perangkat</h1>
    <p class="text-gray-500 mb-6">
      Tambahkan, ubah, atau hapus perangkat TV yang terdaftar di sistem hotel. Setiap perangkat wajib memiliki Device ID unik dan nomor kamar.
    </p>

    <!-- ALERTS -->
    <?php if ($success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH -->
    <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-6">
      <input type="hidden" name="action" value="add_device">

      <div>
        <label class="block text-sm font-medium text-gray-700">Device ID</label>
        <input type="text" name="device_id" placeholder="Contoh: TV-101-A"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Perangkat</label>
        <input type="text" name="device_name" placeholder="Contoh: Smart TV Lobby"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Nomor Kamar</label>
        <input type="text" name="room_number" placeholder="Contoh: 101"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-yellow-400 focus:border-yellow-400">
      </div>

      <div class="flex items-center justify-center">
        <button type="submit" class="px-6 py-2 bg-yellow-400 text-gray-900 font-semibold rounded-lg shadow-md hover:bg-yellow-500 focus:ring-2 focus:ring-yellow-300">
          Simpan
        </button>
      </div>
    </form>

    <hr class="my-8">

    <!-- TABEL PERANGKAT -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Device ID</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Perangkat</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor Kamar</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Terdaftar</th>
            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (empty($devices)): ?>
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada perangkat terdaftar.</td></tr>
          <?php else: ?>
            <?php foreach ($devices as $d): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-3 text-sm text-gray-700"><?= htmlspecialchars($d['id']) ?></td>
                <td class="px-6 py-3 text-sm font-mono text-gray-800"><?= htmlspecialchars($d['device_id']) ?></td>
                <td class="px-6 py-3 text-sm text-gray-600"><?= htmlspecialchars($d['device_name']) ?></td>
                <td class="px-6 py-3 text-sm font-semibold text-gray-800"><?= htmlspecialchars($d['room_number']) ?></td>
                <td class="px-6 py-3 text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($d['registered_at'])) ?></td>
                <td class="px-6 py-3 text-center">
                  <form method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                    <input type="hidden" name="action" value="delete_device">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($d['id']) ?>">
                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>