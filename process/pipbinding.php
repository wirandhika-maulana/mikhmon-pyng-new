<?php
session_start();
error_reporting(0);
if ($removeipbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/remove", array(
		".id" => "$removeipbinding",
	));

	$getqueue = $API->comm("/queue/simple/print", array(
		"?name" => "$macbinding",
	));

	$squeue = $getqueue[0]['.id'];

	$API->comm("/queue/simple/remove", array(
		".id" => "$squeue",
	));

	$getvalid = $API->comm("/system/scheduler/print", array(
		"?name" => "$macbinding",
	));

	$svalid = $getvalid[0]['.id'];

	$API->comm("/system/scheduler/remove", array(
		".id" => "$svalid",
	));

	$getarp = $API->comm("/ip/arp/print", array(
		"?address" => "$ipbinding",
	));
	$sarp = $getarp[0]['.id'];

	$API->comm("/ip/arp/remove", array(
		".id" => "$sarp",
	));

	$getlease = $API->comm("/ip/dhcp-server/lease/print", array(
		"?address" => "$ipbinding",
	));

	$slease = $getlease[0]['.id'];

	$API->comm("/ip/dhcp-server/lease/remove", array(
		".id" => "$slease",
	));			
}

// enable ip binging
elseif ($enableipbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/set", array(
		".id" => "$enableipbinding",
		"disabled" => "no",
	));
}

// disable ip binging
elseif ($disableipbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/set", array(
		".id" => "$disableipbinding",
		"disabled" => "yes",
	));
}

elseif ($blockipbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/set", array(
		".id" => "$blockipbinding",
		"type" => "blocked",
	));
}
elseif ($reguleripbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/set", array(
		".id" => "$reguleripbinding",
		"type" => "regular",
	));
}
elseif ($bypassedipbinding != "") {
	$API->comm("/ip/hotspot/ip-binding/set", array(
		".id" => "$bypassedipbinding",
		"type" => "bypassed",
	));
}

echo "<script>window.location='./?hotspot=ipbinding&session=".$session."'</script>";

