<?php
//file function dalam dir notif
date_default_timezone_set('Asia/Jakarta');

include "Api/routeros_api.class.php";
include "Api/formatbytesbites.php";
include "system.config.php";

function register($nomor,$nores,$nama,$alamat){
	include "readcfg.php";
    global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);

	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$dtuser=lihatuser($nores);
	if (empty($nores) || empty($nama) || empty($alamat)) {
	    $hasil .=garis(1);
		$hasil .="Format Pendaftaran. \n*reg nomorhp#nama#alamat* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if (strlen($nama) < 5) {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Minimal 5 huruf untuk nama. \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if (substr($nores,0,3)<>"628") {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Format Nomor : [ ".$nores." ] salah.\nGunakan awalan 628 \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if (!empty($dtuser['nama_seller'])) {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Nomor : [ ".$nores." ] \nSudah terdaftar atas, \n nama : ".$dtuser['nama_seller']."\n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	global $mikbotamdata;
	$last_id = $mikbotamdata->insert('re_settings', [
		'id_user' 		=> $nores,
		'nama_seller'	=> ucwords($nama),
		'keterangan'	=> ucwords($alamat),
//		'saldo'		=> "$saldo",
		'Waktu' => date('H:i:s'),
		'Tanggal' => date('Y-m-d'),
	]);
	
	$hsl .=$hasil;
	$hsl .="Nomor : ".$nores." \n";
	$hsl .="Nama : ".$nama." \n";
	$hsl .="Alamat : ".$alamat." \n";
	$hsl .=garis(2);
	$hsl .="*Berhasil Didaftarkan* \n";
	$hsl .=garis(2);
	kirim($nores,$hsl,0);
	sleep(3);
	$hasil .="Nomor : ".$nores." \n";
	$hasil .="Nama : ".$nama." \n";
	$hasil .="Alamat : ".$alamat." \n";
	$hasil .=garis(2);
	$hasil .="*Berhasil ditambahkan* \n";
	$hasil  .=garis(1);
	$hasil	.="*Terimakasih dan* ".sapaan1();
	$hasil  .=" \n";	
	$hasil  .=garis(2);
	$hasil  .=" *Created By* : *".$dns2."*\n";
	$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
	$hasil  .=garis(2);
	return $hasil;
}

function unregister($nomor, $nores) {
    include "readcfg.php";
    global $mikbotamdata;
    $data = $mikbotamdata->get('re_settings', [
        'id_user',
        'nama_seller',
    ]);

    $hasil = "".sapaan()." *".lihatuser($nomor)['nama_seller']. "* \n\n";
    $dtuser = lihatuser($nores);
    if (empty($nores)) {
        $hasil .= garis(1);
        $hasil .= "Format Unregistration. \n*unregister nomorhp* \n";
        $hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
        return $hasil;
    }
    if (empty($dtuser['nama_seller'])) {
        $hasil .= "Nomor : ".$nores." belum terdaftar dalam system kami.\n";
        $hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
        return $hasil;
    }
    $mikbotamdata->delete('re_settings', [
        'id_user' => $nores,
    ]);

	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$hasil  .=garis(1);
    $hasil  .= "Nomor : ".$nores." \n";
    $hasil  .= "*Berhasil dihapus dari sistem.*\n";
    $hasil  .=garis(1);
	$hasil	.="*Terimakasih dan* ".sapaan1();
	$hasil  .=" \n";	
	$hasil  .=garis(2);
	$hasil  .=" *Created By* : *".$dns2."*\n";
	$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
	$hasil  .=garis(2);
   return $hasil;
}

function topup1($nomor,$nores,$nominal){
	include "readcfg.php";
    global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);

	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$hasil .=garis(1);
	$dtuser=lihatuser($nores);
	if (empty($dtuser['nama_seller'])) {
		$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
		$hasil  .=garis(1);
		$hasil  .="Nomor : ".$nores." belum terdaftar dalam system kami.\n*topup nomor_tujuan nominal*\n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if ($nominal=='') {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*Nominal, tidak boleh kosong.* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if (preg_match('/^[0-9]+$/', $nominal) == false) {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*Masukan nominal dalam bentuk angka, tanpa tanda baca.* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if ($nomor==$nores) {
	    $hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*No Reseller, tidak boleh sama dengan No Owner.* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	$hasil1	=topupresseller($nores, $dtuser['nama_seller'], $nominal, $nomor);
	kirim($nores,$hasil.$hasil1,0);
	sleep(3);
	$kirim = "*STATUS TOPUP RESELLER* \n".garis(2).$hasil1;
	return $kirim;	
}

function topupresseller($id, $name, $jumlah, $id_own) {
	if (substr($id,0,3)=='628') {
		$type="W";
	}else{
		$type="T";
	}
	global $mikbotamdata;
	$ceksaldoawal = $mikbotamdata->get('re_settings', [
		'id_user',
		'saldo',
	], [
		'id_user' => $id
	]);

	$saldoawal = $ceksaldoawal["saldo"];
	$update = $mikbotamdata->update('re_settings', [
		'saldo' => $jumlah + $saldoawal,
		'Waktu' => date('H:i:s'),
		'Tanggal' => date('Y-m-d'),
	], [
		'id_user' => $id,
	]);

	if ($update == true) {
		$datacek = $mikbotamdata->get('re_settings', [
			'id_user',
			'nama_seller',
			'saldo',
		], [
			'id_user' => $id
		]);
		$nama = $datacek["nama_seller"];
		$saldo = $datacek["saldo"];
		$hasil = $mikbotamdata->insert('re_operating', [
			'id_user' => $id,
			'nama_seller' => $nama,
			'saldo_awal' => $saldoawal,
			'saldo_akhir' => $saldo,
			'top_up' => $jumlah,
			'keterangan' => 'topup',
			'top_up_fromid' => $id_own,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
		]);

		if ($type=='T') {
//			$idowner = lihatowner();
			$text = "<code>  Informasi TOP UP saldo</code>\n";
			$text .= "<code>========================</code>\n";
			$text .= "<code>ID User  :</code> $id \n";
			$text .= "<code>Nama     :</code> $nama \n";
			$text .= "<code>Status   :</code> Berhasil \n";
			$text .= "<code>Nominal  :</code> " . rupiah($jumlah)." \n";
			$text .= "<code>S awal   :</code> " . rupiah($saldoawal)." \n";
			$text .= "<code>S akhir  :</code> " . rupiah($saldo)." \n";
			$text .= "<code>Outletid :</code> " . $id_own. " \n";
			$text .= "<code>========================</code>\n";
		}else{
//			$idowner = lihatownerwa();
			$text = "  Informasi TOP UP saldo\n";
			$text .= "========================\n";
			$text .= "ID User : $id \n";
			$text .= "Nama : $nama \n";
			$text .= "Status : Berhasil \n";
			$text .= "Nominal : " . rupiah($jumlah)." \n";
			$text .= "S awal : " . rupiah($saldoawal)." \n";
			$text .= "S akhir : " . rupiah($saldo)." \n";
			$text .= "Outletid : " . $id_own. " \n";
			$text .= "========================\n";
		
		}
	} else {
		if ($type=='T') {
			$text = "<code>Informasi TOP UP saldo</code>\n";
			$text .= "<code>========================</code>\n";
			$text .= "<code>ID User :</code> $id \n";
			$text .= "<code>Nama    :</code> $name \n";
			$text .= "<code>Status  :</code> Gagal  database error \n";
			$text .= "<code>========================</code>\n";
		}else{
			$text = "Informasi TOP UP saldo\n";
			$text .= "========================\n";
			$text .= "ID User : $id \n";
			$text .= "Nama    : $name \n";
			$text .= "Status  : Gagal  database error \n";
			$text .= "========================\n";
		}
	}
	return $text;
}

function topup($nomor,$dana){
	include "readcfg.php";
    global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);

	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$dtuser=lihatuser($nomor);
	if (empty($dtuser['nama_seller'])) {
		$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Nomor : ".$nomor." belum terdaftar dalam system kami.\n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if ($dana=='') {
		$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*Masukan nominal dalam bentuk angka, tanpa tanda baca.* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if (preg_match('/^[0-9]+$/', $dana) == false) {
		$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*Masukan nominal dalam bentuk angka, tanpa tanda baca.* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	if ($dana<20000) {
		$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="*Minimal topup 20000* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	
	$ftoken='setup.set';
	$idownerwa=explode('|-|',file_get_contents($ftoken))[0];
	$pesan = $hasil."Notifikasi Pengajuan\n";
	$pesan .="TOPUP \n\n";
	$pesan .="Nama : ".$dtuser['nama_seller']."\n";
	$pesan .="Nomor : ".$dtuser['id_user']." \n";
	$pesan .="Sebesar : ".rupiah($dana)." \n".garis(2);
	if ($idownerwa<>$nomor) {
		kirim($idownerwa,$pesan);
	}
	$pesan = $hasil."Silahkan lakukan transfer\n";
	$pesan .="ke No Rekening Owner \n\n";
	$pesan .="DANA \n 0895366809000 \n Irawan Akbar Maulana \n\n";
	$pesan .="Sebesar : ".rupiah($dana)." \n".garis(2);
	return $pesan;
}

function lihatdata() {
	global $mikbotamdata;
	$data = $mikbotamdata->select('re_settings', [
		'id_user',
		'nama_seller',
		'nomer_tlp',
		'saldo',
		'voucher_terjual',
		'jumlah_debit_terjual',
		'type',
		'status',
		'keterangan',
		'Waktu',
		'Tanggal',
	]);

	return $data;
}

function lihatuser($id) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
		'nomer_tlp',
		'saldo',
		'voucher_terjual',
		'jumlah_debit_terjual',
		'type',
		'status',
		'keterangan',
		'Waktu',
		'Tanggal',

	], 
	[
		'id_user' => $id
	]);

	return $data;
}

function simpvcr($id, $usernamepelanggan, $princevoc,$markup, $username, $password, $uptime, $keterangan) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'saldo',
		'id_user'
	], [
		'id_user' => $id

	]);

	$saldoawal = $data["saldo"];
	if (isset($data)) {
		$last_id = $mikbotamdata->insert('re_operating', [
			'id_user' => $id,
			'nama_seller' => $usernamepelanggan,
			'saldo_awal' => $saldoawal,
			'saldo_akhir' => $saldoawal - $princevoc,
			'beli_voucher' => $princevoc,
			'markup_voucher'=>$markup,
			'username_voucher' => $username,
			'password_voucher' => $password,
			'exp_voucher' => $uptime,
			'keterangan' => $keterangan,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),

		]);
	}

	$update = $mikbotamdata->update('re_settings', [
		'saldo[-]' => $princevoc,
		'Waktu' => date('H:i:s'),
		'Tanggal' => date('Y-m-d'),
		'voucher_terjual[+]' => 1,
	], 
	[
		'id_user' => $id,
	]);

	if ($keterangan == 'Success') {
		$report = $mikbotamdata->insert('st_reportdata', [
			'id_user' => $id,
			'nama_user' => $usernamepelanggan,
			'harga' => $princevoc,
			'status' => $keterangan,
			'transaksi' => 'halo',
			'pendapatan' => $princevoc,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),

		]);
	}

	return $update;
}

function cuser($nomor){
	include "readcfg.php";
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
		'nomer_tlp',
		'saldo',
		'voucher_terjual',
		'jumlah_debit_terjual',
		'type',
		'status',
		'keterangan',
		'Waktu',
		'Tanggal',
	],
	[
		'id_user' => $nomor,
	]);

	return $data;
}

function info($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
		'nomer_tlp',
		'saldo',
		'voucher_terjual',
		'jumlah_debit_terjual',
		'type',
		'status',
		'keterangan',
		'Waktu',
		'Tanggal',
		],[
		'id_user' => $nomor
	]);

	if (empty($data['nama_seller'])) {
	    $hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
		$hasil .="Nomor: ".$nomor." \n";
		$hasil .="Status : *Belum terdaftar* \n";
		$hasil .=garis(2);
	}else{
	    $hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Nama : ".$data['nama_seller']."\n";
		$hasil .="Nomor : ".$data['id_user']."\n";
		$hasil .="Alamat : ".$data['keterangan']."\n";
		$hasil .="Saldo : ".rupiah($data['saldo'])."\n";
		$hasil .="Voucher Terjual: " . $data['voucher_terjual'] . "\n";
		if ($data['saldo']<20000) {
			$hasil .=garis(1)."*Segera lakukan TopUp* \n";
		}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(1);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	}
	return $hasil;
}

function reseller(){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->select('re_settings', [
		'id_user',
		'nama_seller',
		'nomer_tlp',
		'saldo',
		'voucher_terjual',
		'jumlah_debit_terjual',
		'type',
		'status',
		'keterangan',
		'Waktu',
		'Tanggal',
	]);
	for ($x=0;$x<count($data);$x++) {
		$no=$x+1;
		$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
		$hasil .=garis(1);
		$hasil .="No : ".$no."\n";
		$hasil .="No Hp: ".$data[$x]['id_user']."\n";
		$hasil .="Nama : ".$data[$x]['nama_seller']."\n";
		$hasil .="Saldo : ".rupiah($data[$x]['saldo'])."\n".garis(1)."\n";
	}
	if (count($data)<1) {
		$hasil .="Belum ada user terdaftar dalam database utama.\n".garis(2);
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);

	return $hasil;
}

function resource($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);

	$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$jambu         = $API->comm("/system/health/print");
		$dhealth       = $jambu['0'];
		$ARRAY         = $API->comm("/system/resource/print");
		$jeruk         = $ARRAY['0'];
		$memperc       = ($jeruk['free-memory'] / $jeruk['total-memory']);
		$hddperc       = ($jeruk['free-hdd-space'] / $jeruk['total-hdd-space']);
		$mem           = ($memperc * 100);
		$hdd           = ($hddperc * 100);
		$sehat         = $dhealth['temperature'];
		$platform      = $jeruk['platform'];
		$board         = $jeruk['board-name'];
		$version       = $jeruk['version'];
		$architecture  = $jeruk['architecture-name'];
		$cpu           = $jeruk['cpu'];
		$cpuload       = $jeruk['cpu-load'];
		$uptime        = $jeruk['uptime'];
		$cpufreq       = $jeruk['cpu-frequency'];
		$cpucount      = $jeruk['cpu-count'];
		$memory        = formatBytes($jeruk['total-memory']);
		$fremem        = formatBytes($jeruk['free-memory']);
		$mempersen     = number_format($mem, 2);
		$hdd           = formatBytes($jeruk['total-hdd-space']);
		$frehdd        = formatBytes($jeruk['free-hdd-space']);
		$hddpersen     = number_format($hdd, 2);
		$sector        = $jeruk['write-sect-total'];
		$setelahreboot = $jeruk['write-sect-since-reboot'];
		$kerusakan     = $jeruk['bad-blocks'];
		$text .= "📡Resource  $sehat C \n".garis(2);
		$text .= "Boardname : $board \n";
		$text .= "Platform : $platform \n";
		$text .= "Uptime is : " . formatDTM($uptime) . "\n";
		$text .= "Cpu Load : $cpuload% \n";
		$text .= "Cpu type : $cpu \n";
		$text .= "Cpu Hz : $cpufreq Mhz / $cpucount core \n".garis(1);
		$text .= "Free memory and memory \n$memory - $fremem / $mempersen % \n".garis(1);
		$text .= "Free disk and disk \n$hdd - $frehdd / $hddpersen % \n".garis(1);
		$text .= "Since reboot, bad blocks \n".angka($sector).", ".angka($setelahreboot)." \n".angka1($kerusakan)." % \n".garis(2);
		$hasil =  $text;
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function angka($angka) {
	$hasil = number_format($angka, 0, ',', '.');
	return $hasil;
}
function angka1($angka) {
	$hasil = number_format($angka, 2, ',', '.');
	return $hasil;
}

function simplequee($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$data=$API->comm('/queue/simple/getall');
		$hasil .="Daftar Simple queue \n".garis(2); 
		for ($x=0;$x<count($data);$x++) {
			$ex=explode('/',$data[$x]['max-limit']);
			$explodeque=explode('/',$data[$x]['rate']);
			$upload=$ex[0];
			$dowload=$ex[1];
			$rateupload=$explodeque[0];
			$ratedowload=$explodeque[1];
			$no=$x+1;
			$text = "Nomor : ".$no."\n"; 
			$text .="Nama : ".$data[$x]['name']."\n"; 
			$text .="Target : ".$data[$x]['target']."\n"; 
			$text .="Parent : ".$data[$x]['parent']."\n"; 
			$text .="Max Limit : ".formatBites($upload)."/".formatBites($dowload)."\n";
			$text .="Upload rate : ".formatBites($rateupload)."\n";
			$text .="Download rate : ".formatBites($ratedowload)."\n";
			$status=$data[$x]['disabled'];
			if ($status == "true") {
				$text .= "Disable : ⚠  Yes \n";
			} else {
				$text .= "Disable : No \n";
			}
			$text .=garis(1);
			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}
		if (count($data)==0) {$hasil .="Data simplequee, tidak ditemukan.\n";}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function netwatch($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm("/tool/netwatch/print");
		$num = count($ARRAY);
		$hasil .= "Daftar Host Netwatch $num\n\n";
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			$host = $ARRAY[$i]['host'];
			$interval = $ARRAY[$i]['interval'];
			$timeout = $ARRAY[$i]['timeout'];
			$status = $ARRAY[$i]['status'];
			$since = $ARRAY[$i]['since'];
			
			$text .= "📝 Netwatch$no \n";
			$text .= "┠ Host : $host \n";
	
			if ($status == "up") {
				$text .= "┠ Status : ✔ UP \n";
			} else {
				$text .= "┠ Status : ⚠ Down \n";
			}
			
			$text .= "┗ Since : $since \n\n";

			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}	
		if ($num==0) {$hasil .="Data netwatch, tidak ditemukan.\n";}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function neighbor($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY3 = $API->comm("/ip/hotspot/user/print");
		$ARRAY2 = $API->comm("/system/scheduler/print");
		$ARRAY = $API->comm("/ip/neighbor/print");
		$num = count($ARRAY);
		$num2 = count($ARRAY2);
		$num3 = count($ARRAY3);
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			$interfaces = "" . $ARRAY[$i]['interface'] ;
			$identity =  $ARRAY[$i]['identity'] ;
			$address =   $ARRAY[$i]['address'] ;
			$mac =  $ARRAY[$i]['mac-address'] ;
			$version =  $ARRAY[$i]['version'] ;
			$uptime =  $ARRAY[$i]['uptime'] ;
			$text	= "👥  $no\n";
			$text	.="┣ Interface :  $interfaces \n";
			$text	.="┣ Nama : $identity\n";
			$text	.="┣ IP address : $address \n";
			$text	.="┣ Mac : $mac\n";
			$text	.="┣ version :    $version\n";
			$text	.="┗ Uptime :     $uptime\n\n";

			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}
		if ($num==0) {$hasil .="Data neighbor, tidak ditemukan.\n";}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function ipbinding($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm('/ip/hotspot/ip-binding/getall');
		$num = count($ARRAY);
		$baris = $ARRAY;
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			$id = $baris[$i]['.id'];
			$address = $baris[$i]['address'];
			$mac = $baris[$i]['mac-address'];
			$toaddress = $baris[$i]['to-address'];
			$server = $baris[$i]['server'];
			$type = $baris[$i]['type'];
			$comment = $baris[$i]['comment'];
			$disabled = $baris[$i]['disabled'];
			$text  = "👥  IP Binding $no\n";
			$text .= "┣Address :  $address \n";
			$text .= "┣Mac address :  $mac \n";
			$text .= "┣To address  : $toaddress\n";
			$text .= "┣Server      : $server \n";
			$text .= "┣Type    : $type\n";
			$text .= "┗Disable : $disabled\n\n";
			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function normal($vcr){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	if ($vcr=="") {
    $hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
		$hasil  .=garis(1);
		$hasil  .="*reset kodevoucher* \n".garis(1)."Perintah *reset* harus diikuti dengan *kode voucher* yang akan di proses.\n".garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		return $hasil;
	}
	$kvcr=kapital($vcr);
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$cekuser 	= $API->comm("/ip/hotspot/user/print", ["?name" => $kvcr,]);
		if (empty($cekuser[0])) {
			$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
			$hasil  .=garis(1);
			$hasil  .="Proses reset gagal, kode vcr *".$kvcr."* tidak ditemukan, silahkan cek kode vcr anda,..\n\n";
			$hasil  .="Jika kode voucher mengandung huruf besar, harus diapit dengan titik.\n\n*reset aBcdEE* \n*reset a.B.cd.EE.*\n";
		}else{
			$cek1=$cek2=$cek3=$cek4=" GAGAL ";
			$cek1=$API->comm("/ip/hotspot/user/set", array(
				".id" => $cekuser[0]['.id'],
				"mac-address" => "00:00:00:00:00:00",
			));
			$cek1=json_encode($cek1,true);
			
			$cekactive = $API->comm("/ip/hotspot/active/print", ["?user" => $kvcr,]);
			if (empty($cekactive[0])) {
					
			}else{
				$cek2=$API->comm("/ip/hotspot/active/remove", array(
					".id" => $cekactive[0]['.id'],
				));
				$cek2=json_encode($cek2,true);
			}
			$cekhost = $API->comm("/ip/hotspot/host/print", ["?mac-address" => $cekuser[0]['mac-address'],]);
			if (empty($cekhost[0])) {
					
			}else{
				$cek3=$API->comm("/ip/hotspot/host/remove", array(
					".id" => $cekhost[0]['.id'],
				));
				$cek3=json_encode($cek3,true);
			}			
			$cekcookies = $API->comm("/ip/hotspot/cookie/print", ["?mac-address" => $cekuser[0]['mac-address'],]);
			if (empty($cekcookies[0])) {
					
			}else{
				$cek4=$API->comm("/ip/hotspot/cookie/remove", array(
					"user" => $kvcr,
				));
				$cek4=json_encode($cek4,true);
			}
			$hasil	= "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
			$hasil .=garis(1);
			$hasil .="Reset kode vcr *".$kvcr."* *berhasil* dilakukan, \n *silahkan* lakukan proses *login* kembali,\n".garis(1)." Terimakasih dan Selamat ".sapaan().".\n";
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function address($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm("/ip/address/print");
		$num = count($ARRAY);
		$hasil ="*".sapaan()."  ".$dns."* \n\n";
		for ($i = 0;$i < $num;$i++) {
			$no=$i+1;
			$address = $ARRAY[$i]['address'];
			$network = $ARRAY[$i]['network'];
			$interface = $ARRAY[$i]['interface'];
			$dynamic = $ARRAY[$i]['dynamic'];
			$disabled = $ARRAY[$i]['disabled'];
			$text  = "Daftar IP Address $no dari $num\n";
			$text .= "\n♨  $no. $interface\n";
			$text .= "┠ IP address : $address\n";
			$text .= "┠ Network    : $network \n";
			$text .= "┠ interface  : $interface \n";
			if ($dynamic == "true") {
				$text .= "┠ Dynamic : Iya \n";
			} else {
				$text .= "┠ Dynamic : Tidak \n";
			}
			if ($disabled == "false") {
				$text .= "┠ Disable : Tidak  \n";
			} else {
				$text .= "┠ Disable : Yes  \n";
			}
			$text .= "┠ Disablenow  : hidden  \n";
			$text .= "┗ Enablenow : hidden \n\n.";
			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
        $hasil  .=garis(1);
    		$hasil	.="*Terimakasih dan* ".sapaan1();
    		$hasil  .=" \n";	
    		$hasil  .=garis(2);
    		$hasil  .=" *Created By* : *".$dns2."*\n";
    		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
    		$hasil  .=garis(2);
			}
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		
	return $hasil;
}

function pool($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm("/ip/pool/print");
		$num = count($ARRAY);
		for ($i = 0;$i < $num;$i++) {
			$namapool = $ARRAY[$i]['name'];
			$rannge = $ARRAY[$i]['ranges'];
			$id = $ARRAY[$i]['.id'];
			$text  = "🎯 List Pool : \n";
			$text .= " ┠ Nama     :$namapool \n";
			$text .= " ┠ range : $rannge \n";
			$text .= " ┗ ID       :$id \n\n";
			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function interfac($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm('/interface/bridge/print');
		$num = count($ARRAY);
		for ($i = 0;$i < $num;$i++) {
			$nama = $ARRAY[$i]['name'];
			$mtu = $ARRAY[$i]['mtu'];
			$Mac_status = $ARRAY[$i]['mac-address'];
			$pro = $ARRAY[$i]['protocol-mode'];
			$run = $ARRAY[$i]['running'];
			$Disable = $ARRAY[$i]['disabled'];
			$text  = "🚗 Bridge \n";
			$text .= "┠ Nama : $nama \n";
			$text .= "┠ Mtu : $mtu \n";
			$text .= "┠ Mac : $Mac_status \n";
			$text .= "┠ Protocol : $pro \n";
			if ($run == "true") {
				$text .= "┠ Active : Iya \n";
			} else {
				$text .= "┠ Active : Tidak \n";
			}
			if ($Disable == "false") {
				$text .= "┠ Disable : Tidak \n";
			} else {
				$text .= "┠ Disable : Iya \n";
			}
			$text .= "┠ Disablenow  : hidden  \n";
			$text .= "┗ Enablenow : hidden \n\n";
			
			if (strlen($hasil.$text)<1000) {
				$hasil .=$text;
			}else{
				kirim($nomor,$hasil,1);
				$hasil = $text;
			}
		}
		$ARRAY = $API->comm("/interface/print");
		$num = count($ARRAY);
		$no=1;
		for ($i = 0;$i < $num;$i++) {
			$no = $i + 1;
			if ($ARRAY[$i]['type']=='ether') {
				$ids = $ARRAY[$i]['.id'];
				$dataid = str_replace('*', 'id', $ids);
				$namaport = $ARRAY[$i]['name'];
				$comentport = $ARRAY[$i]['comment'];
				$typeport = $ARRAY[$i]['type'];
				$tx = formatBytes($ARRAY[$i]['tx-byte'],1);
				$rx = formatBytes($ARRAY[$i]['rx-byte'],1);
				$true = $ARRAY[$i]['running'];
				$text  = "\n💻 Interface$no \n ";
				if ($true == "true") {
					$text .= " ┠🆙 CONNECT \n";
				} else {
					$text .= " ┠⚠ DISCONNECT \n";
				}
				$text .= "  ┠ Nama : $namaport \n";
				$text .= "  ┠ Comment : $comentport \n";
				$text .= "  ┠ Type : $typeport \n";
				$text .= "  ┠ Download : $tx \n";
				$text .= "  ┠ Upload : $rx \n";
				$text .= "  ┠ Disablenow  :/InDlE$dataid  \n";
				$text .= "  ┗ Enablenow :/InEle$dataid \n";

				if (strlen($hasil.$text)<1000) {
					$hasil .=$text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function dhcp(){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$get_lease = $API->comm("/ip/dhcp-server/lease/print");
		$num = count($get_lease);
		$data = "*DHCP Lease ".$num."* \n\n";
		for ($i = 0;$i < $num;$i++) {
			$lease = $get_lease[$i];
			$id = $lease['.id'];
			$address = $lease['address'];
			$macaddress = $lease['mac-address'];
			$server = $lease['server'];
			$acaddr = $lease['active-address'];
			$acmac = $lease['active-mac-address'];
			$hostname = $lease['host-name'];
			$host = str_replace("android", "AD", $hostname);
			$status = $lease['status'];
			if ($lease['dynamic'] == "true") {
				$dy = "🎯 Dynamic";
			} else {
				$dy = "📝 Static";
			}
			$data.= "🔎 Dhcp to $address \n  ";
			$data.= "┠  $dy  \n";
			$data.= "  ┠ IP     : $address\n";
			$data.= "  ┠ Mac   : $macaddress\n";
			$data.= "  ┠ DHCP : $server\n";
			$data.= "  ┗ HOST : $host\n".garis(1)."\n\n";
		}
		$ARRAY = $API->comm("/ip/dhcp-server/print");
		$num = count($ARRAY);
		$data .= "*DHCP Server ".$num."* \n\n";
		for ($i = 0;$i < $num;$i++) {
			$name = $ARRAY[$i]['name'];
			$interface = $ARRAY[$i]['interface'];
			$lease = $ARRAY[$i]['lease-time'];
			$bootp = $ARRAY[$i]['bootp-support'];
			$authoritative = $ARRAY[$i]['authoritative'];
			$use_radius = $ARRAY[$i]['use-radius'];
			$dynamic = $ARRAY[$i]['dynamic'];
			$disable = $ARRAY[$i]['disabled'];
			$no = $i+1;
			$data.= "📋 Dhcp Server $no \n";
			$data.= "  ┠ Nama : $name \n";
			$data.= "  ┠ Interface : $interface \n";
			$data.= "  ┠ Lease-time : $lease \n";
			$data.= "  ┠ Bootp-support : $bootp \n";
			$data.= "  ┠ Authoritative : $authoritative \n";
			$data.= "  ┠ Use-radius: $use_radius \n";
			if ($dynamic == "true") {
				$data.= "  ┠ Dynamic : Iya \n";
			} else {
				$data.= "  ┠ Dynamic : Tidak \n";
			}
			if ($disable == "true") {
				$data.= "  ┗ Status : ⚠ Disable \n";
			} else {
				$data.= "  ┗ Status : ✅ Enable \n";
			}
		}
		$hasil = $hasil.$data;
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function dns(){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$ARRAY = $API->comm("/ip/dns/print");
		$Ipserver = $ARRAY[0]['servers'];
		$dyserver = $ARRAY[0]['dynamic-servers'];
		$Allow  = $ARRAY[0]['allow-remote-requests'];
		$cache  = $ARRAY[0]['cache-used'];
		$hasil .= "🌏 DNS \n";
		$hasil .= "  ┠ Server : $Ipserver\n";
		$hasil .= "  ┠ Dynamic Server : $dyserver\n";
		if ($Allow == "true") {
			$hasil .= "  ┠ Allow Remote : Iya \n";
		} else {
			$hasil .= "  ┠ Allow Remote : Tidak \n";
		}
		$hasil .= "  ┗ Cache Used  : $cache \n";
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}	

function server($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$data = $API->comm('/ip/hotspot/print');
		$hasil .="Server List (".count($data).") \n".garis(1);
		for ($i = 0; $i < count($data); $i++) {
			$no=$i+1;
			$text  = "Nomor : ".$no."\n";
			$text .= "Nama : ".$data[$i]['name']."\n".garis(2);
			$text .= "Interface : ".$data[$i]['interface']."\n";
			$text .= "Address Pool : ".$data[$i]['address-pool']."\n";
			$text .= "Profile : ".$data[$i]['profile']."\n";
			$text .= "Addresses Per Mac : ".$data[$i]['addresses-per-mac']."\n";
			$text .= "Proxy Status : ".$data[$i]['proxy-status']."\n".garis(1)."\n";
		}
		if (count($data)<1) {
			$hasil = "Data Server tidak ditemukan.\n";
		}
		if (strlen($hasil.$text)<1000) {
			$hasil .=$text;
		}else{
			kirim($nomor,$hasil,1);
			$hasil = $text;
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function traffic($nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);

	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$getinterface = $API->comm("/interface/print");
		$num = count($getinterface);
		$Traffic="*Traffic :*\n".garis(2)."\n";
		$bts=1;
		$hal=1;
		$no=1;
		for ($i = 0;$i < $num;$i++) {
			if ($getinterface[$i]['type']<>"0ether") {
				$interface = $getinterface[$i]['name'];
				$getinterfacetraffic = $API->comm("/interface/monitor-traffic", array("interface" => "$interface", "once" => "",));
				$tx = formatBites($getinterfacetraffic[0]['tx-bits-per-second'],3);
				$rx = formatBites($getinterfacetraffic[0]['rx-bits-per-second'],3);
				$ttx= formatBites($getinterface[$i]['tx-byte'],3);
				$trx= formatBites($getinterface[$i]['rx-byte'],3);
				$Traffic	 = $no.". Traffic $interface\n";
				$Traffic	.= garis(1);
				$Traffic	.= "TX Rate $tx \nTotal $ttx \n";
				$Traffic 	.= "RX Rate $rx \nTotal $trx \n";
				$Traffic	.= garis(1)."\n";
				if (strlen($hasil.$Traffic)<1000) {
					$hasil .=$Traffic;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $Traffic;
				}
				$no=$no+1;
			}
		}
	}else{
		$hasil .="*system* GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function ping($mdata){
	if ($mdata=='') {$mdata='google.com';}
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $mdata)) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$PING = $API->comm("/ping", array("address" => "$mdata", "count" => "5",));
			$num = count($PING);
			$hasil .= "*Ping ke $mdata* \n\n";
			for ($i = 0;$i < $num;$i++) {
				$hot = $PING[$i]['host'];
				$status = $PING[$i]['status'];
				$size = $PING[$i]['size'];
				$ttl = $PING[$i]['ttl'];
				$time = $PING[$i]['time'];
				$packet_loss = $PING[$i]['packet-loss'];
				$avg = $PING[$i]['avg-rtt'];
				$packet_loss = $PING[$i]['packet-loss'];
				if ($status == 'timeout') {
					$hasil.= "PING $hot \nStatus $status Loss $packet_loss% \n\n";
				} else {
					$hasil.= "PING $hot \nSize $size TTL $ttl \nTime $time AVG $avg\n\n";
				}
			}
		} else {
			$hasil .= "Tidak Terkoneksi Dengan Mikrotik Coba Lagi\n";
		}
	} elseif (preg_match('/^([a-zA-Z0-9]([-a-zA-Z0-9]{0,61}[a-zA-Z0-9])?\.)?([a-zA-Z0-9]{1,2}([-a-zA-Z0-9]{0,252}[a-zA-Z0-9])?)\.([a-zA-Z]{2,63})$/', $mdata)) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$PING = $API->comm("/ping", array("address" => "$mdata", "count" => "5",));
			$num = count($PING);
			$hasil .= "*Ping  ke $mdata* \n\n";
			for ($i = 0;$i < $num;$i++) {
				$hot = $PING[$i]['host'];
				$status = $PING[$i]['status'];
				$size = $PING[$i]['size'];
				$ttl = $PING[$i]['ttl'];
				$time = $PING[$i]['time'];
				$packet_loss = $PING[$i]['packet-loss'];
				$avg = $PING[$i]['avg-rtt'];
				$packet_loss = $PING[$i]['packet-loss'];
				if ($status == 'timeout') {
					$hasil.= "PING $hot \nStatus $status Loss $packet_loss% \n\n";
				} else {
					$hasil.= "PING $hot \nSize $size TTL $ttl \nTime $time AVG $avg\n\n";
				}
			}
		} else {
			$hasil .= "Tidak Terkoneksi Dengan Mikrotik Coba Lagi\n";
		}
	}else{
		$hasil .="Perintah tidak dikenal. \n";
	}
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}

function hotspot($nomor,$mdata){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	if (explode(' ',$mdata)[1]=='active' || explode(' ',$mdata)[1]=='aktive' || explode(' ',$mdata)[1]=='aktif' ) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm('/ip/hotspot/active/print');
			$hasil .= "List user (".count($ARRAY).") \n".garis(2);
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : " . $no . " \n";
				$text .= "Server : " . $ARRAY[$x]['server'] . " \n";
				$text .= "User : " . $ARRAY[$x]['user'] . " \n";
				$text .= "Address : " . $ARRAY[$x]['address'] . " \n";
				$text .= "Mac : " . $ARRAY[$x]['mac-address'] . " \n";
				$text .= "Login By : " . $ARRAY[$x]['login-by'] . " \n";
				$text .= "Uptime : " . $ARRAY[$x]['uptime'] . " \n";
				$text .= "Byte In : " . formatBytes($ARRAY[$x]['bytes-in'],2) . " \n";
				$text .= "Byte Out : " . formatBytes($ARRAY[$x]['bytes-out'],2) . " \n";
				$text .= "Comment : " . $ARRAY[$x]['comment'] . " \n".garis(1);
				if (strlen($hasil.$text)<1000) {
					$hasil .= $text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Tidak ada user yang sedang active* \n";}
		}else{
			$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='list') {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm('/ip/hotspot/user/print');
			$hasil .= "List user (".count($ARRAY).") \n".garis(1);
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : " . $no . " \n";
				$text .= "User : *" . $ARRAY[$x]['name'] . "* \n";
				$text .= "Server : " . $ARRAY[$x]['server'] . " \n";
				$text .= "Profile : " . $ARRAY[$x]['profile'] . " \n";
				$text .= "Limit Uptime : " . $ARRAY[$x]['limit-uptime'] . " \n";
				$text .= "Uptime : " . $ARRAY[$x]['uptime'] . " \n";
				$text .= "Comment : " . $ARRAY[$x]['comment'] . " \n".garis(1);
				if (strlen($hasil.$text)<1000) {
					$hasil .= $text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Table user masih kosong.* \n";}
		}else{
			$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='profile' || explode(' ',$mdata)[1]=='profil' ) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm('/ip/hotspot/user/profile/print');
			$hasil .= "List profile (".count($ARRAY).") \n".garis(1);
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : " . $no . "\n";
				$text .= "ID : " . $ARRAY[$x]['.id'] . "\n";
				$text .= "Name : " . $ARRAY[$x]['name'] . "\n";
				$text .= "Shared User : " . $ARRAY[$x]['shared-users'] . "\n";
				$text .= "Add Mac : " . $ARRAY[$x]['add-mac-cookie'] . "\n";
				$text .= "Mac Timeout : " . maktif($ARRAY[$x]['mac-cookie-timeout']) . "\n";
				$text .= "Rate-limit : " . explode(' ',$ARRAY[$x]['rate-limit'])[0] . "\n".garis(1)."\n";

				if (strlen($hasil.$text)<1000) {
					$hasil .= $text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Table user profile masih kosong.* \n";}
		}else{
			$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='cek' ) {
		if (explode(' ',$mdata)[2]=="" ) {
		    $hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
		    $hasil .=garis(1);
  			$hasil .="Perintah *hotspot cek*, harus diikuti dengan *kode voucher* yang akan di cek.\n";
  			$hasil  .=garis(1);
    		$hasil	.="*Terimakasih dan* ".sapaan1();
    		$hasil  .=" \n";	
    		$hasil  .=garis(2);
    		$hasil  .=" *Created By* : *".$dns2."*\n";
    		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
    		$hasil  .=garis(2);
		}else{
			$kvcr=kapital(explode(' ',$mdata)[2]);
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {
				$cuser = $API->comm("/ip/hotspot/user/print", array("?name" => "$kvcr",));
				if (empty($cuser[0])) {
					$hasil .="Kode voucher [ *".$kvcr."* ] tidak ditemukan dalam table \nip/hotspot/user . \n";
				}else{
					$cactive = $API->comm("/ip/hotspot/active/print", array("?user" => "$kvcr",));
					if (empty($cactive)) {
						$hasil .="Kode voucher [ *".$kvcr."* ] ditemukan dalam table \nip/hotspot/user \nStatus sedang *tidak active.* \nKet : ".$cuser[0]['comment']."\n";
					}else{
						$hasil .="Kode voucher [ *".$kvcr."* ] ditemukan dalam table \nip/hotspot/user \nStatus sedang *active* dalam table \nip\hotspot\active. \n";
						$x=0;
						$hasil .= garis(1)."Server : " . $cactive[$x]['server'] . " \n";
						$hasil .= "User : " . $cactive[$x]['user'] . " \n";
						$hasil .= "Profile : " . $cuser[$x]['profile'] . " \n";
						$hasil .= "Address : " . $cactive[$x]['address'] . " \n";
						$hasil .= "Mac : " . $cactive[$x]['mac-address'] . " \n";
						$hasil .= "Login By : " . $cactive[$x]['login-by'] . " \n";
						$hasil .= "Uptime : " . $cactive[$x]['uptime'] . " \n";
						$hasil .= "Byte In : " . formatBytes($cactive[$x]['bytes-in'],2) . " \n";
						$hasil .= "Byte Out : " . formatBytes($cactive[$x]['bytes-out'],2) . " \n";
						$hasil .= "Comment : " . $cactive[$x]['comment'] . " \n".garis(2);
					}
				}
			}else{
				$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
			}
		}
	}else{
	    $hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	    $hasil .=garis(1);
		$hasil .="Perintah *hotspot*, harus diikuti dengan perintah \n";
		$hasil .=garis(1);
		$hasil .="*cek* \n";
		$hasil .="*list* \n";
		$hasil .="*aktif* \n";
		$hasil .="*profile* \n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	}
	return $hasil;
}
function ppp($nomor,$mdata){
	include "readcfg.php";
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	if (explode(' ',$mdata)[1]=='active' || explode(' ',$mdata)[1]=='aktive' || explode(' ',$mdata)[1]=='aktif' ) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm("/ppp/active/print");
			$hasil .= "PPP SECRET ACTIVE : (".count($ARRAY).")\n".garis(2);
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : ".$no."\n";
				$text .= "Name : ".$ARRAY[$x]['name']."\n";
				$text .= "Service : ".$ARRAY[$x]['service']."\n";
				$text .= "Caller ID : ".$ARRAY[$x]['caller-id']."\n";
				$text .= "Address : ".$ARRAY[$x]['address']."\n";
				$text .= "Uptime : ".$ARRAY[$x]['uptime']."\n";
				$text .= "Session ID : ".$ARRAY[$x]['session-id']."\n";
				$text .=garis(2);
				if (strlen($hasil.$text)<1000) {
					$hasil .= $text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Tidak ada user secret yang sedang active* \n";}
		}else{
			$hasil .="System GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='secret' || explode(' ',$mdata)[1]=='list' ) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm("/ppp/secret/print");
			$hasil .="Daftar Secret (".count($ARRAY).") \n".garis(2);
			$no=1;
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : ".$no."\n";
				$text .= "Name     : ".$ARRAY[$x]['name']."\n";
				$text .= "Service  : ".$ARRAY[$x]['service']."\n";
				$text .= "Caller   : ".$ARRAY[$x]['caller-id']."\n";
				$text .= "Password : ".$ARRAY[$x]['password']."\n";
				$text .= "Profile  : ".$ARRAY[$x]['profile']."\n";
				$text .= "Loc addr : ".$ARRAY[$x]['local-address']."\n";
				$text .= "Rem addr : ".$ARRAY[$x]['remote-address']."\n";
				$text .= "Limit in : ".formatBites($ARRAY[$x]['limit-bytes-in'])."\n";
				$text .= "Limit out: ".formatBites($ARRAY[$x]['limit-bytes-out'])."\n";
				$data = $ARRAY[$x]['disable'];
				if ($data == "true") {
					$text .= "Disable  : iya \n";
				} else {
					$text .= "Disable  : Tidak \n";
				}
				$text .=garis(1);
				if (strlen($hasil.$text)<1000) {
					$hasil .=$text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Table user secret masih kosong.* \n";}
		}else{
			$hasil .="System GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='profile' || explode(' ',$mdata)[1]=='profil' ) {
		$API = new routeros_api();
		if ($API->connect($vpn, $user, $pass, $port)) {
			$ARRAY = $API->comm("/ppp/profile/print");
			$hasil .= "PPP PROFILE : (".count($ARRAY).")\n".garis(2);
			for ($x=0;$x<count($ARRAY);$x++) {
				$no=$x+1;
				$text  = "Nomor : ".$no."\n";
				$text .= "Name        : ".$ARRAY[$x]['name']."\n";
				$text .= "Mpls        : ".$ARRAY[$x]['use-mpls']."\n";
				$text .= "Compression : ".$ARRAY[$x]['use-compression']."\n";
				$text .= "Only-one    : ".$ARRAY[$x]['only-one']."\n";
				$text .= "Change-tcp  : ".$ARRAY[$x]['change-tcp-mss']."\n";
				$text .= "Use-upnp    : ".$ARRAY[$x]['use-upnp']."\n";
//				$text .= "On-up       : ".$ARRAY[$x]['o']."\n";
//				$text .= "Limit in    : ".$ARRAY[$x]['m']."\n";
//				$text .= "Limit out   : ".$ARRAY[$x]['o']."\n";
				$data = $ARRAY[$x]['default'];
				if ($data == "true") {
					$text.= "Default  : iya\n";
				} else {
					$text.= "Default  : Tidak\n";
				}
				$text .=garis(1);
				if (strlen($hasil.$text)<1000) {
					$hasil .= $text;
				}else{
					kirim($nomor,$hasil,1);
					$hasil = $text;
				}
			}
			if (count($ARRAY)==0) {$hasil .="*Table profile secret masih kosong.* \n";}
		}else{
			$hasil .="Sytem GAGAL terkoneksi ke ".$vpn." failure.\n";
		}
	}elseif (explode(' ',$mdata)[1]=='cek' ) {
		if (explode(' ',$mdata)[2]=="" ) {
		    $hasil  .=garis(2);
			$hasil .="Perintah *ppp cek*, harus diikuti dengan *secret* yang akan di cek.\n";
			$hasil .=garis(2);
		}else{
			$secret=kapital(explode(' ',$mdata)[2]);
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {
				$csecret 	= $API->comm("/ppp/secret/print", ["?name" => $secret,]);
				if (empty($csecret[0])) {
					$hasil .="Secret *".$secret."* tidak ditemukan, ..\n";
				}else{
				    $hasil .=garis(1);
					$hasil .="Secret : ".$csecret[0]['name']." \n";
					$hasil .="Password : ".$csecret[0]['password']." \n";
					$hasil .=garis(1);
					$hasil .="\nProfile : ".$csecret[0]['profile']." \n";
					$hasil .="Service : ".$csecret[0]['service']." \n";
					$hasil .="Local : ".$csecret[0]['local-address']." \n";
					$hasil .="Remote : ".$csecret[0]['remote-address']." \n";
					$hasil .="LLO : ".$csecret[0]['last-logged-out']." \n";
					$hasil .="LCID : ".$csecret[0]['last-caller-id']." \n";
					$hasil .="LDR : ".$csecret[0]['last-disconnect-reason']." \n\n";
					if ($csecret[0]['disabled']=='false') {
					    $hasil .=garis(1);
						$hasil .="*Status : Enable* \n";
						$hasil .=garis(2);

					}else{
					    $hasil .=garis(1);
						$hasil .="*Status : Disable* \n";
						$hasil .=garis(2);

					}
				}
			}else{
				$hasil .="System GAGAL terkoneksi ke ".$vpn." failure.\n";
			}
		}
	}elseif (explode(' ',$mdata)[1]=='create' ) {
		if (explode(' ',$mdata)[2]=="" || explode(' ',$mdata)[3]=="" || explode(' ',$mdata)[4]=="" ) {
		    $hasil .=garis(1);
			$hasil .="Perintah *ppp create*,\nHarus diikuti dengan *profile_ppp newsecret dan newpassword* \nyang akan di create.\n\nNB.\nPenulisan Profile harus identik/sama persisi.\n";
			$hasil .=garis(2);
		}else{
			$profile	=kapital(explode(' ',$mdata)[2]);
			$secret		=kapital(explode(' ',$mdata)[3]);
			$password	=kapital(explode(' ',$mdata)[4]);
			if (strlen($secret)<5 || strlen($password)<5) {
				$hasil .="Panjang karakter Secret / Password, minimal 5 Huruf. \n";
				return $hasil;
			}
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {

				$cariprofileppp = $API->comm("/ppp/profile/print",["?name" => $profile]);
				if (empty($cariprofileppp[0])) {
					$hasil .="Profile ".$profile." tidak ditemukan. \n";
					return $hasil;
				}

				$cuserppp 	= $API->comm("/ppp/secret/print", ["?name" => $secret,]);
				$dataipppp 	= $API->comm("/ppp/secret/print", ["?profile" => $profile,]);
				if (!empty($cuserppp[0])) {
					$hasil .="Secret *".$secret."* sudah terdaftar, ..\n";
					return $hasil;
				}
				$noips=explode('.',$cariprofileppp[0]['local-address']);
				$noip0=$noips[0].'.'.$noips[1].'.'.$noips[2].'.';
				$ok='0';
				for ($noip=2;$noip<255;$noip++) {
					$noipl="$noip0"."$noip";
					$ada='0';
					for ($x=0;$x<count($dataipppp);$x++) {
						if ($dataipppp[$x]['remote-address']<>$noipl) {
							$ada='1';
							break;
						}
					}
					if ($ada=='1') {
						break;
					}
				}
				$service='pppoe';
				$hasil .="Proses Pembuatan secret \n\n*---STATUS BERHASIL DIBUAT---* \n\n";
				$hasil  .=garis(1);
				$hasil .="secret : ".$secret."\n";
				$hasil .="Password : ".$password."\n";
				$hasil .="Secret : ".$secret."\n";
				$hasil .="Service : ".$service."\n\n";
				$hasil .="Local-IP : ".$cariprofileppp[0]['local-address']."\n";
				$hasil .="Remote-IP : ".$noipl."\n\n";
				$add_user_api = $API->comm("/pp/secret/add", [
					"name" 			=> $secret,
					"password" 		=> $password,
					"service" 		=> $service,
					"profile" 		=> $profile,
					"local-address"	=> $cariprofileppp[0]['local-address'],
					"remote-address"=> $noipl,
					"comment"		=> date('m/d/Y H:i:s').' 1 1 '.$nomor,
				]);
				$cekadduser = json_encode($add_user_api);
				if (strpos(strtolower($cekadduser), '!trap')) {
					$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
					$hasil	.=str_replace(":","\n",explode('"',$cekadduser)[5])." \n";
					$hasil	.="\n*Proses pembuatan user secret GAGAl.* \n";
					return $hasil;
				}else{
					$hasil .="*Hasil : Success* \n";
					$hasil  .=garis(2);
				}
			}else{
				$hasil .="Sytem GAGAL terkoneksi ke ".$vpn." failure.\n";
				$hasil  .=garis(2);
			}
		}
	}elseif (explode(' ',$mdata)[1]=='remove' ) {
		if (explode(' ',$mdata)[2]=="" ) {
			$hasil .="Perintah *ppp remove*, harus diikuti dengan *secret* yang akan di hapus.\n";
			$hasil .=garis(2);
		}else{
			$secret=kapital(explode(' ',$mdata)[2]);
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {
				$csecret 	= $API->comm("/ppp/secret/print", ["?name" => $secret,]);
				if (empty($csecret[0])) {
					$hasil .="Secret *".$secret."* tidak ditemukan, ..\n";
				}else{
					$delppp = $API->comm("/ppp/secret/remove", [".id" => $csecret[0]['.id'],]);
					$json = json_encode($delppp);
					if (strpos(strtolower($json), 'no such item') !== false) {
						$gagal = $delppp['!trap'][0]['message'];
						$hasil .= "⛔ Gagal dihapus \nUser tidak ditemukan \nMohon periksa kembali  \n\n<b>KETERANGAN   :</b>\n$gagal";
					} elseif (strpos(strtolower($json), 'invalid internal item number') !== false) {
						$gagal = $delppp['!trap'][0]['message'];
						$hasil .= "⛔ Gagal dihapus \nId user tidak ditemuakn \Mohon periksa kembali\n\n<b>KETERANGAN   :</b>\n$gagal";
					} elseif (strpos(strtolower($json), 'default trial user can not be removed') !== false) {
						$gagal = $delppp['!trap'][0]['message'];
						$hasil .= "⛔ Gagal dihapus\nDefault trial tidak dapat dihapus\n\n<b>KETERANGAN   :</b>\n$gagal";
					} else {
						$hasil .= "Proses remove secret *".$secret."*\n\n*---STATUS BERHASIL DIHAPUS---*\n\n";
						sleep(2);
						$ARRAY3 = $API->comm("/ppp/secret/print");
						$jumlah = count($ARRAY3);
						$hasil .= "Jumlah user saat ini : $jumlah user\n\n";
						$hasil .=garis(2);
					}
				}
			}else{
				$hasil .="System GAGAL terkoneksi ke ".$vpn." failure.\n";
			}
		}
		
	}elseif (explode(' ',$mdata)[1]=='enable' ) {
		if (explode(' ',$mdata)[2]=="" ) {
			$hasil .="Perintah *ppp enable*, harus diikuti dengan *secret* yang akan dibuat enable.\n";
			$hasil .=garis(2);
		}else{
			$secret=kapital(explode(' ',$mdata)[2]);
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {
				$csecret 	= $API->comm("/ppp/secret/print", ["?name" => $secret,]);
				if (empty($csecret[0])) {
				    $hasil  .=garis(2);
					$hasil .="Secret *".$secret."* tidak ditemukan, ..\n";
					$hasil  .=garis(2);
				}else{
					$API->write("/ppp/secret/enable", false);
					$API->write("=.id=" . $csecret[0]['.id']);
					$API->read();
					sleep(2);
					$hasil  .=garis(2);
					$hasil .="Proses enable secret *".$secret."*,\n\n*---STATUS BERHASIL---*\n\n";
					$hasil  .=garis(2);
				}
			}else{
				$hasil .="Sytem GAGAL terkoneksi ke ".$vpn." failure.\n";
			}
		}
		
	}elseif (explode(' ',$mdata)[1]=='disable' ) {
		if (explode(' ',$mdata)[2]=="" ) {
			$hasil .="Perintah *ppp disable*, harus diikuti dengan *secret* yang akan dibuat disable.\n";
			$hasil .=garis(2);
		}else{
			$secret=kapital(explode(' ',$mdata)[2]);
			$API = new routeros_api();
			if ($API->connect($vpn, $user, $pass, $port)) {
				$csecret 	= $API->comm("/ppp/secret/print", ["?name" => $secret,]);
				if (empty($csecret[0])) {
					$hasil .="Secret *".$secret."* tidak ditemukan, ..\n";
				}else{
					$API->write("/ppp/secret/disable", false);
					$API->write("=.id=" . $csecret[0]['.id']);
					$API->read();
					sleep(2);
					$hasil  .=garis(1);
					$hasil	.="Proses disable secret *".$secret."*,\n\n*---STATUS BERHASIL---*\n\n";
					$hasil  .=garis(2);
				}
			}else{
			    $hasil  .=garis(1);
				$hasil .="Sytem GAGAL terkoneksi ke *".$vpn."* failure.\n";
				$hasil  .=garis(2);
			}
		}
		
	}else{
		$hasil .="Perintah *ppp*, harus diikuti dengan perintah \n";
		$hasil .=garis(1);
		$hasil .="*create* \n";
		$hasil .="*enable* \n";
		$hasil .="*disable* \n";
		$hasil .="*remove* \n\n";
		$hasil .=garis(1);
		$hasil .="*cek* \n";
		$hasil .="*aktif* \n";
		$hasil .="*profile* \n";
		$hasil .="*secret* \n".garis(1);
	}
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}
function help($nomor){
	$fdata		= file_get_contents("notifwa.php");
	$fparshing	= explode("%3D%27%2F",urlencode($fdata));
	$fcek		= explode("//<<",$fdata);
	$hasil		= sapaan()."\n\n*DAFTAR PERINTAH :* \n";
	$hasil 		.=garis(1);
	$no=0;
	for ($x=2;$x<count($fparshing)-1;$x++) {
		$no++;
		$text =str_repeat(' ',3-strlen($no)).$no.".  /".explode('%27',$fparshing[$x-1])[0]." \n".str_repeat(" ",8)."*".explode(">>",$fcek[$x-1])[0]."* \n";
		if (strlen($hasil.$text)<1000) {
			$hasil .=$text;
		}else{
			kirim($nomor,$hasil,1);
			$hasil = $text;
		}
	}
	$hasil .=garis(2).$no." Perintah. \n";
//	$JJ=strlen($hasil);
//	$hasil .=$JJ;
	return $hasil;
}
function paket(){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	= "".sapaan()." *".$data['nama_seller']."* \n\n";
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$dtprofile = $API->comm("/ip/hotspot/user/profile/print");
		$hasil	.="*Paket Tersedia :* \n".garis(2);
		$no=0;
		for ($x=0;$x<count($dtprofile);$x++) {
			$maktif=explode(',',$dtprofile[$x]['on-login'])[3];
			if (explode(",",$dtprofile[$x]['on-login'])[2]<>0) {
				$no++;
				$hasil	.="*Paket$no*  ".maktif($maktif).str_repeat(" ",5).rupiah(explode(',',$dtprofile[$x]['on-login'])[4])." \n";
			}
		}
		$hasil	.=garis(1)."Tersedia ".$no." Paket.\n".garis(1);
	}else{
		$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
	return $hasil;
}
function beli($paket,$qty,$nomor){
	include "readcfg.php";
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$API = new routeros_api();
	if ($API->connect($vpn, $user, $pass, $port)) {
		$dtprofile = $API->comm("/ip/hotspot/user/profile/print");
		$no=0;
		$ok=0;
		for ($x=0;$x<count($dtprofile);$x++) {
			$maktif=explode(',',$dtprofile[$x]['on-login'])[3];
			if (explode(",",$dtprofile[$x]['on-login'])[2]<>0) {
				$no++;
				$cpaket="paket$no";
				if (strtolower($paket)==$cpaket) {
					$ok=1;
					break;
				}
			}		
		}
		$hasil	= "".sapaan()."  *".$data['nama_seller']."* \n";
		$hasil  .=garis(1);
		if ($paket=="") {$hasil .="Perintah *beli* harus diikuti dengan *paket* yang akan diproses. \n\nUntuk pembelian 1 Voucher\n*beli paket*\n\nUntuk pembelian banyak vcr\n*beli paket qty*\n";
		$hasil  .=garis(1);
		$hasil	.="*Terimakasih dan* ".sapaan1();
		$hasil  .=" \n";	
		$hasil  .=garis(2);
		$hasil  .=" *Created By* : *".$dns2."*\n";
		$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
		$hasil  .=garis(2);
		    return $hasil;}
		if ($ok==0) {$hasil .=paket()."\n*Paket [".$paket."] tidak ditemukan.*\n*Contoh :* \n*beli paket1* \n*Untuk melakukan pembelian paket1.*\n"; return $hasil;}
		if (preg_match('/^[1-9]+$/', $qty) == false) {
			$qty=1;
		}
		if ($qty>25) {
			$hasil .="Maximal pembuatan 25 Lembar.\n";
			return $hasil;
		}
		for ($y=0;$y<$qty;$y++) {
			$kvcr	= strtoupper(explode(",",$dtprofile[$x]['on-login'])[3]).make_string(6,7);
			$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
			$hasil	.="*Pembelian voucher Berhasil dibuat*.";
			$hasil	.="\nTgl Order : *".date('d/M/Y H:i:s')."* \n";
			$hasil  .=garis(1);
			$hasil	.="Paket : *".$paket."*\n";
			$hasil	.="Kode VCR : ".pisah($kvcr)."\n";
			$hasil	.="Harga : *".rupiah(explode(',',$dtprofile[$x]['on-login'])[4])."* \n";
			$hasil	.="Durasi : ".maktif(explode(",",$dtprofile[$x]['on-login'])[3])." \n";
			$hasil  .=garis(1);
			$hasil	.="*Login Disini*: \n_".$dns1."/login?username=".$kvcr."&password=".$kvcr."_\n";
			$hasil  .=garis(1);
 			$hasil	.="*Terimakasih*\n ".sapaan1();
 			$hasil  .=" \n";	
 			$hasil  .=garis(2);
 			$hasil  .=" *Created By* : *".$dns2."*\n";
 			$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
 			$hasil  .=garis(2);
			$comment="vc-bot_wa_".$nomor."_".date('d/M/Y H:i:s');
			
			{
				$add_user_api = $API->comm("/ip/hotspot/user/add", [
					"server" => 'all',
					"profile" => $dtprofile[$x]['name'],
					"name" => $kvcr,
					"password" => $kvcr,
					"limit-uptime" => explode(",",$dtprofile[$x]['on-login'])[3],
					"comment" => $comment,
				]);
				$cekadduser = json_encode($add_user_api);
				if (strpos(strtolower($cekadduser), '!trap')) {
					$hasil	= "*".sapaan()."  ".$dns."* \n\n";
					$hasil	.=str_replace(":","\n",explode('"',$cekadduser)[5])." \n";
					$hasil	.="\n*Proses pembuatan voucher dihentikan.* \n";
					$hasil  .=garis(1);
					$hasil	.="*Terimakasih dan* ".sapaan1();
					$hasil  .=" \n";	
					$hasil  .=garis(2);
					$hasil  .=" *Created By* : *".$dns2."*\n";
					$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
					$hasil  .=garis(2);
					return $hasil;
				}
			}
			$hjual  = explode(',',$dtprofile[$x]['on-login'])[4];
			$hmodal = explode(',',$dtprofile[$x]['on-login'])[2];
			$laba	= $hjual-$hmodal;
			$hbersih= $hmodal;
			if (cuser($nomor)['saldo']>$hbersih) {
				$ok=simpvcr($nomor, cuser($nomor)['nama_seller'], $hbersih,$laba,$kvcr, $kvcr, maktif(explode(",",$dtprofile[$x]['on-login'])[3]),'Success');
			}else{
                $deluserid = $API->comm("/ip/hotspot/user/remove", ["numbers" => $add_user_api, ]);
        				$hasil  .=garis(1);
        				$hasil	.="*SALDO KURANG* \n Kode voucher\n ".pisah($kvcr)." \n Telah dihapus,\n *Proses pembuatan voucher batal dilakukan.* \n";
                $hasil  .=garis(1);
				return $hasil;
			}
			$mtgl = date('d/m/Y');
			$mtime = date('H:i:s');
			$namawa="User WA";
		    $vfile = "vc-BT".substr($mtgl, 0, 2).substr($mtgl, 3, 2).substr($mtgl, 6, 4).substr($mtime, 0, 2).substr($mtime, 3, 2);
			$mtulis = $nomor."|".$namawa.'|'.$mtgl."|".$mtime."|".$dns."|".$dtprofile[$x]['name']."|".$kvcr."|".explode(",",$dtprofile[$x]['on-login'])[2]."|".explode(",",$dtprofile[$x]['on-login'])[4]."|".explode(",",$dtprofile[$x]['on-login'])[3]."|".maktif(explode(",",$dtprofile[$x]['on-login'])[3])."|".$vfile."|".$mket."# \n";
			$fdtvcr = "../webhook/data/dt".substr($mtgl, 3, 2).substr($mtgl, 6, 4).".txt";
//			file_put_contents($fdtvcr, $mtulis, FILE_APPEND | LOCK_EX);
			if ($qty<>1) {
				$ii=kirim($nomor,$hasil,1);
				sleep(2);
				$hasil="";
			}
		}
	}else{
		$hasil .="system GAGAL terkoneksi ke ".$vpn." failure.\n";
	}
			
	return $hasil;
}
function test() {
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
  $hasil  .=garis(1);
	$hasil  .="Bot WA status Ok,...\n";	
	$hasil  .=garis(1);
	$hasil	.="*Terimakasih dan* ".sapaan1();
	$hasil  .=" \n";	
	$hasil  .=garis(2);
	$hasil  .=" *Created By* : *".$dns2."*\n";
	$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
	$hasil  .=garis(2);
	return $hasil;
}
function ktele($pesan) {
//	$token=""; 
	$token=explode('|',file_get_contents('../webhook/webhookk.php'))[3];
	$pesan	=$pesan."\n".date('d-M-Y H:i:s')."\n.";
	$option = [	'text' 	=> $pesan,'chat_id'	=> '1341792914','parse_mode' => 'html',];
	$respone=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
	return $respone;
}
function kirim($nomor,$pesan,$cek) {
	$psn=$pesan;
	$ffile1="kirim.txt";
	$ffile2="setup.set";
	$cek1	=explode('-',file_get_contents($ffile1))[0];
	$dtapi	= explode('|-|',file_get_contents($ffile2));
	$device=$dtapi[3];
	$token=$dtapi[4];
	if ($cek1=='2') {
		$device=$dtapi[3];
		$token=urlencode($dtapi[4]);
		$hasil = $response." ".$nomor." ".$pesan;
		$pesan=urlencode($pesan);
		$data="proses=20&nomor=$nomor&device=$device&token=$token&pesan=$pesan";
		$ftuj="https://mimoassist.homes/script/index.php?$data";
		$hasil=file_get_contents($ftuj);
	}else{
		$api_key 	= $dtapi[1];
		$endpoint	= $dtapi[2];;
		$pesan		= $pesan."\n*".date('d/M/Y H:i:s')."*";
		$body = array(
			"api_key" => $api_key,
			"receiver" => $nomor,
			"data" => array("message" => $pesan)
		);
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($body),
			CURLOPT_HTTPHEADER => [
				"Accept: **",
				"Content-Type: application/json",
			],
		]);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			$hasil = date('d/M/Y H:i:s')."|-|cURL Error|-|".$cek1."|-|".$err."#\n";
		} else {
			if ($response=='') {
				$hasil = date('d/M/Y H:i:s')."|-|gagal|-|".$cek1."|-|Proses kirim pesan GAGAL.#\n";
			}else{
				$hasil = $response;
			}
		}
	}
	return $hasil;
}
function garis($x){
	if ($x=='1') {
		$hasil=str_repeat("-",50)."\n";
	}else{
		$hasil=str_repeat("=",25)."\n";
	}
	return $hasil;
}
function maktif($angka) {
	$hasil=str_replace("w"," Minggu ",str_replace("d"," Hari ",str_replace("h"," Jam ",str_replace("m"," Menit",$angka))));
	return $hasil;
}
function rupiah($angka) {
	$hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}
function pisah($kvcr) {
	$hasil="";
	for ($x=0;$x<strlen($kvcr);$x++) {
		$hasil .="*".substr($kvcr,$x,1)."* ";
	}
	return $hasil;
}
function kapital($data) {
	$hasil='';
	$chit='L';
	for ($x=0;$x<strlen($data);$x++) {
		$cek=substr($data,$x,1);
		if ($cek=='.') {
			if ($chit=='L') {
				$kapital="Y";
				$chit='K';
			}else{
				$kapital="N";
				$chit='L';
			}
		}
		if ($cek<>'.') {
			if ($kapital=='Y') {
				$hasil .=strtoupper($cek);
			}else{
				$hasil .=$cek;
			}
		}
	}
	return $hasil;
}
function start($nomor) {
    include "readcfg.php";
// ini untuk mencari nama user
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$hasil  .="*DAFTAR PERINTAH :*\n";
	$hasil  .=garis(1);
	$hasil  .="1. *paket*\n- Daftar Paket Hotspot\n";
	$hasil  .="2. *beli*\n- Beli Paket Hotspot\n";
	$hasil  .="3. *normalisasi*\n- Reset Kode Voucher\n";
	$hasil  .="4. *ppp*\n- Tampilkan PPP\n";
	$hasil  .="5. *reg*\n- Daftar Akun Reseller\n";
	$hasil  .="6. *unreg*\n- Hapus Akun Reseller\n";
	$hasil  .="7. *reseller*\n- Dartar list Reseller\n";
	$hasil  .="8. *info*\n- Info Saldo Reseller\n";
	$hasil  .="9. *topup*\n- Topup Saldo Reseller\n";
	$hasil  .="10. *reset*\n- Reset Kode Voucher\n";	
	$hasil  .=garis(1);
	$hasil	.="*Terimakasih dan* ".sapaan1();
	$hasil  .=" \n";	
	$hasil  .=garis(2);
	$hasil  .=" *Created By* : *".$dns2."*\n";
	$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
	$hasil  .=garis(2);
	return $hasil;
}
function tools() {
    include "readcfg.php";
    global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
	]);
	$hasil	 = "".sapaan()." *".lihatuser($nomor)['nama_seller']."* \n\n";
	$hasil  .="*DAFTAR PERINTAH :*\n";	
	$hasil  .=garis(1);
	$hasil  .="1. *interface*\n- Tampilkan Interface\n";
	$hasil  .="2. *server*\n- Tampilkan Server\n";
	$hasil  .="3. *dns*\n- Tampilkan DNS\n";
	$hasil  .="4. *pool*\n- Tampilkan Pool\n";
	$hasil  .="5. *dhcp*\n- Tampilkan DHCP\n";
	$hasil  .="6. *ping*\n- Test Ping Server\n";
	$hasil  .="7. *hotspot*\n- Tampilkan Hotspot\n";
	$hasil  .="8. *traffic*\n- Tampilkan Traffic\n";
	$hasil  .="9. *address*\n- Tampilkan Address\n";
	$hasil  .="10. *neighbor*\n- Tampilkan Neighbor\n";
	$hasil  .="11. *netwacth*\n- Tampilkan Netwacth\n";
	$hasil  .="12. *resource*\n- Tampilkan Resource\n";
	$hasil  .="13. *ipbinding*\n- Tampilkan IP Binding\n";
	$hasil  .="14. *simplequeeue*\n- Tampilkan Simple Queue\n";
	$hasil  .=garis(1);
	$hasil	.="*Terimakasih dan* ".sapaan1();
	$hasil  .=" \n";	
	$hasil  .=garis(2);
	$hasil  .=" *Created By* : *".$dns2."*\n";
	$hasil  .=" *©".tahun()." ".$dns.". All rights reserved.*\n";
	$hasil  .=garis(2);
	return $hasil;
}

function sapaan() {
	$jam0=date('H');
	if ($jam0>4 and $jam0<9){
		$jam1='Pagi';
	}elseif ($jam0>8 and $jam0<14){
		$jam1='Siang';
	}elseif ($jam0>13 and $jam0<19){
		$jam1='Sore';
	}else{
		$jam1='Malam';
	}
	$jam1="*Selamat $jam1*";
	return $jam1;
}

function sapaan1() {
	$jam0=date('H');
	if ($jam0>4 and $jam0<9){
		$jam1='Awali hari dengan senyuman, semangat pagi!';
	}elseif ($jam0>8 and $jam0<14){
		$jam1='Selamat menikmati waktu istirahat siang!';
	}elseif ($jam0>13 and $jam0<19){
		$jam1='Sore yang indah, semoga harimu tetap berwarna!';
	}else{
		$jam1='Selamat beristirahat setelah hari yang panjang.';
	}
	$jam1="*$jam1*";
	return $jam1;
}

function cek($id) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'nama_seller',
	],[	
		'id_user' => $id
	]);
	if (empty($data['nama_seller'])) {
		$hasil="0";
	}else{
		$hasil="1";
	}
	return $hasil;
}

function tahun() {
    return date('Y'); // Mengembalikan tahun saat ini
}
?>