<?php
// hide all error
//error_reporting(0);
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

	$getNet = $API->comm("/tool/netwatch/print");
	$TotalReg = count($getNet);
	$countNet = $API->comm("/tool/netwatch/print", array(
		"count-only" => ""
	));

	// Filter untuk hanya menampilkan yang statusnya "up"
	$filteredNet = array_filter($getNet, function($net) {
		return isset($net['status']) && $net['status'] === 'up';
	});
	$TotalFilteredReg = count($filteredNet);
}

?>
<form autocomplete="off"  method="post" action="">
<div class="row">
	<div id="reloadNetwacthActive">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
            <h3 style="display: flex; justify-content: space-between; align-items: center;">
                    <span>
                        <a href="./?interface=netwatch&iphost=0.0.0.0&flag=A&session=<?= $session; ?>" title="Input Netwatch">
                            <i class="fa fa-sliders"></i> List Netwatch
                        </a>
                    </span>
                    <span>
                        &nbsp; &nbsp;
                        <a href="./?interface=netwatchup&iphost=0.0.0.0&flag=A&session=<?= $session; ?>" title="Netwatch Up">
                            <i class="fa fa-cloud-upload"></i> Up
                        </a>
                        &nbsp; | &nbsp;
                        <a href="./?interface=netwatchdown&iphost=0.0.0.0&flag=A&session=<?= $session; ?>" title="Netwatch Down">
                            <i class="fa fa-cloud-download"></i> Down
                        </a>
                    </span>
                </h3>
			</div>
		</div>
		<div class="card-body">
			<div class="overflow box-bordered" style="max-height: 75vh">
				<hr>
				<table id="dataTable" class="table table-bordered table-hover text-nowrap">
					<thead>
						<tr>
							<th align="center">
								<?php
								if ($TotalFilteredReg < 2) {
									echo "$TotalFilteredReg item  ";
								} elseif ($TotalFilteredReg > 1) {
									echo "$TotalFilteredReg items   ";
								}
								?>
							</th>
							<th> Disable </th>
							<th> Comment </th>
							<th> Host </th>
							<th> Interval </th>
							<th> Timeout </th>
							<th> Status </th>
							<th> Since </th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($filteredNet as $net) {
							?>
							<tr><td style='text-align:center;'><i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to delete host : <?= $net['host']; ?> ?')){loadpage('./?remove-Netwatch=<?= $net['.id'] ?>&session=<?= $session; ?>')}else{}" title='Remove <?= $net['.id']; ?>'></i>
							<?php
							if ($net['disabled']=="true") {
								$uriprocess = "'./?enable-Netwatch=" . $net['.id'] . "&session=" . $session . "'";
								echo "&nbsp&nbsp&nbsp&nbsp<span class='text-warning btnsmall pointer' title='Enable Netwatch ' onclick=loadpage(".$uriprocess.")><i class='fa fa-lock'></i></span>";
							}else{
								$uriprocess = "'./?disable-Netwatch=" . $net['.id'] . "&session=" . $session . "'";
								echo "&nbsp&nbsp&nbsp&nbsp<span title='Disable Netwatch ' class='btnsmall pointer' onclick=loadpage(".$uriprocess.")><i class='fa fa-unlock '></i></span>";
							}
							echo "&nbsp&nbsp&nbsp&nbsp<a title='Ping' href='./?interface=ping&idping=".$getNet[$i]['.id']."&session=".$session."'><i class='fa fa-life-ring' ></i></b>";
							echo "&nbsp&nbsp&nbsp&nbsp <a title='Edit Netwatch Ip ".$getNet[$i]['host']."' href='./?interface=add-netwatch&iphost=".$getNet[$i]['host']."&flag=C&session=".$session."'><i class='fa fa-edit'></i></a>";
							echo "</td>";
							echo "<td>".$net['disabled']."</td>";
							echo "<td>".$net['comment']."</td>";
							echo "<td>".$net['host']."</td>";
							echo "<td>".$net['interval']."</td>";
							echo "<td>".$net['timeout']."</td>";
							echo "<td>".$net['status']."</td>";
							echo "<td>".$net['since']."</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
				<hr>
			</div>
		</div>
	</div>
	</div>
</div>