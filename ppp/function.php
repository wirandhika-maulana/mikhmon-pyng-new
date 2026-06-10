<?php
function rupiah($angka) {
	if (empty($angka)) $angka = 0;
	$hasil_rupiah = "Rp " . number_format((float)$angka, 0, ',', '.');
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
	static $cache = null;
	if ($cache === null) {
		$cache = [];
		$fdtprofil="ppp/csv/dtpelanggan.txt";
		if (file_exists($fdtprofil)) {
			$dtfile=explode("#",file_get_contents($fdtprofil));
			foreach ($dtfile as $line) {
				$row = explode("^", $line);
				if (isset($row[2])) {
					$cache[ltrim($row[2])] = $row;
				}
			}
		}
	}
	$cari = ltrim($cari);
	if (isset($cache[$cari]) && isset($cache[$cari][$index])) {
		return $cache[$cari][$index];
	}
	return "";
}
function caridtpelanggan1($cari,$index) {
	static $cache = null;
	if ($cache === null) {
		$cache = [];
		$fdtprofil="ppp/csv/dtpelanggan.txt";
		if (file_exists($fdtprofil)) {
			$dtfile=explode("#",file_get_contents($fdtprofil));
			foreach ($dtfile as $line) {
				$row = explode("^", $line);
				if (isset($row[0])) {
					$cache[ltrim($row[0])] = $row;
				}
			}
		}
	}
	$cari = ltrim($cari);
	if (isset($cache[$cari]) && isset($cache[$cari][$index])) {
		return $cache[$cari][$index];
	}
	return "";
}
function caridthistory($cari,$index) {
	static $cache = null;
	if ($cache === null) {
		$cache = [];
		$fdtprofil="ppp/csv/dthistory.txt";
		if (file_exists($fdtprofil)) {
			$dtfile=explode("#",file_get_contents($fdtprofil));
			foreach ($dtfile as $line) {
				$row = explode("^", $line);
				if (isset($row[0])) {
					$cache[ltrim($row[0])] = $row;
				}
			}
		}
	}
	$cari = ltrim($cari);
	if (isset($cache[$cari]) && isset($cache[$cari][$index])) {
		return $cache[$cari][$index];
	}
	return "";
}
function caridtharga($cari) {
	static $cache = null;
	if ($cache === null) {
		$cache = [];
		$fdtprofil="ppp/csv/dthrgprofile.txt";
		if (file_exists($fdtprofil)) {
			$dtfile=explode("#",file_get_contents($fdtprofil));
			foreach ($dtfile as $line) {
				$row = explode("^", $line);
				if (isset($row[0]) && isset($row[2])) {
					$cache[ltrim($row[0])] = $row[2];
				}
			}
		}
	}
	$cari = ltrim($cari);
	return isset($cache[$cari]) ? $cache[$cari] : "";
}
function caridtharga1($cari) {
	static $cache = null;
	if ($cache === null) {
		$cache = [];
		$fdtprofil="ppp/csv/dthrgprofile.txt";
		if (file_exists($fdtprofil)) {
			$dtfile=explode("#",file_get_contents($fdtprofil));
			foreach ($dtfile as $line) {
				$row = explode("^", $line);
				if (isset($row[1]) && isset($row[2])) {
					$cache[ltrim($row[1])] = $row[2];
				}
			}
		}
	}
	$cari = ltrim($cari);
	return isset($cache[$cari]) ? $cache[$cari] : "";
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