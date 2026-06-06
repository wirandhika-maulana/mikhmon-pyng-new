<?php
session_start();
// hide all error
error_reporting(0);
echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";
// remove netwatch
if ($removeNetwatch != "") {
	$API->comm("/tool/netwatch/remove", array(
		".id" => "$removeNetwatch",
	));
}

// enable netwatch
elseif ($enableNetwatch != "") {
	$API->comm("/tool/netwatch/set", array(
		".id" => "$enableNetwatch",
		"disabled" => "no",
	));
}

// disable ip binging
elseif ($disableNetwatch != "") {
	$API->comm("/tool/netwatch/set", array(
		".id" => "$disableNetwatch",
		"disabled" => "yes",
	));
}
//redirect to netwatch
echo "<script>window.location='./?interface=netwatch&session=" . $session . "'</script>";
