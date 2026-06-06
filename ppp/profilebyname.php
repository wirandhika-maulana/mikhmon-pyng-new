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
//error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	include "ppp/function.php";
	if (substr($_GET['idpppp'], 0, 1) == "*") {
		$ppp = $_GET['idpppp'];
	} elseif (substr($userprofile, 0, 1) != "") {
		$getprofile = $API->comm("/ppp/profile/print", array(
		"?name" => "$ppp",
		));
		$ppp = $getprofile[0]['.id'];
		if ($ppp == "") {
			echo "<b>User Profile not found</b>";
		}
	} else {
		$ppp = substr($ppp, 13);
	}

	$getbridge = $API->comm("/interface/bridge/print");

	$getprofile = $API->comm("/ppp/profile/print", array(
		"?.id" => "$ppp"
		));
	$profiledetalis = $getprofile[0];
	$pid = $profiledetalis['.id'];
	$pname = $profiledetalis['name'];
	$localaddress = $profiledetalis['local-address'];
	$remoteaddress = $profiledetalis['remote-address'];
	$bridge = $profiledetalis['bridge'];
	$ratelimit = $profiledetalis['rate-limit'];
	$dnsserver = $profiledetalis['dns-server'];

	if (isset($_POST['save'])) {
		$name = (preg_replace('/\s+/', '-', $_POST['name']));
		$localaddress = ($_POST['localaddress']);
		$remoteaddress = ($_POST['remoteaddress']);
		$bridge = ($_POST['bridge']);
		$ratelimit = ($_POST['ratelimit']);
		$dnsserver = ($_POST['dnsserver']);
	
		if ( $bridge == '' and $remoteaddress == '' ) {
			$cek = $API->comm("/ppp/profile/set", array(
				".id" => "$pid",
				"name" => "$name",
				"local-address" => "$localaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
				));
		}elseif ($bridge == '' && $remoteaddress<>'' ) {
			$cek = $API->comm("/ppp/profile/set", array(
				".id" => "$pid",
				"name" => "$name",
				"local-address" => "$localaddress",
				"remote-address" => "$remoteaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
			));
		}elseif ($bridge <> '' && $remoteaddress=='' ) {
			$cek = $API->comm("/ppp/profile/set", array(
				".id" => "$pid",
				"name" => "$name",
				"local-address" => "$localaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
				"bridge" => "$bridge",
			));
		} else {
			$cek = $API->comm("/ppp/profile/set", array(
				".id" => "$pid",
				"name" => "$name",
				"local-address" => "$localaddress",
				"remote-address" => "$remoteaddress",
				"bridge" => "$bridge",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
			));
		}
		$cek=json_encode($cek);
		if (strpos(strtolower($cek), '!trap')) {
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,\nEdit Profile GAGAL proses."); } 
			</script>';
		}else{
			$fdtprofil="ppp/csv/dthrgprofile.txt";
			$tulis =$ppp."^".$name."^".$_POST['mhrg']."#\n";
			if (!file_exists($fdtprofil)) {
				file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
			}else{
				$dtfile=explode("#",file_get_contents($fdtprofil));
				$writecev="";
				$edit="0";
				for ($x=0;$x<count($dtfile)-1;$x++) {
					if (ltrim(explode("^",$dtfile[$x])[0])==$ppp) {
						$edit="1";
					}
				}
				if ($edit=="0") {
					file_put_contents($fdtprofil,$tulis, FILE_APPEND | LOCK_EX);
				}else{
					$writecev="";
					for ($x=0;$x<count($dtfile)-1;$x++) {
						if (ltrim(explode("^",$dtfile[$x])[0])=="$ppp") {
							$writecev .=trim(explode("^",$dtfile[$x])[0]."^".explode("^",$dtfile[$x])[1]."^".$_POST['mhrg'])."#\n";
						}else{
							$writecev .=ltrim($dtfile[$x])."#\n";
						}
					}
					$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $fdtprofil);
					fwrite($handle, $writecev);
					fclose($handle);
					echo "<script>window.location='./?info=Data Berhasil Di Simpan.&session=" . $session . "'</script>";
				}
			}
		}
	}
	
}

?>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3><i class="fa fa-edit"></i> Edit PPP Profiles </h3>
      </div>
      <div class="card-body">
      <div class="w-5">
        <form autocomplete="off" method="post" action="">
          <div>
            <a class="btn bg-warning" href="./?ppp=profiles&session=<?= $session; ?>"> <i class="fa fa-close"></i> <?= $_close ?></a>
            <button type="submit" name="save" class="btn bg-primary"><i class="fa fa-save"></i>
              <?= $_save ?></button>
          </div>
          <table class="table">
            <tr>
              <td class="align-middle"><?= $_name ?></td>
              <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off" name="name" value="<?= $pname; ?>" required="1" autofocus></td>
            </tr>
            <tr>
              <td class="align-middle">Local Address</td>
              <td><input class="form-control" type="text" required="1" size="4" value="<?= $localaddress; ?>" autocomplete="off" name="localaddress"></td>
            </tr>
            <tr>
              <td class="align-middle">Remote Address</td>
              <td><input class="form-control" type="text" size="4" value="<?= $remoteaddress; ?>" autocomplete="off" name="remoteaddress"></td>
            </tr>
            <?php if (count($getbridge) != 0) { ?>
              <tr>
                <td class="align-middle">Bridge</td>
                <td>
                  <select class="form-control " name="bridge">
                    <?php if ($bridge == '') { ?>
                        <option value="">==Pilih==</option>
                    <?php } else { ?>
                        <option value="<?php echo $bridge; ?>"><?php echo $bridge ?></option>
                    <?php } ?>
                    <?php
                    $TotalReg = count($getbridge);
                    for ($i = 0; $i < $TotalReg; $i++) {
                      echo "<option value='" . $getbridge[$i]['name'] . "'>" . $getbridge[$i]['name'] . "</option>";
                    }
                    ?>
                  </select>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td class="align-middle">Outgoing Filter</td>
              <td>
				<select class="form-control" id="outgoingfilter" name="outgoingfilter">
					<?php
					if (empty($outgoingfilter)) {$outgoingfilter="none";}
					$data="input|forward|output|unused-hs-chain|hs-unauth|hs-unauth-to|hs-input|pre-hs-input|none";
					for ($x=0;$x<count(explode("|",$data));$x++) {
						if (explode("|",$data)[$x]==$outgoingfilter) {
							echo "<option value=".explode("|",$data)[$x]." selected>".explode("|",$data)[$x]."</option>";
						}else{
							echo "<option value=".explode("|",$data)[$x].">".explode("|",$data)[$x]."</option>";
						}
					}
					?>
				</select>
              </td>
            </tr>
            <tr>
              <td class="align-middle">DNS Server</td>
              <td><input class="form-control" type="text" size="4" value="<?= $dnsserver; ?>" autocomplete="off" name="dnsserver"></td>
            </tr>
            <tr>
              <td class="align-middle">Rate Limit</td>
              <td><input class="form-control" type="text" required="1" value="<?= $ratelimit; ?>" size="4" autocomplete="off" name="ratelimit" placeholder="example: rx/tx"></td>
            </tr>
            <tr>
              <td class="align-middle">Price / Month </td>
              <td><input class="form-control" type="text" autocomplete="off" name="mhrg" value="<?php echo caridtharga($ppp); ?>" required></td>
			</tr>
          </table>
        </form>
      </div>
      </div>
    </div>
  </div>
</div>
