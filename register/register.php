<?php
date_default_timezone_set('Asia/Jakarta');
$thasil="0";
if(isset($_COOKIE['register'])) {
	$thasil="2";
}
include('function.php');
$harga=capi(15,1);
if ($harga=="-=0=-") {
	$harga=15000;
}
//die($harga);
if (isset($_POST['bayar'])) {
	$xharga=round($_POST['total']/$_POST['durasi']);
	$urlback=$_SERVER['REQUEST_SCHEME'].':/'.$_SERVER['REQUEST_URI'];

	$data	= sendtrx($_POST['pelanggan'],$_POST['email'],$_POST['nowa'],$_POST['merchand'],$_POST['produk'],$_POST['namabrg'],$_POST['domain'],$_POST['userid'],$_POST['pass'],$_POST['ros'],$_POST['durasi'],$xharga,$_POST['total'],$urlback);
	$kirimtrx1='['.$data.']';
	$kirimtrx=json_decode($kirimtrx1,true);
	
	$fdtprofil="bayar.txt";
	file_put_contents($fdtprofil,$kirimtrx1."# \n", FILE_APPEND | LOCK_EX);
	$ok=ceklog($kirimtrx1);
	
	if (capi(14,2)=="1") {
		$pkirim		= "Sdr/Sdri ".$_POST['pelanggan']."\n";
		$pkirim		.="No WhatsApp ".$_POST['nowa']."\n";
		$pkirim		.="melakukan transaksi dengan\n*Methode ".$_POST['merchand']."*.\n";
		$pkirim		.="Untuk pengajuan\n*".$_POST['namabrg']."*\n";
		$pkirim		.="Mikrotik ROS\n*".$_POST['ros']."*\n";
		$pkirim		.="Nama Domain\n*".$_POST['domain']."*\n";
		$pkirim		.="User Mikhmon\n*".$_POST['userid']."*\n";
		$pkirim		.="Pass Mikhmon\n*".$_POST['pass']."*\n";
		$pkirim		.="*Seharga ".rupiah($_POST['total'])."* \n";
		$pkirim		.="*".ucwords(terbilang($_POST['total']))." Rupiah.*";
		$ktele=knotifwa("",$pkirim);
		sleep(2);
		$ktele=knotifwa(ltrim($_POST['nowa']),$pkirim);
	}else{
		$pkirim		= "Sdr/Sdri ".$_POST['pelanggan']."\n";
		$pkirim		.="No WhatsApp ".$_POST['nowa']."\n";
		$pkirim		.="melakukan transaksi dengan\n<b>Methode ".$_POST['merchand']."</b>.\n";
		$pkirim		.="Untuk pengajuan \n<b>".$_POST['namabrg']."</b>\n";
		$pkirim		.="Mikrotik ROS\n*".$_POST['ros']."*\n";
		$pkirim		.="Nama Domain\n*".$_POST['domain']."*\n";
		$pkirim		.="User Mikhmon\n*".$_POST['userid']."*\n";
		$pkirim		.="Pass Mikhmon\n*".$_POST['pass']."*\n";
		$pkirim		.="<b>Seharga ".rupiah($_POST['total'])."</b> \n";
		$pkirim		.="<b>".ucwords(terbilang($_POST['total']))." Rupiah.</b>";
		$ktele=knotiftele($pkirim);
	}
	if ($kirimtrx[0]['success']==true) {
		$cookie_value = $kirimtrx[0]['data']['reference'];
		setcookie("register", $cookie_value, time() + (60*60*1), "/"); // umur cookies 1 jam
		$thasil="1";
	}else{
		$thasil="0";
		echo "<script>window.location='index.php?id=error&pesan=".$kirimtrx[0]['message']."'</script>";
	}
}
?>
<html>
	<head>
		<title>MIKHMON Registrasi</title>
		<meta charset="utf-8">
		<meta http-equiv="cache-control" content="private" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!-- Tell the browser to be responsive to screen width -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- Theme color -->
		<meta name="theme-color" content="<?= $themecolor ?>" />
		<!-- Font Awesome -->
		<link rel="stylesheet" type="text/css" href="../css/font-awesome/css/font-awesome.min.css" />
		<!-- Mikhmon UI -->
		<link rel="stylesheet" href="../css/mikhmon-ui.<?= $theme; ?>.min.css">
		<!-- favicon -->
		<link rel="icon" href="../img/favicon.png" />
		<!-- jQuery -->
		<script src="../js/jquery.min.js"></script>
		<!-- pace -->
		<link href="../css/pace.<?= $theme; ?>.css" rel="stylesheet" />
		<script src="../js/pace.min.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	</head>
	<body>

		<span style="display:none;" id="idto"><?= $idleto ;?></span>

		<div id="navbar" class="navbar">
			<div class="navbar-left">
				<a id="brand" class="text-center" href="javascript:void(0)"><?= $mpage; ?></a>
				<a id="openNav" class="navbar-hover" href="javascript:void(0)"><b><?= $mpage; ?></b></a>
			</div>
			<div class="navbar-right">
				<a title="Idle Timeout" style="<?= $didleto; ?>"><span style="width:70px;" class="pd-5 radius-3"><i class="fa fa-clock-o mr-1"></i>  <span class="mr-1" id="jam"></span></span></a>
			</div>
		</div>
		<div class="row">
			<div class="col-10 mr-a" id="main" style="float:none;">
				<div class="row">
					<div class="col-4">	
						<div class="card box-shadow">
							<div class="box-group">
								<div class="row">
									<div class="col-5">	
										<div class="box-group-icon">
											<img style="margin-top:7px;" src="../img/favicon.png" alt="MIKHMON Logo">
										</div>
									</div>
									<div class="col-7">
										<div class="box-group-area">
											<div style="padding-top:15px;padding-left:10px;">
												<span style="font-size: 24px; margin: 0px;font-weight:bold;">MIKHMON </span><span style="font-size:14px;font-family:times;"> V3.20+</span><br>
												<span style="font-size:14px;margin: 0px;font-family:times;color:Orange;">Mikrotik Monitoring Assistance</span><br>
												<span style="font-size:14px;margin: 0px;font-family:times;">Support :</span><br>
												<span style="font-size:14px;margin: 0px;font-family:times;">Billing Pppoe.</span><br>
												<span style="font-size:14px;margin: 0px;font-family:times;">Bot Whatsapp dan Telegram.</span><br>
												<span style="font-size:14px;margin: 0px;font-family:times;"><a href="https://telerivet.com" target="_newB">Telerivet</a> & <a href="https://tripay.co.id" target="_newT">TriPay</a> Gateway.</span><br>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr style="border:2px solid;">
							<div class="card box-shadow">
							<span style="font-size:16px;margin: 10px;font-family:times;">Harga <?=rupiah($harga)?> / Bulan.</span><br>
							<span style="font-size:16px;margin: 10px;font-family:times;">Harga <?=rupiah($harga * 3)?> / 3 Bulan.</span><br>
							<span style="font-size:16px;margin: 10px;font-family:times;">Harga <?=rupiah($harga * 6)?> / 6 Bulan.</span><br>
							<span style="font-size:16px;margin: 10px;font-family:times;">Harga <?=rupiah($harga * 12)?> / 1 Tahun.</span><br>
							<span style="font-size:14px;margin: 10px;font-family:times;">Berlangganan 6 Bulan, Gratis 1 Bulan.</span><br>
							<span style="font-size:14px;margin: 10px;font-family:times;">Berlangganan 1 Tahun, Gratis 2 Bulan.</span><br>
							<p style="font-size:14px; margin: 10px; font-family: times; color: red;">
                                VPN Remote bisa daftar disini, <a href="https://tunnel.hostddns.us/login">Disini</a>.
                            </p>
							<p style="font-size:14px; margin: 10px; font-family: times; color: red;">
                                ApiKey MimoAssist silahkan ambil, <a href="https://apiwa.mimoassist.homes/">Disini</a>.
                            </p>
							</div>
							<hr style="border:5px solid">
							<div class="card box-shadow" style="font-size:14px;padding-left: 10px;font-family:times;">
								<span><b style="color:blue;font-size:16px;">Untuk account baru.</b><br> -. Silahkan isi semua kolom yang tersedia,<br> -. Pilih Methode Pembayaran<br> -. Pilih Durasi dan Verifikasi<br> -. Klik Tombol Daftar</span><br>
								<span><b style="color:blue;font-size:16px;">Untuk Perpanjangan.</b><br> -. Klik link <i style="color:blue;">Renew.</i><br> -. Isi kolom yang tersdia.<br> -. Pilih Methode Pembayaran<br> -. Pilih Durasi dan Verifikasi<br> -. Klik Tombol Renew</span><br>
							</div>
							<hr style="border:10px solid;">
						</div>
					</div>
					<div class="col-6">	
						<div class="card box-shadow">
							<?php
							if (empty($pesan)) {
								if ($thasil=="1") {
									$styletdl='style="font-family:times;font-size:16px;font-weight:bold;text-align:left;"';
									$styletdr='style="font-family:times;font-size:16px;font-weight:bold;text-align:right;"';
									echo '
							<table class="table">
								<tr><td colspan="3"> &nbsp </td></tr>
								<tr><td colspan="2" style="font-family:times;font-size:20px;text-align:center;"> DETIL TRANSAKSI </td><td><a href="?id=new_account"><i style="cursor:pointer;padding-right:20px;font-family:times;font-weight:bold;font-size:16px;color:yellow;">Done</i></a></td></tr>
							</table>';
									if (!empty($kirimtrx[0]['data']['pay_url'])) {
							echo '
							<div class="row">
								<div class="card">
									<div class="box-group" style="padding-left:20px;">
										<div class="box-group-area" style="font-size:16px;font-family:times;">Id_Merchand<br>Id_Transaksi<br>Merchand<br>Atas Nama<br>No WhatsApp<br>Rupiah<br><br>Batas Akhir</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">:<br>:<br>:<br>:<br>:<br>:<br><br>:</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">'.$kirimtrx[0]['data']['merchant_ref'].'<br>'.$kirimtrx[0]['data']['reference'].'<br>'.$kirimtrx[0]['data']['payment_method'].'<br>'.$kirimtrx[0]['data']['customer_name'].'<br>'.$kirimtrx[0]['data']['customer_phone'].'<br>'.rupiah($kirimtrx[0]['data']['amount']).'<br>'.ucwords(terbilang($kirimtrx[0]['data']['amount'])).' Rupiah.<br>'.date('d-M-Y',$kirimtrx[0]['data']['expired_time']).' Jam '.date('H:i:s',$kirimtrx[0]['data']['expired_time']).'</div>
									</div>
								<div class="w-3">
									<a style="text-align:center;cursor:pointer;" href="'.$kirimtrx[0]['data']['pay_url'].'"><div class="card-header bg-cyan"><h3 style="color:blue;">Lanjutkan</h3></div></a>
								</div>
							</div>
							';
									}elseif (!empty($kirimtrx[0]['data']['pay_code'])) {
							echo '
							<div class="row">
								<div class="card">
									<div class="box-group" style="padding-left:20px;">
										<div class="box-group-area" style="font-size:16px;font-family:times;">Id_Merchand<br>Id_Transaksi<br>Merchand<br>Atas Nama<br>Nomor WhatsApp<br>Rupiah<br><br>Batas Akhir</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">:<br>:<br>:<br>:<br>:<br>:<br><br>:</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">'.$kirimtrx[0]['data']['merchant_ref'].'<br>'.$kirimtrx[0]['data']['reference'].'<br>'.$kirimtrx[0]['data']['payment_method'].'<br>'.$kirimtrx[0]['data']['customer_name'].'<br>'.$kirimtrx[0]['data']['customer_phone'].'<br>'.rupiah($kirimtrx[0]['data']['amount']).'<br>'.ucwords(terbilang($kirimtrx[0]['data']['amount'])).' Rupiah.<br>'.date('d-M-Y',$kirimtrx[0]['data']['expired_time']).' Jam '.date('H:i:s',$kirimtrx[0]['data']['expired_time']).'</div>
									</div>
									<div class="text-center">
											<input type="button" class="bg-cyan" style="color:orange;padding:5px 15px 5px 15px;font-size:20px;font-weight:bold;" value="Pay Code '.$kirimtrx[0]['data']['pay_code'].'">
									</div>
								</div>
							</div>
							';
									}else{
							echo '
							<div class="box-group">
								<div class="row">
									<div class="col-6">	
										<div class="box-group-icon">
											<img style="width:200px;" src='.$kirimtrx[0]['data']['qr_url'].' alt="Q-RIS"><br>
											<span style="font-family:times;font-size:16px;">'.$kirimtrx[0]['data']['qr_string'].'</span>
										</div>
									</div>
									<div class="col-6">
										<div class="box-group-area">
											<div style="padding-top:0px;padding-left:5px;font-family:times;font-size:16px;font-weight:bold;">
												'.$kirimtrx[0]['data']['merchant_ref'].'<br>
												'.$kirimtrx[0]['data']['reference'].'<br>
												'.$kirimtrx[0]['data']['payment_name'].'<br>
												'.$kirimtrx[0]['data']['customer_name'].'<br>
												'.$kirimtrx[0]['data']['customer_phone'].'<br>
												'.rupiah($kirimtrx[0]['data']['amount']).'<br>
												Batas Akhir :<br>
												'.date('d/M/Y H:i:s',$kirimtrx[0]['data']['expired_time']).'
											</div>
										</div>
									</div>
								</div>
							</div>
							';
									}
							echo '
							<div class="card">';
								for ($x=0;$x<count($kirimtrx[0]['data']['instructions']);$x++) {
								echo '<div class="card-header bg-cyan"><h3 style="color:white;">Cara '. $kirimtrx[0]['data']['instructions'][$x]['title'].'</h3></div>
								<div class="card-body" style="font-size:16px;">';
									for ($y=0;$y<count($kirimtrx[0]['data']['instructions'][$x]['steps']);$y++) {
										echo str_replace("Lanjutkan","<b>Lanjutkan</b>",$kirimtrx[0]['data']['instructions'][$x]['steps'][$y]).'<br>';
									}
								echo '
								</div>';
								}
								echo '<div class="card-header bg-cyan"><h3 style="color:white;">Terimakasih</h3></div>
							</div>
									';
								
								}elseif ($thasil=="2") {
//cookies
									$idreference=$_COOKIE['register'];
									$cektrx=cektrx($idreference);
									$cek=ceklog($cektrx);
									$kirimtrx=json_decode('['.$cektrx.']',true);
//
									$styletdl='style="font-family:times;font-size:16px;font-weight:bold;text-align:left;"';
									$styletdr='style="font-family:times;font-size:16px;font-weight:bold;text-align:right;"';
									$style	= 'style="color:black;padding:10px;font-size:16px;font-family:times; font-weight:bold;color:white;background:black;"';
									$berita	= "Transaksi menunggu pembayaran.<br>Transaksi akan Expired otomati setelah melewati Batas Akhir terlampaui.";
									echo '
							<table class="table">
								<tr><td colspan="3"> &nbsp </td></tr>
								<tr><td colspan="2" style="font-family:times;font-size:20px;text-align:center;"> TRANSAKSI PENDING </td><td><a href="?id=Mikhmon"><i style="cursor:pointer;padding-right:20px;font-family:times;font-weight:bold;font-size:16px;color:yellow;">Done</i></a></td></tr>
							</table>';
									if (!empty($kirimtrx[0]['data']['pay_url'])) {
							echo '
							<div class="row">
								<div class="card">
									<div class="box-group" style="padding-left:20px;">
										<div class="box-group-area" style="font-size:16px;font-family:times;">Id_Merchand<br>Id_Transaksi<br>Merchand<br>Atas Nama<br>Nomor WhatsApp<br>Rupiah<br><br>Batas Akhir</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">:<br>:<br>:<br>:<br>:<br>:<br><br>:</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">'.$kirimtrx[0]['data']['merchant_ref'].'<br>'.$kirimtrx[0]['data']['reference'].'<br>'.$kirimtrx[0]['data']['payment_method'].'<br>'.$kirimtrx[0]['data']['customer_name'].'<br>'.$kirimtrx[0]['data']['customer_phone'].'<br>'.rupiah($kirimtrx[0]['data']['amount']).'<br>'.ucwords(terbilang($kirimtrx[0]['data']['amount'])).' Rupiah.<br>'.date('d-M-Y',$kirimtrx[0]['data']['expired_time']).' Jam '.date('H:i:s',$kirimtrx[0]['data']['expired_time']).'</div>
									</div>
									<div class="w-3">
										<a style="text-align:center;cursor:pointer;" href="'.$kirimtrx[0]['data']['pay_url'].'"><div class="card-header bg-cyan"><h3 style="color:white;">Lanjutkan</h3></div></a>
									</div>
								</div>
							</div>
							';
									}elseif (!empty($kirimtrx[0]['data']['pay_code'])) {
							echo '
							<div class="row">
								<div class="card">
									<div class="box-group" style="padding-left:20px;">
										<div class="box-group-area" style="font-size:16px;font-family:times;">Id_Merchand<br>Id_Transaksi<br>Merchand<br>Atas Nama<br>No WhatsApp<br>Rupiah<br><br>Batas Akhir</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">:<br>:<br>:<br>:<br>:<br>:<br><br>:</div>
										<div class="box-group-area" style="font-size:16px;font-family:times;">'.$kirimtrx[0]['data']['merchant_ref'].'<br>'.$kirimtrx[0]['data']['reference'].'<br>'.$kirimtrx[0]['data']['payment_method'].'<br>'.$kirimtrx[0]['data']['customer_name'].'<br>'.$kirimtrx[0]['data']['customer_phone'].'<br>'.rupiah($kirimtrx[0]['data']['amount']).'<br>'.ucwords(terbilang($kirimtrx[0]['data']['amount'])).' Rupiah.<br>'.date('d-M-Y',$kirimtrx[0]['data']['expired_time']).' Jam '.date('H:i:s',$kirimtrx[0]['data']['expired_time']).'</div>
									</div>
									<div class="text-center">
										<input type="button"  style="color:black;background:cyan;padding:5px 15px 5px 15px;font-size:20px;font-weight:bold;" value="Pay Code '.$kirimtrx[0]['data']['pay_code'].' ">
									</div>
								</div>
								<p>
							</div>
							';
									}else{
							echo '
							<div class="box-group">
								<div class="row"> 
									<div class="col-6">	
										<div class="box-group-icon">
											<img style="width:200px;" src='.$kirimtrx[0]['data']['qr_url'].' alt="Q-RIS"><br>
											<span style="font-family:times;font-size:16px;">'.$kirimtrx[0]['data']['qr_string'].'</span>
										</div>
									</div>
									<div class="col-6">
										<div class="box-group-area">
											<div style="padding-top:0px;padding-left:5px;font-family:times;font-size:16px;font-weight:bold;">
												'.$kirimtrx[0]['data']['merchant_ref'].'<br>
												'.$kirimtrx[0]['data']['reference'].'<br>
												'.$kirimtrx[0]['data']['payment_name'].'<br>
												'.$kirimtrx[0]['data']['customer_name'].'<br>
												'.$kirimtrx[0]['data']['customer_phone'].'<br>
												'.rupiah($kirimtrx[0]['data']['amount']).'<br>
												Batas Akhir :<br>
												'.date('d/M/Y H:i:s',$kirimtrx[0]['data']['expired_time']).'
											</div>
										</div>
									</div>
								</div>
							</div>
							';
									}
							echo '
							<div class="card" '.$style.' >
								<span>'.$berita.'</span>
							</div>
							<div class="card">';
								for ($x=0;$x<count($kirimtrx[0]['data']['instructions']);$x++) {
								echo '<div class="card-header bg-cyan"><h3 style="color:white;">Cara '. $kirimtrx[0]['data']['instructions'][$x]['title'].'</h3></div>
								<div class="card-body" style="font-size:16px;">';
									for ($y=0;$y<count($kirimtrx[0]['data']['instructions'][$x]['steps']);$y++) {
										echo str_replace("Lanjutkan","<b>Lanjutkan</b>",$kirimtrx[0]['data']['instructions'][$x]['steps'][$y]).'<br>';
									}
								echo '
								</div>';
								}
								echo '<div class="card-header bg-cyan"><h3 style="color:white;">Terimakasih</h3></div>
							</div>
									';

//
								}else{
								$gaya='style="padding:3px;width:200px;background:silver;font-size:14px;font-weight:bold;boder:1px solid green;border-radius:3px;font-family:times"';
							echo '
							<form method="post" action="proses.php">
								<table class="table" style="">
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td colspan="3" style="font-family:times;font-size:20px;text-align:center;"> FORM PENDAFTARAN </td></tr>
									<tr><td colspan="3" align="right" title="Perpanjangan Account"> <a href="?id=renew"><i style="cursor:pointer;padding-right:20px;font-family:times;font-weight:bold;font-size:16px;color:blue;">Renew</i></a> </td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Nama</td><td> : </td><td><input '.$gaya.' type="text" name="nama" placeholder="Nama Anda" oninput="this.value = this.value.toUpperCase()" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Email</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="email"name="email" placeholder="Email" oninput="this.value = this.value.toLowerCase()" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >NO. WA</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="number" name="idwa" placeholder="08" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Sub Domain</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="text" name="domain" placeholder="name.mimoassist.homes" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Mikrotik ROS</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="text" name="ros" placeholder="6.x / 7.x" value="" required></td></tr>
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >User Name</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="text" name="user" placeholder="User Name Anda" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Password</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="password"name="pass" placeholder="password" value="" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Re Type</td><td> : </td><td style="padding-right:20px;"><input '.$gaya.' type="password"name="pass1" value="" required></td></tr>
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Verifikasi</td><td> : </td><td style="padding-right:20px;">
										<select class="form-control" name="method" style="width:200px;font-weight:bold;font-family:times;font-size:16px;">											<option value="wa" selected>Via WhatsApps</option>
										</select></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Methode</td><td> : </td><td style="padding-right:20px;">
										<select class="form-control" name="method" style="width:200px;font-weight:bold;font-family:times;font-size:16px;" >
											'.listmerchand().'
										</select></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Durasi</td><td> : </td><td>
										<select class="form-control" name="durasi" style="width:200px;font-weight:bold;font-family:times;font-size:16px;" >';
										for ($x=1;$x<13;$x++) {
											echo '<option style="font-weight:bold;font-family:times;font-size:16px;" value="'.$x.'"> '.$x.' Bulan.</option>';
										}
										echo '</select>
									</td></tr>
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr>
										<td align="right"><a href="?id=Mikhmon"><input type="button" title="Back to Login Mikhmon" value=" Back " style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid green;background:#2eb8b8;"></a> </td>
										<td></td><td align="right" style="padding-right:40px;">
										<input type="submit" title="Ajukan Permohonan" name="kirim" value=" Ajukan " class="btn-login bg-yellow" style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid white;color:black;"></td></tr>
								</table>
							</form>
								';
								}
							}else{
								$judul='style="font-family:times;font-size:20px;"';
								$styletd='style="font-family:times;font-size:16px;text-align:right;"';
								$styletd1='style="font-family:times;font-size:16px;font-weight:bold;text-align:left;"';
								$styletd2='style="font-family:times;font-size:16px;text-align:left;padding:10px;border:3px solid yellow;margin-left:20px;background:black;color:white;"';
								$styleinp='style="padding:2px 5px 2px 5px;background:silver;padding:3p;font-size:16px;font-weight:bold;boder:1px solid green;border-radius:3px;"';
								$dtpell=json_decode($pesan,true);
								echo '	
							<form method="post" action="">
								<table class="table">
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td colspan="3" align="center" '.$judul.' > FORM VERIFIKASI </td></tr>
									<tr><td colspan="3">&nbsp</td></tr>
									<tr><td '.$styletd.' >Nama</td><td> : </td><td><input '.$styletd1.' type="text" name="pelanggan" class="form-control" value=" '.$dtpell['nama'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Email</td><td>:</td><td><input '.$styletd1.' type="email" name="email" class="form-control" value="'.$dtpell['email'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >No WA</td><td>:</td><td><input '.$styletd1.' type="number" name="nowa" class="form-control" value="'.$dtpell['idwa'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Methode</td><td>:</td><td><input '.$styletd1.' type="text" name="merchand" class="form-control" value="'.$dtpell['method'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Produk</td><td>:</td><td><input '.$styletd1.' type="text" name="produk" class="form-control" value="'.$dtpell['produk'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Nama Barang</td><td>:</td><td><input '.$styletd1.' type="text" name="namabrg" class="form-control" value="'.$dtpell['namabrg'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Mikrotik ROS</td><td>:</td><td><input '.$styletd1.' type="text" name="ros" class="form-control" value="'.$dtpell['ros'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Nama Domain</td><td>:</td><td><input '.$styletd1.' type="text" name="domain" class="form-control" value="'.$dtpell['domain'].'" readonly ></td></tr>
									<tr><td '.$styletd.' >Durasi</td><td>:</td><td><input '.$styletd1.' type="text" name="durasi" class="form-control" value="'.$dtpell['durasi'].' Bulan " readonly ></td></tr>
									<tr><td '.$styletd.' >Potongan</td><td>:</td><td><input '.$styletd1.' type="text" name="disc" class="form-control" value="'.$dtpell['disc'].' Bulan " readonly ></td></tr>
									<tr><td '.$styletd.' >Harga [+admin]</td><td>:</td>
											<td><input '.$styletd1.' type="text" name="totl" class="form-control" value="'.rupiah($dtpell['total']).'" readonly >
											<input type="hidden" name="total" value="'.$dtpell['total'].'"></td>
										</tr>
									<tr><td></td><td></td><td '.$styletd1.'>'.ucwords(terbilang($dtpell['total'])).' Rupiah.</td></tr>';
									if ($dtpell['method']=="Manual") {
										echo '<tr><td colspan="3" align="right"><a style="padding-right:20px;color:yellow;font-family:times;font-size:16px;" href="?id=register"> Back </a></td></tr>';
										echo '<tr><td colspan="3" '.$styletd2.' >'.$dtpell['pesan'].'</td></tr>';
									}else{
										echo '
										<tr>
											<td align="right"><a href="?id=register"><input type="button" title="Back" value=" Back " style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid green;background:#2eb8b8;"></a> </td>
											<td></td><td align="right" style="padding-right:40px;">
											<input type="submit" title="Ajukan Permohonan" name="bayar" value=" Bayar " class="btn-login bg-green" style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid white;color:black;"></td></tr>
											<tr><td colspan="3" '.$styletd2.' >'.$dtpell['pesan'].'</td></tr>
											';
									}
								
								echo '
								</table>
							</form>
								';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>
	<script type="text/javascript">
		// 1 detik = 1000
		window.setTimeout("waktu()",1000);  
		function waktu() {   
		var tanggal = new Date();  
		setTimeout("waktu()",1000);  
		document.getElementById("jam").innerHTML = tanggal.getHours()+":"+tanggal.getMinutes()+":"+tanggal.getSeconds();
		}

		window.intergramId = "1341792914";
		window.intergramCustomizations = {
			titleClosed: 'Chat',
			titleOpen: 'Ketik Pesan Anda',
			introMessage: 'Selamat datang di MimoAssist.Homes! Kami siap membantu Anda. Ada yang bisa kami bantu?',
			autoResponse: 'Sabar ya,..',
			autoNoResponse: 'Maaf...'+'Om Admin, sedang tidak ada ditempat / ' +
                        'tidak pegang HP.' + 'Silahkan hubungi via WhatsApp di 628933033332'+'Terimakasih.',
			mainColor: "#003300", 
			alwaysUseFloatingButton: false 
		};
	</script>
	<script id="intergram" type="text/javascript" src="https://www.intergram.xyz/js/widget.js"></script>
	</script>
	</body>
</html>
