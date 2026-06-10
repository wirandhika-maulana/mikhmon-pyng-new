<?php
session_start();
// hide all error
error_reporting(0);
if(!isset($_SESSION["mikhmon"])){
  header("Location:../admin.php?id=login");
}else{
	$session = $_GET['session'];
	$idproses = "<".$_GET['iface'].">";

	include('../include/config.php');
	include('../include/readcfg.php');

	include_once('../lib/routeros_api.class.php');
	include_once('../lib/formatbytesbites.php');

	$API = new RouterosAPI();
	$API->debug = false;

    // Load Dual Router Config (Secondary PPPoE Router)
    $dual_router_ip = "";
    if (file_exists('../include/dual_router_config.php')) {
        include('../include/dual_router_config.php');
        if (isset($dual_router[$session]) && !empty($dual_router[$session]['ip'])) {
            $dual_router_ip = $dual_router[$session]['ip'];
            $dual_router_user = $dual_router[$session]['user'];
            $dual_router_pass = decrypt($dual_router[$session]['pass']);
        }
    }

    if (!empty($dual_router_ip)) {
        $connected = $API->connect($dual_router_ip, $dual_router_user, $dual_router_pass);
    } else {
	    $connected = $API->connect($iphost, $userhost, decrypt($passwdhost));
    }

	if($connected){

//		$dtgrafik = $API->comm("/queue/simple/print",['?name'=>"$idproses",]);
		$dtgrafik = $API->comm("/interface/monitor-traffic", array(
			"interface" => "$idproses",
			"once" => "",
		));

		$rows = array(); $rows2 = array();
		$frx=$dtgrafik[0]['rx-bits-per-second'];
		$ftx=$dtgrafik[0]['tx-bits-per-second'];

/*		$trafik	= explode("/",$dtgrafik[0]['bytes']);
		$ftx  	= $trafik[0];
		$frx     = $trafik[1];*/

		$cek=json_encode($dtgrafik);
		
//		file_put_contents("okokok.txt","data -> ".$idproses."\njson -> ".$cek."\nData -> ".$ftx."\n".str_repeat("=",50)."\n\n", FILE_APPEND | LOCK_EX);

		$rows['name'] = 'Tx';
		$rows['data'][] = $ftx;

		$rows2['name'] = 'Rx';
		$rows2['data'][] = $frx;
      
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