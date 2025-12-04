<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

$db = init_db_connection();
$uploadDir = __DIR__ . '/../uploads/dining/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_menu'])) {
        $name = trim($_POST['name'] ?? '');
        $name_en = trim($_POST['name_en'] ?? ''); // Input baru
        $price = (int)($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'active';
        $image = trim($_POST['image'] ?? '');
        
        if (!empty($_FILES['upload_image']['name'])) {
            $fn = 'menu_' . time() . '_' . rand(1000,9999) . '.jpg';
            if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploadDir . $fn)) $image = 'uploads/dining/' . $fn;
        }

        $db->prepare("INSERT INTO dining_menu (name, name_en, price, image_url, status) VALUES (?, ?, ?, ?, ?)")->execute([$name, $name_en, $price, $image, $status]);
        header('Location: ?page=dining'); exit;
    }
    
    if (isset($_POST['edit_id'])) {
        $id = (int)$_POST['edit_id'];
        $name = trim($_POST['name'] ?? '');
        $name_en = trim($_POST['name_en'] ?? ''); // Input baru
        $price = (int)($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'active';
        $image = trim($_POST['image'] ?? '');
        
        if (!empty($_FILES['upload_image']['name'])) {
            $fn = 'menu_' . time() . '_' . rand(1000,9999) . '.jpg';
            if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $uploadDir . $fn)) $image = 'uploads/dining/' . $fn;
        }

        $db->prepare("UPDATE dining_menu SET name=?, name_en=?, price=?, image_url=?, status=? WHERE id=?")->execute([$name, $name_en, $price, $image, $status, $id]);
        header('Location: ?page=dining'); exit;
    }

    if (isset($_POST['delete_id'])) {
        $db->prepare("DELETE FROM dining_menu WHERE id=?")->execute([(int)$_POST['delete_id']]);
        header('Location: ?page=dining'); exit;
    }
}
$menus = $db->query("SELECT * FROM dining_menu ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-yellow-500 mb-4">Dining Menu (Bilingual)</h1>
    <div class="bg-white p-6 rounded shadow mb-6">
        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block font-semibold">Nama (ID)</label><input type="text" name="name" class="w-full border rounded p-2" required></div>
                <div><label class="block font-semibold">Name (EN)</label><input type="text" name="name_en" class="w-full border rounded p-2" placeholder="English Name"></div>
                <div><label class="block font-semibold">Harga</label><input type="number" name="price" class="w-full border rounded p-2" required></div>
                <div><label class="block font-semibold">Upload Gambar</label><input type="file" name="upload_image" class="w-full border rounded p-2"></div>
                <div><label class="block font-semibold">Status</label><select name="status" class="w-full border rounded p-2"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <button type="submit" name="add_menu" class="bg-yellow-500 text-white px-4 py-2 rounded">Tambah Menu</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <table class="w-full border text-sm">
            <thead><tr class="bg-gray-100"><th class="p-2 border">IMG</th><th class="p-2 border">Nama (ID)</th><th class="p-2 border">Name (EN)</th><th class="p-2 border">Harga</th><th class="p-2 border">Aksi</th></tr></thead>
            <tbody>
                <?php foreach($menus as $m): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border text-center"><img src="<?=htmlspecialchars(get_full_url($m['image_url']))?>" class="h-10 w-10 object-cover mx-auto"></td>
                    <td class="p-2 border"><?=$m['name']?></td>
                    <td class="p-2 border italic text-gray-500"><?=$m['name_en']?></td>
                    <td class="p-2 border"><?=number_format($m['price'])?></td>
                    <td class="p-2 border text-center space-x-2">
                        <button onclick="edit(<?=$m['id']?>,'<?=$m['name']?>','<?=$m['name_en']?>',<?=$m['price']?>,'<?=$m['image_url']?>','<?=$m['status']?>')" class="text-blue-500 font-bold">Edit</button>
                        <form method="POST" class="inline" onsubmit="return confirm('Hapus?')"><input type="hidden" name="delete_id" value="<?=$m['id']?>"><button class="text-red-500 font-bold">Hapus</button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h3 class="font-bold text-lg mb-4">Edit Menu</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" id="e_id">
            <input type="text" name="name" id="e_name" class="w-full border mb-2 p-2" placeholder="Nama ID">
            <input type="text" name="name_en" id="e_name_en" class="w-full border mb-2 p-2" placeholder="Name EN">
            <input type="number" name="price" id="e_price" class="w-full border mb-2 p-2">
            <input type="hidden" name="image" id="e_image"> <input type="file" name="upload_image" class="w-full border mb-2 p-2">
            <select name="status" id="e_status" class="w-full border mb-4 p-2"><option value="active">Active</option><option value="inactive">Inactive</option></select>
            <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="bg-gray-300 px-3 py-1 rounded">Batal</button><button class="bg-yellow-500 text-white px-3 py-1 rounded">Simpan</button></div>
        </form>
    </div>
</div>
<script>
function edit(id,n,ne,p,i,s){
    document.getElementById('e_id').value=id; document.getElementById('e_name').value=n; document.getElementById('e_name_en').value=ne;
    document.getElementById('e_price').value=p; document.getElementById('e_image').value=i; document.getElementById('e_status').value=s;
    document.getElementById('editModal').classList.remove('hidden'); document.getElementById('editModal').classList.add('flex');
}
</script>