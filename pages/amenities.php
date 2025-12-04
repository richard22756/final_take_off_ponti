<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
$db = init_db_connection();
$uploadDir = __DIR__ . '/../uploads/amenities/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_amenity'])) {
        $name = $_POST['name']; $name_en = $_POST['name_en'];
        $desc = $_POST['description']; $desc_en = $_POST['description_en'];
        $img = $_POST['image_url'] ?? '';
        
        if (!empty($_FILES['image']['name'])) {
            $fn = 'am_' . time() . '.jpg';
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fn)) $img = 'uploads/amenities/' . $fn;
        }
        $db->prepare("INSERT INTO room_amenities (name, name_en, description, description_en, icon_path) VALUES (?, ?, ?, ?, ?)")->execute([$name, $name_en, $desc, $desc_en, $img]);
    }
    if (isset($_POST['delete_id'])) {
        $db->prepare("DELETE FROM room_amenities WHERE id=?")->execute([(int)$_POST['delete_id']]);
    }
}
$ams = $db->query("SELECT * FROM room_amenities ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 class="text-2xl font-bold mb-4">Amenities (Bilingual)</h1>
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <form method="POST" enctype="multipart/form-data" class="space-y-3">
            <input type="hidden" name="add_amenity" value="1">
            <input name="name" placeholder="Nama (ID)" class="w-full border p-2 rounded" required>
            <input name="name_en" placeholder="Name (EN)" class="w-full border p-2 rounded">
            <textarea name="description" placeholder="Deskripsi (ID)" class="w-full border p-2 rounded"></textarea>
            <textarea name="description_en" placeholder="Description (EN)" class="w-full border p-2 rounded"></textarea>
            <input type="file" name="image" class="w-full border p-2 rounded">
            <button class="w-full bg-yellow-500 py-2 rounded text-white">Simpan</button>
        </form>
    </div>
    <div class="bg-white p-6 rounded shadow max-h-[600px] overflow-auto">
        <?php foreach($ams as $a): ?>
        <div class="border-b pb-2 mb-2 flex justify-between items-start">
            <div class="flex gap-3">
                <img src="<?= htmlspecialchars(get_full_url($a['icon_path'])) ?>" class="w-16 h-16 object-cover rounded">
                <div>
                    <b class="block"><?=$a['name']?> <span class="text-gray-400 text-sm font-normal">/ <?=$a['name_en']?></span></b>
                    <p class="text-xs text-gray-600"><?=$a['description']?></p>
                    <p class="text-xs text-gray-400 italic"><?=$a['description_en']?></p>
                </div>
            </div>
            <form method="POST" onsubmit="return confirm('Hapus?')">
                <input type="hidden" name="delete_id" value="<?=$a['id']?>">
                <button class="text-red-500 text-sm">Hapus</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>