<?php
session_start();
error_reporting(0);

if (!isset($_SESSION["mikhmon"])) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

// load session MikroTik
$session = $_GET['session'];

// load config
include('../include/config.php');
include('../include/readcfg.php');

require('../lib/routeros_api.class.php');
$API = new RouterosAPI();
$API->debug = false;
$API->connect($iphost, $userhost, decrypt($passwdhost));

// Get users
$getuser = $API->comm("/ip/hotspot/user/print", array("?limit-uptime" => "0s"));
$TotalReg = count($getuser);

$acomment = "";
for ($i = 0; $i < $TotalReg; $i++) {
	$ucomment = $getuser[$i]['comment'];
	if ($ucomment != "") {
		$acomment .= "," . $ucomment;
	}
}

$ocomment = explode(",", $acomment);
$comments = array_count_values($ocomment);

$result = array();
foreach ($comments as $tcomment => $value) {
	if (trim($tcomment) != "") {
		$result[] = array(
			'comment' => $tcomment,
			'count' => $value
		);
	}
}

echo json_encode($result);
?>
