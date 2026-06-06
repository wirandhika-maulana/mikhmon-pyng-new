<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo "<center style='margin-top:50px;font-size:24px;'>MimoAssist.homes<hr style='width:300px;'>file system webhook wa</center>";

    exit(0);

}



error_reporting(E_ALL);

ini_set('display_errors', 1);

ini_set('error_log', 'error.log');

header('content-type: application/json; charset=utf-8');



date_default_timezone_set('Asia/Jakarta');

header('HTTP/1.1 200 ');



$body = file_get_contents('php://input');



$data = json_decode($body, true);



if (strpos($body,'@')!== false) {

	$nomor = explode('@',$data['from'])[0];

	$pesan = $data['message'];

	file_put_contents('kirim.txt', "1-apiwa");

	file_put_contents('notifwa.log', date('d/M/Y H:i:s')."|-|recive|-|1|-|".$body." #\n", FILE_APPEND | LOCK_EX);

}elseif (strpos($body,'webhook_type')!== false) {

	$nomor = $data['payload']['sender'];

	$pesan = $data['payload']['text'];

	file_put_contents('kirim.txt', "2-kirimwa.id");

	file_put_contents('notifwa.log', date('d/M/Y H:i:s')."|-|recive|-|2|-|".$body." #\n", FILE_APPEND | LOCK_EX);

	if ($data['payload']['from_me']==true) {

		exit(0);

	}

}else{

	$nomor = $data['from'];

	$pesan = $data['message'];

	file_put_contents('kirim.txt', "3-mpedia");

	file_put_contents('notifwa.log', date('d/M/Y H:i:s')."|-|recive|-|3|-|".$body." #\n", FILE_APPEND | LOCK_EX);

}



include "function.php";



if (cek($nomor)=='0') {



}else{



$nowa	= explode("|-|",file_get_contents("setup.set"))[0];



$kirim="";

if (strtolower(explode(' ',$pesan)[0])=='test' ) {

	$kirim	= test();

//<<Test koneksi.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='ppp' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= ppp($nomor,strtolower($pesan));

	}

//<<Monitoring ppp.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='dns' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= dns();

	}

//<<Cek dns.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='dhcp' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= dhcp();

	}

//<<Cek dhcp.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='help' ) {

	$kirim	= help($nomor);

//<<Daftar perintah.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='start' ) {

	$kirim	= start($nomor);

//<<Daftar perintah start.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='tools' ) {

	$kirim	= tools($nomor);

//<<Daftar perintah tools.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='pool' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= pool($nomor);

	}

//<<Cek pool.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='ping' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= ping(strtolower(explode(' ',$pesan)[1]));

	}

//<<Test ping.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='beli' ) {

	$kirim	= beli(explode(' ',$pesan)[1],explode(' ',$pesan)[2],$nomor);

//<<Buat/Jual voucher.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='info' ) {

	$kirim	= info($nomor);

//<<Info Saldo.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='paket' ) {

	$kirim	= paket();

//<<Daftar harga.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='reseller' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= reseller();

	}

//<<Daftar user.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='reg' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= register($nomor,explode(' ',explode('#',$pesan)[0])[1],explode('#',$pesan)[1],explode('#',$pesan)[2]);

	}

//<<Registrasi user baru.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='unreg' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= unregister($nomor,explode(' ',explode('#',$pesan)[0])[1],explode('#',$pesan)[1],explode('#',$pesan)[2]);

	}

//<<Unregistrasi user baru.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='topup' ) {

	if ($nomor==$nowa) {

		$kirim 	= topup1($nomor,explode(' ',$pesan)[1],explode(' ',$pesan)[2]);

	}else{

		$kirim	= topup($nomor,explode(' ',$pesan)[1]);

	}

//<<Top Up Saldo.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='server' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= server($nomor);

	}

//<<List server.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='traffic' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= traffic($nomor);

	}

//<<Test traffic.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='address' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= address($nomor);

	}

//<<Cek address.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='hotspot' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= hotspot($nomor,strtolower($pesan));

	}

//<<Monitoring hotspot.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='interface' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= interfac($nomor);

	}

//<<Monitoring interface.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='neighbor' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= neighbor($nomor);

	}

//<<Cek neighbor.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='netwatch' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= netwatch($nomor);

	}

//<<Cek netwatch.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='resource' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= resource($nomor);

	}

//<<Cek resource.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='ipbinding' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= ipbinding($nomor);

	}

//<<User ipbinding.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='simplequee' ) {

	if ($nomor<>$nowa) {

		$kirim=sapaan()."\n\nAnda tidak punya akses ,..\n";

	}else{

		$kirim	= simplequee($nomor);

	}

//<<List simplequee.>>

}elseif (strtolower(explode(' ',$pesan)[0])=='reset' ) {

	$kirim	= normal(explode(' ',$pesan)[1]);

//<<Normalisasi voucher.>>

}else{

	if (substr(ltrim($pesan),0,1)=='') {

		$kirim=sapaan().".\n\nPerintah *".explode(' ',$pesan)[0]."* Belum terdaftar.\n\n*start* - List comm \n";

	}else{

		$kirim="";

	}

}



if ($kirim<>"") {

	$tele='0';

	if ($tele=='1') {

		ktele(str_replace('*','',$kirim));

	}else{

		$hasil	=kirim($nomor,$kirim,0);

		if (strpos(strtolower($hasil),'false')== true) {

			file_put_contents('error1.log', date('d/M/Y H:i:s')."|-|error|-|".$hasil." #\n", FILE_APPEND | LOCK_EX);

			$json=json_decode($hasil,true);

			kirim($nomor,"Error API report :\n".$json['data']['message'][0]." \n",0);

		}elseif ($hasil=="") {

			file_put_contents('error1.log', date('d/M/Y H:i:s')."|-|error|-|terjadi kesalahan pada function kirim #\n", FILE_APPEND | LOCK_EX);

		}else{

			if (explode('-',file_get_contents('kirim.txt'))[0]=='2') {

				file_put_contents('notifwa.log', date('d/M/Y H:i:s')."|-|report|-|2|-|".$hasil." #\n", FILE_APPEND | LOCK_EX);

			}else{

				file_put_contents('notifwa.log', date('d/M/Y H:i:s')."|-|report|-|1|-|".$hasil."|-|".$nomor."|-|".str_replace("\n","<br>",$kirim)." #\n", FILE_APPEND | LOCK_EX);

			}

		}

	}

}

}

?>