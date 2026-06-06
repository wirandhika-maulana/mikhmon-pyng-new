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
	
	if (isset($_POST['save'])) {
		$fdtprofil="ppp/csv/dtpelanggan.txt";
		if (!file_exists($fdtprofil)) {
			file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
		}else{
			$dtfile=explode("#",file_get_contents($fdtprofil));
			$writecev="";
			$edit="0";
			for ($x=0;$x<count($dtfile)-1;$x++) {
				if (ltrim(explode("^",$dtfile[$x])[0])==$secretbyname) {
					$edit="1";
				}
			}
			if ($edit=="0") {
				file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
			}else{
				$writecev="";
				for ($x=0;$x<count($dtfile)-1;$x++) {
					if (ltrim(explode("^",$dtfile[$x])[0])==$secretbyname) {
						$writecev .=trim(explode("^",$dtfile[$x])[0]."^".explode("^",$dtfile[$x])[1]."^".explode("^",$dtfile[$x])[2]."^".explode("^",$dtfile[$x])[3]."^".
									explode("^",$dtfile[$x])[4]."^".explode("^",$dtfile[$x])[5]."^".$_POST['mhrg']."^".explode("^",$dtfile[$x])[7]."^".
									explode("^",$dtfile[$x])[8]."^".explode("^",$dtfile[$x])[9]."^".explode("^",$dtfile[$x])[10]."^".idwatele($_POST['nonotif']).
									"^".ucwords($_POST['npelanggan'])."^".explode("^",$dtfile[$x])[13])."^".$_POST['notif']."^#\n";
					}else{
						$writecev .=ltrim($dtfile[$x])."#\n";
					}
				}
				$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $fdtprofil);
				fwrite($handle, $writecev);
				fclose($handle);
				echo '<script type="text/javascript">
				window.onload = function () { alert("INFO,\nData Berhasil Disimpan."); } 
				</script>';
			}
		}
    }
}
?>
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-edit"></i> Detail PPP Secret <?= caridtpelanggan1($secretbyname,2) ?></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class="btn bg-warning" href="./?ppp=secrets&session=<?= $session; ?>"> <i
                                class="fa fa-close"></i> <?= $_close ?></a>
                        <button type="submit" name="save" class="btn bg-primary"><i class="fa fa-save"></i>
                            <?= $_save ?></button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle"><?= $_name ?></td>
                            <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off"
                                    name="name" value="<?= caridtpelanggan1($secretbyname,2) ?>" required="1" readonly></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Password</td>
                            <td><input class="form-control" type="text" size="4" autocomplete="off" name="password"
                                    value="<?= caridtpelanggan1($secretbyname,3) ?>" readonly></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Profile</td>
                            <td><input title="Not Change." class="form-control" type="text"  autocomplete="off" name="profile"
                                    value="<?= caridtpelanggan1($secretbyname,5) ?>" readonly ></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Harga / Bulan</td>
                            <td>
								<input title="Not Change." class="form-control" type="hidden"  autocomplete="off" name="mhrg" value="<?= caridtharga1(caridtpelanggan1($secretbyname,5)) ?>">
								<input title="Not Change." class="form-control" type="text"  autocomplete="off" name="xmhrg" value="<?=rupiah(caridtharga1(caridtpelanggan1($secretbyname,5))) ?>" readonly ></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Local Address</td>
                            <td><input title="Not Change." class="form-control" type="text"  autocomplete="off" name="locadd"
                                    value="<?= caridtpelanggan1($secretbyname,7) ?>" readonly ></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Remote Address</td>
                            <td><input title="Not Change." class="form-control" type="text"  autocomplete="off" name="remadd"
                                    value="<?= caridtpelanggan1($secretbyname,8) ?>" readonly ></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Service</td>
                            <td>
                                <select class="form-control" name="service" required="1">
                                <?php 
								$data="any|async|l2tp|ovpn|pppoe|pptp|sstp|";
								$dtasal=explode("|",$data);
								for ($x=0;$x<count($dtasal);$x++) {
									if ($dtasal[$x]==caridtpelanggan1($secretbyname,4)) {
										echo "<option value=".$dtasal[$x]." selected >".$dtasal[$x]."</option>";
									}else{
//										echo "<option value=".$dtasal[$x].">".$dtasal[$x]."</option>";
									}
								}
								if (caridtpelanggan1($secretbyname,13)=="4") {
									$int="1";
								}else{
									$int=caridtpelanggan1($secretbyname,13);
								}
								?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Interval</td>
                            <td valign="middle"><input type="number" size="4" autocomplete="off" placeholder="1" value="<?=$int?>" name="interva" style="width:70px;font-size:16px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;" readonly> 
								<select  name="msat" style="width:200px;font-size:15px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;font-family:times;">
									<?php
									$data="Hari|Minggu|Bulan|Bulanan";
									for ($x=0;$x<count(explode("|",$data));$x++) {
										$nx=$x+1;
										if (explode("|",$data)[$x]==explode(" ",caridtpelanggan1($secretbyname,13))[1]) {
											echo "<option style='text-align:left;' value=".$nx." selected> ".explode("|",$data)[$x]." </option>";
										}else{
//											echo "<option style='text-align:left;' value=".$nx."> ".explode("|",$data)[$x]." </option>";
										}
									}
									?>
								</select>
							</td>
                        </tr>
                        <tr>
                            <td class="align-middle">No WhatsApp / Id Telegram</td>
                            <td><input class="form-control" type="number" autocomplete="off" name="nonotif" value="<?= caridtpelanggan1($secretbyname,11) ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Nama Pelanggan</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="npelanggan" value="<?= caridtpelanggan1($secretbyname,12) ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Notifikasi</td>
                            <td>
								<div class="w-3">
									<select class="form-control" type="text" name="notif">
										<?php
										if (caridtpelanggan1($secretbyname,14)=="1") {
											echo "<option value='1' selected>On</option><option value='2'>Off</option>";
										}else{
											echo "<option value='1' >On</option><option value='2' selected>Off</option>";
										}
										?>
									</select>
								</div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>