<?php
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	require_once 'system.database.php';
	$file = './notif/setup.set';
	
	if (isset($_POST["send"])) {
		$iduser = $_POST['phone'];
		$dns =file_get_contents("./notif/session.log");
		if (substr($iduser,0,3)=='628') {
			$pesan	= "*Selamat ".sapaan().".* \n\n".$_POST['pesan']."\n\n*".$dns."* \n".date('d/M/Y H:i:s')."\n";
			$hasil=str_replace("\n","",sendWa($iduser,$pesan));
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,..\nStatus pengiriman\n.'.$hasil.'\n"); } 
			</script>';
		}else{
			$pesan	= "<b>Selamat ".sapaan().".</b> \n\n".$_POST['pesan']."\n\n<b>".$dns."</b>\n".date('d/M/Y H:i:s')."\n";
			$token=explode('|',file_get_contents('./webhook/webhookk.php'))[3];
			$hasil=sendMessage($iduser,$pesan,$token);
			$hasil1=json_decode($hasil,true)['ok'];
			if ($hasil1=='1') {
				$hasil1="Pesan berhasil dikirim.";
			}else{
				$hasil1="Pesan gagal dikirim.";
			}
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,..\nStatus pengiriman\n.'.$hasil1.'\n"); } 
			</script>';
		}
	}
	$scheme = $_SERVER['REQUEST_SCHEME'];
    if (strpos(strtolower($scheme), 'http') !== false){
        $cekhttps="https://";
    } else {
        $cekhttps=$scheme."://";
    }
    
    $urlpath = $_SERVER['REQUEST_URI'];
    $urlpath1 = explode('?',$urlpath);
    $urltele1 = $cekhttps.$_SERVER['HTTP_HOST'].$urlpath1[0].'notif/notifwa.php';
    $urltele1 = str_replace("admin.php","",$urltele1);
	if (isset($_POST["save"])) {
		
		$nowa 		= ($_POST['nowa']);
		$nowa1 		= ($_POST['nowa1']);
		$apikey 	= ($_POST['apikey']);
		$endpoint 	= ($_POST['endpoint']);

		$device 	= ($_POST['device']);
		$akwaid 	= ($_POST['akwaid']);
		$email   	= ($_POST['email']);
		
		$mtulis = $nowa."|-|".$apikey."|-|".$endpoint."|-|".$device."|-|".$akwaid."|-|".$nowa1."|-|".$urltele1."|-|".$email."|-|";
		$handle = fopen($file, 'w') or die('Cannot open file:  ' . $file);
		fwrite($handle, $mtulis);
		fclose($handle);
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,..\nData Telah Berhasil Di Simpan."); } 
		</script>';
	}

	if (file_exists($file)) {
		$misi		= file_get_contents($file);
		$misi0		= explode("|-|",$misi);
		$nowa		= $misi0[0];
		$apikey		= $misi0[1];
		$endpoint	= $misi0[2];
		$device		= $misi0[3];
		$akwaid		= $misi0[4];
		$nowa1		= $misi0[5];
		$email		= $misi0[7];
	}else{
		$nowa		= "";
		$apikey		= "";
		$endpoint	= "";
		$device		= "";
		$akwaid		= "";
		$nowa1		= "";
		$email		= "";
	}
	$status=help("kirimwa.id");
	$tinggi="380px";
	$qr="0";
	if (isset($_POST['adddevice'])) {
		$tinggi="150px";
		$status=adddevice($device,$akwaid);
	}
	if (isset($_POST['stadevice'])) {
		$tinggi="150px";
		$status=stadevice($device,$akwaid);
	}
	if (isset($_POST['deldevice'])) {
		$tinggi="150px";
		$status=deldevice($device,$akwaid);
	}
	if (isset($_POST['pairing'])) {
		$tinggi="235px";
		$status=pairing($device,$akwaid);
		$qr="1";
	}
	if (isset($_POST['qouta'])) {
		$tinggi="275px";
		$status=qouta($akwaid);
	}
	if (isset($_POST['webon'])) {
		$tinggi="185px";
		$stat1="Aktif";
		if ($cekhttps=="https://") {
			$device 	= ($_POST['device']);
			$nowa1 		= ($_POST['nowa1']);
			$email 		= ($_POST['email']);
			$akwaid 	= ($_POST['akwaid']);
			$urltele1 	= ($_POST['urltele1']);
		
			$status=webon($urltele1,$akwaid);
		}else{
			$status="Https:// diperlukan.";
		}
	}
	if (isset($_POST['weboff'])) {
		$tinggi="185px";
		if ($cekhttps=="https://") {
			$status=weboff($email,$urltele1,$akwaid);
		}else{
			$status="Https:// diperlukan.";
		}
	}
	if (isset($_POST['websta'])) {
		$tinggi="185px";
		if ($cekhttps=="https://") {
			$status=websta($akwaid);
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

<script>
function pairingFunction() {
    window.location.href = "https://apiwa.mimoassist.homes";
}
function tutorialFunction() {
    window.location.href = "https://youtu.be/wPOJHnI6aJc";
}
function tutorialFunction() {
    window.location.href = "https://wa.me/6289633033332";
}
</script>

<form autocomplete="off" method="post" action="">
	<div class="col-6">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><i class="fa fa-whatsapp " ></i> Setting WebHook Bot WhatsApp.</h3>
			</div>
			<div class="card-body">
				<table class="table table-sm">
					<?php 
                        echo '<tr><td class="align-middle">Device</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="device" placeholder="Device" value="'.$device.'"></td></tr>';
                        echo '<tr><td class="align-middle">Email</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="email" placeholder="Isi dengan email " value="'.$email.'" required="1"/></td></tr>';
                        echo '<tr><td class="align-middle">No Wa Owner</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="nowa" placeholder="62" value="'.$nowa.'" required="1"/></td></tr>';
                        echo '<tr><td class="align-middle">No Wa Bot</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="nowa1" placeholder="62" value="'.$nowa1.'" required="1"/></td></tr>';
                        echo '<tr><td class="align-middle">Api Key ApiWa</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="apikey" placeholder="Api Key" value="'.$apikey.'"></td></tr>';
                        echo '<tr><td class="align-middle">Api Key KirimWa</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="akwaid" placeholder="Api Key kirimwa.id" value="'.$akwaid.'"></td></tr>';
                        echo '<tr><td class="align-middle">End Point</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="endpoint" placeholder="end point" value="https://apiwa.mimoassist.homes/api/send-message" readonly></td></tr>';
                        echo '<tr><td class="align-middle">File Core</td><td>:</td><td><input class="form-control" id="useradm" type="text" size="40" name="filecore" placeholder="Url Hosting" value="'.$urltele1.'" readonly></td></tr>';
                        echo '<tr><td class="align-middle"></td><td></td><td>
                            <div>
                            <input class="btn bg-green" type="submit" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="save" title="Simpan Data Set Up." value="Save">
                            <input class="btn bg-green" type="button" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="pair" title="Pairing" value="Pairing" onclick="pairingFunction()">
                            <input class="btn bg-green" type="button" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="tutorial" title="Tutorial" value="Tutorial" onclick="tutorialFunction()">
                            <input class="btn bg-green" type="button" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="help" title="Help" value="Help CS" onclick="helpFunction()">
                        </div>
                        </td></tr>';
                    ?>
				</table>
			</div>
		</div>
	</div>
	<div class="col-6">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title"><i class="fa fa-rss" aria-hidden="true"></i> Send &nbsp <i class="fa fa-whatsapp " ></i> WhatsApp, &nbsp  <i class="fa fa-telegram " ></i> Telegram</h3>
			</div>
			<div class="card-body">
				<table class="table table-sm">
					<?php 
						$dtuser=lihatdata();
						echo '<tr><td class="align-middle">No Wa Reseller</td><td>:</td><td>
							<select class="form-control" name="phone">
								<option value="">Pilih No / Id User Re-Seller</option>';
								for ($x=0;$x<count($dtuser);$x++) {
									$sid="ID Telegram";
									if (substr($dtuser[$x]['id_user'],0,3)=='628') {
										$sid="ID WA";
									}
									if ($dtuser[$x]['id_user']==$_GET['notuj']) {
										echo '<option value='.$dtuser[$x]['id_user'].' selected>'.$sid." ".$dtuser[$x]['id_user']." ".$dtuser[$x]['nama_seller'].'</option>';
									}else{
										echo '<option value='.$dtuser[$x]['id_user'].'>'.$sid." ".$dtuser[$x]['id_user']." ".$dtuser[$x]['nama_seller'].'</option>';
									}
								}
								if ($ccek=="0") {
									echo '<option valu="">Tidak ada reseller dengan WA</option>';
								}
							echo '</select>
						</td></tr>';
						echo '<tr><td class="align-middle-top" valign="top">Pesan</td><td valign="top">:</td><td>
						<textarea class="form-control class="align-middle-top"" id="useradm"  name="pesan" placeholder="Isi pesan" rows="5" cols="33"></textarea>
						</td></tr>';
						echo '<tr><td class="align-middle">
						</td><td></td><td><input  class="btn bg-green" type="submit" style="color:black;cursor: pointer;font-size:16px;font-family:times new roman;font-weight:bold;border-radius:5px green;" name="send" title="Kirim pesan." value="SEND"></td></tr>';
					?>
				</table>
			</div>
		</div>
		<div class="card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Cara Membuat Bot WhatsApp APIWA </h3>
        </div>
        <textarea id="textnya" class="form-control" name="status" style="margin-top: 0px; margin-bottom: 0px; height:110px; max-width:600px;font-size:14px;" title="Info" readonly></textarea>
    </div>
</div>
<script>
    let i = 0;
    let textArea = document.getElementById('textnya');
    let part1 = "";  // Bagian pertama teks
    let part2 = "";  // Bagian kedua teks

    // Bagian pertama teks (hingga setelah point 5)
    const part1Content = "1. Isikan semua kolom yang disediakan. \n2. Setelah semua terisi klik SAVE. \n3. Copy link dalam End Point. \n4. Setelah link tercopy klik Pairing ApiWa. \n5. Pilih Login as User MimoAssist..........";

    // Bagian kedua teks (dari point 6 dan seterusnya)
    const part2Content = "6. Dalam dasbord pilih + Add User, isi Session Name, klik Submit. \n7. Pilih Session Name yang telah dibuat. \n8. klik Settings dalam baris ACTION untuk melakukan setting webhook. \n9. Klik qrcode dalam baris ACTION untuk melakukan paring. \n10. Setelah semua proses dilakukan, coba kirim pesan dari nomor wa owner ke wa bot dengan perintah start...........";

    // Fungsi untuk menampilkan teks satu per satu
    function typeText() {
        if (i < part1.length) {
            // Menambahkan karakter satu per satu dari part1
            textArea.value += part1.charAt(i);
            i++;
            setTimeout(typeText, 125); // Menunggu 100ms sebelum menampilkan karakter berikutnya
        } else if (i < part1.length + part2.length) {
            // Setelah bagian pertama selesai, hapus teks dan tampilkan bagian kedua
            if (i === part1.length) {
                textArea.value = '';  // Menghapus teks sebelumnya
            }
            textArea.value += part2.charAt(i - part1.length); // Menambahkan karakter dari part2
            i++;
            setTimeout(typeText, 125); // Menunggu 100ms sebelum menampilkan karakter berikutnya
        } else {
            // Setelah bagian kedua selesai, ulangi dari awal
            i = 0;
            textArea.value = '';  // Menghapus teks setelah bagian kedua selesai
            setTimeout(typeText, 500); // Memberi delay sejenak sebelum mulai ulang
        }
    }

    // Memulai fungsi saat halaman dimuat
    window.onload = function() {
        part1 = part1Content;  // Isi bagian pertama
        part2 = part2Content;  // Isi bagian kedua
        typeText();  // Mulai menampilkan teks
    };
</script>
			</div>
</form>

<h7>
<?php
$fcek='./notif/kirim.txt';
$cek=" Api-".explode('-',file_get_contents($fcek))[1];
$flog="./notif/notifwa.log";
?>
<div class="col-12">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"><i class="fa fa-whatsapp " ></i> STATUS LOG CORE WHATSAPP &nbsp &nbsp &nbsp <i stle="font-family:times;"> [ <?php echo explode('/',$flog)[2]." ] ";?> </i> &nbsp <?php echo date('d-M-Y H:i:s');?> </h3>
		</div>
		<div class="card-body">
			<div class='overflow box-bordered mr-t-10' style='max-height: 150vh;'>
				<table id='dataTable' class='table table-bordered table-hover text-nowrap' style='width:650px;'>
<?php
					if (!file_exists($flog)) {
						$hasil = "<caption style='font-size:20px;'>File ".$flog." Tidak Ditemukan / belum tersedia.</caption>";
					}else{
						$misi= explode("#",file_get_contents($flog));
						$nowa= explode("|",file_get_contents('./notif/setup.set'))[0];
						$no=count($misi);
						$hasil = "<tr><th colspan='3'>Log WhatsApp $nowa </th></tr>";
						$hasil .="<tr><th align='right'> No. </th><th> Tanggal Transaksi</th><th> Status </th><th> Nomor </th><th> Pesan </th></tr>";
						for ($a=count($misi)-2;$a>-1;$a--) {
							$no--;
							if (explode('|-|',$misi[$a])[2]=='2') {
								if (strpos(explode('|-|',$misi[$a])[3],'kwid')!==false) {
									if (strpos(explode('|-|',$misi[$a])[3],'webhook_type')!==false) {
										$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
										$hasil .="
										<tr>
											<td align='right' valign='top'>".$no.".</td>
											<td  valign='top'>".explode('|-|',$misi[$a])[0]."</td>
											<td valign='top'>".$dtjson['status']."</td>
											<td valign='top'><a href='?id=wawebhook&notuj=".$dtjson['payload']['phone_number']."' title='Kirim pesan'>".$dtjson['payload']['phone_number']."</a></td>
											<td valign='top'>".str_replace("\n","<br>",str_replace('*','',$dtjson['payload']['message']))."</td>
										</tr>";
									}else{
										$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
										$hasil .="
										<tr>
											<td align='right' valign='top'>".$no.".</td>
											<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
											<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
											<td colspan='2' valign='top'>".$dtjson['message']."</td>
										</tr>";
									}
								}else{
									$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
									$mket="";
									if ($dtjson['payload']['from_me']=='1') {
										$mket=" &nbsp -=> Tidak diproses <=-";
									}else{
										if (substr($dtjson['payload']['text'],0,1)<>'/') {
											$mket=" &nbsp -=> Diabaikan <=-";
										}
									}
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".$dtjson['payload']['sender']."' title='Kirim pesan'>".$dtjson['payload']['sender']."</a></td>
										<td valign='top'>".str_replace('*','',$dtjson['payload']['text'])." ".$mket."</td>
									</tr>";
								}
							}else{
//parsing data mpwa							
//parshing data mpwa
								$cekpesan=explode('|-|',$misi[$a])[3];
								if (explode('|-|',$misi[$a])[1]=='recive') {
									$json=json_decode($cekpesan,true);
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".explode('@',$json['from'])[0]."' title='Kirim pesan'>".explode('@',$json['from'])[0]."</a></td>
										<td valign='top'>".$json['message']."</td>
									</tr>";
								}elseif (explode('|-|',$misi[$a])[1]=='report') {
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".explode('|-|',$misi[$a])[4]."' title='Kirim pesan'>".explode('|-|',$misi[$a])[4]."</a></td>
										<td valign='top'>".str_replace("*","",explode('|-|',$misi[$a])[5])."</td>
									</tr>";
								}elseif (explode('|-|',$misi[$a])[1]=='error') {

								}else{
									
								}
							}
						}
					}
					echo $hasil;
?>				</table>
			</div>	
		</div>
	</div>
</div>
</h7>