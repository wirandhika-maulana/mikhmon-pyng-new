<?php
/*
 *  Process file untuk manage Quick Generate presets
 */
session_start();
error_reporting(0);

if (!isset($_SESSION["mikhmon"])) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

$quickGenFile = dirname(__FILE__) . '/../voucher/quickgen.json';

// Initialize if not exists
if (!file_exists($quickGenFile)) {
	file_put_contents($quickGenFile, json_encode(array()));
}

$genData = json_decode(file_get_contents($quickGenFile), true);
if (!is_array($genData)) {
	$genData = array();
}

// Handle add preset
if (isset($_POST['action']) && $_POST['action'] == 'add') {
	$name = trim($_POST['name']);
	$profile = trim($_POST['profile']);
	$qty = intval($_POST['qty']);
	$prefix = trim($_POST['prefix']);
	$server = isset($_POST['server']) ? trim($_POST['server']) : 'all';
	$usermode = isset($_POST['usermode']) ? trim($_POST['usermode']) : 'vc';
	$userlength = isset($_POST['userlength']) ? intval($_POST['userlength']) : 5;
	$char = isset($_POST['char']) ? trim($_POST['char']) : 'mix1';

	if ($name == '' || $profile == '' || $qty < 1) {
		echo json_encode(array('status' => 'error', 'message' => 'Nama, profile, dan qty harus diisi'));
		exit;
	}

	$newPreset = array(
		'id' => time() . rand(100, 999),
		'name' => $name,
		'profile' => $profile,
		'qty' => $qty,
		'prefix' => $prefix,
		'server' => $server,
		'usermode' => $usermode,
		'userlength' => $userlength,
		'char' => $char,
		'created' => date('Y-m-d H:i:s')
	);

	array_push($genData, $newPreset);
	file_put_contents($quickGenFile, json_encode($genData, JSON_PRETTY_PRINT));
	echo json_encode(array('status' => 'success', 'data' => $genData));
	exit;
}

// Handle delete preset
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
	$deleteId = $_POST['id'];
	$genData = array_values(array_filter($genData, function($item) use ($deleteId) {
		return $item['id'] != $deleteId;
	}));
	file_put_contents($quickGenFile, json_encode($genData, JSON_PRETTY_PRINT));
	echo json_encode(array('status' => 'success', 'data' => $genData));
	exit;
}

// Get all presets
echo json_encode(array('status' => 'success', 'data' => $genData));
?>
