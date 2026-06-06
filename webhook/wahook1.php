<?php
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	require_once 'system.database.php';
	$stat1="Tidak Aktif";
	$file = './webhook/wahookk.php';
		$scheme = $_SERVER['REQUEST_SCHEME'];
		if (strpos(strtolower($scheme), 'http') !== false){
			$okkk=explode(".",$_SERVER['HTTP_HOST']);
			if ($okkk[1]=="ngrok-free") {
				$cekhttps="https://";
			}else{
				$cekhttps=$scheme."://";
			}
		}else{
			$cekhttps="https://";
		}
	$urlpath	=$_SERVER['REQUEST_URI'];
	$urlpath1	=explode('?',$urlpath);
	$urlcore	=$cekhttps.$_SERVER['HTTP_HOST'].$urlpath1[0].'webhook/corewamix.php';
	$urlcore	=str_replace("admin.php","",$urlcore);
	if (isset($_POST["save"])) {
		$phoneid 	= ($_POST['phoneid']);
		$nowa 		= ($_POST['nowa']);
		$nowaa 		= ($_POST['nowaa']);
		$email 		= ($_POST['email']);
		$apikey 	= ($_POST['apikey']);
		$urlcore 	= ($_POST['urlcore']);
		$apikey1 	= ($_POST['apikey1']);
		$endpoint 	= ($_POST['endpoint']);
		
		$mtulis = $phoneid."|".$nowa."|".$apikey."|".$urlcore."|".$email."|".$apikey1."|".$endpoint."|".$nowaa."|";
		$handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
		fwrite($handle, $mtulis);
		fclose($handle);
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,..\nData Telah  Berhasil Di Simpan."); } 
		</script>';
	}

	if (isset($_POST["update"])) {
		$cfile="./include/config.php";
		if (!file_exists($cfile)) {
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO.\nFile Config Tidak Ditemukan.\nProses Update GAGAL."); } 
			</script>';
		}else{
			$misi	= file_get_contents($cfile);
			$misi0	= explode("[",$misi);
			$jr		= count($misi0);
			$kirim	="";
			$cek="";
			$API = new RouterosAPI();
			$API->debug = false;
			$mtulis="";
			$updateok="1";
			for ($i = 3; $i < $jr; $i++) {
				$misi1	= explode("'",$misi0[$i]);
				$router	= $misi1[1];
				$mtulis .= $router;
				$ipr1	= explode($router,$misi0[$i]);
				$ipr	= substr($ipr1[2],1,-3);
				$unr	= substr($ipr1[3],3,-3);
				$pwr	= decrypt(substr($ipr1[4],3,-3));
				if ($API->connect($ipr, $unr, $pwr)){
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
					$mtulis .="#\n\n";
				}else{
					$updateok="0";
				}
			}
			if ($updateok=="0")	{
				if (!empty($mtulis)) {
					echo '<script type="text/javascript">
					window.onload = function () { alert("INFO,..\nGagal Melakukan Pemutakhiran Data Voucher."); } 
					</script>';
				}
			}else{
//tuiis			
				$filec = './webhook/dvoucher.php';
				$handle = fopen($filec, 'w') or die('Cannot open file:  ' . $filec);
				fwrite($handle, $mtulis);
				fclose($handle);
				if (!empty($mtulis)) {
					echo '<script type="text/javascript">
					window.onload = function () { alert("Pemutakhiran Data Voucher BERHASIL Dilakukan."); } 
					</script>';
				}
			}
		}
	}
	if (file_exists($file)) {
		$misi		= file_get_contents($file);
		$misi0		= explode("|",$misi);
		$phoneid	= $misi0[0];
		$nowa		= $misi0[1];
		$apikey		= $misi0[2];
		$urlcore1	= $misi0[3];
		$email		= $misi0[4];
		$apikey1	= $misi0[5];
		$endpoint	= $misi0[6];
		$nowaa		= $misi0[7];
	}else{
		$phoneid	= "";
		$nowa		= "";
		$nowaa		= "";
		$apikey		= "";
		$urlcore1	= "";
		$email		= "";
		$apikey1	= "";
		$endpoint	= "";
	}
	$status=help("kirimwa.id");
	$tinggi="380px";
	$qr="0";
	if (isset($_POST['adddevice'])) {
		$tinggi="150px";
		$status=adddevice($phoneid,$apikey);
	}
	if (isset($_POST['stadevice'])) {
		$tinggi="150px";
		$status=stadevice($phoneid,$apikey);
	}
	if (isset($_POST['deldevice'])) {
		$tinggi="150px";
		$status=deldevice($phoneid,$apikey);
	}
	if (isset($_POST['pairing'])) {
		$tinggi="235px";
		$status=pairing($phoneid,$apikey);
		$qr="1";
	}
	if (isset($_POST['qouta'])) {
		$tinggi="275px";
		$status=qouta($apikey);
	}
	if (isset($_POST['webon'])) {
		$tinggi="185px";
		$stat1="Aktif";
		if ($cekhttps=="https://") {
			$phoneid 	= ($_POST['phoneid']);
			$nowa 		= ($_POST['nowa']);
			$email 		= ($_POST['email']);
			$apikey 	= ($_POST['apikey']);
			$urlcore 	= ($_POST['urlcore']);
		
			$status=webon($urlcore,$apikey);
		}else{
			$status="Https:// diperlukan.";
		}
	}
	if (isset($_POST['weboff'])) {
		$tinggi="185px";
		if ($cekhttps=="https://") {
			$status=weboff($email,$urlcore,$apikey);
		}else{
			$status="Https:// diperlukan.";
		}
	}
	if (isset($_POST['websta'])) {
		$tinggi="185px";
		if ($cekhttps=="https://") {
			$status=websta($apikey);
		}else{
			$status="Https:// diperlukan.";
		}
	}
	if (isset($_POST['help'])) {
		$tinggi="380px";
		$status=help("kirimwa.id");
	}
	if (isset($_POST['help1'])) {
		$tinggi="380px";
		$status=help("mpwa");
	}
//cek 
	$cekk=websta1($urlcore,$apikey);
	$stat1="Tidak Aktif";
	if ($cekk==0) {
		$stats=weboff($email,$urlcore,$apikey);
	}else{
		$stat1="Aktif";
	}
}

?>
<style>
.iFWrapper {
	position: relative;
	padding-bottom: 76.25%; /* 16:9 */
	padding-top: 25px;
	height: 0;
}
.iFWrapper iframe {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	border :none;
}
</style>

<form autocomplete="off" method="post" action="">
	<table class="table table-sm">
		<tr>
		<td><input  class="btn bg-green" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="adddevice" title="Klik,..Tambahkan device." value="Add Device">
		<input  class="btn bg-green" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="stadevice" title="Klik,..Lihat device." value="Sts Device">
		<input  class="btn bg-green" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="deldevice" title="Klik,..Hapus device." value="Del Device"></td>
		<td><input  class="btn bg-cyan" type="submit" style="color:black;width:90px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="pairing" title="Klik,..Untuk Pairing device." value="Pairing">
		<input  class="btn bg-cyan" type="submit" style="color:black;width:90px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="qouta" title="Klik,..Lihat Qouta." value="Qouta">
		<input  class="btn bg-red" type="submit" style="color:black;width:90px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="help1" title="Klik,..Cara Pakai." value="Mpwa">
		<input  class="btn bg-red" type="submit" style="color:black;width:110px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="help" title="Klik,..Cara Pakai." value="Kirimwa"></td>
		<td><input  class="btn bg-blue" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="webon" title="Klik,..Aktifkan Web Hook." value="Hook ON">
		<input  class="btn bg-blue" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="weboff" title="Klik,..Matikan Web Hook." value="Hook OFF">
		<input  class="btn bg-blue" type="submit" style="color:black;width:115px;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px;" name="websta" title="Klik,..Lihat Status WebHook" value="Sts WH"> </td>
		</tr>
	</table>
	<div class="col-6">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><i class="fa fa-whatsapp " ></i> Setting Parameter Bot WHatsApp</h3>
			</div>
			<div class="card-body">
				<table class="table table-sm">
					<?php 
						echo '<tr><td class="align-middle">Phone ID</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="phoneid" placeholder="Phone ID diisi dengan hutuf kecil" value="'.$phoneid.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">Nomor Bot WA</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="nowa" placeholder="62" value="'.$nowa.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">Nomor WA Admin</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="nowaa" placeholder="62" value="'.$nowaa.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">Email</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="email" placeholder="Isi dengan email " value="'.$email.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">API kirimwa.id</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="apikey" placeholder="Isi Dengan API key kirimwa.id" value="'.$apikey.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">API MimoAssist</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="apikey1" placeholder="Isi Dengan API key MimoAssist" value="'.$apikey1.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">End Point MimoAssist</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="endpoint" placeholder="Isi Dengan End Point MimoAssist" value="'.$endpoint.'" required="1"/></td></tr>';
						echo '<tr><td class="align-middle">File Core</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="urlcore" placeholder="Url Hosting" value="'.$urlcore.'" readonly></td></tr>';
						echo '<tr><td class="align-middle">Kirimwa.id Status</td><td>:</td><td><b>Web Hook</b> <h8 style="color:cyan;font-weight:bold;">'.$stat1.'</h8></td></tr>';
						echo '<tr><td class="align-middle">
						<input  class="btn bg-blue" type="submit" style="cursor: pointer;color:black;font-size:14px;font-family:timesnewroman;font-weight:bold;border-radius:5px green;" name="update" value="Update Data">
						</td><td></td><td><input  class="btn bg-green" type="submit" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="save" title="Simpan Data Set Up." value="Save"></td></tr>';
					?>
				</table>
			</div>
		</div>
	</div>
</form>
<div class="col-6">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-rss" aria-hidden="true"></i> Info</h3>
		</div>
		<div class="card-body">
		<?php
		if ($qr=="0") {
			echo '<textarea id="textnya"  type="text" class="form-control"  name="status" style="margin-top: 0px; margin-bottom: 0px; height:'.$tinggi.'; max-width:600px;font-size:14px;" title="Info" value="" readonly>';
	
			if (isset($_GET['mid'])) {
				$flog="./webhook/corewalog.txt";
				if (file_exists($flog)) {
					$misi= explode("#",file_get_contents($flog));
					for ($a=0;$a<count($misi);$a++) {
						$json = json_decode("[".$misi[$a]."]", TRUE);
//						$json = json_decode($misi[$a], TRUE);
						if ($json[0]['webhook_type']=="incoming_message") {
							if ($json[0]['id']==$_GET['mid']) {
								print_r(array_values($json[0]));
								break;
							}
						}
					}
				}
			}else{
				echo $status;
			}		
			echo '</textarea>';
		}else{
			echo "<b>QR CODE akan expired dalam 10 detik.</b>";
			echo "
			<div class='iFWrapper'>
			<iframe src=$status width='150p' height='150px' style='background:white;'></iframe>
			</div>
			";
		}
		?>
		</textarea>
		</div>
	</div>
</div>
<h7>
<div class="col-12">
	<div class="card">
		<div class="card-header">
				<h3 class="card-title"><i class="fa fa-whatsapp " ></i> STATUS LOG CORE WHATSAPP &nbsp &nbsp <i stle="font-family:times;">Looding Status Core.</i> &nbsp &nbsp <i style="color:yellow;" class='fa fa-spinner fa-spin fa-1.5x fa-fw'></i></h3>
		</div>
		<div class="card-body">
			<div class="overflow box-bordered mr-t-10" style="max-height: 50vh">
				<table id="dataTable" class="table table-bordered table-hover text-nowrap">
				<?php
					$flog="corewa.log";
					if (!file_exists($flog)) {
						$hasil .= "<caption>File ".$flog." Tidak Ditemukan</caption>";
					}else{
						$misi= explode("#",file_get_contents($flog));
						$no=count($misi);
						echo "<tr><th>No</th><th>Tanggal</th><th>API [S/R]</th><th>Nomor</th><th>Pesan</th><th>Status</th></tr>";
						for ($a=count($misi)-2;$a>-1;$a--) {
							echo "<tr>	<td>".$a."</td><td>".explode("|",$misi[$a])[0]."</td><td>".explode("|",$misi[$a])[1]."</td><td>".explode("|",$misi[$a])[2]."</td><td>".explode("|",$misi[$a])[3]."</td><td></td></tr>";
						}
					}
				?>
				</table>
			</div>	
		</div>
	</div>
</div>
</h7>