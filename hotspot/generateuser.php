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

ini_set('max_execution_time', 300);

if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
// time zone
date_default_timezone_set($_SESSION['timezone']);

	$genprof = $_GET['genprof'];
	if ($genprof != "") {
		$getprofile = $API->comm("/ip/hotspot/user/profile/print", array(
			"?name" => "$genprof",
		));
		$ponlogin = $getprofile[0]['on-login'];
		$getprice = explode(",", $ponlogin)[2];
		if ($getprice == "0") {
			$getprice = "";
		} else {
			$getprice = $getprice;
		}

		$getvalid = explode(",", $ponlogin)[3];

		$getlocku = explode(",", $ponlogin)[6];
		if ($getlocku == "") {
			$getprice = "Disable";
		} else {
			$getlocku = $getlocku;
		}

		if ($currency == in_array($currency, $cekindo['indo'])) {
			$getprice = $currency . " " . number_format((float)$getprice, 0, ",", ".");
		} else {
			$getprice = $currency . " " . number_format((float)$getprice);
		}
		$ValidPrice = "<b>Validity : " . $getvalid . " | Price : " . $getprice . " | Lock User : " . $getlocku . "</b>";
	} else {
	}

	$srvlist = $API->comm("/ip/hotspot/print");

	// Load reseller data
	$resellerFile = './voucher/reseller.json';
	$resellerData = array();
	if (file_exists($resellerFile)) {
		$resellerData = json_decode(file_get_contents($resellerFile), true);
		if (!is_array($resellerData)) {
			$resellerData = array();
		}
	}

	// Load quick qty data
	$quickQtyFile = './voucher/quickqty.json';
	$quickQtyData = array(10, 25, 100, 200, 300);
	if (file_exists($quickQtyFile)) {
		$tmpQty = json_decode(file_get_contents($quickQtyFile), true);
		if (is_array($tmpQty) && count($tmpQty) > 0) {
			$quickQtyData = $tmpQty;
		}
	}

	// Indonesian month names for comment auto-fill
	$bulanIndo = array(
		1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
		5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
		9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
	);
	$currentBulan = $bulanIndo[(int)date('n')];

	if (isset($_POST['qty'])) {
		
		$qty = ($_POST['qty']);
		$server = ($_POST['server']);
		$user = ($_POST['user']);
		$userl = ($_POST['userl']);
		$prefix = ($_POST['prefix']);
		$char = ($_POST['char']);
		$profile = ($_POST['profile']);
		$timelimit = ($_POST['timelimit']);
		$datalimit = ($_POST['datalimit']);
		$adcomment = ($_POST['adcomment']);
		$mbgb = ($_POST['mbgb']);
		if ($timelimit == "") {
			$timelimit = "0";
		} else {
			$timelimit = $timelimit;
		}
		if ($datalimit == "") {
			$datalimit = "0";
		} else {
			$datalimit = $datalimit * $mbgb;
		}
		if ($adcomment == "") {
			$adcomment = "";
		} else {
			$adcomment = $adcomment;
		}
		$getprofile = $API->comm("/ip/hotspot/user/profile/print", array("?name" => "$profile"));
		$ponlogin = $getprofile[0]['on-login'];
		$getvalid = explode(",", $ponlogin)[3];
		$getprice = explode(",", $ponlogin)[2];
		$getsprice = explode(",", $ponlogin)[4];
		$getlock = explode(",", $ponlogin)[6];
		$_SESSION['ubp'] = $profile;
		$commt = $user . "-" . rand(100, 999) . "-" . date("m.d.y") . "-" . $adcomment;
		$gentemp = $commt . "|~" . $profile . "~" . $getvalid . "~" . $getprice . "!".$getsprice."~" . $timelimit . "~" . $datalimit . "~" . $getlock;
		$gen = '<?php $genu="'.encrypt($gentemp).'";?>';
		$temp = './voucher/temp.php';
		$handle = fopen($temp, 'w') or die('Cannot open file:  ' . $temp);
		$data = $gen;
		fwrite($handle, $data);

		$a = array("1" => "", "", 1, 2, 2, 3, 3, 4);

		if ($user == "up") {
			for ($i = 1; $i <= $qty; $i++) {
				if ($char == "lower") {
					$u[$i] = randLC($userl);
				} elseif ($char == "upper") {
					$u[$i] = randUC($userl);
				} elseif ($char == "upplow") {
					$u[$i] = randULC($userl);
				} elseif ($char == "mix") {
					$u[$i] = randNLC($userl);
				} elseif ($char == "mix1") {
					$u[$i] = randNUC($userl);
				} elseif ($char == "mix2") {
					$u[$i] = randNULC($userl);
				}
				if ($userl == 3) {
					$p[$i] = randN(3);
				} elseif ($userl == 4) {
					$p[$i] = randN(4);
				} elseif ($userl == 5) {
					$p[$i] = randN(5);
				} elseif ($userl == 6) {
					$p[$i] = randN(6);
				} elseif ($userl == 7) {
					$p[$i] = randN(7);
				} elseif ($userl == 8) {
					$p[$i] = randN(8);
				}

				$u[$i] = "$prefix$u[$i]";
			}

			for ($i = 1; $i <= $qty; $i++) {
				$API->comm("/ip/hotspot/user/add", array(
					"server" => "$server",
					"name" => "$u[$i]",
					"password" => "$p[$i]",
					"profile" => "$profile",
					"limit-uptime" => "$timelimit",
					"limit-bytes-total" => "$datalimit",
					"comment" => "$commt",
				));
			}
		}

		if ($user == "vc") {
			$shuf = ($userl - $a[$userl]);
			for ($i = 1; $i <= $qty; $i++) {
				if ($char == "lower") {
					$u[$i] = randLC($shuf);
				} elseif ($char == "upper") {
					$u[$i] = randUC($shuf);
				} elseif ($char == "upplow") {
					$u[$i] = randULC($shuf);
				}
				if ($userl == 3) {
					$p[$i] = randN(1);
				} elseif ($userl == 4 || $userl == 5) {
					$p[$i] = randN(2);
				} elseif ($userl == 6 || $userl == 7) {
					$p[$i] = randN(3);
				} elseif ($userl == 8) {
					$p[$i] = randN(4);
				}

				$u[$i] = "$prefix$u[$i]$p[$i]";

				if ($char == "num") {
					if ($userl == 3) {
						$p[$i] = randN(3);
					} elseif ($userl == 4) {
						$p[$i] = randN(4);
					} elseif ($userl == 5) {
						$p[$i] = randN(5);
					} elseif ($userl == 6) {
						$p[$i] = randN(6);
					} elseif ($userl == 7) {
						$p[$i] = randN(7);
					} elseif ($userl == 8) {
						$p[$i] = randN(8);
					}

					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix") {
					$p[$i] = randNLC($userl);


					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix1") {
					$p[$i] = randNUC($userl);


					$u[$i] = "$prefix$p[$i]";
				}
				if ($char == "mix2") {
					$p[$i] = randNULC($userl);


					$u[$i] = "$prefix$p[$i]";
				}

			}
			for ($i = 1; $i <= $qty; $i++) {
				$API->comm("/ip/hotspot/user/add", array(
					"server" => "$server",
					"name" => "$u[$i]",
					"password" => "$u[$i]",
					"profile" => "$profile",
					"limit-uptime" => "$timelimit",
					"limit-bytes-total" => "$datalimit",
					"comment" => "$commt",
				));
			}
		}


		if ($qty < 2) {
			echo "<script>window.location='./?hotspot-user=" . $u[1] . "&session=" . $session . "'</script>";
		} else {
			echo "<script>window.location='./?hotspot-user=generate&session=" . $session . "'</script>";
		}
	}

	$getprofile = $API->comm("/ip/hotspot/user/profile/print");
	include_once('./voucher/temp.php');
	$genuser = explode("-", decrypt($genu));
	$genuser1 = explode("~", decrypt($genu));
	$umode = $genuser[0];
	$ucode = $genuser[1];
	$udate = $genuser[2];
	$uprofile = $genuser1[1];
	$uvalid = $genuser1[2];
	$ucommt = $genuser[3];
	if ($uvalid == "") {
		$uvalid = "-";
	} else {
		$uvalid = $uvalid;
	}
	$uprice = explode("!",$genuser1[3])[0];
	if ($uprice == "0") {
		$uprice = "-";
	} else {
		$uprice = $uprice;
	}
	$suprice = explode("!",$genuser1[3])[1];
	if ($suprice == "0") {
		$suprice = "-";
	} else {
		$suprice = $suprice;
	}
	$utlimit = $genuser1[4];
	if ($utlimit == "0") {
		$utlimit = "-";
	} else {
		$utlimit = $utlimit;
	}
	$udlimit = $genuser1[5];
	if ($udlimit == "0") {
		$udlimit = "-";
	} else {
		$udlimit = formatBytes($udlimit, 2);
	}
	$ulock = $genuser1[6];
	//$urlprint = "$umode-$ucode-$udate-$ucommt";
	$urlprint = explode("|", decrypt($genu))[0];
	if ($currency == in_array($currency, $cekindo['indo'])) {
		$uprice = $currency . " " . number_format((float)$uprice, 0, ",", ".");
		$suprice = $currency . " " . number_format((float)$suprice, 0, ",", ".");
	} else {
		$uprice = $currency . " " . number_format((float)$uprice);
		$suprice = $currency . " " . number_format((float)$suprice);

	}

}
?>
<div class="row">
<div class="col-8">
<div class="card box-bordered">
	<div class="card-header">
	<h3><i class="fa fa-user-plus"></i> <?= $_generate_user ?> <small id="loader" style="display: none;" ><i><i class='fa fa-circle-o-notch fa-spin'></i> <?= $_processing ?> </i></small></h3> 
	</div>
	<div class="card-body">
<form autocomplete="off" method="post" action="">
	<div>
		<?php if ($_SESSION['ubp'] != "") {
		echo "    <a class='btn bg-warning' href='./?hotspot=users&profile=" . $_SESSION['ubp'] . "&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	} elseif ($_SESSION['vcr'] = "active") {
		echo "    <a class='btn bg-warning' href='./?hotspot=users-by-profile&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	} else {
		echo "    <a class='btn bg-warning' href='./?hotspot=users&profile=all&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	}

	?>
	<a class="btn bg-pink" title="Open User List by Profile 
<?php if ($_SESSION['ubp'] == "") {
	echo "all";
} else {
	echo $uprofile;
} ?>" href="./?hotspot=users&profile=
<?php if ($_SESSION['ubp'] == "") {
	echo "all";
} else {
	echo $uprofile;
} ?>&session=<?= $session; ?>"> <i class="fa fa-users"></i> <?= $_user_list ?></a>
    <button type="submit" name="save" onclick="loader()" class="btn bg-primary" title="Generate User"> <i class="fa fa-save"></i> <?= $_generate ?></button>
    <a class="btn bg-secondary" title="Print Default" href="./voucher/print.php?id=<?= $urlprint; ?>&qr=no&session=<?= $session; ?>" target="_blank"> <i class="fa fa-print"></i> <?= $_print ?></a>
    <a class="btn bg-danger" title="Print QR" href="./voucher/print.php?id=<?= $urlprint; ?>&qr=yes&session=<?= $session; ?>" target="_blank"> <i class="fa fa-qrcode"></i> <?= $_print_qr ?></a>
    <a class="btn bg-info" title="Print Small" href="./voucher/print.php?id=<?= $urlprint; ?>&small=yes&session=<?= $session; ?>" target="_blank"> <i class="fa fa-print"></i> <?= $_print_small ?></a>
</div>
<table class="table">
  <tr>
    <td class="align-middle"><?= $_profile ?></td>
    <td>
      <input type="hidden" id="profselect" name="profile" value="">
      <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <?php 
        $TotalReg = count($getprofile);
        if ($genprof != "") {
          $defaultProfile = $genprof;
        } elseif ($TotalReg > 0) {
          $defaultProfile = $getprofile[0]['name'];
        }
        
        if ($TotalReg > 0) {
          for ($i = 0; $i < $TotalReg; $i++) {
            $profileName = $getprofile[$i]['name'];
            $isSelected = ($profileName == $defaultProfile);
            $borderStyle = $isSelected ? 'border: 3px solid #007bff; box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);' : 'border: 2px solid #e0e0e0;';
            $bgColor = $isSelected ? 'background-color: #007bff; color: white;' : 'background-color: #f0f0f0; color: #333;';
            echo "<button type='button' class='btn btn-sm' style='" . $borderStyle . $bgColor . " border-radius: 6px; padding: 8px 12px; font-weight: 500; transition: all 0.3s ease; cursor: pointer;' onclick=\"setProfile('" . htmlspecialchars($profileName) . "'); GetVP();\" onmouseover=\"this.style.transform='scale(1.05)';\" onmouseout=\"this.style.transform='scale(1)';\">" . htmlspecialchars(substr($profileName, 0, 15)) . "</button>";
          }
        }
        ?>
      </div>
    </td>
  </tr>
  <tr>
    <td class="align-middle"><?= $_qty ?></td>
    <td>
      <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <div style="width: 120px;">
          <input class="form-control" type="number" id="qtyinput" name="qty" min="1" max="500" value="1" required="1" style="border-radius: 6px; border: 2px solid #e0e0e0; padding: 8px 12px; font-weight: 500;">
        </div>
        <div id="quickQtyContainer" style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
          <?php 
          foreach ($quickQtyData as $qtyVal) {
            echo '<div class="quick-qty-item" style="position: relative; display: inline-flex;" data-qty="' . $qtyVal . '">';
            echo '<button type="button" class="btn btn-sm" style="background-color: #17a2b8; color: white; border: none; border-radius: 6px 0 0 6px; padding: 8px 14px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;" onclick="setQty(' . $qtyVal . ');" onmouseover="this.style.backgroundColor=\'#138496\'; this.style.transform=\'scale(1.05)\';" onmouseout="this.style.backgroundColor=\'#17a2b8\'; this.style.transform=\'scale(1)\';">' . $qtyVal . '</button>';
            echo '<button type="button" class="btn btn-sm" style="background-color: #dc3545; color: white; border: none; border-radius: 0 6px 6px 0; padding: 8px 6px; font-size: 10px; cursor: pointer; transition: all 0.3s ease; line-height: 1;" onclick="deleteQuickQty(' . $qtyVal . ', this);" onmouseover="this.style.backgroundColor=\'#c82333\'; this.style.transform=\'scale(1.05)\';" onmouseout="this.style.backgroundColor=\'#dc3545\'; this.style.transform=\'scale(1)\';" title="Hapus ' . $qtyVal . '"><i class="fa fa-times"></i></button>';
            echo '</div>';
          }
          ?>
          <!-- Add Quick Qty Button -->
          <button type="button" class="btn btn-sm" id="btnAddQty" style="background-color: #28a745; color: white; border: none; border-radius: 6px; padding: 8px 12px; font-weight: 500; cursor: pointer; transition: all 0.3s ease;" onclick="showAddQtyInput();" onmouseover="this.style.backgroundColor='#218838'; this.style.transform='scale(1.05)';" onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='scale(1)';" title="Tambah Quick Qty"><i class="fa fa-plus"></i></button>
          <!-- Add Qty Input (hidden by default) -->
          <div id="addQtyInputWrapper" style="display: none; align-items: center; gap: 4px;">
            <input type="number" id="newQtyInput" min="1" max="999" placeholder="Qty" style="width: 70px; border-radius: 6px; border: 2px solid #28a745; padding: 6px 8px; font-weight: 500; font-size: 13px; text-align: center;">
            <button type="button" class="btn btn-sm" style="background-color: #28a745; color: white; border: none; border-radius: 6px; padding: 7px 10px; cursor: pointer; transition: all 0.3s ease;" onclick="addQuickQty();" title="Simpan"><i class="fa fa-check"></i></button>
            <button type="button" class="btn btn-sm" style="background-color: #6c757d; color: white; border: none; border-radius: 6px; padding: 7px 10px; cursor: pointer; transition: all 0.3s ease;" onclick="hideAddQtyInput();" title="Batal"><i class="fa fa-times"></i></button>
          </div>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td class="align-middle">Server</td>
    <td>
		<select class="form-control " name="server" required="1">
			<option>all</option>
				<?php $TotalReg = count($srvlist);
			for ($i = 0; $i < $TotalReg; $i++) {
				echo "<option>" . $srvlist[$i]['name'] . "</option>";
			}
			?>
		</select>
	</td>
	</tr>
	<tr>
    <td class="align-middle"><?= $_user_mode ?></td><td>
			<select class="form-control " onchange="defUserl();" id="user" name="user" required="1">
			    <option value="vc"><?= $_user_user ?></option>
				<option value="up"><?= $_user_pass ?></option>
			</select>
		</td>
	</tr>
  <tr>
    <td class="align-middle"><?= $_user_length ?></td><td>
      <select class="form-control " id="userl" name="userl" required="1">
        <option value="3" selected="selected">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="align-middle">Reseller</td>
    <td>
      <div style="display: flex; gap: 10px; align-items: center;">
        <select class="form-control" id="resellerselect" style="flex: 1;">
          <option value="">-- Select Reseller --</option>
          <?php 
          if (is_array($resellerData) && count($resellerData) > 0) {
            foreach ($resellerData as $reseller) {
              echo "<option value='" . htmlspecialchars($reseller['prefix']) . "'>" . htmlspecialchars($reseller['name']) . " (" . htmlspecialchars($reseller['prefix']) . ")</option>";
            }
          }
          ?>
        </select>
        <a class="btn bg-primary btn-sm" href="./?hotspot=reseller&session=<?= $session; ?>" title="Manage Reseller">
          <i class="fa fa-cog"></i> Manage
        </a>
      </div>
    </td>
  </tr>
  <tr>
    <td class="align-middle"><?= $_prefix ?></td><td><input class="form-control " type="text" size="6" maxlength="6" autocomplete="off" id="prefixinput" name="prefix" value=""></td>
  </tr>
  <tr>
    <td class="align-middle"><?= $_character ?></td><td>
      <select class="form-control " name="char" required="1">
                <option id="mix1" style="display:block;" value="mix1"><?= $_random ?> 5AB2C34D</option>
				<option id="lower" style="display:block;" value="lower"><?= $_random ?> abcd</option>
				<option id="upper" style="display:block;" value="upper"><?= $_random ?> ABCD</option>
				<option id="upplow" style="display:block;" value="upplow"><?= $_random ?> aBcD</option>
				<option id="lower1" style="display:none;" value="lower"><?= $_random ?> abcd2345</option>
				<option id="upper1" style="display:none;" value="upper"><?= $_random ?> ABCD2345</option>
				<option id="upplow1" style="display:none;" value="upplow"><?= $_random ?> aBcD2345</option>
				<option id="mix" style="display:block;" value="mix"><?= $_random ?> 5ab2c34d</option>
				<option id="mix2" style="display:block;" value="mix2"><?= $_random ?> 5aB2c34D</option>
				<option id="num" style="display:none;" value="num"><?= $_random ?> 1234</option>
			</select>
    </td>
  </tr>
	<tr>
    <!-- <td class="align-middle"><?= $_time_limit ?></td><td><input class="form-control " type="text" size="4" autocomplete="off" name="timelimit" value=""></td>
  </tr>
	<tr>
    <td class="align-middle"><?= $_data_limit ?></td><td>
      <div class="input-group">
      	<div class="input-group-10 col-box-9">
        	<input class="group-item group-item-l" type="number" min="0" max="9999" name="datalimit" value="<?= $udatalimit; ?>">
    	</div>
          <div class="input-group-2 col-box-3">
              <select style="padding:4.2px;" class="group-item group-item-r" name="mbgb" required="1">
				        <option value=1048576>MB</option>
				        <option value=1073741824>GB</option>
			        </select>
          </div>
      </div>
    </td> -->
  </tr>
	<tr>
    <td class="align-middle"><?= $_comment ?></td><td><input class="form-control " type="text" title="No special characters" id="comment" autocomplete="off" name="adcomment" value="" placeholder="Auto: BULAN-PROFILE-PREFIX" style="border-radius: 6px; border: 2px solid #e0e0e0; padding: 8px 12px; font-weight: 500;"></td>
  </tr>
   <tr >
    <td  colspan="4" class="align-middle w-12"  id="GetValidPrice">
    	<?php if ($genprof != "") {
					echo $ValidPrice;
				} ?>
    </td>
  </tr>
</table>
</form>
</div>
</div>
</div>

<div class="col-4">
	<div class="card">
		<div class="card-header">
			<h3><i class="fa fa-ticket"></i> <?= $_last_generate ?></h3>
		</div>
		<div class="card-body">
<table class="table table-bordered">
  <tr>
  	<td><?= $_generate_code ?></td><td><?= $ucode ?></td>
  </tr>
  <tr>
  	<td><?= $_date ?></td><td><?= $udate ?></td>
  </tr>
  <tr>
  	<td><?= $_profile ?></td><td><?= $uprofile ?></td>
  </tr>
  <tr>
  	<td><?= $_validity ?></td><td><?= $uvalid ?></td>
  <tr>
  	<!-- <td><?= $_time_limit ?></td><td><?= $utlimit ?></td>
  </tr>
  <tr>
  	<td><?= $_data_limit ?></td><td><?= $udlimit ?></td>
  </tr>
  <tr>
  	<td><?= $_price ?></td><td><?= $uprice ?></td>
  </tr> -->
  <tr>
  	<td><?= $_selling_price ?></td><td><?= $suprice ?></td>
  </tr>
  <tr>
  	<td><?= $_lock_user ?></td><td><?= $ulock ?></td>
  </tr>
  <tr>
    <td colspan="2">
		<p style="padding:0px 5px;">
      <?= $_format_time_limit ?>
    </p>
    <p style="padding:0px 5px;">
      <?= $_details_add_user ?>
    </p>
    </td>
  </tr>
</table>
</div>
</div>
</div>
<script>
// Current month name (Indonesian)
var currentBulan = '<?= $currentBulan ?>';

// get valid $ price
function GetVP(){
  var prof = document.getElementById('profselect').value;
  $("#GetValidPrice").load("./process/getvalidprice.php?name="+prof+"&session=<?= $session; ?> #getdata");
  updateComment();
} 

// Set Qty from quick buttons
function setQty(value) {
  document.getElementById('qtyinput').value = value;
}

// Set Profile from quick buttons
function setProfile(profileName) {
  var profSelect = document.getElementById('profselect');
  profSelect.value = profileName;
  
  // Update button styling
  var profileButtons = document.querySelectorAll('td button[onclick*="setProfile"]');
  profileButtons.forEach(function(btn) {
    var btnText = btn.textContent.trim();
    if (btnText === profileName || btnText.substring(1) === profileName) {
      btn.style.border = '3px solid #007bff';
      btn.style.boxShadow = '0 0 8px rgba(0, 123, 255, 0.5)';
      btn.style.backgroundColor = '#007bff';
      btn.style.color = 'white';
    } else {
      btn.style.border = '2px solid #e0e0e0';
      btn.style.boxShadow = 'none';
      btn.style.backgroundColor = '#f0f0f0';
      btn.style.color = '#333';
    }
  });
  
  updateComment();
}

// Auto-fill comment with format: BULAN-PROFILE-PREFIX
function updateComment() {
  var profile = document.getElementById('profselect').value || '';
  var prefix = document.getElementById('prefixinput').value || '';
  var commentInput = document.getElementById('comment');
  
  if (profile && prefix) {
    var profileClean = profile.replace(/\s+/g, '_').toUpperCase();
    var prefixClean = prefix.toUpperCase();
    commentInput.value = currentBulan + '-' + profileClean + '-' + prefixClean;
  } else if (profile && !prefix) {
    var profileClean = profile.replace(/\s+/g, '_').toUpperCase();
    commentInput.value = currentBulan + '-' + profileClean;
  } else {
    commentInput.value = '';
  }
}

// Quick Qty Management
function showAddQtyInput() {
  document.getElementById('btnAddQty').style.display = 'none';
  document.getElementById('addQtyInputWrapper').style.display = 'flex';
  document.getElementById('newQtyInput').focus();
}

function hideAddQtyInput() {
  document.getElementById('btnAddQty').style.display = 'inline-block';
  document.getElementById('addQtyInputWrapper').style.display = 'none';
  document.getElementById('newQtyInput').value = '';
}

function addQuickQty() {
  var newQty = parseInt(document.getElementById('newQtyInput').value);
  if (!newQty || newQty < 1 || newQty > 999) {
    alert('Masukkan qty antara 1 - 999');
    return;
  }
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', './process/quickqty.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var resp = JSON.parse(xhr.responseText);
      if (resp.status === 'success') {
        location.reload();
      } else {
        alert(resp.message || 'Qty sudah ada atau tidak valid');
      }
    }
  };
  xhr.send('action=add&qty=' + newQty);
}

function deleteQuickQty(qty, btn) {
  if (!confirm('Hapus quick qty ' + qty + '?')) return;
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', './process/quickqty.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var resp = JSON.parse(xhr.responseText);
      if (resp.status === 'success') {
        // Remove the button group with animation
        var item = btn.closest('.quick-qty-item');
        item.style.transition = 'all 0.3s ease';
        item.style.opacity = '0';
        item.style.transform = 'scale(0.5)';
        setTimeout(function() { item.remove(); }, 300);
      }
    }
  };
  xhr.send('action=delete&qty=' + qty);
}

// Set Prefix from Reseller & auto-fill comment
document.addEventListener('DOMContentLoaded', function() {
  var resellerSelect = document.getElementById('resellerselect');
  var profSelect = document.getElementById('profselect');
  var prefixInput = document.getElementById('prefixinput');
  
  if (resellerSelect) {
    resellerSelect.addEventListener('change', function() {
      if (this.value) {
        prefixInput.value = this.value;
      } else {
        prefixInput.value = '';
      }
      updateComment();
    });
  }
  
  if (profSelect) {
    profSelect.addEventListener('change', function() {
      GetVP();
    });
  }
  
  // Also update comment when prefix is manually typed
  if (prefixInput) {
    prefixInput.addEventListener('input', function() {
      updateComment();
    });
  }
  
  // Handle Enter key on new qty input
  var newQtyInput = document.getElementById('newQtyInput');
  if (newQtyInput) {
    newQtyInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        addQuickQty();
      }
    });
  }
  
  // Set default profile value
  var defaultProfile = '<?= isset($defaultProfile) ? $defaultProfile : '' ?>';
  if (defaultProfile && profSelect) {
    profSelect.value = defaultProfile;
  }
});
</script>
</div>
