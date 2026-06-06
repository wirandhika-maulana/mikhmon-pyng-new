<?php
session_start();
//error_reporting(0);

echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Processing...</b>";

if ($removesecr != "") {
	$cnama	= $API->comm("/ppp/secret/print", ["?.id" => $removesecr, ]);
	
	$csche	= $cnama[0]['name'];
	$cidsc	= $API->comm("/system/schedule/print", ["?name" => $csche, ]);
	$idsch	= $cidsc[0]['.id'];

	$API->comm("/ppp/secret/remove", array(
		".id" => "$removesecr",
	));

	$API->comm("/system/scheduler/remove", array(
		".id"	=> "$idsch",
	));

	$fdtprofil="ppp/csv/dtpelanggan.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		$writecev="";
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (trim(explode("^",$dtfile[$x])[0])<>$removesecr) {
				$writecev .=ltrim($dtfile[$x])."#\n";
			}
		}
		$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $filec);
		fwrite($handle, $writecev);
		fclose($handle);
	}

	$fdtprofil="ppp/csv/dthistory.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		$writecev="";
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (trim(explode("^",$dtfile[$x])[0])<>$removesecr) {
				$writecev .=ltrim($dtfile[$x])."#\n";
			}
		}
		$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $filec);
		fwrite($handle, $writecev);
		fclose($handle);
	}

	echo "<script>window.location='./?ppp=secrets&session=".$session."'</script>";
}elseif ($enablesecr !="") {
	$API->comm("/ppp/secret/set", array(
		".id" => "$enablesecr",
		"disabled" => "false",
	));

	$cnama	= $API->comm("/ppp/secret/print", ["?.id" => $enablesecr, ]);
	$csche	= $cnama[0]['name'];
	$cidsc	= $API->comm("/system/schedule/print", ["?name" => $csche, ]);
	$idsch	= $cidsc[0]['.id'];

	$API->comm("/system/scheduler/set", array(
		".id"	=> "$idsch",
		"disabled" => "false",
	));
	echo "<script>window.location='./?ppp=secrets&session=".$session."'</script>";
}elseif ($disablesecr !="") {
	$API->comm("/ppp/secret/set", array(
		".id" => "$disablesecr",
		"disabled" => "true",
	));
	
	$cnama	= $API->comm("/ppp/secret/print", ["?.id" => $disablesecr, ]);
	$csche	= $cnama[0]['name'];
	$cidsc	= $API->comm("/system/schedule/print", ["?name" => $csche, ]);
	$idsch	= $cidsc[0]['.id'];

	$API->comm("/system/scheduler/set", array(
		".id"	=> "$idsch",
		"disabled" => "true",
	));
	echo "<script>window.location='./?ppp=secrets&session=".$session."'</script>";

}elseif ($idbayar !="") {
	$cnama	= $API->comm("/ppp/secret/print", ["?.id" => $idbayar, ]);
	$csche	= $cnama[0]['name'];
	$cidsc	= $API->comm("/system/schedule/print", ["?name" => $csche, ]);
	
	$idsch	= $cidsc[0]['.id'];
	$mtgl	= explode(" ",$cidsc[0]['next-run'])[0];
	$mjam	= explode(" ",$cidsc[0]['next-run'])[1];
	
/*	$cek=$API->comm("/system/scheduler/set", array(
		".id"			=> $idsch,
		"start-date" 	=> $mtgl,
		"start-time" 	=> $mjam,
	));
*/
	$tulis = "Mentah ".$cidsc[0]['next-run']."\n";
	$tulis .="strtotime -> ".strtotime('M/d/Y',$cidsc[0]['next-run'])."\n";
	$tulis .="strtotime -> ".strtotime($cidsc[0]['next-run'],'Y/M/d')."\n";
	$fdtprofil="process/test.txt";
	file_put_contents($fdtprofil,$tulis."\n\n", FILE_APPEND | LOCK_EX);


	include "ppp/function.php";
	$kk=test(json_encode($cek));
	$tulis=$idbayar."^".strtotime(date('Y/m/d H:i:s'))."^bayar^Tanggal ".date('d-M-Y')." Jam ".date('H:i:s')." telah melakukan pembayaran sebesar^".caridtpelanggan1($idbayar,6)."^#\n";
	
	$fdtprofil="ppp/csv/dthistory.txt";
	file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);

	$rincian = "User secret ".caridtpelanggan1($idbayar,2)."<br>";
	$rincian .="Tanggal Bayar ".date('d-M-Y',caridtpelanggan1($idbayar,1))."<br>";
	$rincian .="Sebesar ".rupiah(caridtpelanggan1($idbayar,6))."<br>";
	$rincian .=ucwords(terbilang(caridtpelanggan1($idbayar,6)))." Rupiah.<br>";
	echo "<script>window.location='./?info=Berhasil|Data Pembayaran User Secret ".$csche." Berhasil Di Simpan.|".$rincian."&session=".$session."'</script>";
}elseif ($removepprofile !="") {
	$API->comm("/ppp/profile/remove", array(
		".id" => "$removepprofile",
	));
	$fdtprofil="ppp/csv/dthrgprofile.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		$writecev="";
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (ltrim(explode("^",$dtfile[$x])[0])<>ltrim($removepprofile)) {
				$writecev .=ltrim($dtfile[$x])."#\n";
			}
		}
		$filec =$fdtprofil;
		$handle = fopen($filec, 'w') or die('Cannot open file:  ' . $filec);
		fwrite($handle, $writecev);
		fclose($handle);
	}
	echo "<script>window.location='./?ppp=profiles&session=" . $session . "'</script>";

}elseif ($onoffnotif !="") {
	file_put_contents("ppp/csv/kkkk.log",$onoffnotif."\n".str_repeat("=",75)."\n\n", FILE_APPEND | LOCK_EX);
	$notif=explode("/",$onoffnotif)[0];
	$idsecret=explode("/",$onoffnotif)[1];
	$fdtprofil="ppp/csv/dtpelanggan.txt";
	$dtfile=explode("#",file_get_contents($fdtprofil));
	$writecev="";
	$edit="0";
	for ($x=0;$x<count($dtfile)-1;$x++) {
		if (ltrim(explode("^",$dtfile[$x])[0])==$idsecret) {
			$edit="1";
			$writecev .=explode("^",$dtfile[$x])[0]."^".explode("^",$dtfile[$x])[1]."^".explode("^",$dtfile[$x])[2]."^".explode("^",$dtfile[$x])[3]."^".
						explode("^",$dtfile[$x])[4]."^".explode("^",$dtfile[$x])[5]."^".explode("^",$dtfile[$x])[6]."^".explode("^",$dtfile[$x])[7]."^".
						explode("^",$dtfile[$x])[8]."^".explode("^",$dtfile[$x])[9]."^".explode("^",$dtfile[$x])[10]."^".explode("^",$dtfile[$x])[11]."^".
						explode("^",$dtfile[$x])[12]."^".explode("^",$dtfile[$x])[13]."^".$notif."^#\n";
		}else{
			$writecev .=ltrim($dtfile[$x])."#\n";
		}
	}
	if ($edit=="1") {
		$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $fdtprofil);
		fwrite($handle, $writecev);
		fclose($handle);
	}	
	echo "<script>window.location='./?billing=billing&session=".$session."&ok=ok'</script>";
}

