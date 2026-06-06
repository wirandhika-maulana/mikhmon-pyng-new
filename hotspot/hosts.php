<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {

	if ($hotspot == "hostp") {
		$gethosts = $API->comm("/ip/hotspot/host/print", array(
			"?bypassed" => "yes",
		));
		$TotalReg = count($gethosts);

		$counthosts = $API->comm("/ip/hotspot/host/print", array(
			"?bypassed" => "yes",
			"count-only" => "",
		));

	} elseif ($hotspot == "hosta") {
		$gethosts = $API->comm("/ip/hotspot/host/print", array(
			"?authorized" => "yes",
		));
		$TotalReg = count($gethosts);

		$counthosts = $API->comm("/ip/hotspot/host/print", array(
			"?authorized" => "yes",
			"count-only" => "",
		));

	} else {
		$gethosts = $API->comm("/ip/hotspot/host/print");
		$TotalReg = count($gethosts);

		$counthosts = $API->comm("/ip/hotspot/host/print", array(
			"count-only" => "",
		));
	}
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
				<h3><i class="fa fa-laptop"></i> Hosts  
	<?php
	if ($counthosts < 2) {
		echo "$counthosts item";
	} elseif ($counthosts > 1) {
		echo "$counthosts items ";
	};
	?>&nbsp;
		 | <a class="text-info" title="All" href="./?hotspot=hosts&session=<?= $session; ?>">&nbsp;&nbsp;All&nbsp;&nbsp;</a>
		 | <a class="text-success" title="Authorized" href="./?hotspot=hosta&session=<?= $session; ?>">&nbsp;&nbsp;A&nbsp;&nbsp;</a>
		 | <a class="text-primary" title="Bypassed" href="./?hotspot=hostp&session=<?= $session; ?>">&nbsp;&nbsp;P&nbsp;&nbsp;</a>
		 | &nbsp;&nbsp;<i onclick="location.reload();" class="fa fa-refresh pointer " title="Reload data"></i>
				</h3>
			</div>
<!-- /.card-header -->
			<div class="card-body">	
				<div class="w-6">
					<input id="filterTable" type="text" class="form-control" placeholder="Search..">
				</div>
				<div class="overflow box-bordered mr-t-10" style="max-height: 75vh"> 	   
					<table id="dataTable" class="table table-bordered table-hover text-nowrap">
						<thead>
							<tr>
								<th></th>
								<th></th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> MAC Address</th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Address</th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> To Address</th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Server</th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> <?= $_comment ?></th>
							</tr>
						</thead>
						<tbody>  	
<?php
for ($i = 0; $i < $TotalReg; $i++) {
	$hosts = $gethosts[$i];
	$id = $hosts['.id'];

	$maca = $hosts['mac-address'];
	$addr = $hosts['address'];
	$toaddr = $hosts['to-address'];
	$server = $hosts['server'];
	$commt = $hosts['comment'];

	$uriprocess = "'./?remove-host=" . $id . "&session=" . $session . "'";

	echo "<tr>";
	echo "<td style='text-align:center;width:80px;'>
		&nbsp&nbsp&nbsp<span class='pointer'  title='Remove " . $maca . "' onclick=loadpage(".$uriprocess.")><i class='fa fa-minus-square text-danger'></i></span>";

	$ceknetwatch = $API->comm("/tool/netwatch/print",["?host" => "$toaddr"]);
	if (empty($ceknetwatch)) {
		echo "&nbsp&nbsp&nbsp&nbsp<a title='Netwatch Mac ".$maca."\nIp ".$toaddr."\nKlik untuk melakukan Netwatch' href='./?interface=add-netwatch&iphost=".$toaddr."&flag=E&session=".$session."'><i class='fa fa-cubes text-blue pointer' aria-hidden='true'></i></a>";
	}else{
		echo "&nbsp&nbsp&nbsp&nbsp<span style='cursor:pointer;' title='Ip ".$toaddr."\nSudah di Netwatch.'><a href='./?interface=netwatch&session=".$session."'><i class='fa fa-cubes text-red pointer' aria-hidden='true'></i></a></span>";
	}
	
	$dtbind = $API->comm("/ip/hotspot/ip-binding/print",["?mac-address" => "$maca" ]);
	if (empty($dtbind)) {
		echo "&nbsp&nbsp&nbsp <a title='Binding Ip ".$toaddr."' href='./?hotspot=binding-ip&flag=B&idbinding=".$gethotspotactive[$i]['.id']."&session=".$session."'><i class='fa fa-binoculars text-warning pointer' aria-hidden='true'></i></a>";
	}else{
		echo "&nbsp&nbsp&nbsp <a href='./?hotspot=ipbinding&session=".$session."'><span title='Sudah Binding'><i class='fa fa-binoculars text-green pointer' aria-hidden='true'></i></span></a>";
	}

	echo "</td>";
	echo "<td style='text-align:center;'>";
	
	if ($hosts['authorized'] == "true" && $hosts['DHCP'] == "true") {
		echo "<b class='text-success' title='A - authorized, H - DHCP'>A H</b>";
	} elseif ($hosts['authorized'] == "true" && $hosts['dynamic'] == "true") {
		echo "<b class='text-success' title='A - Authorized, D - dynamic'>A D</b>";
	} elseif ($hosts['authorized'] == "true") {
		echo "<b class='text-success' title='A - authorized'>A</b>";
	} elseif ($hosts['DHCP'] == "true") {
		echo "<b class='text-success' title='H - DHCP'>H</b>";
	} elseif ($hosts['dynamic'] == "true") {
		echo "<b class='text-success' title='D - dynamic'>D</b>";
	} elseif ($hosts['bypassed'] == "true") {
		echo "<b class='text-primary' title='P - Bypassed'>P</b>";
	} else {
	
	}
	
	echo "</td>";

	echo "<td>" . $maca . "</td>";
	echo "<td>" . $addr . "</td>";
	echo "<td>".$toaddr."</td>";
	echo "<td>" . $server . "</td>";
	echo "<td>" . $commt . "</td>";
	echo "</tr>";
}
?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>