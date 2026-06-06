<?php
date_default_timezone_set('Asia/Jakarta');
include('function.php');
$harga=capi(15,1);
if ($harga=="-=0=-") {
	$harga=20000;
}
$stt='style="font-family:times;font-size:16px;padding:5px 25px 5px 25px;border:1px white solid;border-radius:5px;background:green;color:white;"';
?>
<html>
	<head>
		<title>MIKHMON Error</title>
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
							<span style="font-size:14px;margin: 10px;font-family:times;">Berlangganan 6 Bulan, Gratis 1 Bulan.</span><br>
							<span style="font-size:14px;margin: 10px;font-family:times;">Berlangganan 1 Tahun, Gratis 2 Bulan.</span><br>
							<span style="font-size:14px;margin: 10px;font-family:times;color:yellow;">Tidak menyediakan Ip Remote.</span><br>
							<span style="font-size:14px;margin: 10px;font-family:times;color:yellow;">API, silahkan buat sendiri, bisa saya bantu.</span><br>
							</div>
							<hr style="border:5px solid">
							<div class="card box-shadow" style="font-size:14px;padding-left: 10px;font-family:times;">
								<span><b style="color:yellow;font-size:16px;">Untuk account baru.</b><br> -. Silahkan isi semua kolom yang tersedia,<br> -. Pilih Methode Pembayaran<br> -. Pilih Durasi dan Verifikasi<br> -. Klik Tombol Daftar</span><br>
								<span><b style="color:yellow;font-size:16px;">Untuk Perpanjangan.</b><br> -. Klik link <i style="color:yellow;">Renew.</i><br> -. Isi kolom yang tersdia.<br> -. Pilih Methode Pembayaran<br> -. Pilih Durasi dan Verifikasi<br> -. Klik Tombol Renew</span><br>
							</div>
							<hr style="border:10px solid;">
						</div>
					</div>
					<div class="col-6">	
						<div class="card box-shadow">
							<div class="card box-shadow">
								<div class="card-header bg-warning"><h3 style="color:black;">Ada kesalahan dalam input data.<br>No WA harus berawalan 08</h3></div>
								<div class="card-header"><h3 style="color:black;"><?=$pesan?></h3></div>
								<?=logom()?>
							</div>
							<div class="card box-shadow">
								<a href="./index.php?id=Mikhmon"><b <?=$stt?> >BACK</b></a>
							</div>
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
			introMessage: 'Selamat datang,..',
			autoResponse: 'Sabar ya,..',
			autoNoResponse: 'Maaf...'+'Om Admin, sedang tidak ada ditempat / ' +
                        'tidak pegang HP.' + 'Silahkan hubungi via WhatsApp di 6289633033332'+'Terimakasih.',
			mainColor: "#003300", 
			alwaysUseFloatingButton: false 
		};
	</script>
	<script id="intergram" type="text/javascript" src="https://www.intergram.xyz/js/widget.js"></script>
	</script>
	</body>
</html>
