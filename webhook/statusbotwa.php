<?php
require_once 'system.database.php';
$file = 'wahookk.php';
if (file_exists($file)) {
	$misi		= file_get_contents($file);
	$misi0		= explode("|",$misi);
	$phoneid	= $misi0[0];
	$nowa		= $misi0[1];
	$apikey		= $misi0[2];
	$urlcore1	= $misi0[3];
	$email		= $misi0[4];
}

try {
	$reqParams = [
	'token' => $apikey,
	'url' => 'https://api.kirimwa.id/v1/webhooks'
	];
	$response = apiKirimWaRequest($reqParams);
	$data=$response['body'];
	} catch (Exception $e) {
		$data=$e;
	}
	$cekk="Tidak Aktif";
	if (strpos(strtolower($data), 'bad') !== false){$cekk="Un Stable";}
	if (strpos(strtolower($data), 'active') !== false){$cekk="Aktif";}
	echo  "<b style='color:yellow;'>".$cekk."</b>";
?>