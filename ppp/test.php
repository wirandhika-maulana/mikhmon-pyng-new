<?php
	include_once('../lib/routeros_api.class.php');
	include_once('../lib/formatbytesbites.php');
	$API = new RouterosAPI();
	$API->debug = false;


	if($API->connect( "10.5.50.1:2828", "admin", "admin98")){
//		$data = $API->comm("/interface/monitor-traffic/print", ["interface" => "$interface","once" => "",]);
//		$data = $API->comm("/interface/list-all/print");
//		$data = $API->comm("/ppp/interface/print",);
//		$data = $API->comm("/interface/monitor-traffic/print", ["interface" => "ether2","once" => "",]);
//		<pppoe-kuya>
//		$data = $API->comm("/ppp/print",);

//		$data = $API->comm("/interface",["?name" => "<pppoe-kuya>",]);
//		$data = $API->comm("/queue/simple/print");
		$mfile="test.log";
//		$data = $API->comm("/system/schedule/print");
		$interface="";

		$data = $API->comm("/interface/monitor-traffic", array(
			"interface" => "<pppoe-kuya>",
			"once" => "",
		));
		$dt1=$data[0]['rx-bits-per-second'];
		$dt2=$data[0]['tx-bits-per-second'];
//		$data = $API->comm("/interface/print");
		echo $dt1." / ".$dt2."<hr>";
//		$ftx = $getinterfacetraffic[0]['tx-bits-per-second'];
//		$frx = $getinterfacetraffic[0]['rx-bits-per-second'];

		$cek=json_encode($data);
		$tanda='{".id';

		$data=explode("id",$cek);
		$tul="";
		for ($x=0;$x<count($data);$x++) {
			$tul .='{.id'.$data[$x]."<hr>";
		}
		file_put_contents($mfile,$tul."\n".str_repeat("=",100)."\n\n", FILE_APPEND | LOCK_EX);
		echo $tul;
	}else{
		echo "Not Connected,..";
	}

?>