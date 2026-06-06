<?php
	include "function.php";
	$disc	= 0;
	if ($_GET['durasi']>5 and $_GET['durasi']<12) {
		$disc	= 1;
	}elseif ($_GET['durasi']>11) {
		$disc	= 2;
	}
	$total=($_GET['durasi']-$disc)*capi(15,1);
	$kirimtrx	= sendtrx($_GET['pelanggan'],$_GET['email'],$_GET['nowa'],$_GET['merchand'],$_GET['produk'],$_GET['namabrg'],$_GET['durasi'],$total);
	$fdtprofil="bayar.txt";
	file_put_contents($fdtprofil,$kirimtrx." \n", FILE_APPEND | LOCK_EX);
	echo "<a href='index.php?id=register'>Back</a>";
?>