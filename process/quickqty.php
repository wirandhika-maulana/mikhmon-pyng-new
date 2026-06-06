<?php
/*
 *  Process file untuk manage quick qty buttons
 */
session_start();
error_reporting(0);

if (!isset($_SESSION["mikhmon"])) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

$quickQtyFile = dirname(__FILE__) . '/../voucher/quickqty.json';

// Initialize if not exists
if (!file_exists($quickQtyFile)) {
	file_put_contents($quickQtyFile, json_encode(array(10, 25, 100, 200, 300)));
}

$qtyData = json_decode(file_get_contents($quickQtyFile), true);
if (!is_array($qtyData)) {
	$qtyData = array(10, 25, 100, 200, 300);
}

// Handle add quick qty
if (isset($_POST['action']) && $_POST['action'] == 'add') {
	$newQty = intval($_POST['qty']);
	if ($newQty > 0 && $newQty <= 999 && !in_array($newQty, $qtyData)) {
		array_push($qtyData, $newQty);
		sort($qtyData);
		file_put_contents($quickQtyFile, json_encode($qtyData));
		echo json_encode(array('status' => 'success', 'data' => $qtyData));
	} else {
		echo json_encode(array('status' => 'error', 'message' => 'Invalid or duplicate qty'));
	}
	exit;
}

// Handle delete quick qty
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
	$delQty = intval($_POST['qty']);
	$qtyData = array_values(array_filter($qtyData, function($item) use ($delQty) {
		return $item != $delQty;
	}));
	file_put_contents($quickQtyFile, json_encode($qtyData));
	echo json_encode(array('status' => 'success', 'data' => $qtyData));
	exit;
}

// Get all quick qty
echo json_encode(array('status' => 'success', 'data' => $qtyData));
?>
