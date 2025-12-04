<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
$db = init_db_connection();
$uploadDir = __DIR__ . '/../uploads/info/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_info'])) {
        $title = $_POST['title']; $title_en = $_POST['title_en'];
        $desc = $_POST['description']; $desc_en = $_POST['description_en'];
        $showDesc = (int)($_POST['show_description'] ?? 1); // NEW
        $img = $_POST['image_url'] ?? '';
        
        if (!empty($_FILES['image']['name'])) {
            $fn = 'info_' . time() . '.jpg';
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fn)) $img = 'uploads/info/' . $fn;
        }
        $db->prepare("INSERT INTO hotel_info (title, title_en, description, description_en, icon_path, show_description) VALUES (?, ?, ?, ?, ?, ?)")->execute([$title, $title_en, $desc, $desc_en, $img, $showDesc]);
    }
    if (isset($_POST['delete_id'])) {
        $db->prepare("DELETE FROM hotel_info WHERE id=?")->execute([(int)$_POST['delete_id']]);
    }
}
$infos = $db->query("SELECT * FROM hotel_info ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 class="text-2xl font-bold mb-4">Hotel Info (Bilingual)</h1>
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <form method="POST" enctype="multipart/form-data" class="space-y-3">
            <input type="hidden" name="add_info" value="1">
            <input name="title" placeholder="Judul (ID)" class="w-full border p-2 rounded" required>
            <input name="title_en" placeholder="Title (EN)" class="w-full border p-2 rounded">
            <textarea name="description" placeholder="Deskripsi (ID)" class="w-full border p-2 rounded"></textarea>
            <textarea name="description_en" placeholder="Description (EN)" class="w-full border p-2 rounded"></textarea>
            
            <div>
                <label class="block text-sm font-medium mb-1">Tampilkan Keterangan di TV?</label>
                <select name="show_description" class="w-full border p-2 rounded">
                    <option value="1">Ya, Tampilkan Teks</option>
                    <option value="0">Tidak (Hanya Gambar Full)</option>
                </select>
            </div>

            <input type="file" name="image" class="w-full border p-2 rounded">
            <button class="w-full bg-yellow-500 py-2 rounded text-white">Simpan</button>
        </form>
    </div>
    <div class="bg-white p-6 rounded shadow max-h-[600px] overflow-auto">
        <?php foreach($infos as $i): ?>
        <div class="border-b pb-2 mb-2 flex justify-between items-start">
            <div class="flex gap-3">
                <img src="<?= htmlspecialchars(get_full_url($i['icon_path'])) ?>" class="w-16 h-16 object-cover rounded">
                <div>
                    <b class="block"><?=$i['title']?> <span class="text-gray-400 text-sm font-normal">/ <?=$i['title_en']?></span></b>
                    <p class="text-xs text-gray-600"><?=$i['description']?></p>
                    <span class="text-xs px-2 py-0.5 rounded <?= $i['show_description'] ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' ?>">
                        <?= $i['show_description'] ? 'Teks Aktif' : 'Gambar Full' ?>
                    </span>
                </div>
            </div>
            <form method="POST" onsubmit="return confirm('Hapus?')">
                <input type="hidden" name="delete_id" value="<?=$i['id']?>">
                <button class="text-red-500 text-sm">Hapus</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>