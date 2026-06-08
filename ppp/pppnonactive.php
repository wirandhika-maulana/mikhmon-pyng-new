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
            $API->connect($dual_router_ip, $dual_router_user, $dual_router_pass);
        } else {
            $API->connect($iphost, $userhost, decrypt($passwdhost));
        }
    }

    // load session MikroTik
    $session = $_GET['session'];

    include_once __DIR__ . "/function.php";

    // Ambil semua PPP secrets
    $allsecrets = $API->comm("/ppp/secret/print");
    
    // Ambil semua PPP active connections
    $activeconnections = $API->comm("/ppp/active/print");
    
    // Buat array nama user yang sedang active
    $activenames = array();
    foreach ($activeconnections as $active) {
        $activenames[] = $active['name'];
    }
    
    // Filter secrets yang tidak ada di active connections
    $getnonactive = array();
    foreach ($allsecrets as $secret) {
        if (!in_array($secret['name'], $activenames)) {
            $getnonactive[] = $secret;
        }
    }
    
    $TotalReg = count($getnonactive);
    $countnonactive = $TotalReg;

    $cek = json_encode($allsecrets);
    if (strpos($cek, "ArrayA")) {
        echo "<script>window.location='./?info=Tidak Dapat Terhubung Ke Router.&session=" . $session . "'</script>";
    }
}
?>

<div class="row">
	<div id="reloadPppoeNonActive">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3>
						<i class="fa fa-wifi"></i> <?= $_ppp_non_active ?>
						&nbsp &nbsp &nbsp | &nbsp<a href="./?ppp=addsecret&session=<?= $session; ?>" title="Add Secrets"><i class="fa fa-user-plus"></i> Add PPP Secret </a>
						&nbsp &nbsp &nbsp &nbsp<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small>
					</h3>
				</div>
				<div class="card-body overflow">
					<br>
					<table id="dataTable" class="table table-bordered table-hover text-nowrap">
						<thead>
							<tr>
								<th style="min-width:50px;" class="text-center">
									<?php
									if ($countnonactive < 2) {
										echo "$countnonactive items";
									} elseif ($countnonactive > 1) {
										echo "$countnonactive items";
									};
									?>
								</th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i>&nbsp&nbsp&nbsp<?= $_name ?> </th>
								<th class="align-middle">Service</th>
								<th class="align-middle">Profile</th>
								<th class="align-middle">Caller ID</th>
								<th class="align-middle">Address</th>
								<th class="align-middle">Uptime</th>
								<th class="align-middle">Comment</th>
							</tr>
						</thead>
						<tbody>
							<?php
							for ($i = 0; $i < $TotalReg; $i++) {
								$uriprocess = "'./?remove-pppsecret=" . $getnonactive[$i]['.id'] . "&session=" . $session . "'";
								echo "<tr>";
							?>
								<td style='text-align:center;'>
									<i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to remove ppp secret ( <?= $getnonactive[$i]['name']; ?> )?')){loadpage('./?remove-pppsecret=<?= $getnonactive[$i]['.id']; ?>&session=<?= $session; ?>')}else{}" title='Remove <?= $getnonactive[$i]['name']; ?>'></i>
								</td>
								<td><?= $getnonactive[$i]['name']; ?></td>
								<td><?= $getnonactive[$i]['service']; ?></td>
								<td><?= $getnonactive[$i]['profile']; ?></td>
								<td><?= isset($getnonactive[$i]['caller-id']) ? $getnonactive[$i]['caller-id'] : '-'; ?></td>
								<td><?= isset($getnonactive[$i]['local-address']) ? $getnonactive[$i]['local-address'] : '-'; ?></td>
								<td>-</td>
								<td><?= isset($getnonactive[$i]['comment']) ? $getnonactive[$i]['comment'] : caridtpelanggan($getnonactive[$i]['name'], 9); ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
