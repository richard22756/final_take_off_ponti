<?php
$logFile = 'launcher_log.txt';
$msg = date('Y-m-d H:i:s') . ' | ' . ($_POST['msg'] ?? 'No message') . "\n";
file_put_contents($logFile, $msg, FILE_APPEND);
echo "OK";
?>