<?php
	date_default_timezone_set('Asia/Jakarta');
	$body = file_get_contents('php://input');
	$file="TriPay.Log";

	file_put_contents($file, date('Y-m-d H:i:s')."|[".$body."]|#\n", FILE_APPEND | LOCK_EX);

	$response = array("success" => true);
	echo json_encode($response);
?>