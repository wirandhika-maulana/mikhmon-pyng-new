<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);

if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {
	include "ppp/function.php";
    $getprofile = $API->comm("/ppp/profile/print");
    date_default_timezone_set('Asia/Jakarta');

    if (isset($_POST['save'])) {
		$name 		= preg_replace('/\s+/', '-', $_POST['name']);
		$password 	= $_POST['password'];
		$idwatele	= $_POST['idwatele'];
		$npelanggan	= $_POST['npelanggan'];
        if (strlen($name)<4) {
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,\nPembuatan new secret GAGAL .......\nUsername, min 4 Huruf."); } 
			</script>';
		}elseif (strlen($password)<4) {
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,\nPembuatan new secret GAGAL .......\nPassword, min 4 Huruf."); } 
			</script>';
		}else{
			$service 	= ($_POST['service']);
			$profile 	= ($_POST['profile']);

			if ($_POST['msat']=="1") {
				$interval0 	= $_POST['interva']*1;
				$interval	= $interval0."d 00:00:00";

				$tambah=(60*60*24)*$interval0; // Harian +
				$cek=date('M/d/Y H:i:s',strtotime(date('Y/m/d H:i:s'))+$tambah);
				$start_date	= explode(" ",$cek)[0];
				$start_time	= explode(" ",$cek)[1];

				$mket		= $_POST['interva']." Hari";
			}elseif ($_POST['msat']=="2") {
				$interval0 	= $_POST['interva']*7;
				$interval	= $interval0."d 00:00:00";

				$tambah=(60*60*24)*$interval0; //mingguan +
				$cek=date('M/d/Y H:i:s',strtotime(date('Y/m/d H:i:s'))+$tambah);
				$start_date	= explode(" ",$cek)[0];
				$start_time	= explode(" ",$cek)[1];

				$mket		= $_POST['interva']." Minggu";
			}elseif ($_POST['msat']=="3") {
				$interval0 	= $_POST['interva']*31;
				$interval	= $interval0."d 00:00:00";

				$tambah=(60*60*24)*$interval0; //x bulan +
				$cek=date('M/d/Y H:i:s',strtotime(date('Y/m/d H:i:s'))+$tambah);
				$start_date	= explode(" ",$cek)[0];
				$start_time	= explode(" ",$cek)[1];

				$mket		= $_POST['interva']." Bulan";
			}else{

				$tambah=(60*60*24)*31;
				$cek=date('M/d/Y H:i:s',strtotime(date('Y/m/d H:i:s'))+$tambah);
				$start_date	= explode(" ",$cek)[0];
				$start_time	= explode(" ",$cek)[1];

				$interval	= "31d 00:00:00";
				$mket		= "x Bulanan";
			}
		
			date_default_timezone_set('Asia/Jakarta');


			// $on_event = "/ppp secret set disabled=yes [/ppp secret find name=" . $name . "] \r /system scheduler remove [find name=" . $name . "]";

			$on_event = "/ppp secret set disabled=yes [/ppp secret find name=" . $name . "] \r /system scheduler disable [find name=" . $name . "]";

			$addppp	= $API->comm("/ppp/secret/add", array(
				/*"add-mac-cookie" => "yes",*/
				"name" 				=> $name,
				"password" 			=> $password,
				"service" 			=> $service,
				"profile" 			=> $profile,
				"local-address"		=> $_POST['locadd'],
				"remote-address"	=> $_POST['remadd'],
				"comment"			=> "Start in ".date('M/d/Y H:i:s'),
			));
			$cek = json_encode($addppp);
			if (strpos(strtolower($cek), '!trap')) {
//				$text	= str_replace(":","\n",explode('"',$cek)[5])."\n";

//				echo "<script>window.location='./?info=".$cek."&session=" . $session . "'</script>";

				echo '<script type="text/javascript">
				window.onload = function () { alert("INFO,\nPembuatan new secret GAGAL, \nPada Proses Add Secret....."); } 
				</script>';

			}else{
				if ($msat<>4) {
					$comm="Monitoring ".$mket." ".$name;
					$addsch	= $API->comm("/system/scheduler/add", array(
						/*"add-mac-cookie" => "yes",*/
						"name"				=> "$name",
						"start-date" 		=> "$start_date",
						"start-time" 		=> "$start_time",
						"interval" 			=> "$interval",
						"on-event" 			=> "$on_event",
						"comment" 			=> $comm,
					));
					$cek1 = json_encode($addsch);
					if (strpos(strtolower($cek1), '!trap')) {
						$addsch	= $API->comm("/ppp/secret/remove", array(
							".id"	=> $cek,
						));
						
//						echo "<script>window.location='./?info=".$cek."&session=" . $session . "'</script>";
						
						echo '<script type="text/javascript">
						window.onload = function () { alert("INFO,\nPembuatan new secret GAGAL, \nPada Bagian Add Schedule .......\n<?=$cek1?>"); } 
						</script>';
						
					}else{
						$fdtprofil="ppp/csv/dtpelanggan.txt";
						$tulis=str_replace('"','',$cek)."^".strtotime(date('Y/m/d H:i:s'))."^".$name."^".$password."^".$service."^".$profile."^".$_POST['mhrg']."^".$_POST['locadd']."^".$_POST['remadd']."^Start in ".date('M/d/Y H:i:s')."^".$interval."^".idwatele($_POST['idwatele'])."^".ucwords($_POST['npelanggan'])."^".$mket."#\n";
						if (!file_exists($fdtprofil)) {
							file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
						}else{
							$dtfile=explode("#",file_get_contents($fdtprofil));
							$writecev="";
							for ($x=0;$x<count($dtfile)-1;$x++) {
								$writecev .=ltrim($dtfile[$x])."#\n";
							}
							$writecev .=$tulis;
				
							$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $filec);
							fwrite($handle, $writecev);
							fclose($handle);
						}
						sleep(1);
						echo "<script>window.location='./?ppp=secrets&profile=all&session=" . $session . "'</script>";
					}
				}else{
					$fdtprofil="ppp/csv/dtpelanggan.txt";
					$tulis=str_replace('"','',$cek)."^".strtotime(date('Y/m/d H:i:s'))."^".$name."^".$password."^".$service."^".$profile."^".$_POST['mhrg']."^".$_POST['locadd']."^".$_POST['remadd']."^Start in ".date('M/d/Y H:i:s')."^".$interval."^".idwatele($_POST['idwatele'])."^".ucwords($_POST['npelanggan'])."^".$mket."#\n";
					if (!file_exists($fdtprofil)) {
						file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
					}else{
						$dtfile=explode("#",file_get_contents($fdtprofil));
						$writecev="";
						for ($x=0;$x<count($dtfile)-1;$x++) {
							$writecev .=ltrim($dtfile[$x])."#\n";
						}
						$writecev .=$tulis;
			
						$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $filec);
						fwrite($handle, $writecev);
						fclose($handle);
					}
					sleep(1);
					echo "<script>window.location='./?ppp=secrets&profile=all&session=" . $session . "'</script>";
				}
			}
		}
    }
	
	
	$dtprofile	= $API->comm("/ppp/profile/print",["?.id"=>$_GET['idprof'],]);
	$locadd		= $dtprofile[0]['local-address'];
       
//	$dtsecret	= $API->comm("/ppp/secret/print",["?profile"=>$dtprofile[0]['name'],]);
	$dtsecret	= $API->comm("/ppp/secret/print");

	if (count($dtsecret)<1) {
		$remadd	= explode(".",$dtprofile[0]['local-address'])[0].".".explode(".",$dtprofile[0]['local-address'])[1].".".explode(".",$dtprofile[0]['local-address'])[2].".2";
	}else{
		$lastip3	= explode(".",$dtprofile[0]['local-address'])[0].".".explode(".",$dtprofile[0]['local-address'])[1].".".explode(".",$dtprofile[0]['local-address'])[2];

		$lastip		= 0;

		for ($x=2;$x<255;$x++) {
			$tul=$lastip3.".".$x;
			$dtsecretest = $API->comm("/ppp/secret/print",["?remote-address"=>"$tul",]);
			if (count($dtsecretest)==0) {
				break;
			}
		}
		if ($x>254) {
			echo "<script>window.location='./?info=Ip Full&session=" . $session . "'</script>";
		}
		$remadd= trim($tul);
	}
}

?>
<div class="row">
    <div class="col-8">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-plus"></i> Add PPP Secrets By Profile<small id="loader" style="display: none;"><i><i
                                class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class="btn bg-warning" href="./?ppp=secrets&session=<?= $session; ?>"> <i
                                class="fa fa-close btn-mrg"></i> <?= $_close ?></a>
                        <button type="submit" name="save" class="btn bg-primary btn-mrg"><i
                                class="fa fa-save btn-mrg"></i> <?= $_save ?></button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle">Profile</td>
                            <td>
                                <select class="form-control" name="profile" required="1">
                                    <?php
                                    $TotalReg = count($getprofile);
                                    for ($i = 0; $i < $TotalReg; $i++) {
                                        if ($getprofile[$i]['default']=='false') {
											if ($getprofile[$i]['.id']==$_GET['idprof']) {
												echo "<option value=".$getprofile[$i]['name'].">" . $getprofile[$i]['name'] . "</option>";
											}else{
												
											}
										}
									}
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Harga/ Bulan</td>
                            <td>
							<input class="form-control" type="hidden" autocomplete="off" name="mhrg" value="<?=caridtharga($_GET['idprof']) ?>" >
							<input class="form-control" type="text" autocomplete="off" name="harga" value="<?=rupiah(caridtharga($_GET['idprof'])) ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Local Address</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="locadd" value="<?=$locadd ?>"readonly>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Remote Address</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="remadd" value="<?=$remadd?>"readonly>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle"><?= $_user_name ?></td>
                            <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off"
                                    name="name" value="<?=$name?>" required="1" autofocus></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Password</td>
                            <td><input class="form-control" type="text" size="4" autocomplete="off" name="password" value="<?=$password?>" required="1">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Service</td>
                            <td>
                                <select class="form-control" name="service" required="1">
                                    <option value="any">any</option>
                                    <option value="async">async</option>
                                    <option value="l2tp">l2tp</option>
                                    <option value="ovpn">ovpn</option>
                                    <option value="pppoe" selected>pppoe</option>
                                    <option value="pptp">pptp</option>
                                    <option value="sstp">sstp</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Interval</td>
                            <td valign="middle"><input type="number" size="4" autocomplete="off" placeholder="1" value="1" name="interva" style="width:70px;font-size:16px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;" required="1"> 
								<select  name="msat" style="width:200px;font-size:15px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;font-family:times;">
									<option value="1" style='text-align:left;'> Hari &nbsp&nbsp </option>
									<option value="2" style='text-align:left;'> Minggu &nbsp&nbsp </option>
									<option value="3" style='text-align:left;'> Bulan &nbsp&nbsp </option>
									<option value="4" style='text-align:left;' selected > Bulanan &nbsp&nbsp&nbsp </option>
								</select>
							</td>
                        </tr>
                        <tr>
                            <td class="align-middle">No WhatsApp / Id Telegram</td>
                            <td><input class="form-control" type="number" autocomplete="off" name="idwatele" value="<?=$idwatele?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Nama Pelanggan</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="npelanggan" value="<?=$npelanggan?>">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function remSpace() {
    var upName = document.getElementsByName("name")[0];
    var newUpName = upName.value.replace(/\s/g, "-");
    //alert("<?php if ($currency == in_array($currency, $cekindo['indo'])) {
    echo "Nama Profile tidak boleh berisi spasi";
} else {
    echo "Profile name can't containing white space!";
} ?>
");
upName.value = newUpName;
upName.focus();
}
</script>
