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
	include('../ppp/pppsecrets.php');
	
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

		// Hitung jumlah pengguna yang sedang down
		$countDownUsers = 0;
		for ($i = 0; $i < $TotalReg; $i++) {
			if ($getNet[$i]['status'] == "down") {
				$countDownUsers++;
			}
		}
}

?>
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3><i class=" fa fa-align-justify"></i> <?= $_ppp_log ?> &nbsp; | &nbsp;&nbsp;<i onclick="location.reload();" class="fa fa-refresh pointer " title="Reload data"></i></h3>
									</div>
								<div class="card-body">

							<div style="max-width: 350px;">
							<input id="filterTable" type="text" class="form-control" placeholder="Search.."> 
								</div>
							<div style="padding: 5px; max-height: 75vh;" class="mr-t-10 overflow">
						<table class="table table-sm table-bordered table-hover" id="dataTable" >
					<thead>
						<tr>
							<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Comment </th>
							<th class="pointer" title="Click to sort"><i class="fa fa-sort"> IP Host </th>
							<th> Status </th>
							<th class="pointer" title="Click to sort"><i class="fa fa-sort"> Since </th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ($i = 0; $i < $TotalReg; $i++) {
							if ($getNet[$i]['status'] == "down") { // Check if status is "down"
						?>
							<tr>
								<?php
								echo "</td>";
								echo "<td>".$getNet[$i]['comment']."</td>";
	//							echo "<td><a title='Edit Binding Ip ".$getNet[$i]['host']."\nKlik untuk edit Netwatch' href='./?interface=add-netwatch&iphost=".$getNet[$i]['host']."&flag=C&session=".$session."'><b>".$getNet[$i]['host']."</b></a></td>";
								echo "<td>".$getNet[$i]['host']."</td>";
								echo "<td>".$getNet[$i]['status']."</td>";
								echo "<td>".$getNet[$i]['since']."</td>";
								echo "</tr>";
							}
						}
						?>
					</tbody>
				</table>
				</div>
				</tr>
		</div>
	</div>
	</div>
</div>
