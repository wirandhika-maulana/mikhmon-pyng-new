<?php
$start = microtime(true);
$socket = @stream_socket_client("wiran.my.id:8733:8728", $errno, $errstr, 3, STREAM_CLIENT_CONNECT);
$end = microtime(true);
echo "Time: " . ($end - $start) . "\n";
echo "Errno: $errno, Errstr: $errstr\n";
?>
