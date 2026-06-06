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
                        <i class="fa fa-pie-chart"></i> List Netwatch |
                        <a href="./?interface=add-netwatch&iphost=0.0.0.0&flag=A&session=<?= $session; ?>" title="Input Netwatch">
                            <i class="fa fa-sliders"></i> Add NetWatch
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
								if ($countNet < 2) {
									echo "$countNet item  ";
								} elseif ($countNet > 1) {
									echo "$countNet items   ";
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
						for ($i = 0; $i < $TotalReg; $i++) {
							?>
							<tr><td style='text-align:center;'><i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to delete host : <?= $getNet[$i]['host']; ?> ?')){loadpage('./?remove-Netwatch=<?= $getNet[$i]['.id'] ?>&session=<?= $session; ?>')}else{}" title='Remove <?= $getNet[$i]['.id']; ?>'></i>
							<?php
							if ($getNet[$i]['disabled']=="true") {
								$uriprocess = "'./?enable-Netwatch=" . $getNet[$i]['.id'] . "&session=" . $session . "'";
								echo "&nbsp&nbsp&nbsp&nbsp<span class='text-warning btnsmall pointer' title='Enable Netwatch ' onclick=loadpage(".$uriprocess.")><i class='fa fa-lock'></i></span>";
							}else{
								$uriprocess = "'./?disable-Netwatch=" . $getNet[$i]['.id'] . "&session=" . $session . "'";
								echo "&nbsp&nbsp&nbsp&nbsp<span title='Disable Netwatch ' class='btnsmall pointer' onclick=loadpage(".$uriprocess.")><i class='fa fa-unlock '></i></span>";
							}
							echo "&nbsp&nbsp&nbsp&nbsp<a title='Ping' href='./?interface=ping&idping=".$getNet[$i]['.id']."&session=".$session."'><i class='fa fa-life-ring' ></i></b>";
							echo "&nbsp&nbsp&nbsp&nbsp <a title='Edit Netwatch Ip ".$getNet[$i]['host']."' href='./?interface=add-netwatch&iphost=".$getNet[$i]['host']."&flag=C&session=".$session."'><i class='fa fa-edit'></i></a>";
							echo "</td>";
							echo "<td>".$getNet[$i]['disabled']."</td>";
							echo "<td>".$getNet[$i]['comment']."</td>";

//							echo "<td><a title='Edit Binding Ip ".$getNet[$i]['host']."\nKlik untuk edit Netwatch' href='./?interface=add-netwatch&iphost=".$getNet[$i]['host']."&flag=C&session=".$session."'><b>".$getNet[$i]['host']."</b></a></td>";

							echo "<td>".$getNet[$i]['host']."</td>";
							echo "<td>".$getNet[$i]['interval']."</td>";
							echo "<td>".$getNet[$i]['timeout']."</td>";
							echo "<td>".$getNet[$i]['status']."</td>";
							echo "<td>".$getNet[$i]['since']."</td>";
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

