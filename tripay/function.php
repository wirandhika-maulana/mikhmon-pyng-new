<?php
date_default_timezone_set('Asia/Jakarta');
function capi($brs,$clm) {
	$fdtprofil="tripay/api/tripay.txt";
	$hasil="-=0=-";
	if (file_exists($fdtprofil)) {
		$hasil= explode("|",explode("*",file_get_contents($fdtprofil))[$brs])[$clm];
	}
	return $hasil;
}

function intruksi($merchand) {
	if (capi(13,2)=="1") {
		$apiKey 	= capi(7,1);
		$endPoint	= capi(9,1);
	}else{
		$apiKey 	= capi(7,2);
		$endPoint	= capi(9,2);
	}
	$payload = ['code' => $merchand];
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_FRESH_CONNECT  => true,
		CURLOPT_URL            => $endPoint.'?'.http_build_query($payload),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER         => false,
		CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
		CURLOPT_FAILONERROR    => false,
		CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
	]);

	$response = curl_exec($curl);
	$error = curl_error($curl);

	curl_close($curl);

	if (empty($error)) {
		$hasil=$response;
		$dtjson=json_decode($response,true);
		$hasil	= "<tr><td>".$dtjson['message']."</td></tr>";
		$hasil	= "<tr><td>&nbsp</td></tr>";
		$tul="";
		for ($x=0;$x<count($dtjson['data']);$x++) {
			$hasil	.="<tr><td style='font-size:24px;font-family:times;color:white;'><u>".$dtjson['data'][$x]['title']."</u></td></tr>";
			$tul="INFO : ".$dtjson['data'][$x]['title']."\n\n";
			for ($y=0;$y<count($dtjson['data'][$x]['steps']);$y++) {
				$hasil	.="<tr><td style='font-size:18px;font-family:times;'>".$dtjson['data'][$x]['steps'][$y]."</td></tr>";
				$tul .=$dtjson['data'][$x]['steps'][$y]."\n";
			}
		}
		$hasil	.="<tr><td>&nbsp</td></tr>";
	}else{
		$hasil=$error;
		$hasil="
		<tr><td>Terjadi kesalahan.</td></tr>
		<tr><td>&nbsp</td></tr>
		<tr><td>".$error."</td></tr>
		";
	}
	return $hasil;

}

function bcore($cari) {
	$hasil="";
	$dtdump="tripay/dtdump.txt";
	if (file_exists($dtdump)) {
		$cek=file_get_contents($dtdump);
		if (strlen($cek)==0) {
			unlink ($dtdump);
		}
	}
	if ($cari=="update") {
		unlink ($dtdump);
	}
	
	if (file_exists($dtdump)==false || $cari=="update" ) {
		
		$dtcore=explode("#",file_get_contents(str_replace("tripay.php","bacacore.php",capi(4,1))));
		$tul="";
		for ($x=0 ; $x < count($dtcore)-1 ;$x++) {
			file_put_contents($dtdump, ltrim($dtcore[$x])."# \n", FILE_APPEND | LOCK_EX);
		}


		$payload = [
			'page' 		=> 1,
			'sort'		=> 'desc',
			'per_page'	=> 50,
		];
		
		
		if (capi(13,2)=="1") {
			$apiKey 	= capi(7,2);
			$endPoint	= 'https://tripay.co.id/api-sandbox/merchant/transactions?';
		}else{
			$apiKey 	= capi(7,1);
			$endPoint	= 'https://tripay.co.id/api/merchant/transactions?';
		}


		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_FRESH_CONNECT  => true,
			CURLOPT_URL            => 'https://tripay.co.id/api/merchant/transactions?'.http_build_query($payload),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
			CURLOPT_FAILONERROR    => false,
			CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
		]);

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);
		if (empty($error)) {
			$tul	="";
			$data	=json_decode($response,true);
			for ($x=0;$x<count($data['data']);$x++) {
				$tul .=date('Y-m-d H:i:s',$data['data'][$x]['created_at']);
				$tul .= "|[".json_encode($data['data'][$x],true)."]# \n";
			}
//			die("die");
//			$handle = fopen($dtdump, 'w') or die('Cannot open file:  ' . $dtdump);
//			fwrite($handle, $tul);
//			fclose($handle);
		}else{
			file_put_contents($dtdump,$error."\n\n", FILE_APPEND | LOCK_EX);
		}
		echo "<script>window.location='./admin.php?id=payment&page=history-status-all'</script>";
	}

	$dtdump="tripay/dtdump.txt";
//	$dtdump="tripay/data/dtdump.txt";

	$dtcore=explode("#",file_get_contents($dtdump));
	if (explode("-",$cari)[0]=="list") {
		if (explode("-",$cari)[1]=="status") {
			$noall=$noexp=$nodev=$nopaid=0;
			$listpaid=$listexp=$listdev=$listall="";
			for ($x=count($dtcore)-2 ; $x >= 0 ;$x--) {
				$parshing 	= $dtcore[$x];
				$tanggal	= explode("|",$parshing)[0];
				$dtjson		= json_decode(explode("|",$parshing)[1],true);
				$noall++;
				$listall	.="<tr>
				<td align='right'>".$noall.".</td>
				<td>".$tanggal."</td>
				<td align='right'>".$dtjson[0]['reference']."</td>
				<td>".$dtjson[0]['merchant_ref']."</td>
				<td>".$dtjson[0]['payment_method']."</td>
				<td>".$dtjson[0]['payment_method_code']."</td>
				<td align='right'>".rupiah($dtjson[0]['total_amount'])."</td>
				<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
				<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
				<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
				<td align='left'>".$dtjson[0]['status']."</td>
				</tr>";
				if (substr(ltrim(strtolower(($dtjson[0]['reference']))),0,4)<>"dev-") {
					if (strtolower(ltrim($dtjson[0]['status']))=="paid") {
						$nopaid++;
						$listpaid .="<tr>
						<td align='right'>".$nopaid.".</td>
						<td>".$tanggal."</td>
						<td align='right'>".$dtjson[0]['reference']."</td>
						<td>".$dtjson[0]['merchant_ref']."</td>
						<td>".$dtjson[0]['payment_name']."</td>
						<td>".$dtjson[0]['payment_method']."</td>
						<td align='right'>".rupiah($dtjson[0]['amount'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
						<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
						<td align='left'>".$dtjson[0]['status']."</td>
						</tr>";
					}
					if (strtolower(ltrim($dtjson[0]['status']))=="expired") {
						$noexp++;
						$listexp .="<tr>
						<td align='right'>".$noexp.".</td>
						<td>".$tanggal."</td>
						<td align='right'>".$dtjson[0]['reference']."</td>
						<td>".$dtjson[0]['merchant_ref']."</td>
						<td>".$dtjson[0]['payment_name']."</td>
						<td>".$dtjson[0]['payment_method']."</td>
						<td align='right'>".rupiah($dtjson[0]['amount'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
						<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
						<td align='left'>".$dtjson[0]['status']."</td>
						</tr>";
					}
				}else{
					$nodev++;
					$listdev	.="<tr>
					<td align='right'>".$nodev.".</td>
					<td>".$tanggal."</td>
					<td align='right'>".$dtjson[0]['reference']."</td>
					<td>".$dtjson[0]['merchant_ref']."</td>
					<td>".$dtjson[0]['payment_name']."</td>
					<td>".$dtjson[0]['payment_method']."</td>
					<td align='right'>".rupiah($dtjson[0]['amount'])."</td>
					<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
					<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
					<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
					<td align='left'>".$dtjson[0]['status']."</td>
					</tr>";
				}
			}
			if (explode("-",$cari)[2]=="paid") {
				$hasil=$nopaid."#".$listpaid;
			}elseif (explode("-",$cari)[2]=="okk") {
				$hasil=$noexp."#".$listexp;
			}elseif (explode("-",$cari)[2]=="all") {
				$hasil=$noall."#".$listall;
			}elseif (explode("-",$cari)[2]=="dev") {
				$hasil=$nodev."#".$listdev;
			}
		}elseif (explode("-",$cari)[1]=="merchand") {
			$hasil1="";
			$merchand=ltrim(explode("-",$cari)[2]);
			for ($x=count($dtcore)-2 ; $x >= 0 ;$x--) {
				$parshing 	= $dtcore[$x];
				$tanggal	= explode("|",$parshing)[0];
				$dtjson		= json_decode(explode("|",$parshing)[1],true);
				if ($merchand=="all") {
					$no++;
					$hasil1	.="<tr>
					<td align='right'>".$no.".</td>
					<td>".$tanggal."</td>
					<td align='right'>".$dtjson[0]['reference']."</td>
					<td>".$dtjson[0]['merchant_ref']."</td>
					<td>".$dtjson[0]['payment_method_code']."</td>
					<td>".$dtjson[0]['payment_method']."</td>
					<td align='right'>".rupiah($dtjson[0]['total_amount'])."</td>
					<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
					<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
					<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
					<td align='left'>".$dtjson[0]['status']."</td>
					</tr>";
				}else{
					if ($dtjson[0]['payment_method_code']==$merchand) {
						$no++;
						$hasil1	.="<tr>
						<td align='right'>".$no.".</td>
						<td>".$tanggal."</td>
						<td align='right'>".$dtjson[0]['reference']."</td>
						<td>".$dtjson[0]['merchant_ref']."</td>
						<td>".$dtjson[0]['payment_method_code']."</td>
						<td>".$dtjson[0]['payment_method']."</td>
						<td align='right'>".rupiah($dtjson[0]['total_amount'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_merchant'])."</td>
						<td align='right'>".rupiah($dtjson[0]['fee_customer'])."</td>
						<td align='right'>".rupiah($dtjson[0]['amount_received'])."</td>
						<td align='left'>".$dtjson[0]['status']."</td>
						</tr>";
					}
				}
			}
			$hasil =$no."#".$hasil1;
		}	
	}elseif ($cari=="hitung") {
		$hasil=count($dtcore)-1;
	}elseif ($cari=="detail") {
		$tot=$paid=$expired=$dump=0;
		for ($x=0 ; $x < count($dtcore)-1 ;$x++) {
			$tot++;
			$parshing 	= $dtcore[$x];
			$tanggal	= explode("|",$parshing)[0];
			$dtjson		= json_decode(explode("|",$parshing)[1],true);
			if (substr(ltrim(strtolower(($dtjson[0]['reference']))),0,4)<>"dev-") {
				if (strtolower(ltrim($dtjson[0]['status']))=="paid") {
					$paid++;
				}else{
					$expired++;
				}
			}else{
				$dump++;
			}
		}
		$hasil=$paid." Trx.|".$expired." Trx.|".$dump." Trx.|".$tot." Trx.|";
	}
	return $hasil;
}

function listmerchand($ok) {
	$hasil="";
	$dtdump="tripay/dtdump1.txt";
	if (!file_exists($dtdump) || $ok=="update") {
		if (capi(13,2)=="1") {
			$apiKey = capi(7,1);
			$endPoint= capi(11,1);
		}else{
			$apiKey = capi(7,2);
			$endPoint= capi(11,2);
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_FRESH_CONNECT  => true,
			CURLOPT_URL            => $endPoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
			CURLOPT_FAILONERROR    => false,
			CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
		));

		$response = curl_exec($curl);
		$error = curl_error($curl);

		curl_close($curl);

		if (empty($error)) {
			
			$handle = fopen($dtdump, 'w') or die('Cannot open file:  ' . $dtdump);
			fwrite($handle, $response);
			fclose($handle);
			$hasil	="<tr><td align='center'>File berhasil di update.</td></tr>";
			$hasil .="<tr><td>".$response."</td></tr>";
		}else{
			$hasil="<tr><td align='center'>".$error."</td></tr>";
		}
		echo "<script>window.location='./admin.php?id=payment&page=merchand'</script>";
	}
	if ($ok=="hitung") {
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$hasil=count($dtjson[0]['data']);
		return $hasil;
	}elseif ($ok=="asli") {
		$hasil=file_get_contents($dtdump);
		return $hasil;
	}elseif ($ok=="logo") {
		$hasil="";
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$no=0;
		for ($x=0 ; $x<count($dtjson) ; $x++) {
			for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
				if ($dtjson[$x]['data'][$y]['active']==true) {$status="Active";}else{$status="Error";}
				$no++;
				$hasil .="<a href='?id=payment&page=".$dtjson[$x]['data'][$y]['code']."&logo=".$dtjson[$x]['data'][$y]['icon_url']."' style='cursor:pointer;margin-right:10px;'><img style='width:200px;height:100px;background-color:white;border-radius:5px;' src=".$dtjson[$x]['data'][$y]['icon_url']."></a>";
			}
		}
		$cek="<center>".$hasil."</center>";
		$hasil=$cek;
		return $hasil;
	}elseif ($ok=="select") {
		$hasil="";
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$no=0;
		for ($x=0 ; $x<count($dtjson) ; $x++) {
			$hasil .= "<option value='./admin.php?id=payment&page=history-merchand-all'> All Merchand </option>";
			for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
				$no++;
				$fee=$dtjson[$x]['data'][$y]['total_fee']['percent'];
				$hasil .= "<option value='./admin.php?id=payment&page=history-merchand-".$dtjson[$x]['data'][$y]['code']."'> ".$no.". ".$dtjson[$x]['data'][$y]['code']."</option>";
			}
		}
	}elseif ($ok=="selecti") {
		$hasil="";
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$no=0;
		for ($x=0 ; $x<count($dtjson) ; $x++) {
			for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
				$no++;
				$fee=$dtjson[$x]['data'][$y]['total_fee']['percent'];
				$hasil .= "<option value='./admin.php?id=payment&page=".$dtjson[$x]['data'][$y]['code']."&logo=".$dtjson[$x]['data'][$y]['icon_url']."'> ".$no.". ".$dtjson[$x]['data'][$y]['code']."</option>";
			}
		}
	}elseif ($ok=="list") {
		$hasil="";
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$no=0;
		for ($x=0 ; $x<count($dtjson) ; $x++) {
			for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
				if ($dtjson[$x]['data'][$y]['active']==true) {$status="Active";}else{$status="Error";}
				$no++;
				$fee=$dtjson[$x]['data'][$y]['total_fee']['percent'];
				$hasil .= "<tr>
				<td align='right'>".$no.".</td>
				<td>".$status."</td>
				<td>".$dtjson[$x]['data'][$y]['group']."</td>
				<td>".$dtjson[$x]['data'][$y]['code']."</td>
				<td>".$dtjson[$x]['data'][$y]['name']."</td>
				<td>".$dtjson[$x]['data'][$y]['type']."</td>
				<td align='right'>".rupiah($dtjson[$x]['data'][$y]['total_fee']['flat'])."</td>
				<td align='right'>".$fee." %</td>
				</tr>";
			}
		}
		$parshing=explode('","',$data);
	}
	return $hasil;
}

function rupiah($angka) {
	$hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}
function penyebut($nilai) {
	$nilai = abs($nilai);
	$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " ". $huruf[$nilai];
	} else if ($nilai <20) {
		$temp = penyebut($nilai - 10). " belas";
	} else if ($nilai < 100) {
		$temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " seratus" . penyebut($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " seribu" . penyebut($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
	}     
	return $temp;
}

function terbilang($nilai) {
	if($nilai<0) {
		$hasil = " *minus* ". trim(penyebut($nilai));
	} else {
		$hasil = trim(penyebut($nilai));
	}     		
	return $hasil;
}

function editapi($edit) {
	$fapi="tripay/api/tripay.txt";
	
	if (!file_exists($fapi)) {
		$hasil="File ".$fapi." tidak ditemukan.";
		return $hasil;
	}
	$dtap=file_get_contents($fapi);
	$dtapi=explode("*",$dtap);
	$tulis	="";
	for ($x=0 ; $x < count($dtapi)-1; $x++) {
		if ($edit=="1") {
			if ($x==13) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|1|".$dtedit[2])."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="2") {
			if ($x==13) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|2|".$dtedit[2])."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="3") {
			if ($x==13) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|".$dtedit[1]."|2")."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="4") {
			if ($x==13) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|".$dtedit[1]."|1")."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="5") {
			if ($x==14) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|2|")."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="6") {
			if ($x==14) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|3|")."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}elseif ($edit=="7") {
			if ($x==14) {
				$dtedit=explode("|",$dtapi[$x]);
				$tulis	.= ltrim($dtedit[0]."|1|")."*\n";
			}else{
				$tulis	.= ltrim($dtapi[$x])."*\n";
			}
		}
	}
	$handle = fopen($fapi, 'w') or die('Cannot open file:  ' . $fapi);
	fwrite($handle, $tulis);
	fclose($handle);
	echo "<script>window.location='./admin.php?id=payment'</script>";
}
function ktele($pesan) {
	$token	= '6014190093:AAFTDaO2_1XH_pfoyQ3cDNXWmUPjidnbvBs';
	$pesan	= $pesan."\n".date('d-M-Y H:i:s')."\n.";
	$option = [	'text' => $pesan,'chat_id' => '1341792914','parse_mode' => 'html',];
	$response=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
	return $response;
}
?>