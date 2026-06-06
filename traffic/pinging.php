<?php
// hide all error
//error_reporting(0);
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$session = $_GET['session'];

	include('../include/config.php');
	include('../include/readcfg.php');
	
// lang
	include('../include/lang.php');
	include('../lang/'.$langid.'.php');


	include_once('../lib/routeros_api.class.php');
	include_once('../lib/formatbytesbites.php');

	$API = new RouterosAPI();
	$API->debug = false;
	$API->connect($iphost, $userhost, decrypt($passwdhost));
	
	$getNet = $API->comm("/tool/netwatch/print", ["?.id" => $idping,]);
	$address= $getNet[0]['host'];
	$comment= $getNet[0]['comment'];
	$PING = $API->comm("/ping", array("address" => "$address", "count" => "3",));
	$num = count($PING);
	$text = "Ping  $address<br>";
	$text .="===================<br>";
	for ($i = 0;$i < $num;$i++) {
		$hot = $PING[$i]['host'];
		$status = $PING[$i]['status'];
		$size = $PING[$i]['size'];
		$ttl = $PING[$i]['ttl'];
		$time = $PING[$i]['time'];
		$packet_loss = $PING[$i]['packet-loss'];
		$avg = $PING[$i]['avg-rtt'];
		$packet_loss = $PING[$i]['packet-loss'];
		if ($status == 'timeout') {
			$text.= "PING $hot <br>Status $status Loss $packet_loss% <br>";
		} else {
			$text.= "PING $hot <br>Size $size TTL $ttl <br>Time $time AVG $avg<br>";
		}
		$text .="===================<br>";
	}
	$kirim="Ping to ".$address."|Keterangan ".$comment."|Result.<p>".$text;
	echo "<script>window.location='./?info=".$kirim."&session=".$session."'</script>";
}