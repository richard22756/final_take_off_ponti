<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

include 'config.php'; 
include 'functions.php'; 
header('Content-Type: application/json; charset=utf-8');

$db = init_db_connection();
if ($db === null) {
    http_response_code(503);
    echo json_encode(['STATUS' => 'FAIL', 'description' => 'Koneksi database gagal.', 'message' => 'Failed']);
    exit;
}

$action = $_GET['action'] ?? '';
$lang   = $_GET['lang'] ?? 'id'; // Default bahasa Indonesia

function get_full_url($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return $path;
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function handleVHPAuth($db) {
    if (!defined('VHP_USER') || !defined('VHP_PASS')) {
        $vhp_user = 'vhp_admin';
        $vhp_pass = 'PassHotelRahasia123!';
    } else {
        $vhp_user = VHP_USER;
        $vhp_pass = VHP_PASS;
    }
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($auth_header) || strpos($auth_header, 'Basic ') !== 0) {
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            if ($_SERVER['PHP_AUTH_USER'] === $vhp_user && $_SERVER['PHP_AUTH_PW'] === $vhp_pass) return true;
        }
        http_response_code(401);
        header('WWW-Authenticate: Basic realm="VHP Integration"');
        echo json_encode(['STATUS' => 'FAIL', 'description' => 'Authorization required.', 'message' => 'Failed']);
        exit;
    }
    $base64_auth = substr($auth_header, 6);
    $decoded_auth = base64_decode($base64_auth);
    list($username, $password) = explode(':', $decoded_auth, 2);
    if ($username === $vhp_user && $password === $vhp_pass) return true;
    http_response_code(401);
    echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid credentials.', 'message' => 'Failed']);
    exit;
}

try {
    // === Integrasi VHP ===
    if (in_array($action, ['vhp_checkin', 'vhp_modifyguest', 'vhp_checkout'])) {
        handleVHPAuth($db); 
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            http_response_code(400); echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid JSON.', 'message' => 'Failed']); exit;
        }
        switch ($action) {
            // case 'vhp_checkin':
                
            //     if ($action === 'vhp_checkin') {
            //         handleVHPAuth($db);
            //         $input = json_decode(file_get_contents('php://input'), true);

            //         // Validasi input JSON
            //         if (!is_array($input)) {
            //             http_response_code(400);
            //             echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid JSON.', 'message' => 'Failed']);
            //             exit;
            //         }

            //         // Ambil data dari request
            //         $roomNo = $input['roomNo'] ?? null;
            //         $firstName = $input['firstName'] ?? '';
            //         $lastName = $input['lastName'] ?? '';
            //         $checkinDate = $input['checkinDate'] ?? null;
            //         $checkoutDate = $input['checkoutDate'] ?? null; // Checkout date untuk check-in yang benar

            //         // Validasi roomNo
            //         if (!$roomNo) {
            //             http_response_code(400);
            //             echo json_encode(['STATUS' => 'FAIL', 'description' => 'Missing roomNo.', 'message' => 'Failed']);
            //             exit;
            //         }

            //         // Format nama tamu
            //         $fullName = trim("$firstName $lastName");
            //         if (empty($fullName)) $fullName = "Room Guests of $roomNo";

            //         // Format waktu check-in jika ada
            //         $checkinTimeVal = 'NOW()';
            //         if ($checkinDate) {
            //             $dt = DateTime::createFromFormat('d/m/Y H:i:s', $checkinDate);
            //             if ($dt) $checkinTimeVal = "'" . $dt->format('Y-m-d H:i:s') . "'";
            //         }

            //         // Update status check-out untuk room yang sudah ter-check-in
            //         $db->prepare("UPDATE guest_checkin SET status='checked_out', checkout_time=NOW() WHERE room_number = ? AND status='checked_in'")->execute([$roomNo]);

            //         // SQL untuk insert check-in tamu baru
            //         $sql = "INSERT INTO guest_checkin (room_number, guest_name, checkin_time, status, checkout_time) VALUES (?, ?, " . ($checkinTimeVal === 'NOW()' ? 'NOW()' : '?') . ", 'checked_in', ?)";
            //         $params = [$roomNo, $fullName];
            //         if ($checkinTimeVal !== 'NOW()') $params[] = trim($checkinTimeVal, "'");
            //         $params[] = $checkoutDate ? $checkoutDate : 'NULL';

            //         $stmt = $db->prepare($sql);
            //         $stmt->execute($params);

            //         echo json_encode(['STATUS' => 'SUCCESS', 'description' => 'Room Checked In', 'message' => 'Success']);
            //         exit;
            //     }

            case 'vhp_checkin':
                if ($action === 'vhp_checkin') {
                    handleVHPAuth($db);
                    $input = json_decode(file_get_contents('php://input'), true);

                    // Validasi input JSON
                    if (!is_array($input)) {
                        http_response_code(400);
                        echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid JSON.', 'message' => 'Failed']);
                        exit;
                    }

                    // Ambil data dari request
                    $roomNo = $input['roomNo'] ?? null;
                    $firstName = $input['firstName'] ?? '';
                    $lastName = $input['lastName'] ?? '';
                    $checkinDate = $input['checkinDate'] ?? null;
                    $checkoutDate = $input['checkoutDate'] ?? null; // Checkout date untuk check-in yang benar

                    // Validasi roomNo
                    if (!$roomNo) {
                        http_response_code(400);
                        echo json_encode(['STATUS' => 'FAIL', 'description' => 'Missing roomNo.', 'message' => 'Failed']);
                        exit;
                    }

                    // Format nama tamu
                    $fullName = trim("$firstName $lastName");
                    if (empty($fullName)) $fullName = "Room Guests of $roomNo";

                    // Format waktu check-in jika ada
                    $checkinTimeVal = 'NOW()';
                    if ($checkinDate) {
                        // Pastikan formatnya benar dengan DateTime
                        $dt = DateTime::createFromFormat('d/m/Y H:i:s', $checkinDate);
                        if ($dt) {
                            $checkinTimeVal = "'" . $dt->format('Y-m-d H:i:s') . "'";
                        } else {
                            http_response_code(400);
                            echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid checkinDate format. Please use d/m/Y H:i:s.', 'message' => 'Failed']);
                            exit;
                        }
                    }

                    // Format checkoutDate jika ada
                    $checkoutTimeVal = 'NULL';
                    if ($checkoutDate) {
                        // Pastikan formatnya benar dengan DateTime
                        $dtCheckout = DateTime::createFromFormat('d/m/Y H:i:s', $checkoutDate);
                        if ($dtCheckout) {
                            $checkoutTimeVal = "'" . $dtCheckout->format('Y-m-d H:i:s') . "'";
                        } else {
                            http_response_code(400);
                            echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid checkoutDate format. Please use d/m/Y H:i:s.', 'message' => 'Failed']);
                            exit;
                        }
                    }

                    // Update status check-out untuk room yang sudah ter-check-in
                    $db->prepare("UPDATE guest_checkin SET status='checked_out', checkout_time=NOW() WHERE room_number = ? AND status='checked_in'")->execute([$roomNo]);

                    // SQL untuk insert check-in tamu baru
                    $sql = "INSERT INTO guest_checkin (room_number, guest_name, checkin_time, status, checkout_time) VALUES (?, ?, " . ($checkinTimeVal === 'NOW()' ? 'NOW()' : '?') . ", 'checked_in', $checkoutTimeVal)";
                    $params = [$roomNo, $fullName];
                    if ($checkinTimeVal !== 'NOW()') $params[] = trim($checkinTimeVal, "'");
                    
                    $stmt = $db->prepare($sql);
                    $stmt->execute($params);

                    echo json_encode(['STATUS' => 'SUCCESS', 'description' => 'Room Checked In', 'message' => 'Success']);
                    exit;
                }

            case 'vhp_modifyguest':

                if ($action === 'vhp_modifyguest') {
                    handleVHPAuth($db);
                    $input = json_decode(file_get_contents('php://input'), true);

                    // Validasi input JSON
                    if (!is_array($input)) {
                        http_response_code(400);
                        echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid JSON.', 'message' => 'Failed']);
                        exit;
                    }

                    // Ambil data dari request
                    $roomNo = $input['roomNo'] ?? null;
                    $firstName = $input['firstName'] ?? '';
                    $lastName = $input['lastName'] ?? '';
                    $modifyDate = $input['modifyDate'] ?? null; // Modify date untuk memperpanjang masa inap

                    // Validasi roomNo
                    if (!$roomNo) {
                        http_response_code(400);
                        echo json_encode(['STATUS' => 'FAIL', 'description' => 'Missing roomNo.', 'message' => 'Failed']);
                        exit;
                    }

                    // Format nama tamu
                    $fullName = trim("$firstName $lastName");
                    if (empty($fullName)) $fullName = "Room Guests of $roomNo";

                    // Format modify date jika ada
                    // $modifyTimeVal = 'NOW()';
                    // if ($modifyDate) {
                    //     $dt = DateTime::createFromFormat('d/m/Y H:i:s', $modifyDate);
                    //     if ($dt) $modifyTimeVal = "'" . $dt->format('Y-m-d H:i:s') . "'";
                    // }

                    $modifyTimeVal = 'NOW()';
                    if ($modifyDate) {
                        // Pastikan formatnya benar dengan DateTime
                        $dt = DateTime::createFromFormat('d/m/Y H:i:s', $modifyDate);
                        if ($dt) {
                            $modifyTimeVal = "'" . $dt->format('Y-m-d H:i:s') . "'";
                        } else {
                            http_response_code(400);
                            echo json_encode(['STATUS' => 'FAIL', 'description' => 'Invalid modifyDate format. Please use d/m/Y H:i:s.', 'message' => 'Failed']);
                            exit;
                        }
                    }

                    // Update status tamu yang sudah ada untuk room yang dimodifikasi
                    $sql = "UPDATE guest_checkin SET guest_name = ?, checkout_time = ? WHERE room_number = ? AND status = 'checked_in'";
                    $params = [$fullName, $modifyTimeVal, $roomNo];
                    
                    $stmt = $db->prepare($sql);
                    $stmt->execute($params);

                    echo json_encode(['STATUS' => 'SUCCESS', 'description' => 'Guest Information Modified', 'message' => 'Success']);
                    exit;
                }

            case 'vhp_checkout':
                $roomNo = $input['roomNo'] ?? null;
                if (!$roomNo) { http_response_code(400); echo json_encode(['STATUS' => 'FAIL', 'description' => 'Missing roomNo.', 'message' => 'Failed']); exit; }
                $db->beginTransaction();
                $db->prepare("UPDATE guest_checkin SET status = 'checked_out', checkout_time = NOW() WHERE room_number = ? AND status = 'checked_in'")->execute([$roomNo]);
                $db->prepare("DELETE FROM hotel_orders WHERE room_number = ?")->execute([$roomNo]);
                $db->prepare("DELETE FROM amenity_requests WHERE room_number = ?")->execute([$roomNo]);
                $db->commit();
                echo json_encode(['STATUS' => 'SUCCESS', 'description' => 'Room Checked Out', 'message' => 'Success']);
                break;
        }
        exit;
    }

    // === API Frontend ===
    switch ($action) {
        case 'checkRegistration':
            $device_id = strtoupper(trim($_GET['device_id'] ?? ''));
            if (!$device_id) throw new Exception('Device ID kosong.');
            $stmt = $db->prepare("SELECT COUNT(*) FROM managed_devices WHERE device_id=?");
            $stmt->execute([$device_id]);
            echo json_encode(['status' => 'success', 'is_registered' => $stmt->fetchColumn() > 0]);
            break;

        case 'getStatus':
            $stmt = $db->prepare("SELECT setting_value FROM global_settings WHERE setting_key='launcher_enabled'");
            $stmt->execute();
            echo json_encode(['status' => 'success', 'is_launcher_enabled' => (bool)($stmt->fetchColumn() ?? 0)]);
            break;

        case 'getGuestInfo':
            $device_id = strtoupper(trim($_GET['device_id'] ?? ''));
            $stmt_room = $db->prepare("SELECT room_number FROM managed_devices WHERE device_id=?");
            $stmt_room->execute([$device_id]);
            $room_number = $stmt_room->fetchColumn();
            if (!$room_number) throw new Exception('Perangkat tidak terdaftar.');
            $stmt_guest = $db->prepare("SELECT guest_name FROM guest_checkin WHERE room_number = ? AND status = 'checked_in' ORDER BY checkin_time DESC LIMIT 1");
            $stmt_guest->execute([$room_number]);
            $guest_name = $stmt_guest->fetchColumn() ?: "Guest";
            echo json_encode(['status'=>'success', 'data'=>['guest_name'=> $guest_name, 'room_number'=> $room_number]]);
            break;

        case 'getMarqueeText':
            $text = $db->query("SELECT content FROM system_marquee WHERE id=1")->fetchColumn() ?: 'Welcome!';
            echo json_encode(['status'=>'success','text'=>$text]);
            break;

        case 'getAppVisibility':
            $nameField = ($lang === 'en') ? 'COALESCE(NULLIF(app_name_en, ""), app_name) as app_name' : 'app_name';
            
            $apps=$db->query("SELECT app_key, $nameField, icon_path, android_package, is_visible FROM system_apps WHERE is_visible=1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($apps as &$a) $a['icon_path'] = get_full_url($a['icon_path']);
            echo json_encode(['status'=>'success','apps'=>$apps]);
            break;

        case 'getFacilities':
            // Bilingual & Show Description Logic
            $nameField = ($lang === 'en') ? 'COALESCE(NULLIF(name_en, ""), name) as name' : 'name';
            $descField = ($lang === 'en') ? 'COALESCE(NULLIF(description_en, ""), description) as description' : 'description';
            
            $rows = $db->query("SELECT id, $nameField, $descField, icon_path, show_description FROM hotel_facilities WHERE is_active=1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as &$r) $r['icon_path'] = get_full_url($r['icon_path']);
            echo json_encode(['status'=>'success','data'=>$rows]);
            break;

        case 'getInfo':
            // Bilingual & Show Description Logic
            $titleField = ($lang === 'en') ? 'COALESCE(NULLIF(title_en, ""), title) as name' : 'title as name';
            $descField  = ($lang === 'en') ? 'COALESCE(NULLIF(description_en, ""), description) as description' : 'description';
            
            $rows = $db->query("SELECT id, $titleField, $descField, icon_path, show_description FROM hotel_info ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as &$r) $r['icon_path'] = get_full_url($r['icon_path']);
            echo json_encode(['status'=>'success','data'=>$rows]);
            break;

        case 'getAmenities':
            // Bilingual
            $nameField = ($lang === 'en') ? 'COALESCE(NULLIF(name_en, ""), name) as name' : 'name';
            $descField = ($lang === 'en') ? 'COALESCE(NULLIF(description_en, ""), description) as description' : 'description';
            $rows = $db->query("SELECT id, $nameField, $descField, icon_path FROM room_amenities ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as &$r) $r['icon_path'] = get_full_url($r['icon_path']);
            echo json_encode(['status'=>'success','data'=>$rows]);
            break;

        case 'getDining':
            // Bilingual
            $nameField = ($lang === 'en') ? 'COALESCE(NULLIF(name_en, ""), name) as name' : 'name';
            $rows = $db->query("SELECT id, $nameField, price, image_url AS icon_path, status FROM dining_menu WHERE status='active' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as &$r) $r['icon_path'] = get_full_url($r['icon_path']);
            echo json_encode(['status'=>'success','data'=>$rows]);
            break;

        case 'submitDiningOrder':
            $input=json_decode(file_get_contents('php://input'),true);
            if(!is_array($input)||empty($input['items'])) { echo json_encode(['status'=>'error','message'=>'Invalid Data']); break; }
            $guest=$input['guest_name']??'Guest';
            $room=$input['room_number']??'-';
            $items=$input['items']; $total=0;
            foreach($items as $it) $total+=($it['qty']??0)*($it['price']??0);
            $json=json_encode($items,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $stmt=$db->prepare("INSERT INTO hotel_orders(room_number,guest_name,items,total_price,status,ordered_at)VALUES(?,?,?,?,'Pending',NOW())");
            $stmt->execute([$room,$guest,$json,$total]);
            echo json_encode(['status'=>'success','message'=>'Order Saved']);
            break;

        case 'submitAmenityRequest':
            $input=json_decode(file_get_contents('php://input'),true);
            if(!is_array($input)||empty($input['items'])) { echo json_encode(['status'=>'error','message'=>'Invalid Data']); break; }
            $guest=$input['guest_name']??'Guest';
            $room=$input['room_number']??'-';
            $items=$input['items'];
            $json=json_encode($items,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $stmt=$db->prepare("INSERT INTO amenity_requests(room_number,guest_name,items,status,requested_at)VALUES(?,?,?,'Pending',NOW())");
            $stmt->execute([$room,$guest,$json]);
            echo json_encode(['status'=>'success','message'=>'Request Saved']);
            break;

        case 'getSplash':
            $stmt=$db->prepare("SELECT setting_value FROM global_settings WHERE setting_key='splash_enabled'");
            $stmt->execute();
            if(!((int)($stmt->fetchColumn()??0))) { echo json_encode(['status'=>'disabled']); break; }
            $metaFile=__DIR__.'/uploads/flashscreen/metadata.json';
            if(!file_exists($metaFile)) { echo json_encode(['status'=>'error']); break; }
            $meta=json_decode(file_get_contents($metaFile),true);
            $url=$meta['url']??'';
            if(!empty($url) && strpos($url, 'http') !== 0) $url = BASE_URL . ltrim($url, '/');
            echo json_encode(['status'=>'success', 'url'=>$url]);
            break;
            
        case 'getHomeBackground':
            $bg = $db->query("SELECT setting_value FROM global_settings WHERE setting_key='launcher_home_bg'")->fetchColumn() ?: '';
            echo json_encode(['status' => 'success', 'background_url' => get_full_url($bg)]);
            break;

        case 'getWeather':
            $city = "Jakarta,ID"; $apiKey = "acb2744e5516a24f85e86a97e73f9427"; 
            $apiLang = ($lang === 'en') ? 'en' : 'id';
            $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$apiKey}&units=metric&lang={$apiLang}";
            $fallback = ['temp' => 28, 'description' => 'Cerah Berawan', 'icon' => '02d'];
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                $json = @file_get_contents($url, false, $ctx);
                if(!$json) throw new Exception("Error");
                $data = json_decode($json, true);
                echo json_encode(['status' => 'success', 'data' => ['temp' => (int)$data['main']['temp'], 'description' => ucwords($data['weather'][0]['description']), 'icon' => $data['weather'][0]['icon']]]);
            } catch (Throwable $e) { echo json_encode(['status' => 'success', 'data' => $fallback]); }
            break;

        case 'getCustomGreeting':
            $title = get_setting('custom_greeting_title') ?? 'Welcome';
            $content = get_setting('custom_welcome_greeting') ?? 'Welcome to our Hotel';
            $image = get_setting('custom_greeting_image') ?? 'img/hotel3.png';
            echo json_encode(['status' => 'success', 'data' => ['title' => htmlspecialchars_decode($title), 'content' => htmlspecialchars_decode($content), 'image' => get_full_url($image)]]);
            break;

        default: throw new Exception('Invalid Action');
    }

} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
?>