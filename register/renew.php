<?php
if (isset($_POST['kirim'])) {

}
include('function.php');
$harga=capi(15,1);
if ($harga=="-=0=-") {
	$harga=15000;
}
?>
<html>
	<head>
		<title>MIKHMON Renew</title>
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
                                ApiKey MimoAssist silahkan ambil, <a href="https://apiwa.mimoassist.homes">Disini</a>.
                            </p>
							</div>
							<hr style="border:10px solid;">
						</div>
					</div>
					<div class="col-6">	
						<div class="card box-shadow">
								<?php
								if (empty($pesan)) {
								echo '
							<form method="post" action="renew.php">
								<table class="table" style="">
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td colspan="3" style="font-family:times;font-size:20px;text-align:center;"> FORM RENEW </td></tr>
									<tr><td colspan="3" align="right" title="Perpanjangan Account"> <a href="?id=register"><i style="cursor:pointer;padding-right:20px;font-family:times;font-weight:bold;font-size:16px;color:blue;">New Account</i></a> </td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Nama</td><td> : </td><td><input style="padding:2px;width:200px;background:silver;padding:3p;font-size:16px;font-weight:bold;boder:1px solid green;border-radius:3px;" type="text" name="nama" oninput="this.value = this.value.toUpperCase()" value="Nama" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >Email</td><td> : </td><td style="padding-right:20px;"><input style="padding:2px;width:200px;background:silver;padding:3p;font-size:16px;font-weight:bold;boder:1px solid green;border-radius:3px;" type="email"name="email" oninput="this.value = this.value.toLowerCase()" value="Email" required></td></tr>
									<tr><td style="font-family:times;font-size:16px;text-align:right;" >WhatsApp</td><td> : </td><td style="padding-right:20px;"><input style="padding:2px;width:200px;background:silver;padding:3p;font-size:16px;font-weight:bold;boder:1px solid green;border-radius:3px;" type="number" name="idwa" value="WhatsApp" required></td></tr>
									<tr><td colspan="3"> &nbsp </td></tr>
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
										<input type="submit" title="Ajukan Permohonan" name="kirim" value=" RENEW " class="btn-login bg-yellow" style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid white;color:black;"></td></tr>
								</table>
							</form>
								';
								}else{
								$judul='style="font-family:times;font-size:20px;"';
								$styletd='style="font-family:times;font-size:16px;text-align:right;"';
								$styletd1='style="font-family:times;font-size:18px;font-weight:bold;text-align:left;"';
								$styletd2='style="font-family:times;font-size:18px;text-align:left;padding:10px;border:3px solid white;margin-left:20px;"';
								$styleinp='style="padding:2px 5px 2px 5px;background:silver;padding:3p;font-size:16px;font-weight:bold;boder:1px solid green;border-radius:3px;"';
								$dtpell=json_decode($pesan,true);
								echo '	
							<form method="post" action="bayar.php">
								<table class="table">
									<tr><td colspan="3"> &nbsp </td></tr>
									<tr><td colspan="3" align="center" '.$judul.' > FORM RENEW </td></tr>
									<tr><td colspan="3" align="right" title="Perpanjangan Account"> <a href=""><i style="cursor:pointer;padding-right:20px;font-family:times;font-weight:bold;font-size:16px;color:yellow;"> Back </i></a> </td></tr>
									<tr><td '.$styletd.' >Nama</td><td> : </td><td '.$styletd1.'>'.$dtpell['nama'].'</td></tr>
									<tr><td '.$styletd.' >Email</td><td>:</td><td '.$styletd1.'>'.$dtpell['email'].'</td></tr>
									<tr><td '.$styletd.' >No WhatsApp</td><td>:</td><td '.$styletd1.'>'.$dtpell['idwa'].'</td></tr>
									<tr><td '.$styletd.' >Methode</td><td>:</td><td '.$styletd1.'>'.$dtpell['method'].'</td></tr>
									<tr><td '.$styletd.' >Produk</td><td>:</td><td '.$styletd1.'>'.$dtpell['produk'].'</td></tr>
									<tr><td '.$styletd.' >Nama Barang</td><td>:</td><td '.$styletd1.'>'.$dtpell['nama_brg'].'</td></tr>
									<tr><td '.$styletd.' >Dursi</td><td>:</td><td '.$styletd1.'>'.$dtpell['durasi'].'. Bulan</td></tr>
									<tr><td '.$styletd.' >Potongan</td><td>:</td><td '.$styletd1.'>'.$dtpell['disc'].'. Bulan</td></tr>
									<tr><td '.$styletd.' >Harga [+admin]</td><td>:</td><td '.$styletd1.'>'.rupiah($dtpell['total']).'</td></tr>
									<tr><td></td><td></td><td '.$styletd1.'>'.ucwords(terbilang($dtpell['total'])).' Rupiah.</td></tr>';
										echo '<tr><td colspan="3">&nbsp</td></tr>';
									if ($dtpell['method']=="Manual") {
										echo '<tr><td colspan="3" '.$styletd2.' >'.$dtpell['pesan'].'</td></tr>';
									}else{
										echo '
										<tr>
											<td align="right"><a href="?id=register"><input type="button" title="Back" value=" Back " style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid green;background:#2eb8b8;"></a> </td>
											<td></td><td align="right" style="padding-right:40px;">
											<input type="submit" title="Ajukan Permohonan" name="bayar" value=" Bayar " class="btn-login bg-yellow" style="padding:5px 20px 5px 20px;font-family:times;font-size:16px;border-radius:5px;font-weight:bold;cursor:pointer;border:1px solid white;color:black;"></td></tr>
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
                        'tidak pegang HP.' + 'Silahkan hubungi via WhatsApp di 6289633033332'+'Terimakasih.',
			mainColor: "#000000", 
			alwaysUseFloatingButton: false 
		};
	</script>
	<script id="intergram" type="text/javascript" src="https://www.intergram.xyz/js/widget.js"></script>
	</script>
	</body>
</html>
