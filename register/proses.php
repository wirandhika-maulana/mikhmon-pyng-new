<?php
	date_default_timezone_set('Asia/Jakarta');
	include "function.php";
	$harga=capi(15,1);
	if ($harga=="-=0=-") {
		$harga=15000;
	}
	$disc	= 0;
	if ($_POST['durasi']>5 and $_POST['durasi']<12) {
		$disc	= 1;
	}elseif ($_POST['durasi']>11) {
		$disc	= 2;
	}
	$tothrg=($_POST['durasi']-$disc)*$harga;
	if (empty($_POST['method'])) {
		$admin=2000;
		$method="Manual";
		$total=$tothrg+$admin;
		$ktopup=capi(15,2);

		if ($ktopup=="-=0=-" || empty($ktopup) ) {
			$pesan="<span style='color:black'>Mohon maaf,<br>No Rekening belum di setting.<p>Silahkan <b>chat</b> admin.<br>Terimakasih</span>";
			
		}else{
			$pesan="<span style='color:white'>Manual<br><b>Silahkan transfer/topup kerekening</b> <p>".$ktopup."<p>Sebesar ".rupiah($total).".<br>( ".ucwords(terbilang($total))." Rupiah).<p>Setelah melakukan tranfer/topup, silahkan Chat ke Admin MimoAssist.<br> 089633033332<p><table style='color:black;'><tr><td>Harga </td><td>:</td><td align='right'>".rupiah($tothrg)."</td></tr><tr><td>Admin </td><td>:</td><td align='right'> ".rupiah($admin)."</td></tr><tr><td>Total </td><td>:</td><td align='right'> ".rupiah($total)."</td></tr></table><p>Pengajuan selesi, kami menunggu konfirmasi transfer/topup.<br>Terimakasih.</span>";
		}

		if (capi(14,2)=="1") {
			$pkirim		= "Sdr/Sdri ".$_POST['nama']."\n";
			$pkirim		.="No WhatsApp ".$_POST['idwa']."\n";
			$pkirim		.="melakukan transaksi dengan\n*Methode Manual*.\n";
			$pkirim		.="Untuk pengajuan\n*Sewa MimoAssist ".$_POST['durasi']." Bulan.*\n";
			$pkirim		.="Dengan Rincian:\n".$_POST['domain']."\n";
			$pkirim		.="User: ".$_POST['user']."\n";
			$pkirim		.="Password: ".$_POST['pass']."\n";
			$pkirim		.="*Seharga ".rupiah($total)."* \n";
			$pkirim		.="*".ucwords(terbilang($total))." Rupiah.*";
			$kirwa=knotifwa("",$pkirim);
			sleep(2);
			$kirwa=knotifwa(ltrim($_POST['idwa']),$pkirim);
		}else{
			$pkirim		= "Sdr/Sdri ".$_POST['nama']."\n";
			$pkirim		.="No WhatsApp ".$_POST['idwa']."\n";
			$pkirim		.="melakukan transaksi dengan\n<b>Methode Manual</b>.\n";
			$pkirim		.="Untuk pengajuan\n<b>Sewa MimoAssist ".$_POST['durasi']." Bulan.</b>\n";
			$pkirim		.="Dengan Rincian:\n".$_POST['domain']."\n";
			$pkirim		.="User: ".$_POST['user']."\n";
			$pkirim		.="Password: ".$_POST['pass']."\n";
			$pkirim		.="<b>Seharga ".rupiah($total)."</b> \n";
			$pkirim		.="<b>".ucwords(terbilang($total))." Rupiah.</b>";
			$ktele=knotiftele($pkirim);
		}
	}else{
		$biaya=biayatrx($_POST['method'],$harga*$_POST['durasi']);
		$dtmerc		= json_decode($biaya,true);
		$byflat		= $dtmerc['data'][0]['fee']['flat'];
		$bypersen	= $dtmerc['data'][0]['fee']['percent'];
		$bymin		= $dtmerc['data'][0]['fee']['min'];
		$bymax		= $dtmerc['data'][0]['fee']['max'];
		if ($dtmerc['data'][0]['fee']['percent']<>0) {
			$admin=$tothrg*$dtmerc['data'][0]['fee']['percent']/100;
			if ($dtmerc['data'][0]['fee']['min']<>0) {
				if ($admin<$dtmerc['data'][0]['fee']['min']) {
					$admin=$dtmerc['data'][0]['fee']['min'];
				}
			}
			if ($dtmerc['data'][0]['fee']['max']<>0) {
				if ($admin>$dtmerc['data'][0]['fee']['max']) {
					$admin=$dtmerc['data'][0]['fee']['max'];
				}
			}
		}else{
			$admin	= $dtmerc['data'][0]['fee']['flat'];
		}

		if (capi(14,1)=="1") {
			$total	= $tothrg-$admin;
		}elseif (capi(14,1)=="2") {
			$total	= $tothrg+$admin;
		}else{
			$total	= $tothrg+($admin/2);
		}
		
		$method=$_POST['method'];
		$pesan= "<span style='color:white'><b>Disclaimer :</b><p>Anda akan melakukan pembayaran dengan aplikasi ".$dtmerc['data'][0]['name']."<p>Apabila sdr/sdri ".$_POST['nama']." setuju dengan harga diatas, silahkan klik tombol Bayar.<p>Dengan menekan tombol Bayar, anda setuju dengan transksi diatas.</span>";
	}
	$nama_brg="Sewa Mikhmon ".ltrim($_POST['durasi'])." Bulan.";	
	$tulis	 = array( "nama" => $_POST['nama'], "email" => $_POST['email'] , "idwa" => $_POST['idwa'] , "subdomain" => $_POST['subdomain'], "username" => $_POST['user'],
				"password" => $_POST['pass'] , "method" => $method , "produk" => "Mikhmon" , "namabrg" => $nama_brg , "durasi" => $_POST['durasi'] , "disc" => $disc , 
				"harga" => $harga , "admin" => $admin , "total" => $total , "pesan" => $pesan );

	$jadi = json_encode($tulis);

	$fdata="trxdump.txt";
	$hasil=$jadi;
	$handle = fopen($fdata, 'w') or die('Cannot open file:  ' . $filec);
	fwrite($handle, $hasil);
	fclose($handle);
	echo "<script>window.location='index.php?id=register'</script>";
?>
