<?php
include("lib/routeros_api.class.php");
$API = new RouterosAPI();
$API->connect("10.10.10.2", "mikhmon", "1234");
print_r($API->comm("/tool/netwatch/print"));
?>
