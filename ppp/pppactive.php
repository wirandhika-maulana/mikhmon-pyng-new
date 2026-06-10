<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$session = $_GET['session'];

	include_once(__DIR__ . '/../include/config.php');
	include_once(__DIR__ . '/../include/readcfg.php');
	
// lang
	include_once(__DIR__ . '/../include/lang.php');
    if (file_exists(__DIR__ . '/../lang/'.$langid.'.php')) {
	    include_once(__DIR__ . '/../lang/'.$langid.'.php');
    }

    // Include RouterOS API class for AJAX reload
    if (!isset($API) || !is_object($API)) {
        include_once(__DIR__ . '/../lib/routeros_api.class.php');
        $API = new RouterosAPI();
        $API->debug = false;

        // Load Dual Router Config (Secondary PPPoE Router)
        $dual_router_ip = "";
        $dual_router_user = "";
        $dual_router_pass = "";
        $dual_file = __DIR__ . "/../include/dual_router_config.php";
        if (file_exists($dual_file)) {
            include_once($dual_file);
            if (isset($dual_router[$session]) && !empty($dual_router[$session]['ip'])) {
                $dual_router_ip = $dual_router[$session]['ip'];
                $dual_router_user = $dual_router[$session]['user'];
                $dual_router_pass = decrypt($dual_router[$session]['pass']);
            }
        }

        // Connect to dual router if available, otherwise main router
        if (!empty($dual_router_ip)) {
            $conn_result = $API->connect($dual_router_ip, $dual_router_user, $dual_router_pass);
        } else {
            $conn_result = $API->connect($iphost, $userhost, decrypt($passwdhost));
        }
    }

	// load session MikroTik
	$session = $_GET['session'];
//	$serveractive = $_GET['server'];

	include_once __DIR__ . "/function.php";

	$getactive = $API->comm("/ppp/active/print");
    if (!is_array($getactive)) $getactive = [];
	$TotalReg = count($getactive);
 
    // Fetch all secrets ONCE to avoid making API calls inside the loop
    $allsecrets = $API->comm("/ppp/secret/print");
    $secrets_by_name = [];
    if (is_array($allsecrets)) {
        foreach ($allsecrets as $s) {
            $secrets_by_name[$s['name']] = $s;
        }
    }

	$countactive = $TotalReg;
	$cek=json_encode($getactive);
	if (strpos($cek,"ArrayA")) {
        echo "<script>window.location='./?info=Tidak Dapat Terhubung Ke Router.&session=" . $session . "'</script>";
	}
}
?>
<div class="row">
	<div id="reloadPppoeActive">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3>
				<i class="fa fa-wifi"></i> <?= $_ppp_active ?> 
				&nbsp &nbsp &nbsp | &nbsp<a href="./?ppp=addsecret&session=<?= $session; ?>" title="Add Secrets"><i class="fa fa-user-plus"></i> Add PPP Secret </a>
				&nbsp &nbsp &nbsp &nbsp<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small>
				</h3>
			</div>
			<div class="card-body overflow">
				<div class="input-group">
					<div class="input-group-3 col-box-3">
						<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder="Search...">
					</div>
				</div>
				<br>
				<table id="dataTable" class="table table-bordered table-hover text-nowrap">
					<thead>
						<tr>
							<th style="min-width:50px;" class="text-center">
							<?php
							if ($countactive < 2) {
								echo "$countactive items";
							} elseif ($countactive > 1) {
								echo "$countactive items";
							};
							?></th>
							<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i>&nbsp&nbsp&nbsp<?= $_name ?> </th>
							<th class="align-middle">Service</th>
							<th class="align-middle">Caller ID</th>
							<th class="align-middle">Encoding</th>
							<th class="align-middle">Address</th>
							<th class="align-middle">Uptime</th>
							<th class="align-middle">Comment</th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ($i = 0; $i < $TotalReg; $i++) {
							$uriprocess = "'./?remove-pppsecret=" . $getactive[$i]['.id'] . "&session=" . $session . "'";
							echo "<tr>";
						?>
							<td style='text-align:center;'><i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to remove ppp active ( <?= $getactive[$i]['name']; ?> )?')){loadpage('./?remove-pactive=<?= $getactive[$i]['.id']; ?>&disabled-name=<?= $getactive[$i]['name']; ?>&session=<?= $session; ?>')}else{}" title='Remove <?= $getactive[$i]['name']; ?>'></i>
						<?php
							$matched_secret = isset($secrets_by_name[$getactive[$i]['name']]) ? $secrets_by_name[$getactive[$i]['name']] : null;
							$sec_id = isset($matched_secret['.id']) ? $matched_secret['.id'] : '';
							$sec_comment = isset($matched_secret['comment']) ? $matched_secret['comment'] : caridtpelanggan($getactive[$i]['name'],9);

							echo "&nbsp&nbsp&nbsp <a title='Grafik User' href='./?ppp=grafik&idsecret=".$getactive[$i]['service']."-".$getactive[$i]['name']."&session=".$session."'><i class='fa fa-area-chart' aria-hidden='true'></i></a>";
							echo "&nbsp&nbsp&nbsp <a title='Billing User' href='./?ppp=billing&idsecret=".$sec_id."&session=".$session."'><i class='fa fa-money' aria-hidden='true'></i></a>";
							echo "&nbsp&nbsp&nbsp <a title='History User' href='./?ppp=history&idhistory=".$sec_id."&session=".$session."'><i class='fa fa-list-ol' aria-hidden='true'></i></a>";
							echo "</td>";
							echo "<td>" . $getactive[$i]['name'] . "</td>";
							echo "<td>" . $getactive[$i]['service'] . "</td>";
							echo "<td>" . $getactive[$i]['caller-id'] . "</td>";
							echo "<td>" . $getactive[$i]['encoding'] . "</td>";
							echo "<td>" . $getactive[$i]['address'] . "</td>";
							echo "<td>" . $getactive[$i]['uptime'] . "</td>";
							echo "<td>" . $sec_comment . "</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	</div>
</div>