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

	// Load quick generate presets
	$quickGenFile = './voucher/quickgen.json';
	$quickGenData = array();
	if (file_exists($quickGenFile)) {
		$tmpGen = json_decode(file_get_contents($quickGenFile), true);
		if (is_array($tmpGen)) {
			$quickGenData = $tmpGen;
		}
	}

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
		$gentemp = $commt . "|~" . $profile . "~" . $getvalid . "~" . $getprice . "!".$getsprice."~" . $timelimit . "~" . $datalimit . "~" . $getlock . "~" . $qty . "~" . $prefix;
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
	$uqty = isset($genuser1[7]) && $genuser1[7] != "" ? $genuser1[7] : "-";
	$uprefix = isset($genuser1[8]) && $genuser1[8] != "" ? $genuser1[8] : "-";
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
<style>
/* ============================================
   MODERN GENERATE USER - PREMIUM DESIGN
   ============================================ */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.gen-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    width: 100%;
}

/* Glassmorphism Card */
.gen-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9));
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 
        0 8px 32px rgba(0,0,0,0.08),
        0 2px 8px rgba(0,0,0,0.04),
        inset 0 1px 0 rgba(255,255,255,0.8);
    overflow: hidden;
    animation: cardSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    margin-bottom: 24px;
}

@keyframes cardSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.gen-header {
    padding: 24px 30px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
}

.gen-header.purple { background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%); }
.gen-header.blue { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
.gen-header.primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.gen-header.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

.gen-header::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz48L3N2Zz4=');
    mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
}

.gen-header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    backdrop-filter: blur(10px);
    z-index: 1;
}

.gen-header-text {
    z-index: 1;
    display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: center;
    cursor: pointer;
}

.gen-header-text h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.gen-body {
    padding: 24px 30px;
}

/* Form Styles */
.gen-form-group {
    display: flex;
    align-items: center;
    margin-bottom: 16px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 16px;
}
.gen-form-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.gen-label {
    width: 25%;
    font-weight: 600;
    color: #475569;
    font-size: 14px;
}
.gen-input-wrap {
    width: 75%;
}

.gen-form-control {
    width: 100%;
    padding: 10px 14px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #1e293b;
    font-weight: 500;
    transition: all 0.3s ease;
}

.gen-form-control:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.action-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

.modern-btn {
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: white;
}
.modern-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.btn-pink { background: linear-gradient(135deg, #ec4899, #db2777); }
.btn-primary { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.btn-secondary { background: linear-gradient(135deg, #64748b, #475569); }
.btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
.btn-info { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
.btn-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }

/* Modern Profile & Qty Buttons */
.profile-btn {
    padding: 10px 16px;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.profile-btn:hover {
    border-color: #3b82f6;
    background: #f8fafc;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}
.profile-btn.prof-active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.qty-input-modern {
    flex: 1;
    min-width: 80px;
    padding: 10px 14px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #1e293b;
    font-weight: 600;
    transition: all 0.3s ease;
}
.qty-input-modern:focus {
    outline: none;
    border-color: #3b82f6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.quick-qty-item-modern {
    display: inline-flex;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}
.quick-qty-item-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.quick-qty-btn {
    background: #fff;
    color: #475569;
    border: none;
    padding: 8px 16px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.quick-qty-btn:hover {
    background: #f8fafc;
    color: #3b82f6;
}
.quick-qty-del {
    background: #f8fafc;
    color: #ef4444;
    border: none;
    border-left: 1px solid #e2e8f0;
    padding: 8px 10px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.quick-qty-del:hover {
    background: #fee2e2;
}

/* Responsive Styles */
@media screen and (max-width: 768px) {
    .gen-form-group {
        flex-direction: column;
        align-items: flex-start;
    }
    .gen-label {
        width: 100%;
        margin-bottom: 8px;
    }
    .gen-input-wrap {
        width: 100%;
    }
    .action-bar {
        flex-wrap: wrap;
        flex-direction: row;
        gap: 8px;
    }
    .action-bar .modern-btn {
        flex: 1 1 calc(50% - 8px);
        justify-content: center;
        width: auto !important;
        margin-bottom: 0 !important;
        font-size: 13px;
        padding: 8px 12px;
    }
    .action-bar .modern-btn:first-child {
        flex: 1 1 100%;
    }
    .gen-body .col-6 {
        width: 100% !important;
        float: none !important;
        padding: 0 !important;
        margin-bottom: 15px;
    }
    .qty-input-modern {
        flex: 1 1 100%;
    }
    .profile-btn {
        flex: 1 1 calc(50% - 10px);
        justify-content: center;
        width: auto !important;
        padding: 8px 10px;
    }
}

</style>

<div class="gen-container">
<!-- Quick Generate Panel -->
<div class="row" style="margin-bottom: 15px;">
<div class="col-12">
<div class="gen-card">
	<div class="gen-header purple" onclick="toggleQuickGen();" style="cursor: pointer;">
        <div class="gen-header-icon">
            <i class="fa fa-bolt"></i>
        </div>
        <div class="gen-header-text">
            <h3>Quick Generate</h3>
			<i class="fa fa-chevron-down" id="quickGenChevron" style="transition: transform 0.3s ease; color: white; font-size: 18px;"></i>
		</div>
	</div>
	<div class="gen-body" id="quickGenBody" style="display: none;">
		<?php if (count($quickGenData) > 0) { ?>
		<div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
			<?php foreach ($quickGenData as $preset) { 
				// Find reseller name by prefix
				$resellerName = $preset['prefix'];
				foreach ($resellerData as $res) {
					if ($res['prefix'] == $preset['prefix']) {
						$resellerName = $res['name'] . ' (' . $res['prefix'] . ')';
						break;
					}
				}
			?>
			<div class="quick-gen-item" style="position: relative; background: linear-gradient(135deg, #ffffff, #f8f9fa); border: 2px solid #6f42c1; border-radius: 12px; padding: 15px; min-width: 220px; max-width: 300px; box-shadow: 0 3px 10px rgba(111, 66, 193, 0.15); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(111, 66, 193, 0.25)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 10px rgba(111, 66, 193, 0.15)';">
				<button type="button" onclick="deleteQuickGen('<?= $preset['id'] ?>', this);" style="position: absolute; top: 8px; right: 8px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; line-height: 1;" onmouseover="this.style.backgroundColor='#c82333'; this.style.transform='scale(1.2)';" onmouseout="this.style.backgroundColor='#dc3545'; this.style.transform='scale(1)';" title="Hapus preset"><i class="fa fa-times"></i></button>
				<button type="button" onclick='editQuickGen(<?= htmlspecialchars(json_encode($preset)) ?>);' style="position: absolute; top: 8px; right: 35px; background: #007bff; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; line-height: 1;" onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.2)';" onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='scale(1)';" title="Edit preset"><i class="fa fa-pencil"></i></button>
				<div style="font-weight: 700; font-size: 14px; color: #6f42c1; margin-bottom: 8px; padding-right: 50px;"><?= htmlspecialchars($preset['name']) ?></div>
				<div style="font-size: 12px; color: #666; margin-bottom: 4px;"><i class="fa fa-tag" style="width: 16px; color: #17a2b8;"></i> <?= htmlspecialchars($preset['profile']) ?></div>
				<div style="font-size: 12px; color: #666; margin-bottom: 4px;"><i class="fa fa-users" style="width: 16px; color: #28a745;"></i> Qty: <strong><?= $preset['qty'] ?></strong></div>
				<?php if ($preset['prefix']) { ?>
				<div style="font-size: 12px; color: #666; margin-bottom: 10px;"><i class="fa fa-store" style="width: 16px; color: #e83e8c;"></i> <?= htmlspecialchars($resellerName) ?></div>
				<?php } else { ?>
				<div style="font-size: 12px; color: #999; margin-bottom: 10px;"><i class="fa fa-store" style="width: 16px; color: #ccc;"></i> Tanpa Reseller</div>
				<?php } ?>
				<button type="button" class="btn" onclick="executeQuickGen(<?= htmlspecialchars(json_encode($preset)) ?>);" style="width: 100%; background: linear-gradient(135deg, #6f42c1, #e83e8c); color: white; border: none; border-radius: 8px; padding: 10px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 6px rgba(111, 66, 193, 0.3);" onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 4px 12px rgba(111, 66, 193, 0.5)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(111, 66, 193, 0.3)';">
					<i class="fa fa-bolt"></i> Generate Sekarang
				</button>
			</div>
			<?php } ?>
		</div>
		<hr style="border-color: #e0e0e0;">
		<?php } else { ?>
		<p style="color: #999; font-style: italic; margin-bottom: 15px;">Belum ada preset. Tambahkan preset untuk generate cepat!</p>
		<?php } ?>

		<!-- Add Preset Form -->
		<button type="button" class="btn" id="btnShowAddPreset" onclick="toggleAddPreset();" style="background-color: #6f42c1; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#5a32a3'; this.style.transform='scale(1.03)';" onmouseout="this.style.backgroundColor='#6f42c1'; this.style.transform='scale(1)';">
			<i class="fa fa-plus"></i> Tambah Preset
		</button>

		<div id="addPresetForm" style="display: none; margin-top: 15px;">
			<div class="card" style="border: 2px solid #6f42c1; border-radius: 10px;">
				<div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white; padding: 12px 15px; border-radius: 8px 8px 0 0;">
					<h5 id="presetFormTitle" style="margin: 0; font-weight: 600;"><i class="fa fa-plus-circle"></i> Tambah Preset Baru</h5>
				</div>
				<div class="card-body" style="padding: 15px;">
					<input type="hidden" id="presetId" value="">
					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Nama Preset *</label>
							<input type="text" id="presetName" placeholder="e.g., RGS - 12 Jam x50" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Profile *</label>
							<select id="presetProfile" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<?php 
								$TotalProf = count($getprofile);
								for ($i = 0; $i < $TotalProf; $i++) {
									echo "<option value='" . htmlspecialchars($getprofile[$i]['name']) . "'>" . htmlspecialchars($getprofile[$i]['name']) . "</option>";
								}
								?>
							</select>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Qty *</label>
							<input type="number" id="presetQty" min="1" max="500" value="50" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Reseller</label>
							<select id="presetReseller" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<option value="">-- Tanpa Reseller --</option>
								<?php 
								if (is_array($resellerData) && count($resellerData) > 0) {
									foreach ($resellerData as $reseller) {
										echo "<option value='" . htmlspecialchars($reseller['prefix']) . "'>" . htmlspecialchars($reseller['name']) . " (" . htmlspecialchars($reseller['prefix']) . ")</option>";
									}
								}
								?>
							</select>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Server</label>
							<select id="presetServer" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<option value="all">all</option>
								<?php 
								$TotalSrv = count($srvlist);
								for ($i = 0; $i < $TotalSrv; $i++) {
									echo "<option>" . $srvlist[$i]['name'] . "</option>";
								}
								?>
							</select>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">User Mode</label>
							<select id="presetUsermode" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<option value="vc">User=Password (Voucher)</option>
								<option value="up">User &amp; Password</option>
							</select>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">User Length</label>
							<select id="presetUserlength" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<option value="3" selected>3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
							</select>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; color: inherit;">Character</label>
							<select id="presetChar" class="form-control" style="border-radius: 6px; padding: 8px 12px;">
								<option value="mix1">Random 5AB2C34D</option>
								<option value="lower">Random abcd</option>
								<option value="upper">Random ABCD</option>
								<option value="upplow">Random aBcD</option>
								<option value="mix">Random 5ab2c34d</option>
								<option value="mix2">Random 5aB2c34D</option>
							</select>
						</div>
					</div>
					<div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 12px; border-top: 2px solid #e0e0e0;">
						<button type="button" onclick="saveQuickGenPreset();" class="btn" style="background-color: #6f42c1; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#5a32a3'; this.style.transform='scale(1.03)';" onmouseout="this.style.backgroundColor='#6f42c1'; this.style.transform='scale(1)';">
							<i class="fa fa-save"></i> Simpan Preset
						</button>
						<button type="button" onclick="toggleAddPreset();" class="btn" style="background-color: #6c757d; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.03)';" onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)';">
							<i class="fa fa-times"></i> Batal
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>

<!-- Quick Print Panel -->
<div class="row" style="margin-bottom: 15px;">
<div class="col-12">
	<div class="gen-card">
		<div class="gen-header blue" onclick="toggleQuickPrint();" style="cursor: pointer;">
            <div class="gen-header-icon">
                <i class="fa fa-print"></i>
            </div>
            <div class="gen-header-text">
                <h3>Quick Print</h3>
				<i class="fa fa-chevron-down" id="quickPrintChevron" style="transition: transform 0.3s ease; color: white; font-size: 18px;"></i>
			</div>
		</div>
		<div class="gen-body" id="quickPrintBody" style="display: none;">
			<div class="row">
				<!-- Print Baru (Last Generate) -->
				<div class="col-6">
					<div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
						<label style="font-weight: 600; font-size: 14px; color: #17a2b8; display: block; margin-bottom: 10px;"><i class="fa fa-bolt"></i> Print by Comment (Baru)</label>
						<div style="display: flex; gap: 8px; margin-bottom: 12px;">
							<select id="qpCommentBaru" class="form-control" style="flex: 1; border-radius: 6px; padding: 8px 12px;">
								<?php if ($urlprint) { ?>
									<option value="<?= htmlspecialchars($urlprint) ?>" selected><?= htmlspecialchars($urlprint) ?> (Baru)</option>
								<?php } else { ?>
									<option value="">Belum ada generate baru</option>
								<?php } ?>
							</select>
						</div>
						<div style="display: flex; gap: 8px;">
							<button class="btn" style="flex: 1; background: #007bff; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'no', 'no')"><i class="fa fa-print"></i> Default</button>
							<button class="btn" style="flex: 1; background: #dc3545; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'yes', 'no')"><i class="fa fa-qrcode"></i> QR</button>
							<button class="btn" style="flex: 1; background: #28a745; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('baru', 'no', 'yes')"><i class="fa fa-print"></i> Small</button>
						</div>
					</div>
				</div>
				
				<!-- Print Semua (History) -->
				<div class="col-6">
					<div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px;">
						<label style="font-weight: 600; font-size: 14px; color: #17a2b8; display: block; margin-bottom: 10px;"><i class="fa fa-history"></i> Print by Comment (Semua)</label>
						<div style="display: flex; gap: 8px; margin-bottom: 12px;">
							<select id="qpCommentSemua" class="form-control" style="flex: 1; border-radius: 6px; padding: 8px 12px;">
								<option value="">Pilih Comment...</option>
							</select>
							<button class="btn bg-secondary" onclick="loadCommentsForPrint()" title="Refresh Comments" style="border-radius: 6px;"><i class="fa fa-refresh"></i></button>
						</div>
						<div style="display: flex; gap: 8px;">
							<button class="btn" style="flex: 1; background: #007bff; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'no', 'no')"><i class="fa fa-print"></i> Default</button>
							<button class="btn" style="flex: 1; background: #dc3545; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'yes', 'no')"><i class="fa fa-qrcode"></i> QR</button>
							<button class="btn" style="flex: 1; background: #28a745; color: white; border-radius: 6px; font-weight: 600;" onclick="doQuickPrint('semua', 'no', 'yes')"><i class="fa fa-print"></i> Small</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>

<div class="row" style="margin-bottom: 24px;">
<div class="col-12">
<div class="gen-card">
	<div class="gen-header primary">
        <div class="gen-header-icon">
            <i class="fa fa-user-plus"></i>
        </div>
        <div class="gen-header-text">
            <h3><?= $_generate_user ?> <small id="loader" style="display: none; font-size: 14px; margin-left: 10px; font-weight: normal;"><i class='fa fa-circle-o-notch fa-spin'></i> <?= $_processing ?></small></h3> 
        </div>
	</div>
	<div class="gen-body">
<form autocomplete="off" method="post" action="">
	<div class="action-bar">
		<?php if ($_SESSION['ubp'] != "") {
		echo "    <a class='modern-btn btn-warning' style='background: linear-gradient(135deg, #f59e0b, #d97706);' href='./?hotspot=users&profile=" . $_SESSION['ubp'] . "&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	} elseif ($_SESSION['vcr'] = "active") {
		echo "    <a class='modern-btn btn-warning' style='background: linear-gradient(135deg, #f59e0b, #d97706);' href='./?hotspot=users-by-profile&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	} else {
		echo "    <a class='modern-btn btn-warning' style='background: linear-gradient(135deg, #f59e0b, #d97706);' href='./?hotspot=users&profile=all&session=" . $session . "'> <i class='fa fa-close'></i> ".$_close."</a>";
	}

	?>
	<a class="modern-btn btn-pink" title="Open User List" href="./?hotspot=users&profile=<?php if ($_SESSION['ubp'] == "") { echo "all"; } else { echo $uprofile; } ?>&session=<?= $session; ?>"> <i class="fa fa-users"></i> <?= $_user_list ?></a>
    <button type="submit" name="save" onclick="loader()" class="modern-btn btn-primary" title="Generate User"> <i class="fa fa-save"></i> <?= $_generate ?></button>
    <a class="modern-btn btn-secondary" title="Print Default" href="./voucher/print.php?id=<?= $urlprint; ?>&qr=no&session=<?= $session; ?>" target="_blank"> <i class="fa fa-print"></i> <?= $_print ?></a>
    <a class="modern-btn btn-danger" title="Print QR" href="./voucher/print.php?id=<?= $urlprint; ?>&qr=yes&session=<?= $session; ?>" target="_blank"> <i class="fa fa-qrcode"></i> <?= $_print_qr ?></a>
    <a class="modern-btn btn-info" title="Print Small" href="./voucher/print.php?id=<?= $urlprint; ?>&small=yes&session=<?= $session; ?>" target="_blank"> <i class="fa fa-print"></i> <?= $_print_small ?></a>
</div>

<div class="gen-form-container">
  <div class="gen-form-group">
    <div class="gen-label"><?= $_profile ?></div>
    <div class="gen-input-wrap">
      <input type="hidden" id="profselect" name="profile" value="">
      <div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 8px 0;">
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
            $activeClass = $isSelected ? 'prof-active' : '';
            $iconClass = $isSelected ? 'fa-check-circle' : 'fa-tag';
            
            echo "<button type='button' class='profile-btn {$activeClass}' data-profile='" . htmlspecialchars($profileName) . "' onclick=\"setProfile('" . htmlspecialchars($profileName) . "'); GetVP();\">
                <i class='fa {$iconClass}'></i> " . htmlspecialchars(substr($profileName, 0, 18)) . "
            </button>";
          }
        }
        ?>
      </div>
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_qty ?></div>
    <div class="gen-input-wrap">
      <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 8px 0;">
        <input class="qty-input-modern" type="number" id="qtyinput" name="qty" min="1" max="500" value="1" required="1" placeholder="Manual Qty">
        
        <div id="quickQtyContainer" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
          <?php 
          foreach ($quickQtyData as $qtyVal) {
            echo '<div class="quick-qty-item-modern" data-qty="' . $qtyVal . '">';
            echo '<button type="button" class="quick-qty-btn" onclick="setQty(' . $qtyVal . ');"><i class="fa fa-cube" style="margin-right: 4px; opacity: 0.7;"></i>' . $qtyVal . '</button>';
            echo '<button type="button" class="quick-qty-del" onclick="deleteQuickQty(' . $qtyVal . ', this);" title="Hapus ' . $qtyVal . '"><i class="fa fa-times"></i></button>';
            echo '</div>';
          }
          ?>
          <button type="button" class="modern-btn btn-info" id="btnAddQty" style="padding: 8px 14px; font-size: 13px;" onclick="showAddQtyInput();" title="Tambah Quick Qty"><i class="fa fa-plus" style="margin-right: 3px;"></i> Tambah</button>
          <div id="addQtyInputWrapper" style="display: none; align-items: center; gap: 6px; background: #f8fafc; padding: 6px 10px; border-radius: 14px; border: 1px solid #e2e8f0;">
            <input type="number" id="newQtyInput" min="1" max="999" placeholder="Qty" style="width: 65px; border-radius: 8px; border: 2px solid #e2e8f0; padding: 6px 8px; font-weight: 700; font-size: 13px; text-align: center; outline: none;" onfocus="this.style.borderColor='#3b82f6';">
            <button type="button" class="modern-btn btn-primary" style="padding: 6px 12px;" onclick="addQuickQty();" title="Simpan"><i class="fa fa-check"></i></button>
            <button type="button" class="modern-btn btn-secondary" style="padding: 6px 12px; background: #e2e8f0; color: #475569;" onclick="hideAddQtyInput();" title="Batal"><i class="fa fa-times"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="gen-form-group">
    <div class="gen-label">Server</div>
    <div class="gen-input-wrap">
		<select class="gen-form-control" name="server" required="1">
			<option>all</option>
				<?php $TotalReg = count($srvlist);
			for ($i = 0; $i < $TotalReg; $i++) {
				echo "<option>" . $srvlist[$i]['name'] . "</option>";
			}
			?>
		</select>
	</div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_user_mode ?></div>
    <div class="gen-input-wrap">
        <select class="gen-form-control" onchange="defUserl();" id="user" name="user" required="1">
            <option value="vc"><?= $_user_user ?></option>
            <option value="up"><?= $_user_pass ?></option>
        </select>
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_user_length ?></div>
    <div class="gen-input-wrap">
      <select class="gen-form-control" id="userl" name="userl" required="1">
        <option value="3" selected="selected">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select>
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label">Reseller</div>
    <div class="gen-input-wrap">
      <div style="display: flex; gap: 10px; align-items: center;">
        <select class="gen-form-control" id="resellerselect" style="flex: 1;">
          <option value="">-- Select Reseller --</option>
          <?php 
          if (is_array($resellerData) && count($resellerData) > 0) {
            foreach ($resellerData as $reseller) {
              echo "<option value='" . htmlspecialchars($reseller['prefix']) . "'>" . htmlspecialchars($reseller['name']) . " (" . htmlspecialchars($reseller['prefix']) . ")</option>";
            }
          }
          ?>
        </select>
        <a class="modern-btn btn-primary btn-sm" href="./?hotspot=reseller&session=<?= $session; ?>" title="Manage Reseller" style="white-space: nowrap; padding: 10px 14px;">
          <i class="fa fa-cog"></i> Manage
        </a>
      </div>
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_prefix ?></div>
    <div class="gen-input-wrap">
        <input class="gen-form-control" type="text" size="6" maxlength="6" autocomplete="off" id="prefixinput" name="prefix" value="">
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_character ?></div>
    <div class="gen-input-wrap">
      <select class="gen-form-control" name="char" required="1">
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
    </div>
  </div>
  
  <div class="gen-form-group">
    <div class="gen-label"><?= $_comment ?></div>
    <div class="gen-input-wrap">
        <input class="gen-form-control" type="text" title="No special characters" id="comment" autocomplete="off" name="adcomment" value="" placeholder="Auto: BULAN-PROFILE-PREFIX">
    </div>
  </div>
  
  <?php if ($genprof != "") { ?>
  <div class="gen-form-group" style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; border-radius: 10px; padding: 12px; margin-top: 10px; color: #059669; text-align: center; justify-content: center;">
      <div id="GetValidPrice" style="font-size: 14px;"><?= $ValidPrice ?></div>
  </div>
  <?php } else { ?>
  <div id="GetValidPrice" style="display: none; background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; border-radius: 10px; padding: 12px; margin-top: 10px; color: #059669; text-align: center; font-size: 14px;"></div>
  <?php } ?>
</div>
</form>
</div>
</div>
</div>

<div class="row">
<div class="col-12">
	<div class="gen-card">
		<div class="gen-header success">
            <div class="gen-header-icon">
                <i class="fa fa-ticket"></i>
            </div>
            <div class="gen-header-text">
                <h3><?= $_last_generate ?></h3>
            </div>
		</div>
		<div class="gen-body">
            <table class="gen-table">
            <tr>
                <td><?= $_generate_code ?></td><td><b><?= $ucode ?></b></td>
            </tr>
            <tr>
                <td><?= $_date ?></td><td><b><?= $udate ?></b></td>
            </tr>
            <tr>
                <td><?= $_profile ?></td><td><b><?= $uprofile ?></b></td>
            </tr>
            <tr>
                <td><?= $_validity ?></td><td><b><?= $uvalid ?></b></td>
            <tr>
            <tr>
                <td><?= $_selling_price ?></td><td><b><?= $suprice ?></b></td>
            </tr>
            <tr>
                <td><?= $_lock_user ?></td><td><b><?= $ulock ?></b></td>
            </tr>
            <tr>
                <td>Total Voucher</td><td><b style="color: #3b82f6;"><?= $uqty ?> pcs</b></td>
            </tr>
            <tr>
                <td>Reseller Prefix</td><td><b style="color: #10b981;"><?= $uprefix ?></b></td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 16px 0 0 0;">
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 14px 16px; text-align: left; box-shadow: 0 2px 4px rgba(56, 189, 248, 0.1);">
                        <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: 500; color: #0369a1; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa fa-info-circle" style="font-size: 16px; margin-top: 2px; color: #0ea5e9;"></i> 
                            <span><?= $_format_time_limit ?></span>
                        </p>
                        <p style="margin: 0; font-size: 13px; font-weight: 500; color: #0369a1; line-height: 1.5; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fa fa-lightbulb-o" style="font-size: 16px; margin-top: 1px; color: #0ea5e9;"></i>
                            <span><?= $_details_add_user ?></span>
                        </p>
                    </div>
                </td>
            </tr>
            </table>
        </div>
    </div>
</div>
</div>
</div> <!-- End gen-container -->
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
  var input = document.getElementById('qtyinput');
  input.value = value;
  // Pulse animation feedback without scaling to prevent overlapping
  input.style.borderColor = '#3b82f6';
  input.style.boxShadow = '0 0 0 4px rgba(59, 130, 246, 0.2)';
  input.style.backgroundColor = '#f0f9ff';
  setTimeout(function() {
    input.style.borderColor = '#e2e8f0';
    input.style.boxShadow = 'none';
    input.style.backgroundColor = '#f8fafc';
  }, 300);
}

// Set Profile from quick buttons
function setProfile(profileName) {
  var profSelect = document.getElementById('profselect');
  profSelect.value = profileName;
  
  // Update button styling using standard classes
  var profileButtons = document.querySelectorAll('.profile-btn');
  for (var i = 0; i < profileButtons.length; i++) {
    var btn = profileButtons[i];
    var btnProfile = btn.getAttribute('data-profile');
    var icon = btn.querySelector('i.fa');
    
    if (btnProfile === profileName) {
      btn.classList.add('prof-active');
      if(icon) {
          icon.classList.remove('fa-tag');
          icon.classList.add('fa-check-circle');
      }
    } else {
      btn.classList.remove('prof-active');
      if(icon) {
          icon.classList.remove('fa-check-circle');
          icon.classList.add('fa-tag');
      }
    }
  }
  
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
    // Mark default profile button as active
    var profBtns = document.querySelectorAll('.prof-btn');
    for (var i = 0; i < profBtns.length; i++) {
      if (profBtns[i].getAttribute('data-profile') === defaultProfile) {
        profBtns[i].classList.add('prof-active');
      }
    }
  }
});

// Quick Generate Panel
function toggleQuickGen() {
  var body = document.getElementById('quickGenBody');
  var chevron = document.getElementById('quickGenChevron');
  if (body.style.display === 'none') {
    body.style.display = 'block';
    body.style.opacity = '0';
    body.style.transition = 'opacity 0.3s ease';
    setTimeout(function() { body.style.opacity = '1'; }, 10);
    chevron.style.transform = 'rotate(180deg)';
  } else {
    body.style.opacity = '0';
    setTimeout(function() { body.style.display = 'none'; }, 300);
    chevron.style.transform = 'rotate(0deg)';
  }
}

function toggleAddPreset() {
  var form = document.getElementById('addPresetForm');
  var btn = document.getElementById('btnShowAddPreset');
  
  if (form.style.display === 'none') {
    // Reset form for adding new
    document.getElementById('presetId').value = '';
    document.getElementById('presetFormTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Tambah Preset Baru';
    document.getElementById('presetName').value = '';
    document.getElementById('presetQty').value = '50';
    document.getElementById('presetReseller').value = '';
    
    form.style.display = 'block';
    form.style.opacity = '0';
    form.style.transition = 'opacity 0.3s ease';
    setTimeout(function() { form.style.opacity = '1'; }, 10);
    btn.style.display = 'none';
  } else {
    form.style.opacity = '0';
    setTimeout(function() { form.style.display = 'none'; }, 300);
    btn.style.display = 'inline-block';
  }
}

function editQuickGen(preset) {
  // Show form if hidden
  var form = document.getElementById('addPresetForm');
  var btn = document.getElementById('btnShowAddPreset');
  
  if (form.style.display === 'none') {
    form.style.display = 'block';
    form.style.opacity = '1';
    btn.style.display = 'none';
  }
  
  // Update title and set hidden ID
  document.getElementById('presetFormTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Preset';
  document.getElementById('presetId').value = preset.id;
  
  // Fill fields
  document.getElementById('presetName').value = preset.name;
  document.getElementById('presetProfile').value = preset.profile;
  document.getElementById('presetQty').value = preset.qty;
  document.getElementById('presetReseller').value = preset.prefix || '';
  document.getElementById('presetServer').value = preset.server || 'all';
  document.getElementById('presetUsermode').value = preset.usermode || 'vc';
  document.getElementById('presetUserlength').value = preset.userlength || '5';
  document.getElementById('presetChar').value = preset.char || 'mix1';
  
  // Scroll to form smoothly
  form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function saveQuickGenPreset() {
  var id = document.getElementById('presetId').value;
  var name = document.getElementById('presetName').value.trim();
  var profile = document.getElementById('presetProfile').value;
  var qty = document.getElementById('presetQty').value;
  var prefix = document.getElementById('presetReseller').value;
  var server = document.getElementById('presetServer').value;
  var usermode = document.getElementById('presetUsermode').value;
  var userlength = document.getElementById('presetUserlength').value;
  var charType = document.getElementById('presetChar').value;

  if (!name || !profile || !qty) {
    alert('Nama preset, profile, dan qty harus diisi!');
    return;
  }

  var actionType = id ? 'edit' : 'add';
  var dataStr = 'action=' + actionType + 
                (id ? '&id=' + encodeURIComponent(id) : '') +
                '&name=' + encodeURIComponent(name) + 
                '&profile=' + encodeURIComponent(profile) + 
                '&qty=' + qty + 
                '&prefix=' + encodeURIComponent(prefix) + 
                '&server=' + encodeURIComponent(server) + 
                '&usermode=' + encodeURIComponent(usermode) + 
                '&userlength=' + userlength + 
                '&char=' + encodeURIComponent(charType);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', './process/quickgen.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var resp = JSON.parse(xhr.responseText);
      if (resp.status === 'success') {
        location.reload();
      } else {
        alert(resp.message || 'Gagal menyimpan preset');
      }
    }
  };
  xhr.send(dataStr);
}

function deleteQuickGen(id, btn) {
  if (!confirm('Hapus preset ini?')) return;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', './process/quickgen.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var resp = JSON.parse(xhr.responseText);
      if (resp.status === 'success') {
        var item = btn.closest('.quick-gen-item');
        item.style.transition = 'all 0.3s ease';
        item.style.opacity = '0';
        item.style.transform = 'scale(0.5)';
        setTimeout(function() { item.remove(); }, 300);
      }
    }
  };
  xhr.send('action=delete&id=' + encodeURIComponent(id));
}

function executeQuickGen(preset) {
  if (!confirm('Generate ' + preset.qty + ' user dengan profile "' + preset.profile + '"?')) return;

  // Fill the main form with preset values
  var form = document.querySelector('form[method="post"]');
  if (!form) return;

  // Set form field values
  document.getElementById('profselect').value = preset.profile;
  document.getElementById('qtyinput').value = preset.qty;
  document.getElementById('prefixinput').value = preset.prefix || '';
  
  // Set user mode
  var userSelect = document.getElementById('user');
  if (userSelect) userSelect.value = preset.usermode || 'vc';
  
  // Set user length
  var userlSelect = document.getElementById('userl');
  if (userlSelect) userlSelect.value = preset.userlength || 5;
  
  // Set server
  var serverSelect = form.querySelector('select[name="server"]');
  if (serverSelect) serverSelect.value = preset.server || 'all';
  
  // Set character
  var charSelect = form.querySelector('select[name="char"]');
  if (charSelect) charSelect.value = preset.char || 'mix1';
  
  // Update comment with auto format
  updateComment();
  
  // Add hidden fields for timelimit, datalimit, mbgb if not present
  var existingTimelimit = form.querySelector('input[name="timelimit"]');
  if (!existingTimelimit) {
    var tlInput = document.createElement('input');
    tlInput.type = 'hidden';
    tlInput.name = 'timelimit';
    tlInput.value = '';
    form.appendChild(tlInput);
  }
  var existingDatalimit = form.querySelector('input[name="datalimit"]');
  if (!existingDatalimit) {
    var dlInput = document.createElement('input');
    dlInput.type = 'hidden';
    dlInput.name = 'datalimit';
    dlInput.value = '';
    form.appendChild(dlInput);
  }
  var existingMbgb = form.querySelector('select[name="mbgb"]');
  if (!existingMbgb) {
    var mbInput = document.createElement('input');
    mbInput.type = 'hidden';
    mbInput.name = 'mbgb';
    mbInput.value = '1048576';
    form.appendChild(mbInput);
  }

  // Show loader
  var loader = document.getElementById('loader');
  if (loader) loader.style.display = 'inline';
  
  // Submit the form
  form.submit();
}

// Quick Print Logic
function toggleQuickPrint() {
  var body = document.getElementById('quickPrintBody');
  var chevron = document.getElementById('quickPrintChevron');
  if (body.style.display === 'none') {
    body.style.display = 'block';
    body.style.opacity = '0';
    body.style.transition = 'opacity 0.3s ease';
    setTimeout(function() { body.style.opacity = '1'; }, 10);
    chevron.style.transform = 'rotate(180deg)';
    
    // Auto load comments if empty
    if (document.getElementById('qpCommentSemua').options.length <= 1) {
      loadCommentsForPrint();
    }
  } else {
    body.style.opacity = '0';
    setTimeout(function() { body.style.display = 'none'; }, 300);
    chevron.style.transform = 'rotate(0deg)';
  }
}

function loadCommentsForPrint() {
  var select = document.getElementById('qpCommentSemua');
  select.innerHTML = '<option value="">Loading...</option>';
  
  var xhr = new XMLHttpRequest();
  xhr.open('GET', './process/getcomments.php?session=<?= $session; ?>', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        var resp = JSON.parse(xhr.responseText);
        select.innerHTML = '<option value="">Pilih Comment...</option>';
        var currentGen = '<?= $urlprint ?>';
        
        for (var j = 0; j < resp.length; j++) {
          var item = resp[j];
          var opt = document.createElement('option');
          opt.value = item.comment;
          opt.textContent = item.comment + ' [' + item.count + ' user]';
          if (item.comment === currentGen) {
            opt.selected = true;
          }
          select.appendChild(opt);
        }
      } catch (e) {
        select.innerHTML = '<option value="">Gagal memuat</option>';
      }
    }
  };
  xhr.send();
}

function doQuickPrint(by, qr, small) {
  var val = '';
  var url = '';
  
  if (by === 'baru') {
    val = document.getElementById('qpCommentBaru').value;
    if (!val) {
      alert('Belum ada generate baru!');
      return;
    }
    url = "./voucher/print.php?id=" + encodeURIComponent(val) + "&qr=" + qr + "&small=" + small + "&session=<?= $session; ?>";
  } else if (by === 'semua') {
    val = document.getElementById('qpCommentSemua').value;
    if (!val) {
      alert('Pilih comment terlebih dahulu!');
      return;
    }
    url = "./voucher/print.php?id=" + encodeURIComponent(val) + "&qr=" + qr + "&small=" + small + "&session=<?= $session; ?>";
  }
  
  var win = window.open(url, '_blank');
  win.focus();
}
</script>
</div>
