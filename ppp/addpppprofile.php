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

  $getbridge = $API->comm("/interface/bridge/print");
  $getremoteaddress = $API->comm("/ip/pool/print");

  if (isset($_POST['save'])) {
		$name = (preg_replace('/\s+/', '-', $_POST['name']));
		$localaddress = ($_POST['localaddress']);
		$remoteaddress = ($_POST['remoteaddress']);
		$bridge = ($_POST['bridge']);
		$ratelimit = ($_POST['retelimit']);
		$dnsserver = ($_POST['dnsserver']);

		if ( $bridge == '' and $remoteaddress == '' ) {
			$addok=$API->comm("/ppp/profile/add", array(
				"name" => "$name",
				"local-address" => "$localaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
				));
		}elseif ($bridge == '' && $remoteaddress<>'' ) {
			$addok=$API->comm("/ppp/profile/add", array(
				"name" => "$name",
				"local-address" => "$localaddress",
				"remote-address" => "$remoteaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
			));
		}elseif ($bridge <> '' && $remoteaddress=='' ) {
			$addok=$API->comm("/ppp/profile/add", array(
				"name" => "$name",
				"local-address" => "$localaddress",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
				"bridge" => "$bridge",
			));
		}else{
			$addok=$API->comm("/ppp/profile/add", array(
				"name" => "$name",
				"local-address" => "$localaddress",
				"remote-address" => "$remoteaddress",
				"bridge" => "$bridge",
				"rate-limit" => "$ratelimit",
				"dns-server" => "$dnsserver",
				));
		}
	
	$cek=json_encode($addok);
	
	if (strpos(strtolower($cek), '!trap')) {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,\nAdd Profile GAGAL proses."); } 
		</script>';
	}else{
		$fdtprofil="ppp/csv/dthrgprofile.txt";
		$tulis = str_replace('"','',$cek)."^".$name."^".$_POST['mhrg']."#\n";
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
		echo "<script>window.location='./?ppp=profiles&session=" . $session . "'</script>";
	}
  }
}
?>
<div class="row">
  <div class="col-12">
    <div class="card box-bordered">
      <div class="card-header">
        <h3><i class="fa fa-plus"></i> Add PPP Profiles <small id="loader" style="display: none;"><i><i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
      </div>
      <div class="card-body">
      <div class="w-7">
        <form autocomplete="off" method="post" action="">
          <div>
            <a class="btn bg-warning" href="./?ppp=profiles&session=<?= $session; ?>"> <i class="fa fa-close btn-mrg"></i> <?= $_close ?></a>
            <button type="submit" name="save" class="btn bg-primary btn-mrg"><i class="fa fa-save btn-mrg"></i> <?= $_save ?></button>
          </div>
          <table class="table">
            <tr>
              <td class="align-middle"><?= $_name ?></td>
              <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off" name="name" value="" required="1" autofocus></td>
            </tr>
            <tr>
              <td class="align-middle">Local Address</td>
              <td><input class="form-control" type="text" size="4" required="1" autocomplete="off" name="localaddress"></td>
            </tr>
            <tr>
              <td class="align-middle">Remote Address</td>
               <td>
                  <select class="form-control " name="remoteaddress" >
                    <?php $TotalRemote = count($getremoteaddress);
                    echo "<option value=''>none</option>";
                    for ($i = 0; $i < $TotalRemote; $i++) {
                      echo "<option value='" . $getremoteaddress[$i]['name'] . "'>" . $getremoteaddress[$i]['name'] . "</option>";
                    }
                    ?>
                  </select>
                </td>
            </tr>
            <?php if (count($getbridge) != 0) { ?>
              <tr>
                <td class="align-middle">Bridge</td>
                <td>
                  <select class="form-control " name="bridge">
                    <option value="">==Pilih==</option>
                    <?php $Totalbridge = count($getbridge);
                    for ($i = 0; $i < $Totalbridge; $i++) {
                      echo "<option value='" . $getbridge[$i]['name'] . "'>" . $getbridge[$i]['name'] . "</option>";
                    }
                    ?>
                  </select>
                </td>
              </tr>
            <?php } ?>
            <tr>
              <td class="align-middle">DNS Server</td>
              <td><input class="form-control" type="text" size="4" autocomplete="off" name="dnsserver" required></td>
            </tr>
            <tr>
              <td class="align-middle">Rate Limit</td>
              <td><input class="form-control" type="text" size="4" autocomplete="off" required="1" name="retelimit" placeholder="example: rx/tx"></td>
            </tr>
            <tr>
              <td class="align-middle">Price / Month</td>
              <td><input class="form-control" type="number" autocomplete="off" name="mhrg" value="<?= $mhrg; ?>" required></td>
            </tr>
          </table>
        </form>
      </div>
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
              } ?>");
    upName.value = newUpName;
    upName.focus();
  }
</script>