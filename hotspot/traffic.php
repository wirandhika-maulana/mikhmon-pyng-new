<?php
session_start();
// hide all error
error_reporting(0);
if(!isset($_SESSION["mikhmon"])){
  header("Location:../admin.php?id=login");
}else{
	$session = $_GET['session'];
//	$idproses = "<".strtolower(explode("-",$_GET['iface'])[0])."-".explode("-",$_GET['iface'])[1].">";
	$idproses = $_GET['iface']."/32";

	include('../include/config.php');
	include('../include/readcfg.php');

	include_once('../lib/routeros_api.class.php');
	include_once('../lib/formatbytesbites.php');

	$API = new RouterosAPI();
	$API->debug = false;


	if($API->connect( $iphost, $userhost, decrypt($passwdhost))){

		$dtgrafik = $API->comm("/queue/simple/print",['?target'=>"$idproses",]);

		$rows = array(); $rows2 = array();

		$trafik	= explode("/",$dtgrafik[0]['rate']);
		$ftx 	= $trafik[0];
		$frx    = $trafik[1];

		$rows['name'] = 'Tx';
		$rows['data'][] = $ftx;

		$rows2['name'] = 'Rx';
		$rows2['data'][] = $frx;
		
		$cek=json_encode($dtgrafik);
		
	}else{
		echo "<font color='#ff0000'>Connection Failed!!</font>";
	}
  
	$API->disconnect();
  
	$result = array();

	array_push($result,$rows);
	array_push($result,$rows2);
	print json_encode($result);
}
?>