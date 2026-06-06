<?php
/*
 *  Process file untuk mendapatkan prefix reseller
 */
session_start();
error_reporting(0);

$resellerFile = './voucher/reseller.json';

if (file_exists($resellerFile)) {
	$resellerData = json_decode(file_get_contents($resellerFile), true);
	
	if (isset($_GET['getprefix'])) {
		$resellerId = $_GET['getprefix'];
		
		// Find reseller by ID
		$prefix = '';
		foreach ($resellerData as $reseller) {
			if ($reseller['id'] == $resellerId) {
				$prefix = $reseller['prefix'];
				break;
			}
		}
		
		echo json_encode(array('prefix' => $prefix));
		exit;
	}
	
	// Get all resellers
	echo json_encode($resellerData);
} else {
	echo json_encode(array());
}
?>
