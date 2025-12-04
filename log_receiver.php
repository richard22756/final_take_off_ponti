<?php
// simpan log launcher ke file server
$logDir = __DIR__ . "/logs";
if (!file_exists($logDir)) mkdir($logDir, 0777, true);

$date = date("Y-m-d_H-i-s");
$file = $logDir . "/launcher_" . $date . ".log";

$data = file_get_contents("php://input");
if (empty($data)) $data = json_encode($_REQUEST, JSON_PRETTY_PRINT);

file_put_contents($file, $data . "\n", FILE_APPEND);
echo "OK";
?>