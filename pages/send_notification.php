<?php
/**
 * pages/send_notification.php
 *
 * Admin page untuk mengirim POPUP message ke device tertentu (berdasarkan kamar).
 * Cara kerja:
 *  - Admin memilih kamar + isi pesan
 *  - Server insert ke tabel popup_notifications per device_id (status=pending)
 *  - Android TV/STB ambil via polling: api.php?action=getNotifications&device_id=...
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$db = init_db_connection();
if ($db === null) {
    http_response_code(500);
    echo "<div style='padding:12px;background:#fee2e2;color:#991b1b;border-radius:8px;'>❌ Gagal koneksi database.</div>";
    exit;
}

function json_response(array $payload, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    if (ob_get_length()) { ob_clean(); }  // bersihkan output “nyasar”
    echo json_encode($payload);
    exit;
}

/**
 * Ambil daftar device dari managed_devices.
 * room_number dipakai untuk tampilan & pemilihan kamar.
 */
function getManagedDevices(PDO $db): array {
    $stmt = $db->prepare("
        SELECT id, device_id, device_name, room_number
        FROM managed_devices
        ORDER BY
            CASE WHEN room_number REGEXP '^[0-9]+$' THEN CAST(room_number AS UNSIGNED) ELSE 999999 END,
            room_number,
            device_name
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Enqueue popup untuk target devices.
 * title boleh null/empty.
 */
function enqueuePopupNotifications(PDO $db, array $targets, ?string $title, string $body, int $ttlMinutes = 1440): int {
    $insert = $db->prepare("
        INSERT INTO popup_notifications
            (device_id, room_number, title, body, status, created_at, expires_at)
        VALUES
            (:device_id, :room_number, :title, :body, 'pending', NOW(), DATE_ADD(NOW(), INTERVAL :ttl MINUTE))
    ");

    $count = 0;
    foreach ($targets as $t) {
        $deviceId = trim((string)($t['device_id'] ?? ''));
        if ($deviceId === '') {
            continue; // skip device yang belum punya device_id
        }

        $insert->execute([
            ':device_id'   => $deviceId,
            ':room_number' => (string)($t['room_number'] ?? ''),
            ':title'       => ($title !== null && $title !== '') ? $title : null,
            ':body'        => $body,
            ':ttl'         => $ttlMinutes,
        ]);
        $count++;
    }
    return $count;
}

/**
 * Resolve selected managed_devices.id -> target rows.
 */
function resolveTargetsByIds(PDO $db, array $ids): array {
    $ids = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
    if (!$ids) return [];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("
        SELECT id, device_id, room_number
        FROM managed_devices
        WHERE id IN ($placeholders)
    ");
    $stmt->execute($ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/* =========================
 * POST handler (AJAX)
 * ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Anda bisa menambahkan auth check di sini (session/role) jika diperlukan.

    $title = trim((string)($_POST['title'] ?? ''));
    $body  = trim((string)($_POST['body'] ?? ''));
    $rooms = $_POST['rooms'] ?? [];
    $ttl   = (int)($_POST['ttl_minutes'] ?? 1440);

    if ($ttl <= 0) $ttl = 1440;
    if ($ttl > 10080) $ttl = 10080; // max 7 hari

    if ($body === '') {
        json_response(['status' => 'error', 'message' => 'Pesan (body) wajib diisi.'], 400);
    }

    // Batasi panjang agar aman
    if (mb_strlen($title) > 255) $title = mb_substr($title, 0, 255);
    if (mb_strlen($body) > 2000) $body = mb_substr($body, 0, 2000);

    if (!is_array($rooms) || empty($rooms)) {
        json_response(['status' => 'error', 'message' => 'Pilih minimal 1 kamar.'], 400);
    }

    // Resolve target device(s)
    $targets = resolveTargetsByIds($db, $rooms);
    if (!$targets) {
        json_response(['status' => 'error', 'message' => 'Target device tidak ditemukan.'], 404);
    }

    try {
        $db->beginTransaction();

        $queued = enqueuePopupNotifications(
            $db,
            $targets,
            ($title !== '' ? $title : null),
            $body,
            $ttl
        );

        $db->commit();

        if ($queued <= 0) {
            json_response([
                'status' => 'error',
                'message' => 'Tidak ada device yang valid untuk menerima pesan (device_id kosong / belum terdaftar).'
            ], 400);
        }

        json_response([
            'status' => 'success',
            'message' => "Pesan berhasil di-queue ke {$queued} device."
        ]);
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        json_response(['status' => 'error', 'message' => 'Terjadi kesalahan saat enqueue pesan.'], 500);
    }
}

/* =========================
 * GET: Render HTML page
 * ========================= */
$devices = getManagedDevices($db);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Kirim Popup ke Kamar</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #0b1220;
            margin: 0;
            padding: 24px;
            color: #e5e7eb;
        }

        .wrap {
            max-width: 980px;
            margin: 0 auto;
        }

        .card {
            background: #0f172a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }

        h1 {
            margin: 0 0 14px;
            font-size: 20px;
            font-weight: 700;
        }

        .muted {
            color: #9ca3af;
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media (max-width: 860px) {
            .grid { grid-template-columns: 1fr; }
        }

        label {
            display: block;
            font-size: 13px;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.10);
            background: rgba(255,255,255,0.03);
            color: #e5e7eb;
            padding: 10px 12px;
            outline: none;
        }

        textarea { min-height: 110px; resize: vertical; }

        .row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #f59e0b;
            color: #111827;
        }

        .btn-primary:hover { background: #d97706; }

        .btn-ghost {
            background: rgba(255,255,255,0.06);
            color: #e5e7eb;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .btn-ghost:hover { background: rgba(255,255,255,0.10); }

        .alert {
            display: none;
            margin: 12px 0 0;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .alert.success {
            display: block;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #a7f3d0;
        }

        .alert.error {
            display: block;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fecaca;
        }

        .rooms-toolbar {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .rooms {
            margin-top: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 10px;
            background: rgba(255,255,255,0.02);
            max-height: 360px;
            overflow: auto;
        }

        .rooms ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px 10px;
        }

        @media (max-width: 860px) {
            .rooms ul { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 520px) {
            .rooms ul { grid-template-columns: 1fr; }
        }

        .room-item {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.06);
            background: rgba(255,255,255,0.02);
        }

        .room-item small {
            display: block;
            color: #9ca3af;
            font-weight: 400;
            margin-top: 2px;
        }

        .disabled {
            opacity: 0.55;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>Kirim Popup ke Kamar</h1>
        <div class="muted">
            Pesan akan di-queue ke device yang dipilih, lalu TV/STB akan menampilkan popup saat polling.
            Pastikan <code>managed_devices.device_id</code> sesuai dengan <code>device_id</code> yang dipakai di TV (localStorage).
        </div>

        <form id="notificationForm" method="post">
            <div class="grid">
                <div>
                    <label>Judul (opsional)</label>
                    <input class="input" name="title" placeholder="Contoh: Informasi" />
                </div>

                <!-- <div>
                    <label>Masa berlaku (menit)</label>
                    <select class="input" name="ttl_minutes">
                        <option value="60">60 menit</option>
                        <option value="240">240 menit</option>
                        <option value="1440" selected>1 hari</option>
                        <option value="4320">3 hari</option>
                        <option value="10080">7 hari</option>
                    </select>
                </div> -->
            </div>

            <div style="margin-top:14px;">
                <label>Pesan (wajib)</label>
                <textarea class="input" name="body" required placeholder="Tulis isi popup di sini..."></textarea>
            </div>

            <div class="rooms-toolbar">
                <input class="input" id="roomSearch" placeholder="Cari kamar / nama device..." style="flex:1;min-width:240px;" />
                <button type="button" class="btn btn-ghost" id="selectAllBtn">Pilih Semua</button>
                <button type="button" class="btn btn-ghost" id="deselectAllBtn">Batal Pilih</button>
            </div>

            <div class="rooms">
                <ul id="roomsList">
                    <?php if (!$devices): ?>
                        <li style="grid-column:1/-1;color:#9ca3af;">Tidak ada data device di managed_devices.</li>
                    <?php else: ?>
                        <?php foreach ($devices as $d): ?>
                            <?php
                                $id = (int)$d['id'];
                                $room = htmlspecialchars((string)($d['room_number'] ?? ''));
                                $name = htmlspecialchars((string)($d['device_name'] ?? ''));
                                $hasDeviceId = trim((string)($d['device_id'] ?? '')) !== '';
                            ?>
                            <li class="room-item <?= $hasDeviceId ? '' : 'disabled' ?>" data-room="<?= $room ?>" data-name="<?= $name ?>">
                                <input type="checkbox" name="rooms[]" value="<?= $id ?>" id="room_<?= $id ?>" <?= $hasDeviceId ? '' : 'disabled' ?> />
                                <label for="room_<?= $id ?>" style="margin:0;">
                                    <div><strong>Kamar <?= $room !== '' ? $room : '-' ?></strong></div>
                                    <small><?= $name !== '' ? $name : 'Device' ?><?= $hasDeviceId ? '' : ' (device_id belum terdaftar)' ?></small>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="row" style="margin-top:14px; justify-content: space-between;">
                <div class="muted" style="margin:0;">
                    Tips: jika popup muncul berulang, pastikan API <code>getNotifications</code> menandai status menjadi <code>delivered</code>.
                </div>
                <button type="submit" class="btn btn-primary">Kirim Popup</button>
            </div>

            <div id="alertMessage" class="alert"></div>
        </form>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('notificationForm');
    const alertBox = document.getElementById('alertMessage');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const searchInput = document.getElementById('roomSearch');
    const roomsList = document.getElementById('roomsList');

    function setAlert(type, msg) {
        alertBox.classList.remove('success', 'error');
        alertBox.classList.add(type);
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
    }

    function clearAlert() {
        alertBox.style.display = 'none';
        alertBox.textContent = '';
        alertBox.classList.remove('success', 'error');
    }

    selectAllBtn.addEventListener('click', () => {
        roomsList.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = true);
    });

    deselectAllBtn.addEventListener('click', () => {
        roomsList.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    });

    searchInput.addEventListener('input', () => {
        const q = (searchInput.value || '').toLowerCase().trim();
        roomsList.querySelectorAll('.room-item').forEach(item => {
            const room = (item.getAttribute('data-room') || '').toLowerCase();
            const name = (item.getAttribute('data-name') || '').toLowerCase();
            const match = room.includes(q) || name.includes(q);
            item.style.display = match ? '' : 'none';
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearAlert();

        // Validasi minimal client-side
        const body = (form.querySelector('textarea[name="body"]').value || '').trim();
        if (!body) {
            setAlert('error', 'Pesan (body) wajib diisi.');
            return;
        }

        const checked = roomsList.querySelectorAll('input[type="checkbox"]:checked');
        if (!checked.length) {
            setAlert('error', 'Pilih minimal 1 kamar.');
            return;
        }

        const formData = new FormData(form);

        try {
            const res = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const data = await res.json().catch(() => null);
            if (!data) {
                setAlert('error', 'Respons server tidak valid.');
                return;
            }

            if (data.status === 'success') {
                setAlert('success', data.message || 'Berhasil mengirim popup.');
                // optional: reset body saja
                form.reset();
            } else {
                setAlert('error', data.message || 'Gagal mengirim popup.');
            }
        } catch (err) {
            setAlert('error', 'Gagal menghubungi server.');
        }
    });
})();
</script>
</body>
</html>
