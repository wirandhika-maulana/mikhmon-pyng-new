<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$session = $_GET['session'];

	include('../include/config.php');
	include('../include/readcfg.php');
	
// lang
	include('../include/lang.php');
	include('../lang/'.$langid.'.php');

	$API = new RouterosAPI();
	$API->debug = false;
	$API->connect($iphost, $userhost, decrypt($passwdhost));

	// load session MikroTik
	$session = $_GET['session'];
//	$serveractive = $_GET['server'];

	include "ppp/function.php";

	$getactive = $API->comm("/ppp/active/print");
    if (!is_array($getactive)) $getactive = [];
	$TotalReg = count($getactive);
 
	$countactive = $API->comm("/ppp/active/print", array(
		"count-only" => "",
	));
	$cek=json_encode($countactive);
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
							$iduser=$API->comm("/ppp/secret/print",['?name'=>$getactive[$i]['name'],]);
							echo "&nbsp&nbsp&nbsp <a title='Grafik User' href='./?ppp=grafik&idsecret=".$getactive[$i]['service']."-".$getactive[$i]['name']."&session=".$session."'><i class='fa fa-area-chart' aria-hidden='true'></i></b>";
							echo "&nbsp&nbsp&nbsp <a title='Billing User' href='./?ppp=billing&idsecret=".$iduser[0]['.id']."&session=".$session."'><i class='fa fa-money' aria-hidden='true'></i></b>";
							echo "&nbsp&nbsp&nbsp <a title='History User' href='./?ppp=history&idhistory=".$iduser[0]['.id']."&session=".$session."'><i class='fa fa-list-ol' aria-hidden='true'></i></b>";
							echo "</td>";
							echo "<td>" . $getactive[$i]['name'] . "</td>";
							echo "<td>" . $getactive[$i]['service'] . "</td>";
							echo "<td>" . $getactive[$i]['caller-id'] . "</td>";
							echo "<td>" . $getactive[$i]['encoding'] . "</td>";
							echo "<td>" . $getactive[$i]['address'] . "</td>";
							echo "<td>" . $getactive[$i]['uptime'] . "</td>";
							echo "<td>" . (isset($iduser[0]['comment']) ? $iduser[0]['comment'] : caridtpelanggan($getactive[$i]['name'],9)) . "</td>";
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