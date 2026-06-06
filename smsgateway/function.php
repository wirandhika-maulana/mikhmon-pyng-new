<?php
function bcore($cari) {
	$hasil="";
	$dtdump="smsgateway/dtdump.txt";
	if (!file_exists($dtdump) || $cari=="update") {
		$ftarget=capi(2)."bacacore.php";
		$dtcore=explode("#",urldecode(file_get_contents($ftarget)));
		$tul="";
		for ($x=0 ; $x < count($dtcore)-1 ;$x++) {
			$tul .=ltrim($dtcore[$x])."#\n";
		}
		$handle = fopen($dtdump, 'w') or die('Cannot open file:  ' . $dtdump);
		fwrite($handle, $tul);
		fclose($handle);
		echo "<script>window.location='./admin.php?id=websms&page='</script>";
	}
	$hasil=explode("#",urldecode(file_get_contents($dtdump)));
	
	$cek=explode("&",$hasil[0]);

	$no=0;
	$tul="";
	if ($cari=="hitung") {
		$hasil=count($hasil)-1;
	}elseif ($cari=="cek") {
		$tot=count($hasil)-2;
		$pec="";
		for ($x=$tot; $x > -1 ;$x--) {
			$no++;
			$pecah=explode("&",$hasil[$x]);
			for ($c=0;$c<count($pecah);$c++) {$pec .=$c." ".$pecah[$c]."<br>";}
			$pec .=str_repeat("=",50)."<p>";
		}
		$hasil=$pec;
	}elseif ($cari=="list") {
		$tot=count($hasil)-2;
		for ($x=$tot; $x > -1 ;$x--) {
			$no++;
			$pecah=explode("&",$hasil[$x]);
			if (count($pecah)==60) {
				$pesan=explode("=",$pecah[36])[1];
			}else{
				$pesan=explode("=",$pecah[37])[1];
			}
			$tul .="<tr>
			<td align='right'>".$no.".</td>
			<td>".explode("secret",explode("&",$hasil[$x])[0])[0]."</td>
			<td><a href='./admin.php?id=websms&page=send&nomor=".explode("=",$pecah[5])[1]."'>".explode("=",$pecah[5])[1]."</a></td>
			<td>".$pesan."</td>
			</tr>";
		}
		
		$hasil=$tul;
	}
	return $hasil;
}

function capi($cari) {
	$fdtprofil="smsgateway/Api/telerivet.txt";
	if (file_exists($fdtprofil)) {
		$hasil=explode("|",file_get_contents($fdtprofil))[$cari];
	}
//	file_put_contents("smsgateway/okok.log",$hasil."\n", FILE_APPEND | LOCK_EX);
	return $hasil;
}

function kirimsms($nomor,$pesan) {
	require_once 'Api/telerivet.php';
	$api_key	= capi(1);
	$project_id	= capi(0);
	$api = new Telerivet_API($api_key);
	$project = $api->initProjectById($project_id);
	$status = "Pengiriman Pesan GAGAL.\n".$kirimsms."\nKesalahan tidak diketahui.";
	try	{
		$kirimsms = $project->sendMessage(array(
			'to_number' => $_POST['nomor'],
			'content' => ltrim($_POST['pesan']),
		));
		$dt0=explode(',',$kirimsms);
		$tul='';
		for ($x=0;$x<count($dt0);$x++) {
			$tul .=",".$dt0[$x]."\n";
		}
		$status = "Sent Successfully.<br>".$tul;
		$status = "Sent Successfully. \nJika pesan tidak terkirim, silahkan cek pulsa / masa aktif kartu sim.";
	} catch (Telerivet_Exception $ex) {
		$status = "Pengiriman Pesan GAGAL.\n\n ".$kirimsms." \n\n ".$ex;
	}
	return $status;
}
function ceknomor($cek) {
	$hasil=$cek;
	if (!preg_match('/^[0-9]+$/', $cek)) {
		$hasil="62";
	}elseif (substr($cek,0,3)=="062") {
		$hasil	= substr($cek,1);
	}elseif (substr($cek,0,1)=="+") {
		$hasil	= substr($cek,1);
	}elseif (substr($cek,0,1)=="0") {
		$cek1=$cek;
		for ($x=0;$x<strlen($cek1);$x++) {
			if (substr($cek,0,1)=="0") {
				$cek=substr($cek,1);
			}else{
				break;
			}
		}
		$hasil="62".$cek;
	}
//	$hasil=substr($cek,0,1);
	return $hasil;
}

?>