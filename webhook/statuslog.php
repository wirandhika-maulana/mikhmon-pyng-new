<?php
$flog="corewa.log";
$hasil	="<table id='dataTable' class='table table-bordered table-hover text-nowrap'>";
if (!file_exists($flog)) {
	$hasil	.= "<caption>File ".$flog." Belum Tersedia.</caption>";
}else{
	$misi= explode("#",file_get_contents($flog));
	$no=count($misi)-1;
	$hasil	.="<th>No.</th><th>Status</th><th>ID Device</th><th>Nomor</th></th><th>Pesan</th>";
	for ($a=count($misi)-2;$a>-1;$a--) {
		$json = json_decode("[".$misi[$a]."]", TRUE);
		if ($json[0]['webhook_type']=="device_status_changed") {
//			$hasil	.= "<tr><td align='right'>".$no.".</td><td>".$json[0]['webhook_type']."</td><td>".$json[0]['device_id']."</td><td>".$json[0]['status']."</td><td>".$json[0]['changed_at']."</td></tr>";
		}elseif ($json[0]['webhook_type']=="incoming_message") {
			$hasil	.= "<tr><td align='right'>".$no.".</td><td>".$json[0]['webhook_type']."</td><td>".$json[0]['payload']['device_id']."</td><td>".$json[0]['payload']['sender']."</td><td> ".$json[0]['payload']['text']."</td></tr>";
		}else{
			$hasil	.= "<tr><td align='right'>".$no.".</td><td>".$json[0]['webhook_type']."</td><td>".$json[0]['payload']['device_id']."</td><td>".$json[0]['payload']['phone_number']."</td><td> ".$json[0]['payload']['message']."</td></tr>";
		}
		$no--;
	}
	echo $hasil."</table>";
}
?>