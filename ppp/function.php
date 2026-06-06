<?php
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

function caridtpelanggan($cari,$index) {
	$hasil="";
	$fdtprofil="ppp/csv/dtpelanggan.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		for ($x=0;$x<count($dtfile);$x++) {
			if (explode("^",$dtfile[$x])[2]==ltrim($cari)) {
				$hasil=explode("^",$dtfile[$x])[$index];
				break;
			}
		}
	}
	return $hasil;
}
function caridtpelanggan1($cari,$index) {
	$hasil="";
	$fdtprofil="ppp/csv/dtpelanggan.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (ltrim(explode("^",$dtfile[$x])[0])==ltrim($cari)) {
				$hasil=explode("^",$dtfile[$x])[$index];
				break;
			}
		}
	}
	return $hasil;
}
function caridthistory($cari,$index) {
	$hasil="";
	$fdtprofil="ppp/csv/dthistory.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (ltrim(explode("^",$dtfile[$x])[0])==ltrim($cari)) {
				$hasil=explode("^",$dtfile[$x])[$index];
				break;
			}
		}
	}
	return $hasil;
}
function caridtharga($cari) {
	$hasil="";
	$fdtprofil="ppp/csv/dthrgprofile.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (ltrim(explode("^",$dtfile[$x])[0])==ltrim($cari)) {
				$hasil=explode("^",$dtfile[$x])[2];
				break;
			}
		}
	}
	return $hasil;
}
function caridtharga1($cari) {
	$hasil="";
	$fdtprofil="ppp/csv/dthrgprofile.txt";
	if (file_exists($fdtprofil)) {
		$dtfile=explode("#",file_get_contents($fdtprofil));
		for ($x=0;$x<count($dtfile)-1;$x++) {
			if (ltrim(explode("^",$dtfile[$x])[1])==ltrim($cari)) {
				$hasil=explode("^",$dtfile[$x])[2];
				break;
			}
		}
	}
	return $hasil;
}

function test($cari) {
	$ftest="ppp/csv/dttest.txt";
//	$cari=json_encode($cari);
	file_put_contents($ftest,$cari."\n".str_repeat("=",50)."\n\n", FILE_APPEND | LOCK_EX);
}

function idwatele($cek) {
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