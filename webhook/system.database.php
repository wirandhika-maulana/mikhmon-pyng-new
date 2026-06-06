<?php
date_default_timezone_set('Asia/Jakarta');

include("whatsva.php");
include("system.config.php");
include "qrcode/qrlib.php"; 


function kirimwa($nomor,$tulis,$device,$logo) {
//$kk=ktele($tulis."\nDari Function kirimwa.\n".$nomor);
//return $kk;
$file = 'wahookk.php';
$misi		= file_get_contents($file);
$misi0		= explode("|",$misi);
$token		= $misi0[2];

try {
  $reqParams = [
    'token' => $token,
    'url' => 'https://api.kirimwa.id/v1/messages',
    'method' => 'POST',
    'payload' => json_encode([
      'message' => $tulis,
      'phone_number' => $nomor,
      'message_type' => 'text',
      'device_id' => $device,
    ])
  ];

  $response = apiKirimWaRequest($reqParams);
  $data = $response['body'];
} catch (Exception $e) {
  $data=$e;
}
return $data;	
}

function adddevice($id,$token) {
try {
	$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/devices',
		'method' => 'POST',
		'payload' => json_encode([
			'device_id' => $id
		])
	];

	$response = apiKirimWaRequest($reqParams);
	$data=$response['body'];
	} catch (Exception $e) {
	$data=$e;
	}
	$data1=datacek($data);
	return $data1;
}

function stadevice($id,$token) {
try {
	$reqParams = [
		'token' => $token,
		'url' => sprintf('https://api.kirimwa.id/v1/devices/%s',$id)
	];

	$response = apiKirimWaRequest($reqParams);
	$data=$response['body'];
	} catch (Exception $e) {
	$data=$e;
	}
	$data1=datacek($data);
	return $data1;
}

function deldevice($id,$token) {
try {
	$reqParams = [
		'token' => $token,
		'url' => sprintf('https://api.kirimwa.id/v1/devices/%s',$id),
		'method' => 'DELETE'
	];
	$response = apiKirimWaRequest($reqParams);
	} catch (Exception $e) {
	$data=$e;
	}
	$data1=datacek($data);
	return $data1;
}

function pairing($id,$token) {
try {
	$query = http_build_query(['device_id' => $id]);
	$reqParams = [
		'token' => $token,
		'url' => sprintf('https://api.kirimwa.id/v1/qr?%s', $query)
	];

	$response = apiKirimWaRequest($reqParams);
	$qrcode0=explode('"',$response['body']);
	$data=$qrcode0[7];
	} catch (Exception $e) {
	$data=$e;
	}
	return $data;
}

function qouta($token) {
try {
	$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/quotas'
		];

	$response = apiKirimWaRequest($reqParams);
	$data=$response['body'];
	$json=json_decode($data, TRUE ) ;
	$status="Kouta pemakaian harian\n\n";
		foreach ($json as $hargas) {
		$sisa=$hargas['message_per_day']-$hargas['today_usage'];
		if ($hargas['incoming_message_per_day']== -1) {$ket="unlimited";}else{$ket=$hargas['incoming_message_per_day'];}
		$status  .= "message_per_day : ".$hargas['message_per_day']."\n";
		$status  .= "incoming_message_per_day : ".$ket."\n";
		$status  .= "incoming_media_bytes_per_month : ".$hargas['incoming_media_bytes_per_month']."\n";
		$status  .= "batch_message_per_request : ".$hargas['batch_message_per_request']."\n";
		$status  .= "today_usage : ".$hargas['today_usage']."\n";
		$status  .= "today_incoming_usage : ".$hargas['today_incoming_usage']."\n";
		$status  .= "current_month_incoming_media_bytes : ".$hargas['current_month_incoming_media_bytes']."\n";
		$status  .= "current_date : ".$hargas['current_date']."\n";
		$status  .= "next_reset : ".$hargas['next_reset']."\n";
		$status  .= "daily_next_reset : ".$hargas['daily_next_reset']."\n";
		$status  .= "countdown_reset : ".$hargas['countdown_reset']."\n";
		$status  .= "daily_next_countdown_reset : ".$hargas['daily_next_countdown_reset']."\n\n";
		$status  .= "sisa qouta : ".$sisa."\n";
		}
	} catch (Exception $e) {
		$status=$e;
	}
	return $status;
}

function webon($url,$token) {
try {
	$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/webhooks',
		'method' => 'POST',
		'payload' => json_encode([
			'webhook_url' => $url
		])
	];

	$response = apiKirimWaRequest($reqParams);
	$data = $response['body'];
	} catch (Exception $e) {
	$data = $e;
	}
	$data1=datacek($data);
	return $data1;
}


function weboff($email,$url,$token) {
$url="https://api.kirimwa.id/v1/webhooks/whu-$email";
try {
	$reqParams = [
		'token' => $token,
		'url' => $url,
		'method' => 'DELETE',
			'payload' => json_encode([
			'webhook_url' => $url
		])
	];
	$response = apiKirimWaRequest($reqParams);
	$data=$response['body'];
	} catch (Exception $e) {
		$data = $e;
	}
	$data1=datacek($data);
	return $data1;
}	

function websta($token) {
	try {
		$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/webhooks'
		];
		$response = apiKirimWaRequest($reqParams);
		$data=$response['body'];
	} catch (Exception $e) {
		$data=$e;
	}
	$data1=datacek($data);
	return $data1;
}

function websta1($urlcek,$token) {
	try {
		$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/webhooks'
		];
		$response = apiKirimWaRequest($reqParams);
		$data=$response['body'];
	} catch (Exception $e) {
		$data=$e;
	}
	$cekk=0;
	if (strpos(strtolower($data), strtolower($urlcek) ) !== false){$cekk=1;}
	return $cekk;
}

function datacek($cek) {
	$cek1=explode(",",$cek);
	$cekk="";
	for ($a=0;$a<count($cek1);$a++) {
		$cekk	.=$cek1[$a]."\n";
	}
	if (strpos(strtolower($cek), 'device id already exists') !== false){$cekk="Device sudah terdaftar.\n";}
	if (strpos(strtolower($cek), 'device not found') !== false){$cekk="Tidak ada device terdaftar.\n";}
	if (strpos(strtolower($cek), 'webhook not found') !== false){$cekk="Tidak ada Web Hook yang terdaftar.\n";}
	if (strpos(strtolower($cek), '[]') !== false){$cekk="Web Hook belum di set.\n";}
	
	return $cekk;
}

function help($data) {
	if ($data=="kirimwa.id") {
		$data  ="PETUNUK PENGGUNAAN\n";
		$data .="Untuk Api kirimwa.id.\n\n";
		$data .="Isi semua bilah yang disediakan.\n\n";
		$data .="Sebelum melakukan Pairing, persiapkan HP Anda pada posisi siap untuk melakukan Scan QE code yang berada dalam menu Link Device WhatsApp Anda.\n\n";
	}else if ($data=="mpwa") {
		$data = "PETUNUK PENGGUNAAN\n";
		$data .= "Untuk Api Mpwa.\n\n";
		$data .= "-. Registrasi.\n";
		$data .= "1. Daftar di https://api.mimoassist.homes/.\n";
		$data .= "2. Setelah melakukan pendaftaran, silakan konfirmasi di\n";
		$data .= "   https://wa.me/6289633033332.\n";
		$data .= "3. Masa aktif mengikuti masa aktif Mkhmon Online.\n\n";
		$data .= "-. Penggunaan.\n";
		$data .= "1. Isi semua bilah yang disediakan.\n\n";
		$data .= "2. Buka situs Mpwa Anda.\n";
		$data .= "   2.1 Lakukan Login.\n";
		$data .= "   2.2 Lakukan Add Device.\n";
		$data .= "   2.3 Masukkan Nomor HP yang akan dijadikan Bot.\n";
		$data .= "   2.4 Isi URL Webhook dengan alamat Core.\n      Lihat bilah file core.\n";
		$data .= "   2.5 Klik Scan Device/Pairing.\n\n";
		$data .= "3. Scan QR-Code yang muncul dengan Aplikasi WA.\n";
		$data .= "   3.1 Biarkan Aplikasi WA melakukan sinkronisasi sampai sukses.\n\n";
		$data .= "-. End Point.\n";
		$data .= "   Bisa Anda temukan pada menu API Docs. Ambil alamat URL-nya saja.\n\n";
		$data .= "\n";
		$data .= "created https://t.me/Cs_MimoAssist";
	}
	return $data;   
}
function apkbelivoucher($id, $usernamepelanggan, $princevoc,$markup, $username, $password, $uptime, $keterangan, $id_own) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', ['saldo','id_user'], ['id_user' => $id]);
	$saldoawal = $data["saldo"];
	if ($id==$id_own){
		if (isset($data)) {
			$last_id = $mikbotamdata->insert('re_operating', [
				'id_user' => $id,
				'nama_seller' => $usernamepelanggan,
				'saldo_awal' => $saldoawal,
				'saldo_akhir' => $saldoawal,
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
			'saldo[-]' => 0,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
			'voucher_terjual[+]' => 1,
			'jumlah_debit_terjual[+]' => $princevoc,
			], [
				'id_user' => $id,
		]);
	} else {
		if (isset($data)) {
			$last_id = $mikbotamdata->insert('re_operating', [
				'id_user' => $id,
				'nama_seller' => $usernamepelanggan,
				'saldo_awal' => $saldoawal,
				'saldo_akhir' => $saldoawal - $princevoc + $markup,
				'beli_voucher' => $princevoc - $markup,
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
			'saldo[-]' => $princevoc - $markup,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
			'voucher_terjual[+]' => 1,
			'jumlah_debit_terjual[+]' => $princevoc - $markup,
			], [
				'id_user' => $id,
		]);
	}
	if ($keterangan == 'Success') {
		$report = $mikbotamdata->insert('st_reportdata', [
			'id' => $id,
			'nama_user' => $usernamepelanggan,
			'harga' => $princevoc,
			'status' => $keterangan,
			'transaksi' => 'halo',
			'pendapatan' => $princevoc - $markup,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
		]);
	}
	return $saldoawal;
}

function apkdaftarr($id, $id1, $nama) {
	global $mikbotamdata;
	$test = $mikbotamdata->get('st_smsgateway', [
		'_id',
		'Token',
		'ipserver',
	], [
		'Token' => $id1,
	]);
	$tulis = "Selamat...\n\nID WA : ".$id1."\nNama : ".$nama."\n\n";
	if (empty($test['ipserver'])) {
		$data = $mikbotamdata->insert('st_smsgateway', [
			'_id' => $id,
			'Token' => $id1,
			'ipserver' => $nama,
		]);
		$tulis  .= "Telah masuk dalam data.\n\n";
	}else{
		$data = $mikbotamdata->update('st_smsgateway', [
			'ipserver' => $nama,
		], [
			'Token' => $id1,
		]);
		$tulis  .= "Berhasil diupdate.\n\n";
	}
	$tulis  .= "Terimakasih dan Selamat ".sapaan();
	return $tulis;
}

function lpelanggan($nomor) {
	global $mikbotamdata;
	$data = $mikbotamdata->select('st_smsgateway', [
		'_id',
		'Token',
		'ipserver',
	],[ 
		'_id' => $nomor
	],[ 
		'ORDER' => [ 'ipserver' => 'ASC' ]
	]);
	return $data;
}
function cnpelanggan($idp) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('st_smsgateway', [
		'_id',
		'Token',
		'ipserver',
	],[ 
		'Token' => $idp
	]);
	return $data;
}

function tdeposit($id, $name, $jumlah, $id_own) {
	global $mikbotamdata;
	$ceksaldoawal = $mikbotamdata->get('re_settings', [
		'id_user',
		'nomer_tlp',
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
			'nomer_tlp',
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
//		$idowner = $mikbotamdata->select('st_mikbotam', [
//			"Id_owner",
//		]);

		$text = "Informasi TOP UP saldo\n";
		$text .=lain(garis);
		$text .= "ID WA       : $id\n";
		$text .= "Username    : $nama\n";
		$text .= "Status      : Berhasil \n";
		$text .= "Nominal     : " . rupiah($jumlah) . " \n";
		$text .= "Saldo Awal  : " . rupiah($saldoawal) . " \n";
		$text .= "Saldo Akhir : " . rupiah($saldo) . " \n";
		$text .= "Outletid    : " . $id_own . "\n";
		$text .=lain(garis);
	} else {
		$text = "Informasi TOP UP saldo\n";
		$text .=lain(garis);
		$text .= "ID WA	 : $id\n";
		$text .= "Nama	 : $nama\n";
		$text .= "Status : dtbase error\n";
		$text .=lain(garis);
	}
	if ($jumlah<0) {
		$jum=$jumlah*-1;
		$text .="*Deposit Anda telah dikurangi ".rupiah($jum)."* \n";
	}else{
		$text .="*Deposit Anda telah ditambah ".rupiah($jumlah)."* \n";
	}
	$error = $mikbotamdata->error();
	return $text;
}

function pdeposit($id, $name, $jumlah, $id_own) {
    global $mikbotamdata;

    // Ambil saldo awal pengguna
    $ceksaldoawal = $mikbotamdata->get('re_settings', [
        'nomer_tlp',
        'id_user',
        'saldo',
    ], [
        'nomer_tlp' => $id
    ]);

    $saldoawal = $ceksaldoawal["saldo"];

    // Cek apakah reseller memiliki saldo yang cukup
    if ($saldoawal >= $jumlah) {
        // Update saldo dengan mengurangi jumlah deposit
        $update = $mikbotamdata->update('re_settings', [
            'saldo' => $saldoawal - $jumlah, // Kurangi saldo dengan jumlah deposit
            'Waktu' => date('H:i:s'),
            'Tanggal' => date('Y-m-d'),
        ], [
            'nomer_tlp' => $id,
        ]);

        if ($update == true) {
            // Ambil data pengguna setelah update
            $datacek = $mikbotamdata->get('re_settings', [
                'nomer_tlp',
                'id_user',
                'nama_seller',
                'saldo',
            ], [
                'nomer_tlp' => $id
            ]);

            $nama = $datacek["nama_seller"];
            $saldo = $datacek["saldo"];

            // Catat transaksi ke dalam tabel re_operating
            $hasil = $mikbotamdata->insert('re_operating', [
                'id_user' => $id,
                'nama_seller' => $nama,
                'saldo_awal' => $saldoawal,
                'saldo_akhir' => $saldo,
                'top_up' => $jumlah,
                'keterangan' => 'pengurangan', // Gunakan label yang berbeda untuk pengurangan
                'top_up_fromid' => $id_own,
                'Waktu' => date('H:i:s'),
                'Tanggal' => date('Y-m-d'),
            ]);

            // Buat pesan informasi untuk pengguna
            $text = "Informasi PENGURANGAN saldo\n";
            $text .= "==========================\n";
            $text .= "ID WA       : $id\n";
            $text .= "Username    : $nama\n";
            $text .= "Status      : Berhasil \n";
            $text .= "Nominal     : " . rupiah($jumlah) . " \n";
            $text .= "Saldo Awal  : " . rupiah($saldoawal) . " \n";
            $text .= "Saldo Akhir : " . rupiah($saldo) . " \n";
            $text .= "Outletid    : " . $id_own . "\n";
            $text .= "==========================\n";

            return $text; // Kembalikan pesan informasi
        } else {
            return "Informasi PENGURANGAN saldo\n==========================\nID WA : $id\nStatus : Database error\n==========================\n";
        }
    } else {
        return "Saldo tidak mencukupi untuk melakukan pengurangan.\n";
    }
}

function creseller($id) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
      	'type',
      	'status',
		'voucher_terjual',
		'jumlah_debit_terjual',
      	'saldo',
		'keterangan',
	],[ 
		'id_user'	=> $id,
	]);
	return $data;
}

function lreseller() {
	global $mikbotamdata;
	$data = $mikbotamdata->select('re_settings', [
		'id_user',
		'nama_seller',
      	'type',
      	'status',
		'keterangan',
	],[ 
		'ORDER' => [ 'nama_seller' => 'ASC' ]
	]);
	return $data;
}
function cdata($nomor) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'nama_seller',
      	'status',
		'keterangan',
	], [
		'id_user' => $nomor,
	]);
	return $data;
}

function cakses($nomor) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'id_user',
		'status',
	], [
		'id_user' => $nomor,
	]);
  $data1=$data['status'];
  return $data1;
}

function apkaktifasi($id, $mkey) {
	global $mikbotamdata;
	$data = $mikbotamdata->update('re_settings', [
		'status'	=> $mkey,
	], [
		'id_user' => $id,
	]);
	$tulis   = "Nomor Anda behasil di Aktifasi ke system kami.\n\n";
	$tulis  .= "Terimakasih dan Selamat ".sapaan();
	return $tulis;
}

function apkdaftar($id, $nama, $router, $mkey, $mprefix) {
	global $mikbotamdata;
	$test = $mikbotamdata->get('re_settings', [
		'nama_seller',
	], [
		'id_user' => $id,
	]);
	$tulis = "Selamat...\n\nID WA : ".$id."\nNama : ".$nama."\n\n";
	if (empty($test['nama_seller'])) {
		$data = $mikbotamdata->insert('re_settings', [
			'id_user'		=> $id,
			'nama_seller' 	=> $nama,
			'saldo'			=> 0,
			'settings' 		=> "0/1/2/3//5/6/7/8/9/A",
			'type'			=> $mprefix,
			'status'		=> $mkey,
			'voucher_terjual'	=> 0,
			'jumlah_debit_terjual'	=> 0,
			'keterangan'=> $router,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
		]);
		if ($saldo<>0) {
			$tulis  .= "Telah masuk dalam data.\nSelamat anda mendapatkan Saldo Awal ".rupiah($saldo)." GRATIS.\n\n";
		}else{
			$tulis  .= "Telah masuk dalam data kami.\n\n";
		}
	}else{
		$data = $mikbotamdata->update('re_settings', [
			'nama_seller' => $nama,
			'type'		=> $mprefix,
			'status'	=> $mkey,
			'keterangan'=> $router,
		], [
			'id_user' => $id,
		]);
		$tulis  .= "Berhasil diupdate.\n\n";
	}
	$tulis  .= "Terimakasih dan Selamat ".sapaan();
	return $tulis;
}
function apkdaftars($id, $nama, $router, $mkey, $mprefix) {
	global $mikbotamdata;
	$test = $mikbotamdata->get('re_settings', [
		'nama_seller',
	], [
		'id_user' => $id,
	]);
	$tulis = "Selamat...\n\nID WA : ".$id."\nNama : ".$nama."\n\n";
	if (empty($test['nama_seller'])) {
		$data = $mikbotamdata->insert('re_settings', [
			'id_user'		=> $id,
			'nama_seller' 	=> $nama,
			'saldo'			=> 0,
			'settings' 		=> "0/1/2/3//5/6/7/8/9/A",
			'type'			=> $mprefix,
			'status'		=> $mkey,
			'voucher_terjual'	=> 0,
			'jumlah_debit_terjual'	=> 0,
			'keterangan'=> $router,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
		]);
		if ($saldo<>0) {
			$tulis  .= "Telah masuk dalam data.\nSelamat anda mendapatkan Saldo Awal ".rupiah($saldo)." GRATIS.\n\n";
		}else{
			$tulis  .= "Telah masuk dalam data kami.\n\n";
		}
	}else{
		$data = $mikbotamdata->update('re_settings', [
			'nama_seller' => $nama,
			'status'	=> $mkey,
			'keterangan'=> $router,
		], [
			'id_user' => $id,
		]);
		$tulis  .= "Berhasil diupdate.\n\n";
	}
	$tulis  .= "Terimakasih dan Selamat ".sapaan();
	return $tulis;
}

function nomor($xnomor) {
	if (substr($xnomor,0,1)=="0") {
		$xnomor="62".substr($xnomor,1,strlen($xnomor)-1);
	}
	return $xnomor;
}

function manipulasiTanggal($tgl,$jumlah=1,$format='days'){
	$currentDate = $tgl;
	return date('m-Y', strtotime($jumlah.' '.$format, strtotime($currentDate)));
}
function manipulasiTanggal1($tgl,$jumlah=1,$format='days'){
	$currentDate = $tgl;
	return date('M-Y', strtotime($jumlah.' '.$format, strtotime($currentDate)));
}
function manipulasiTanggal2($tgl,$jumlah=1,$format='days'){
	$currentDate = $tgl;
	return date('d-M', strtotime($jumlah.' '.$format, strtotime($currentDate)));
}
function manipulasiTanggal3($tgl,$jumlah=1,$format='days'){
	$currentDate = $tgl;
	return date('d/m/Y', strtotime($jumlah.' '.$format, strtotime($currentDate)));
}
function manipulasiTanggal4($tgl,$jumlah=1,$format='days'){
	$currentDate = $tgl;
	return date('M/d/Y', strtotime($jumlah.' '.$format, strtotime($currentDate)));
}

function wacupdate() {
	$hasil="OK|";
	$mfile="../include/config.php";
	$berita="";
	if (file_exists($mfile)) {
		$misi	= file_get_contents($mfile);
		$misi0	= explode("[",$misi);
		$jr		= count($misi0);
		$kirim	="";
		$cek="";
		$API = new routeros_api();
		$mtulis="";
		$updateok="1";
		$mfdserver="";
		for ($i = 3; $i < $jr; $i++) {
			$misi1	= explode("'",$misi0[$i]);
			$router	= $misi1[1];
			$mtulis .= $router;
			$ipr1	= explode($router,$misi0[$i]);
			$ipr	= substr($ipr1[2],1,-3);
			$unr	= substr($ipr1[3],3,-3);
			$pwr	= decrypt(substr($ipr1[4],3,-3));
			$pwrr	= substr($ipr1[4],3,-3);
			$mfdserver=$router."|".$ipr."|".$unr."|".$pwrr."#\n ";
			if ($API->connect(explode(":",$ipr)[0], $unr, $pwr, explode(":",$ipr)[1])){
				$getprofile = $API->comm("/ip/hotspot/user/profile/print");
				$TotalReg = count($getprofile);
				for ($ii = 0; $ii < $TotalReg; $ii++) {
					$vcr=$ii+0;
					$profile 	= $getprofile[$ii];
					$mcet 		=$profile['name'];
					$idpro		=str_replace("*","",$profile['.id']);
					$kvcr		="/P".$vcr;
					$speed 		=$profile['rate-limit'];
					$spead		=explode(" ",$speed);
					$spaed		=$spead[0];
					$mhrg0		=$profile['on-login'];
					$mhrg1		=explode(',',$mhrg0);
					$modal		=$mhrg1[2];
					$mpak 		=$mhrg1[3];
					$mhrg 		=$mhrg1[4];
					$limituptime=$mpak;
					switch ($limituptime) {
						case null:
							$limituptimereal = '00:00:00';
						case '00:00:00':
							$limituptimereal = '00:00:00';
						default:
							$limituptimereal = $limituptime;
						if (strpos(strtolower($limituptimereal), 'h') !== false) {
							$uptime = str_replace('h', ' Jam', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'd') !== false) {
							$uptime = str_replace('d', ' Hari', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'w') !== false) {
							$uptime = str_replace('w', ' Minggu', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'm') !== false) {
							$uptime = str_replace('m', ' Menit', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'y') !== false) {
							$uptime = str_replace('y', ' Tahun', $limituptime);
						}
						if ($modal>1) {
							$mtulis .="*\n".$mcet."|".$uptime."|".$modal."|".$mhrg."|".$mpak."|".$router."|".$spaed."|".$idpro."|".$kvcr;
						}
					}
				}
				$filed = '../webhook/dserver.php';
				$handle = fopen($filed, 'w') or die('Cannot open file:  ' . $filed);
				fwrite($handle, $mfdserver);
				fclose($handle);
				
				$mtulis .="#\n\n";
		
				$filec = '../webhook/dvoucher.php';
				$handle = fopen($filec, 'w') or die('Cannot open file:  ' . $filec);
				fwrite($handle, $mtulis);
				fclose($handle);

				$hasil .= "PEMUTAKHIRAN DATA VOUCHER. BERHASIL DILAKUKAN\n\nSELAMAT BERAKTIFITAS DAN TERIMAKASIH.";
				
			}else{
				$hasil 	= "Tidak dapat terhubung ke router ".$router."\n";
				$hasil 	.="GAGAL MELAKUKAN PEMUTAKHIRAN DATA VOUCHER.\n\nCEK KONEKSI DAN ROUTER ANDA.\n\nTUNGGU 5 MENIT DAN LAKUKAN UPDATE KEMBALI.";
			}
		}
	}else{
		$hasil = "File config Mikhmon tidak ditemukan.\n";
		$hasil .="GAGAL MELAKUKAN PEMUTAKHIRAN DATA VOUCHER.\n\nCEK KONEKSI DAN ROUTER ANDA.\n\nTUNGGU 5 MENIT DAN LAKUKAN UPDATE KEMBALI.";
	}
	return $hasil;
}

function cupdate() {
	$hasil="OK";
	$mfile="../include/config.php";
	$berita="";
	if (file_exists($mfile)) {
		$misi	= file_get_contents($mfile);
		$misi0	= explode("[",$misi);
		$jr		= count($misi0);
		$kirim	="";
		$cek="";
		$API = new routeros_api();
		$mtulis="";
		$updateok="1";
		$mfdserver="";
		for ($i = 3; $i < $jr; $i++) {
			$misi1	= explode("'",$misi0[$i]);
			$router	= $misi1[1];
			$mtulis .= $router;
			$ipr1	= explode($router,$misi0[$i]);
			$ipr	= substr($ipr1[2],1,-3);
			$unr	= substr($ipr1[3],3,-3);
			$pwr	= decrypt(substr($ipr1[4],3,-3));
			$pwrr	= substr($ipr1[4],3,-3);
			$mfdserver=$router."|".$ipr."|".$unr."|".$pwrr."#\n ";
			if ($API->connect(explode(":",$ipr)[0], $unr, $pwr, explode(":",$ipr)[1])){
				$getprofile = $API->comm("/ip/hotspot/user/profile/print");
				$TotalReg = count($getprofile);
				for ($ii = 0; $ii < $TotalReg; $ii++) {
					$vcr=$ii+0;
					$profile 	= $getprofile[$ii];
					$mcet 		=$profile['name'];
					$idpro		=str_replace("*","",$profile['.id']);
					$kvcr		="/P".$vcr;
					$speed 		=$profile['rate-limit'];
					$spead		=explode(" ",$speed);
					$spaed		=$spead[0];
					$mhrg0		=$profile['on-login'];
					$mhrg1		=explode(',',$mhrg0);
					$modal		=$mhrg1[2];
					$mpak 		=$mhrg1[3];
					$mhrg 		=$mhrg1[4];
					$limituptime=$mpak;
					switch ($limituptime) {
						case null:
							$limituptimereal = '00:00:00';
						case '00:00:00':
							$limituptimereal = '00:00:00';
						default:
							$limituptimereal = $limituptime;
						if (strpos(strtolower($limituptimereal), 'h') !== false) {
							$uptime = str_replace('h', ' Jam', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'd') !== false) {
							$uptime = str_replace('d', ' Hari', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'w') !== false) {
							$uptime = str_replace('w', ' Minggu', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'm') !== false) {
							$uptime = str_replace('m', ' Menit', $limituptime);
						} elseif (strpos(strtolower($limituptime), 'y') !== false) {
							$uptime = str_replace('y', ' Tahun', $limituptime);
						}
						if ($modal>1) {
							$mtulis .="*\n".$mcet."|".$uptime."|".$modal."|".$mhrg."|".$mpak."|".$router."|".$spaed."|".$idpro."|".$kvcr;
						}
					}
				}
				
			}else{
				$hasil ="NO";
				$berita .="Tidak dapat terhubung ke router ".$router."\n";
			}
			$mtulis .="#\n\n";
		}
	}else{
		$berita	.="File config Mikhmon tidak ditemukan.\n";
	}
	if ($hasil=="NO")	{
		if (!empty(capi1(3))) {
			$text= "GAGAL MELAKUKAN PEMUTAKHIRAN DATA VOUCHER.\n\nCEK KONEKSI DAN ROUTER ANDA.\n\nTUNGGU 5 MENIT DAN LAKUKAN UPDATE KEMBALI.";
			$cek =sendMessage(capi1(0), $text, capi1(3));
		}else{
			$berita	.="Token Bot bekum diisi.\n";
		}
	}else{
		$filed = '../webhook/dserver.php';
		$handle = fopen($filed, 'w') or die('Cannot open file:  ' . $filed);
		fwrite($handle, $mfdserver);
		fclose($handle);
		
		
		$filec = '../webhook/dvoucher.php';
		$handle = fopen($filec, 'w') or die('Cannot open file:  ' . $filec);
		fwrite($handle, $mtulis);
		fclose($handle);
		if (!empty(capi1(3))) {
			$text= "PEMUTAKHIRAN DATA VOUCHER. BERHASIL DILAKUKAN\n\nSELAMAT BERAKTIFITAS DAN TERIMAKASIH.";
			$cek =sendMessage(capi1(0), $text, capi1(3));
		}
	}
	$hasil = $hasil."|".$berita;
	return $hasil;
}


function cdataid($mid) {
	
	$hasil="Belum di buat";
	return $hasil;
}


function catat($idx,$userx,$namax) {   
	$file="../webhook/data/dtuser.txt";
	$mtgl=date('d/m/Y');
	$mtime=date('H:i:s');
	$tulis="1";
	$hasil	= "0";
	if (file_exists($file)) {
		$misi		= file_get_contents($file);
		$misi0		= explode("#",$misi);
		for ($i=0 ; $i < count($misi0) ; $i++) {
			$misi1	=explode("|",$misi0[$i]);
			if ($misi1[0]==$idx) {$tulis="0";}
		}
	}
	if ($tulis=="1")  {
		$mtulis=$idx."|".$userx."|".$namax."|".$mtgl." ".$mtime."|3000# \n";
		file_put_contents($file, $mtulis, FILE_APPEND | LOCK_EX);
		fclose($handle);
		$hasil = "1";
	}
return $hasil;
}

function capi1($cari) {
	$file 	= 'webhookk.php';
	$hasil	= ltrim(explode("|",file_get_contents($file))[$cari]);
	return $hasil;
}


function webhookk() {   
	$file="../webhook/webhookk.php";
	$hasil="Tidak Ada|Tidak Ada|Tidak Ada|Tidak Ada|";
	if (file_exists($file)) {
		$misix		= file_get_contents($file);
		$misi0		= explode("|",$misix);
		$hasil = $misi0[0]."|".$misi0[1]."|".$misi0[2]."|".$misi0[3]."|".$misi0[4]."|".$misi0[5];
	}
return $hasil;
}

function cbot1() {   
	$file="./webhook/webhookk.php";
	if (file_exists($file)) {
		$misix		= file_get_contents($file);
		$misi0		= explode("|",$misix);
		$idtele		= $misi0[0];
		$namatele	= $misi0[1];
		$bottele	= $misi0[2];
		$tokentele	= $misi0[3];
	}
	$hasil = $bottele;		
return $hasil;
}

function cbot() {   
	$file="../webhook/webhookk.php";
	if (file_exists($file)) {
		$misix		= file_get_contents($file);
		$misi0		= explode("|",$misix);
		$idtele		= $misi0[0];
		$namatele	= $misi0[1];
		$bottele	= $misi0[2];
		$tokentele	= $misi0[3];
	}
	$hasil = $bottele;		
return $hasil;
}

function kuser($panjang) {   
  $karakter = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';     
  $string = '';   
  for($i = 0; $i < $panjang; $i++) {   
     $pos = rand(0, strlen($karakter)-1);   
     $string .= $karakter{$pos};   
  }
  $string=trim($string);
return $string;   
}   

function rupiah($angka) {
	$hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}

function cdatabot($xfile) {
	$mfile	="./webhook/data/".$xfile;
	$mdata	= file_get_contents($mfile);
	$hasil	= explode("#",$mdata);
	return $hasil;
}

function cdatadt1($xfile,$xtele,$xtgl,$xprof) {
	$hasil="";
	if (!file_exists($xfile)) {
			$hasil="Penjualan bulan ".$xtgl.".\n\n<b>TIDAK DITEMUKAN.</b>\n";
	}else{
		$mdata	= file_get_contents($xfile);
		$misi	= explode("#",$mdata);
		if ($xtele=="") {
			$tvcr	=0;
			$hmodal	=0;
			$hjual	=0;
			$hasil 	= "Detail sales bulan ".$xtgl."\n";
			$hasil	.="<b>".$xprof."</b>\n";
			$hasil  .="=============================\n";
			$hasil 	.="<b>PAKET</b>\n";
			$hasil	.=" <b>QTY      HARGA-1   HARGA-2</b>\n";
			$hasil 	.="---------------------------------------------------------";
			$paket=explode("*",cvoucher($xprof));			
			for ( $i =1 ; $i < count($paket); $i++ ) {
				$paket0=explode("|",$paket[$i]);
				$jvcr=0;
				$hmodald	=0;
				$hjuald	=0;
				for ( $ii = 0 ; $ii < count($misi); $ii++ ) {
					$misi1=explode("|",$misi[$ii]);
					if (trim($paket0[0])==trim($misi1[5])) {
						$jvcr++;
						$tvcr++;
						$hmodald = $hmodald+$misi1[7];
						$hjuald = $hjuald+$misi1[8];
						$hmodal = $hmodal+$misi1[7];
						$hjual = $hjual+$misi1[8];
					}
				}
				if ($tvcr<>0)  {
					$hasil .= $paket0[0]."\n".$jvcr." Vcr.   ".rupiah($hmodald)."   ".rupiah($hjuald)."\n";
					$hasil .="--------------------------------------------------------\n";
				}
			}
			$hasil	.="<b>Total Sales ".$xtgl."</b>\n";
			$hasil 	.="<b>VOUCHER	".$tvcr." Lembar.</b>\n";
			$hasil	.="<b>".rupiah($hmodal)."   ".rupiah($hjual)."</b>\n";
		}else{
			$tvcr	=0;
			$hmodal	=0;
			$hjual	=0;
			$hasil 	= "Detail sales bulan ".$xtgl."\n";
			$hasil	.="<b>".$xtele."</b>\n";
			$hasil	.="<b>".$xprof."</b>\n";
			$hasil  .="=============================\n";
			$hasil 	.="<b>PAKET</b>\n";
			$hasil	.=" <b>QTY      HARGA-1   HARGA-2</b>\n";
			$hasil 	.="---------------------------------------------------------";
			$paket=explode("*",cvoucher($xprof));			
			for ( $i =1 ; $i < count($paket); $i++ ) {
				$paket0=explode("|",$paket[$i]);
				$jvcr=0;
				$hmodald	=0;
				$hjuald	=0;
				for ( $ii = 0 ; $ii < count($misi); $ii++ ) {
					$misi1=explode("|",$misi[$ii]);
					if (trim($paket0[0])==trim($misi1[5])) {
						if ($xtele==$misi1[0]) {
							$jvcr++;
							$tvcr++;
							$hmodald = $hmodald+$misi1[7];
							$hjuald = $hjuald+$misi1[8];
							$hmodal = $hmodal+$misi1[7];
							$hjual = $hjual+$misi1[8];
						}
					}
				}
				if ($jvcr<>0)  {
					$hasil .= $paket0[0]."\n".$jvcr." Vcr.   ".rupiah($hmodald)."   ".rupiah($hjuald)."\n";
					$hasil .="--------------------------------------------------------\n";
				}
			}
			$hasil	.="<b>Total Sales ".$xtgl."</b>\n";
			$hasil 	.="<b>VOUCHER	".$tvcr." Lembar.</b>\n";
			$hasil	.="<b>".rupiah($hmodal)."   ".rupiah($hjual)."</b>\n";
		}
	}
	return $hasil;
}

function cdatadt($xfile,$xtele,$xtgl,$xprof) {
	$hasil="";
	if (!file_exists($xfile)) {
			$hasil="File tidak ditemukan.#";
	}else{
		$mdata	= file_get_contents($xfile);
		$misi	= explode("#",$mdata);
		if ($xtele=="") {
			$no		=0;
			$hmodal	=0;
			$hjual	=0;
			$hasil 	= "Detail sales tanggal ".$xtgl."\n";
			$hasil	.="<b>".$xprof."</b>\n";
			$hasil	.="=============================\n";
			$hasil	.="  <b>ID TELE   VOUCHER       Aktif</b>\n";
			$hasil 	.="---------------------------------------------------------";
			for ( $i = 0 ; $i < count($misi); $i++ ) {
				$misi1=explode("|",$misi[$i]);
				if ($misi1[2]==$xtgl) {
					$no++; 
					$hmodal = $hmodal+$misi1[7];
					$hjual = $hjual+$misi1[8];
					$hasil .=$misi1[0]." ".trim($misi1[6])." ".$misi1[9]."";  
				}
			}
			if ($no<>0) {
				$hasil .="\n=============================\n";
				$hasil .=" Total ".$no." Vcr.\n";
				$hasil .=" Hrg Awal ".rupiah($hmodal)."\n";
				$hasil .=" Hrg  Jual ".rupiah($hjual)."\n";
				$hasil .="============================= \n";
			}
			$paket=explode("*",cvoucher($xprof));			
			for ( $i =1 ; $i < count($paket); $i++ ) {
				$paket0=explode("|",$paket[$i]);
				$jvcr=0;
				$hmodald	=0;
				$hjuald	=0;
				for ( $ii = 0 ; $ii < count($misi); $ii++ ) {
					$misi1=explode("|",$misi[$ii]);
					if ($misi1[2]==$xtgl) {
						if (trim($paket0[0])==trim($misi1[5])) {
							$jvcr++;
							$hmodald = $hmodald+$misi1[7];
							$hjuald = $hjuald+$misi1[8];
						}
					}
				}
				if ($jvcr<>0)  {
					$hasil .= $paket0[0]."\n".$jvcr." Vcr.   ".rupiah($hmodald)."   ".rupiah($hjuald)."\n";
					$hasil .="--------------------------------------------------------\n";
				}
			}
			if ($no==0)  {
				$hasil .="<b>Tidak ada penjualan</b>\n";
			}else{
					$hasil .=$no." Vcr.   ".rupiah($hmodal)."   ".rupiah($hjual)."\n";
				
			}
		}else{
			$no		=0;
			$hmodal	=0;
			$hjual	=0;
			$hasil  ="=============================\n";
			$hasil .="   <b>JAM    VOUCHER       Aktif</b>\n";
			$hasil .="----------------------------------------------------------\n";
			for ( $i = 0 ; $i < count($misi); $i++ ) {
				$misi1=explode("|",$misi[$i]);
				if ($misi1[2]==$xtgl) {
					if ($misi1[0]==$xtele) {
						$no++; 
						$hmodal = $hmodal+$misi1[7];
						$hjual = $hjual+$misi1[8];
						$hasil .= $misi1[3]." ".trim($misi1[6])."  ".$misi1[10]."\n";  
					}
				}
			}
			if ($no<>0) {
				$hasil .="=============================\n";
				$hasil .=" Total ".$no." Vcr.\n";
				$hasil .=" Hrg Awal ".rupiah($hmodal)."\n";
				$hasil .=" Hrg  Jual ".rupiah($hjual)."\n";
				$hasil .="============================= \n";
			}
			$paket=explode("*",cvoucher($xprof));			
			for ( $i =1 ; $i < count($paket); $i++ ) {
				$paket0=explode("|",$paket[$i]);
				$jvcr=0;
				$hmodald	=0;
				$hjuald	=0;
				for ( $ii = 0 ; $ii < count($misi); $ii++ ) {
					$misi1=explode("|",$misi[$ii]);
					if ($misi1[2]==$xtgl) {
						if (trim($paket0[0])==trim($misi1[5])) {
							if ($misi1[2]==$xtgl) {
								if ($misi1[0]==$xtele) {
									$jvcr++;
									$hmodald = $hmodald+$misi1[7];
									$hjuald = $hjuald+$misi1[8];
								}
							}
						}
					}
				}
				if ($jvcr<>0)  {
					$hasil .= $paket0[0]."\n".$jvcr." Vcr.   ".rupiah($hmodald)."   ".rupiah($hjuald)."\n";
					$hasil .="--------------------------------------------------------\n";
					$hasil .=" TOTAL :\n";
					$hasil .=$no." Vcr.   ".rupiah($hmodal)."   ".rupiah($hjual)."\n";
				}
			}
			if ($jvcr==0)  {
				$hasil .="<b>Tidak ada penjualan</b>\n";
			}
		}
	}
	return $hasil;
}

function cdatadt0($xfile,$xtele,$xtgl,$xprof) {
	$hasil="";
	if (!file_exists($xfile)) {
			$hasil="File tidak ditemukan.#";
	}else{
		$mdata	= file_get_contents($xfile);
		$misi	= explode("#",$mdata);
		if ($xtele=="") {
			$no=1;
			for ( $i = 0 ; $i < count($misi); $i++ ) {
				$misi1=explode("|",$misi[$i]);
				if ($misi1[2]==$xtgl) {
//					$hasil .=$misi[$i]."#";
					$hasil .= $misi1[0]." ".trim($misi1[6])." ".$misi1[9]." ";  
					$no++;
				}
			} 
			$paket=explode("*",cvoucher($xprof));			
			for ( $i =1 ; $i < count($paket); $i++ ) {
				$paket0=explode("|",$paket[$i]);
				
				$hasil .=$paket0[0]." ";
			}
		}else{
			$hasil	.="User Belum#";
		}
	}
	return $hasil;
}
function cdatabot0($xfile,$xtele) {
	if (!file_exists($xfile)) {
			$hasil="File||Tidak||Ada||||||#";
	}else{
		$mdata	= file_get_contents($xfile);
		$misi	= explode("#",$mdata);
		if ($xtele=="") {
			$hasil=$mdata;
		}else{
			$hasil="";
			for ($i=0 ; $i < count($misi)-1 ; $i++) {
				$misi1=explode("|",$misi[$i]);
				if ($misi1[0]==$xtele) {
					$hasil .=$misi[$i]."#";
				}
			}	
		}
	}
	return $hasil;
}
function cdatabot1($xfile) {
	$mfile	="./data/".$xfile;
	$mdata	= file_get_contents($mfile);
	$hasil	= explode("#",$mdata);
	return $hasil;
}
function cdatabot2($xfile) {
	$mfile	="./webhook/data/".$xfile;
	$mdata	= file_get_contents($mfile);
	$hasil	= explode("#",$mdata);
	return $hasil;
}

function cvoucher($xcek) {
	$misi	= file_get_contents("../webhook/dvoucher.php");
	$misi0	= explode("#",$misi);
	$jr		= count($misi0)-1;
	$kirim	=$jr."\n";
	for ($i = 0; $i < $jr; $i++) {
		$cek0	= explode("*",$misi0[$i]);
		$cek 	= trim($cek0[0]);
		if ($cek==$xcek) {
			$kirim .=trim($misi0[$i]);
		}
	}
	$hasil	= $kirim;
	return $hasil;
}

function cvoucher1($xcek) {
	$misi	= file_get_contents("./webhook/dvoucher.php");
	$misi0	= explode("#",$misi);
	$jr		= count($misi0)-1;
	$kirim	=$jr."\n";
	for ($i = 0; $i < $jr; $i++) {
		$cek0	= explode("*",$misi0[$i]);
		$cek 	= trim($cek0[0]);
		if ($cek==$xcek) {
			$kirim .=trim($cek0[$i]);
		}
	}
	$hasil	= $kirim;
	return $hasil;
}

function cvdetail($xrouter, $xvcr) {
	$misi	= file_get_contents("../webhook/dvoucher.php");
	$misi0	= explode("#",$misi);
	$jr		= count($misi0)-1;
	$kirim	="";
	for ($i = 0; $i < $jr; $i++) {
		$cek0	= explode("*",$misi0[$i]);
		$cek 	= trim($cek0[0]);
		if ($cek==$xrouter) {
			$cdtv	=$misi0[$i];
			$cdtv0	=explode("*",$cdtv);
			for ($ii = 0; $ii < count($cdtv0); $ii++) {
				$cdtv1 	=explode("|",$cdtv0[$ii]);
				if (trim($cdtv1[0])==$xvcr) {
					$kirim =trim($cdtv0[$ii])."\n";
				}
			}
		}
	}
	$hasil	= $kirim;
	return $hasil;
}

function crouter1($mrouter) {
	$misi	= file_get_contents("../include/config.php");
	$misi0	= explode("[",$misi);
	$jr		= count($misi0);
	$kirim	="";
	for ($i = 3; $i < $jr; $i++) {
		$misi1	= explode("'",$misi0[$i]);
		$router	= $misi1[1];
		if ($router==$mrouter) {
			$ipr1	= explode($router,$misi0[$i]);
			$ipr	= substr($ipr1[2],1,-3);
			$unr	= substr($ipr1[3],3,-3);
			$pwr	= substr($ipr1[4],3,-3);
			$kirim	.=$router."|".$ipr."|".$unr."|".$pwr."|";
		}	
	}	
	$hasil	= $kirim;
	return $hasil;
}

function ciprouter($cariip) {
	$misi	= file_get_contents("../webhook/dserver.php");
	$misi0	= explode("#",$misi);
	$kirim	="";
	for ($i = 0; $i < count($misi0)-1; $i++) {
		$misi1	= explode("|",$misi0[$i]);
		$iprouter	= $misi1[1];
		if ($iprouter==$cariip) {
			$kirim	= $misi0[$i];
		}	
	}	
	$hasil	= $kirim;
	return $hasil;
}

function crouter() {
	$misi	= file_get_contents("../include/config.php");
	$misi0	= explode("[",$misi);
	$jr		= count($misi0);
	$kirim	="";
	for ($i = 3; $i < $jr; $i++) {
		$misi1	= explode("'",$misi0[$i]);
		$router	= $misi1[1];
		$ipr1	= explode($router,$misi0[$i]);
		$ipr	= substr($ipr1[2],1,-3);
		$unr	= substr($ipr1[3],3,-3);
		$pwr	= substr($ipr1[4],3,-3);
		$kirim	.=$router."|".$ipr."|".$unr."|".$pwr."!";
	}	
	$hasil	= $kirim;
	return $hasil;
}

function setwebhook($urlpath,$token) {
	$url = "https://api.telegram.org/bot".$token."/setWebhook";

	$ch = curl_init($url);
	$post_data = [
		"url" => $urlpath,
	];

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	return $result;
}

function unssetwebhook($token) {
	$url = file_get_contents("https://api.telegram.org/bot".$token."/setWebhook");

	return $url;
}

function getWebhookInfo($token) {
	$url = file_get_contents("https://api.telegram.org/bot".$token."/getWebhookInfo");

	return $url;
}

function info() {
	$getdata=file_get_contents('https://download.mikbotam.net/scari.php?Runing');
echo  $getdata;
}

function  Version() {
	$getdata=file_get_contents('https://download.mikbotam.net/scari.php?Version');
echo  $getdata;
}

function sendMessage($id, $text, $token) {
	$website = "https://api.telegram.org/bot" . $token;
	$params = [
		'chat_id' => $id,
		'text' => $text,
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [
                ['text' => 'My Home Page', 'url' => 'https://mimoassist.homes/'],
            ], 
		]]),
		'parse_mode' => 'html',
	];
	$ch = curl_init($website . '/sendMessage');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function sendWa($nomor,$pesan) {
	$psn=$pesan;
	$ffile1="./notif/kirim.txt";
	$ffile2="./notif/setup.set";

	$cek1	=explode('-',file_get_contents($ffile1))[0];
	$dtapi	= explode('|-|',file_get_contents($ffile2));

	$device=$dtapi[3];
	$token=$dtapi[4];

	if ($cek1=='2') {
		$device=$dtapi[3];
		$token=urlencode($dtapi[4]);
		$hasil=$pesan;
		$pesan=urlencode($pesan);
		$data="proses=2&nomor=$nomor&device=$device&token=$token&pesan=$pesan";
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
				"Accept: */*",
				"Content-Type: application/json",
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$hasil = "cURL Error #:" . $err;
		} else {
			if ($response=='') {
				$hasil = 'Proses kirim pesan GAGAL.';
			}else{
				$hasil = $response;
			}
		}
	}
	return $hasil;
}

function cbulan($xbulan,$hari) {
	$xket=substr($xbulan,0,3);
	if ($xket=="jan") {
		$mket="01";
	}elseif ($xket=="feb") {
		$mket="02";
	}elseif ($xket=="mar") {
		$mket="03";
	}elseif ($xket=="apr") {
		$mket="04";
	}elseif ($xket=="mei") {
		$mket="05";
	}elseif ($xket=="jun") {
		$mket="06";
	}elseif ($xket=="jul") {
		$mket="07";
	}elseif ($xket=="agu") {
		$mket="08";
	}elseif ($xket=="sep") {
		$mket="09";
	}elseif ($xket=="oct") {
		$mket="10";
	}elseif ($xket=="nov") {
		$mket="11";
	}elseif ($xket=="dec") {
		$mket="12";
	}
	$xtgl1=date('m/d/Y');
	$xtgl=$xtgl1;
	$xbulan=$mket.substr($xbulan,3,8);
	$msel=strtotime($xtgl)-strtotime($xbulan);
	if ($msel < 1 ) {
		$ket="Ganti Tanggal commnet,tambah sales di scrip";
		$rub="1";
	}else{
		$ket="Buat Vucher Baru";
		$rub="0";
	}
	return $xbulan;
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
	return $jam1;
}

function sapaan1() {
	$jam0=date(H);
	if ($jam0>4 and $jam0<9){
        $jam1='Semoga di pagi hari ini kita diberikan kesehatan dan rejeki yang berlimpah.  Amiiin.';
	}elseif ($jam0>8 and $jam0<14){
      	$jam1='Jangan melupakan makan siang, silahkan untuk mengunjungi tempat makan terdekat. Xixixixixi.';
	}elseif ($jam0>13 and $jam0<19){
      	$jam1='Ada yang bisa saya bantu.?';
	}else{
      	$jam1='Ada yang bisa saya bantu.?';
	}
	return $jam1;
}

//function kirimwa($tujuan, $pesan) {
//	$whatsva = new Whatsva();
//	$instance_key = capiwa();
//	$jid = $tujuan;
//	$message = $pesan;
//	$sendMessage = $whatsva->sendMessageText($instance_key,$jid,$message);
//	return $sendMessage;
//}

function kirimwa1($tujuan, $pesan) {
	$whatsva = new Whatsva();
	$instance_key= capiwa();
	$jid = $tujuan;
	$message = $pesan;
    $imageUrl = "https://mimoassist.homes/img/logobot1.png";
	$sendMessage = $whatsva->sendImageUrl($instance_key, $jid, $imageUrl, $message);
	return $sendMessage;
}

function apkkirimwa($tujuan, $pesan) {
	$whatsva = new Whatsva();
	$instance_key = apkcapiwa();
	$jid = $tujuan;
	$message = $pesan;
	$sendMessage = $whatsva->sendMessageText($instance_key,$jid,$message);
	return $sendMessage;
}

function apkkirimwa1($tujuan, $pesan) {
	$whatsva = new Whatsva();
	$instance_key= apkcapiwa();
	$jid = $tujuan;
	$message = $pesan;
    $imageUrl = "https://mimoassist.homes/img/logobot1.png";
	$sendMessage = $whatsva->sendImageUrl($instance_key, $jid, $imageUrl, $message);
	return $sendMessage;
}

function dtidwa($dt) {
	$mfile="../webhook/idwa.txt";
	if (file_exists($mfile)) {
		$isi	= explode("|",file_get_contents($mfile));
		$data	=$isi[$dt];
	}else{
		$data	="";
	}
	return $data;
}

function capiwa() {
	$mfile = "./webhook/idwa.txt";
	if (file_exists($mfile)) {
		$misi	= explode("|",file_get_contents($mfile));
		$nowa	=$misi[0];
		$apiwa	=$misi[1];
	}else{
		$nowa	="";
		$apiwa	="";
	}
	return $apiwa;
}

function apkcapiwa() {
	$mfile = "idwa.txt";
	if (file_exists($mfile)) {
		$misi	= explode("|",file_get_contents($mfile));
		$nowa	=$misi[0];
		$apiwa	=$misi[1];
	}else{
		$nowa	="";
		$apiwa	="";
	}
	return $apiwa;
}

function ktele($pesan) {
	$token="7570046381:AAHHagnrH6s2m7GjedqbD3JQO_wu02kBMBg"; 
	$pesan	=$pesan."\n".date('d-M-Y H:i:s')."\n.";
	$option = [	'text' 	=> $pesan,'chat_id'	=> '1341792914','parse_mode' => 'html',];
	$respone=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
	return $respone;
}

function kirimtele($pesan) {
	$token	= '6014190093:AAFTDaO2_1XH_pfoyQ3cDNXWmUPjidnbvBs';
	$website = "https://api.telegram.org/bot" . $token;
	$params = [
		
		'chat_id' => '1341792914',
		'text' => $pesan,
		'parse_mode' => 'html',
	];
	$ch = curl_init($website . '/sendMessage');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
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


function wacnama($nomor) {
	if (empty($nomor)) {
		return "NO NAME";
	}else{
		global $mikbotamdata;
		$data = $mikbotamdata->get('re_settings', [
			'nama_seller',
			'saldo',
		], [
			'id_user' => $nomor
		]);
	return $data['nama_seller'];
	}
}
function wacsaldo($nomor) {
	global $mikbotamdata;
	$data="Data Tidak Di Temukan.\n";
	if ($nomor=='wa') {
		$data1 = $mikbotamdata->select('re_settings', [
			'id_user',
			'nama_seller',
			'saldo',
		]);
		if (empty($data1)) {
			return $data;
		}
		$data="";
		$no=1;
		for ($x=0;$x<count($data1);$x++) {
			if (substr($data1[$x]['id_user'],0,2)=='62') {
				$data	.=$no.". Nomor Id : ".$data1[$x]['id_user']."\nNama : ".$data1[$x]['nama_seller']."\n*Saldo : ".rupiah($data1[$x]['saldo'])."* \n".lain(garis);
				$no++;
			}
		}
		return $data;
	}
	if ($nomor=='all') {
		$data1 = $mikbotamdata->select('re_settings', [
			'id_user',
			'nama_seller',
			'saldo',
		]);
		if (empty($data1)) {
			return $data;
		}
		$data="";
		$no=1;
		for ($x=0;$x<count($data1);$x++) {
			$data	.=$no.". Nomor Id : ".$data1[$x]['id_user']."\nNama : ".$data1[$x]['nama_seller']."\n*Saldo : ".rupiah($data1[$x]['saldo'])."* \n".lain(garis);
			$no++;
		}
		return $data;
	}
	if ($nomor=='tele') {
		$data1 = $mikbotamdata->select('re_settings', [
			'id_user',
			'nama_seller',
			'saldo',
		]);
		if (empty($data1)) {
			return $data;
		}
		$data="";
		$no=1;
		for ($x=0;$x<count($data1);$x++) {
			if (substr($data1[$x]['id_user'],0,2)<>'62') {
				$data	.=$no.". Nomor Id : ".$data1[$x]['id_user']."\nNama : ".$data1[$x]['nama_seller']."\n*Saldo : ".rupiah($data1[$x]['saldo'])."* \n".lain(garis);
				$no++;
			}
		}
		return $data;
	}
	$data = $mikbotamdata->get('re_settings', [
		'nama_seller',
		'saldo',
	], [
		'id_user' => $nomor
	]);
	return $data['saldo'];
}

function linka($user,$kvcr,$dns) {
	$data="http://".$dns."/login?username=".$user."%26password=".$kvcr;
	return $data;
}
function boxcode($isi,$nfile) {
	$tempdir="qrcode/img-qr/";
	if (!file_exists($tempdir))
	mkdir($tempdir, 0755);
	$file_name=$nfile.".png";	
	$file_path = $tempdir.$file_name;
	QRcode::png($isi, $file_path, "H", 5, 2);
	return $file_path;
}
function wahelp() {
	$tulis  =$dnsname."\nDaftar Command Line.\n";
	$tulis .=lain(garis);
	$tulis .="*daftar #Nama#Alamat* \nMelakukan Pendafataran\n";
	$tulis .="*deposit* \nCek Deposit & Deposit\n";
	$tulis .="*update Nama* \nUpdate Nama Pengguna.\n";
	$tulis .="*paket* \nLihat Paket Tersedia.\n";
	$tulis .="*csaldo NoHpAgen* \nCek Saldo Re_Seller\n";
	$tulis .="*ksaldo NoHpAgen Nominal* \nKirim Saldo Ke Re_Seller\n";
	$tulis .="*info* \nInfo Api.\n";
	$tulis .="*report* \nTool's Mikrotik.\n";
	$tulis .="*tools* \nTool's Mikrotik.\n";
	$tulis .=lain(garis);
	$tulis .="Terimakasih dan Selamat ".sapaan()."\n";
	return $tulis;
}
function kilat() {
$misi		= file_get_contents("dvoucher.php");
$misi0		= explode("*",explode("#",$misi)[0]);
return $misi0;
}
function has($id) {
	global $mikbotamdata;
	$data = $mikbotamdata->has('re_settings', [
		'id_user' => $id
	]);
	return $data;
}
function wainfo() {
	$info="*".kilat()[0]."* \n\nAnda belum terdaftar dalam layanan kami.\nSilahkan ketik\n*/Daftar* diikuti dengan nama kamu\nContoh  :\n*/Daftar #Nama#Alamat*\nkirim\n\nDengan melakukan pendaftaran kami berasumsi anda setuju dengan peraturan yang berlaku.\nTerimakasih dan Selamat ".sapaan()."\n";
	return $info;
}
function wabelivoucher($id, $usernamepelanggan, $princevoc,$markup, $username, $password, $uptime, $keterangan, $no) {
	global $mikbotamdata;
	$data = $mikbotamdata->get('re_settings', [
		'saldo',
		'id_user'
	], [
		'id_user' => $id

	]);

	$saldoawal = $data["saldo"];

	if ($id==$no) {
		if (isset($data)) {
			$last_id = $mikbotamdata->insert('re_operating', [
				'id_user' => $id,
				'nama_seller' => $usernamepelanggan,
				'saldo_awal' => $saldoawal,
				'saldo_akhir' => $saldoawal,
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
	}else{
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
	}
	if ($id==$no) {
		$update = $mikbotamdata->update('re_settings', [
			'saldo[-]' => 0,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
			'voucher_terjual[+]' => 1,
		], [
			'id_user' => $id,
		]);
	}else{
		$update = $mikbotamdata->update('re_settings', [
//			'saldo[-]' => $princevoc+$markup,
			'saldo[-]' => $princevoc,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
			'voucher_terjual[+]' => 1,
		], [
			'id_user' => $id,
		]);
	}
	
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

function kirimwaidbutton($tujuan,$pesan,$device,$dns,$nowa) {
$file = 'wahookk.php';
$misi		= file_get_contents($file);
$misi0		= explode("|",$misi);
$token		= ltrim($misi0[2]);

	try {
	$buttonMessage = [
		"description" => "Sedia berbagai makanan dan minuman khas Indonesia",
		"footer" => "visit https://www.mimoassist.homes",
		"buttons" => [
			[ "id" => "merah", "label" => "Merah" ],
			[ "id" => "kuning", "label" => "Kuninu" ],
			[ "id" => "hijau", "label" => "Hijau" ],
			[ "id" => "hitam", "label" => "Hitam" ],
			[ "id" => "Biru", "label" => "biru" ]
		]
	];
	$reqParams = [
		'token' => $token,
		'url' => 'https://api.kirimwa.id/v1/messages',
		'method' => 'POST',
		'payload' => json_encode([
			'message' => $buttonMessage,
			'phone_number' => $tujuan,
			'message_type' => 'buttons',
			'device_id' => $device
		], JSON_UNESCAPED_SLASHES)
	];

	$response = apiKirimWaRequest($reqParams);
	$hasil= $response['body'];
	} catch (Exception $e) {
		$hasil=($e);
	}
	return $hasil;
}

function kirimwaidlist($tujuan,$pesan,$device,$dns,$nowa) {
//$kk=ktele("1.".$tujuan."\n2.".ltrim($pesan)."\n3.".ltrim($device)."\n4.".$dns."\n5.".$nowa);
//return $kk;
$file = 'wahookk.php';
$misi		= file_get_contents($file);
$misi0		= explode("|",$misi);
$token		= ltrim($misi0[2]);

	try {
		$listMessage = [
			"title" => "Menu Depot ABC",
			"description" => "Sedia berbagai makanan dan minuman khas Indonesia",
			"footer" => "visit https://www.mimoassist.homes",
			"label_menu" => "Menu",
			"list" => [
				[
					"title" => "Layanan Internet",
					"items" => [
						[
							"id" => "id soto_ayam",
							"label" => "lab Soto Ayam Lamongan",
							"description" => "desc Perpaduan ayam kampung dan bumbu yang khas"
						],
						[
							"id" => "rawon",
							"label" => "Rawon",
							"description" => "Sup dading berwarna hitam dengan bumbu kluwek"
						],
						[
							"id" => "sate_kambing",
							"label" => "Sate Kambing",
							"description" => "Daging kambing yang empuk dan gurih dengan bumbu kecap atau kacang"
						],
						[
							"id" => "sate_kambing",
							"label" => "Sate Kambing",
							"description" => "Daging kambing yang empuk dan gurih dengan bumbu kecap atau kacang"
						]
					]
				],
				[
					"title" => "Makanan",
					"items" => [
						[
							"id" => "soto_ayam",
							"label" => "Soto Ayam Lamongan",
							"description" => "Perpaduan ayam kampung dan bumbu yang khas"
						],
						[
							"id" => "rawon",
							"label" => "Rawon",
							"description" => "Sup dading berwarna hitam dengan bumbu kluwek"
						],
						[
							"id" => "sate_kambing",
							"label" => "Sate Kambing",
							"description" => "Daging kambing yang empuk dan gurih dengan bumbu kecap atau kacang"
						]
					]
				],
				[
					"title" => "Minuman",
					"items" => [
						[
							"id" => "es_teh",
							"label" => "Es Teh",
							"description" => "Es teh dari kebun teh pilihan"
						],
						[
							"id" => "jus_jeruk",
							"label" => "Jus Jeruk",
							"description" => "Jus jeruk segar dan dingin"
						],
						[
							"id" => "air_putih",
							"label" => "Air Putih"
						]
					]
				]
			]
		];

		$reqParams = [
			'token' => $token,
			'url' => 'https://api.kirimwa.id/v1/messages',
			'method' => 'POST',
			'payload' => json_encode([
				'message' => $listMessage,
				'phone_number' => $tujuan,
				'message_type' => 'list',
				'device_id' => $device
			], JSON_UNESCAPED_SLASHES)
		];

		$response = apiKirimWaRequest($reqParams);
		$hasil=$response['body'];
	} catch (Exception $e) {
		$hasil=($e);
	}
	return $hasil;
}

function wadaftar($id, $name, $saldo) {
	$text=explode(" ",$name);
	$nama="";
	for ($i=1 ; $i<count($text); $i++) {
		$nama .=$text[$i]." ";
	}
	if (empty(ltrim($nama)))  {
		$tulis = $dnsname."\n\nFormat salah, silahkan ketik.\n\n*Daftar Nama Kamu*\nLalu kirim/send\n\nTerimakasih.";
		return $tulis;
	}
	
	$tanda="1";
	
	global $mikbotamdata;
	$test = $mikbotamdata->get('re_settings', [
		'nama_seller',
	], [
		'id_user' => $id,
	]);
	$tulis = $dnsname."\nSelamat...\n\nID WA ".$id."\nAtas Nama ".ucwords($nama)."\n\n";
	if (empty($test['nama_seller'])) {
		$data = $mikbotamdata->insert('re_settings', [
			'id_user' => $id,
			'nomer_tlp' => $id,
			'nama_seller' => ucwords($nama),
			'saldo'		=> $saldo,
			'settings' => "0/1/2/3//5/6/7/8/9/A",
			'status'	=> $tanda,
			'jumlah_debit_terjual'	=> 0,
			'Waktu' => date('H:i:s'),
			'Tanggal' => date('Y-m-d'),
		]);
		if ($saldo<>0) {
			$tulis  .= "Telah masuk dalam data.\nSelamat anda mendapatkan Saldo Awal ".rupiah($saldo)." GRATIS.\n";
		}else{
			$tulis  .= "Telah masuk dalam data kami.\n\n";
		}
	}else{
		$data = $mikbotamdata->update('re_settings', [
			'nama_seller' => ucwords($nama),
		], [
			'id_user' => $id,
		]);
		$tulis  .= "Berhasil diupdate.\n\n";
	}
	$tulis  .= "Silahkan ketik *PAKET* untuk melanjutkan transaksi..\n\nTerimkasih dan Selamat ".sapaan();
	return $tulis;
}
function encrypturl($pamerbojo) {
	$kunciobeng = '4ku4ll';
	for ($i = 0; $i < strlen($pamerbojo); $i++) {
		$buahnanas = substr($pamerbojo, $i, 1);
		$kunciinggris = substr($kunciobeng, ($i % strlen($kunciobeng)) - 1, 1);
		$buahnanas = chr(ord($buahnanas) + ord($kunciinggris));
		$serondenggosong .= $buahnanas;
	}
	return base64_encode($serondenggosong);
}

function decrypturl($pamerbojo) {
	$pamerbojo = base64_decode($pamerbojo);
	$serondenggosong = '';
	$kunciobeng = '4ku4ll';
	for ($i = 0; $i < strlen($pamerbojo); $i++) {
		$buahnanas = substr($pamerbojo, $i, 1);
		$kunciinggris = substr($kunciobeng, ($i % strlen($kunciobeng)) - 1, 1);
		$buahnanas = chr(ord($buahnanas) - ord($kunciinggris));
		$serondenggosong .= $buahnanas;
	}
	return $serondenggosong;
}

function daftar($id, $name, $alamat) {
	global $mikbotamdata;
	$last_id = $mikbotamdata->insert('re_settings', [
		'id_user' => $id,
		'nama_seller' => $name,
		'Waktu' => date('H:i:s'),
		'Tanggal' => date('Y-m-d'),
		'saldo'		=> 0,
		'settings' => "",
		'other' => $alamat,
		'jumlah_debit_terjual'	=> 0,
		'status'	=> "*"
	]);
	return $last_id;
}

function countvoucher() {
	date_default_timezone_set('Asia/Jakarta');
	global $mikbotamdata;
	$dateinput = date("Y-m-d");
	$date = date('t',strtotime($dateinput));
	$startTime = [date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y"))), date("Y-m-d", mktime(0, 0, 0, date("m"), $date, date("Y")))];

	$gethistory = $mikbotamdata->select('re_operating', [
		"keterangan",
		"Waktu",
		"Tanggal"

	], [
		'AND' => [
			'keterangan' => 'Success',
			'Tanggal[<>]' => $startTime,
		]

	]
	);
	$ech = count($gethistory);
	return $ech;
}

function getcounttopup() {
	date_default_timezone_set('Asia/Jakarta');
	$dateinput = date("Y-m-d");
	$date = date('t',strtotime($dateinput));
	$startTime = [date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y"))), date("Y-m-d", mktime(0, 0, 0, date("m"), $date, date("Y")))];

	global $mikbotamdata;
	$reportekstimasi = $mikbotamdata->sum('re_operating', [
		'top_up',
	], [
		'AND' => [
			'keterangan' => 'topup',
			'Tanggal[<>]' => $startTime,
		]

	]);

	return $reportekstimasi;
}
function estimasidata() {
	date_default_timezone_set('Asia/Jakarta');
	$dateinput = date("Y-m-d");
	$date = date('t',strtotime($dateinput));
	$startTime = [date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y"))), date("Y-m-d", mktime(0, 0, 0, date("m"), $date, date("Y")))];

	global $mikbotamdata;
	$reportekstimasi = $mikbotamdata->sum('st_reportdata', [
		'pendapatan',
	], [

		'Tanggal[<>]' =>$startTime,
	]);

	return $reportekstimasi;
}
function countuser() {
	date_default_timezone_set('Asia/Jakarta');
	$dateinput = date("Y-m-d");
	$date = date('t',strtotime($dateinput));
	$startTime = [date("Y-m-d", mktime(0, 0, 0, date("m"), 1, date("Y"))), date("Y-m-d", mktime(0, 0, 0, date("m"), $date, date("Y")))];
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

	], [
		'AND' => [

			'Tanggal[<>]' => $startTime,
		]]);

	$ech = count($data);
	return $ech;
}
function kapital($pesan) {
	$pesan=explode(".",$pesan);
	$isipesan="";
	for ($x=0;$x<count($pesan);$x++) {
		if ($x==1) {
			$isipesan .=strtoupper($pesan[$x]);
		}elseif ($x==3) {
			$isipesan .=strtoupper($pesan[$x]);
		}elseif ($x==5) {
			$isipesan .=strtoupper($pesan[$x]);
		}elseif ($x==7) {
			$isipesan .=strtoupper($pesan[$x]);
		}elseif ($x==9) {
			$isipesan .=strtoupper($pesan[$x]);
		}else{
			$isipesan .=$pesan[$x];
		}
	}
	return $isipesan;
}
function ktolol($pesan) {
	$token	= '6452559861:AAGt1zUr-oTM-fUT2E12KPpmypbR7g0qHug';
	$pesan	.= "\n".date('d-M-Y H:i:s')."\n.";
	$option = [	'text' => $pesan,'chat_id' => '1341792914','parse_mode' => 'html',];
	$respone=file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($option) );
	return $respone;
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
?>
