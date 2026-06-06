<?php

function cekpass1($cek,$cek1) {
	$hasil="Password, tidak sama";
	if ($cek==$cek1) {
		$hasil="";
	}
	return $hasil;
}

function cekpass($cek) {
	$hasil="Karakter Password terlalu pendek, min 6 karakter.";
	if (strlen($cek)>5) {
		$hasil="";
	}
	if (strpos($cek,' ',true)) {
		$hasil="Karakter user Tidak boleh mengandung spasi.";
	}
	return $hasil;
}
function cekuser($cek) {
	$hasil="Karakter user terlalu pendek, min 6 karakter.";
	if (strlen($cek)>5) {
		$hasil="";
	}
	if (strpos($cek,' ',true)) {
		$hasil="Karakter user Tidak boleh mengandung spasi.";
	}
	return $hasil;
}

function cekidwa($cek) {
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
	return $hasil;
}

function capi($brs,$clm) {
	$fdtprofil="../tripay/api/tripay.txt";
	$hasil="-=0=-";
	if (file_exists($fdtprofil)) {
		$hasil= trim(explode("|",explode("*",file_get_contents($fdtprofil))[$brs])[$clm]);
	}
	return $hasil;
}

function listmerchand() {
$hasil="";
if (capi(13,1)=="2") {	
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
file_put_contents("okok.log",$response."\n".str_repeat('=',80)." \n\n", FILE_APPEND | LOCK_EX);
	curl_close($curl);
	if (empty($error)) {
		$hasil="";
		$data='['.$response.']';
		$dtjson=json_decode($data,true);
		if ($dtjson[0]['success']==true) {
			$no=0;
			for ($x=0 ; $x<count($dtjson) ; $x++) {
				$hasil .= "<option value=''> Manual </option>";
				for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
					$no++;
					$hasil .= "<option value='".$dtjson[$x]['data'][$y]['code']."' > ".$no.". ".$dtjson[$x]['data'][$y]['code']."</option>";
				}
			}
		}else{
			$hasil	= "<option value=''> Manual </option>";
			$hasil	.="<option value=''> Pembayaran</option>";
			$hasil	.="<option value=''> On line </option>";
			$hasil	.="<option value=''> Sedang Off.</option>";
		}
	}else{
		$hasil	= "<option value=''> Manual </option>";
		$hasil	.="<option value=''> Pembayaran</option>";
		$hasil	.="<option value=''> On line </option>";
		$hasil	.="<option value=''> Sedang Off.</option>";
	}
}else{
	$hasil	= "<option value=''> Manual </option>";
	$hasil	.="<option value=''> Pembayaran</option>";
	$hasil	.="<option value=''> On line </option>";
	$hasil	.="<option value=''> Sedang Off.</option>";
}
return $hasil;
}

function biayatrx($merchand,$nominal) {
	$hasil="";
	if (capi(13,2)=="1") {
		$apiKey = capi(7,1);
		$endPoint= capi(12,1);
	}else{
		$apiKey = capi(7,2);
		$endPoint= capi(12,2);
	}

	$payload = [
		'code' => $merchand,
		'amount' => $nominal,
	];

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
	}else{
		$hasil=$error;
	}
	return $hasil;
}

function sendtrx($pelanggan,$email,$nowa,$merchand,$produk,$namabrg,$qty,$hrg,$amount,$urlback) {
	$hasil="";
	if (capi(13,2)=="1") {
		$apiKey 		= capi(7,1);
		$endPoint		= capi(11,1);
		$privateKey		= capi(6,1);
		$merchantCode	= capi(5,1);
	}else{
		$apiKey		 	= capi(7,2);
		$endPoint		= capi(11,2);
		$privateKey		= capi(6,2);
		$merchantCode	= capi(5,2);
	}
	
	$merchantRef  = 'MIKH-'.kodeacak(8);

	$data = [
		'method'         => $merchand,
		'merchant_ref'   => $merchantRef,
		'amount'         => $amount,
		'customer_name'  => $pelanggan,
		'customer_email' => $email,
		'customer_phone' => $nowa,
		'order_items'    => [
			[
				'sku'         => $produk,
				'name'        => $namabrg,
				'price'       => $hrg,
				'quantity'    => $qty,
				'product_url' => '',
				'image_url'   => '',
			],
/*			[
				'sku'         => 'FB-07',
				'name'        => 'Nama Produk 2',
				'price'       => 500000,
				'quantity'    => 1,
				'product_url' => 'https://tokokamu.com/product/nama-produk-2',
				'image_url'   => 'https://tokokamu.com/product/nama-produk-2.jpg',
			]
*/
		],
		'return_url'   => $urlback,
		'expired_time' => (time() + (1 * 60 * 60)), // 1 jam
		'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey)
	];

	$curl = curl_init();

	if (capi(13,2)=="1") {
		curl_setopt_array($curl, [
			CURLOPT_FRESH_CONNECT  => true,
			CURLOPT_URL            => 'https://tripay.co.id/api-sandbox/transaction/create',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
			CURLOPT_FAILONERROR    => false,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query($data),
			CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
		]);
	}else{
		curl_setopt_array($curl, [
			CURLOPT_FRESH_CONNECT  => true,
			CURLOPT_URL            => 'https://tripay.co.id/api/transaction/create',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
			CURLOPT_FAILONERROR    => false,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query($data),
			CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
		]);
	}
	$response = curl_exec($curl);
	$error = curl_error($curl);

	curl_close($curl);

//	echo empty($error) ? $response : $error;
	if (empty($error)) {
		$hasil=$response;
	}else{
		$hasil=$error;
	}
	return $hasil;
}
function cektrx($ref_tri) {
	$hasil="";
	if (capi(13,2)=="1") {
		$apiKey 		= capi(7,1);
		$endPoint		= capi(8,1);
	}else{
		$apiKey		 	= capi(7,2);
		$endPoint		= capi(8,2);
	}
	
	$payload = ['reference'	=> $ref_tri];
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
	}else{
		$hasil=$error;
	}
	return $hasil;
}

function kodeacak($panjang) {   
	$karakter = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';     
	$string = '';   
	for($i = 0; $i < $panjang; $i++) {   
		$pos = rand(0, strlen($karakter)-1);   
		$string .= $karakter{$pos};   
	}
	$string=strtoupper(trim($string));
	return $string;   
}   

function ceklog($cek){
	$cek=explode(",",$cek);
	$tul="";
	for ($x=0;$x<count($cek);$x++) {
		$tul .=$cek[$x].", \n";
	}
	file_put_contents("logbayar.log",date('d/M/Y H:i:s')."\n".$tul."\n".str_repeat('=',60)."\n\n", FILE_APPEND | LOCK_EX);
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

function knotiftele($pesan) {
	$fdttele="../webhook/webhookk.php";
	if (file_exists($fdttele)) {
		$dttele=explode("|",file_get_contents($fdttele));
		$idtele	= $dttele[0];
		$token	= $dttele[3];
		$kirim	="Mikhmon Registrasi On-Line.\n\n".$pesan."\n\nTerimakasih\n".date('d-M-Y H:i:s')."\n.";

		$option = [	'text' => $kirim,'chat_id' => $idtele,'parse_mode' => 'html',];
		$respone=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
		return $respone;
	} 
}

function knotifwa($notuj,$pesan) {
	$fdtwa="../webhook/wahookk.php";
	if (file_exists($fdtwa)) {
		$dtwa=explode("|",file_get_contents($fdtwa));
		$urlk	= $dtwa[6];
		$apiwa	= $dtwa[5];
		$nobot	= $dtwa[1];
		if (strlen($notuj)<8) {
			$tujuan	= capi(1,1);
		}else{
			$tujuan	= cekidwa($notuj);
		}
		
		$kirim	="Mikhmon Registrasi On-Line.\n\n".$pesan."\n\nTerimakasih\n".date('d-M-Y H:i:s')."\n.";

		$fhost=$urlk."send-message?api_key=$apiwa&sender=$nobot&number=".$tujuan."&message=".urlencode($kirim);
		$data=file_get_contents($fhost);
		return $data;
	}
}
function logom() {
	$dtdump="../tripay/dtdump1.txt";
	if (file_exists($dtdump))	{
		$data='['.file_get_contents($dtdump).']';
		$dtjson=json_decode($data,true);
		$no=0;
		$hasil="";
		for ($x=0 ; $x<count($dtjson) ; $x++) {
			for ($y=0 ; $y<count($dtjson[$x]['data']) ; $y++) {
				if ($dtjson[$x]['data'][$y]['active']==true) {$status="Active";}else{$status="Error";}
				$no++;
				$hasil .="<img style='width:150px;height:75px;background-color:white;border-radius:5px;' src=".$dtjson[$x]['data'][$y]['icon_url'].">  ";
			}
		}
		$cek="<center>".$hasil."</center>";
		$hasil=$cek;
	}else{
		$hasil="File..salah tempat.";
	}
	return $hasil;
}	
?>