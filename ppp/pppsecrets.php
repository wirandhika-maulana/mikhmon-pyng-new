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

// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	echo '
<html>
<head><title>403 Forbidden</title></head>
<body bgcolor="white">
<center><h1>403 Forbidden</h1></center>
<hr><center>nginx/1.14.0</center>
</body>
</html>
';
} else {
	include "ppp/function.php";
	if ($prof == "all") {
		$getsecret = $API->comm("/ppp/secret/print");
        if (!is_array($getsecret)) $getsecret = [];
		$TotalReg = count($getsecret);
		$countsecret = $TotalReg;
	} elseif ($prof == "") {
		$getsecret = $API->comm("/ppp/secret/print");
        if (!is_array($getsecret)) $getsecret = [];
		$TotalReg = count($getsecret);
		$countsecret = $TotalReg;
	} elseif ($prof != "all") {
		$getsecret = $API->comm("/ppp/secret/print", array(
			"?profile" => "$prof",
		));
        if (!is_array($getsecret)) $getsecret = [];
		$TotalReg = count($getsecret);
		$countsecret = $TotalReg;
	}
	if ($comm != "") {
		$getsecret = $API->comm("/ppp/secret/print", array(
			"?comment" => "$comm",
			//"?uptime" => "00:00:00"
		));
        if (!is_array($getsecret)) $getsecret = [];
		$TotalReg = count($getsecret);
		$countsecret = $TotalReg;
	}
	$getprofile = $API->comm("/ppp/profile/print");
	$TotalReg2 = count($getprofile);


	// // get user secret
	// $getsecret = $API->comm("/ppp/secret/print");
	// $TotalReg = count($getsecret);
	// // count user secret
	// $countsecret = $API->comm("/ppp/secret/print", array(
	// 	"count-only" => "",
	// ));
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
				<h3><i class=" fa fa-pie-chart"></i> PPP Secrets 
					&nbsp; | &nbsp; <a href="./?ppp=addsecret&session=<?= $session; ?>" title="Add Secrets"><i class="fa fa-user-plus"></i> Add PPP Secret </a>
				</h3>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<div class="row">
					<div class="col-6 pd-t-5 pd-b-5">
						<div class="input-group">
							<div class="input-group-4 col-box-4">
								<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder="<?= $_search ?>">
							</div>
							<div class="input-group-4 col-box-4" style="margin-left:10px;">
								<select style="padding:5px;" class="group-item group-item-m" onchange="location = this.value; loader()" title="Filter by Profile">
									<option><?= $_profile ?> </option>
									<option value="./?ppp=secrets&profile=all&session=<?= $session; ?>">
										<?= $_show_all ?></option>
									<?php
									for ($i = 0; $i < $TotalReg2; $i++) {
										$profile = $getprofile[$i];
										if ($getprofile[$i]['default']<>'true') {
											echo "<option value='./?ppp=secrets&profile=" . $profile['name'] . "&session=" . $session . "'>" . $profile['name'] . "</option>";
										}
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="overflow box-bordered" style="max-height: 75vh">
					<table id="dataTable" class="table table-bordered table-hover text-nowrap">
						<thead>
							<tr>
								<th style="min-width:50px;" class="text-center">
									<?php
									if ($countsecret < 2) {
										echo "$countsecret item  ";
									} elseif ($countsecret > 1) {
										echo "$countsecret items   ";
									}
									?></th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> <?= $_name ?> </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Password </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Service </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Last Caller Id </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Profile </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Loc Address </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Rem Address </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Last Logged Out </th>
								<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Comment </th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php

								// Optimize: Fetch all netwatch hosts ONCE
								$all_netwatch_raw = $API->comm("/tool/netwatch/print");
								$netwatch_hosts = [];
								if (is_array($all_netwatch_raw)) {
									foreach ($all_netwatch_raw as $nw) {
										if (isset($nw['host'])) {
											$netwatch_hosts[$nw['host']] = true;
										}
									}
								}

								for ($i = 0; $i < $TotalReg; $i++) {
								?>
									<td style='text-align:center;'><i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to delete secret (<?= $getsecret[$i]['name']; ?>)?')){loadpage('./?remove-pppsecret=<?= $getsecret[$i]['.id']; ?>&rempname=<?= $getsecret[$i]['name']; ?>&session=<?= $session; ?>')}else{}" title='Delete/Hapus User  <?= $getsecret[$i]['name']; ?>'></i>
										<?php
										
										$toaddr=$getsecret[$i]['remote-address'];
										$is_netwatched = isset($netwatch_hosts[$toaddr]);

										if (!$is_netwatched) {
											echo "&nbsp&nbsp&nbsp<a title='Netwatch User ".$getsecret[$i]['name']."\nIp ".$toaddr."\nKlik untuk melakukan Netwatch' href='./?interface=add-netwatch&iphost=".$toaddr."&flag=D&session=".$session."'><i class='fa fa-cubes text-warning pointer' aria-hidden='true'></i></a>";
										}else{
											echo "&nbsp&nbsp&nbsp<a href='./?interface=netwatch&session=".$session."'><span style='cursor:pointer;' title='Ip ".$toaddr."\nSudah di Netwatch.'><i class='fa fa-cubes text-grren pointer' aria-hidden='true'></i></span>";
										}

										if ($getsecret[$i]['disabled'] == "true") {
											$sprocess = "'./?enable-pppsecret=" .$getsecret[$i]['.id']. "&session=" . $session . "'";
											echo '&nbsp&nbsp&nbsp<span class="text-warning pointer" title="Enable User ' .$getsecret[$i]['name']. '"  onclick="loadpage(' . $sprocess . ')"><i class="fa fa-lock "></i></span>';
										} else {
											$sprocess = "'./?disable-pppsecret=" .$getsecret[$i]['.id']. "&session=" . $session . "'";
											echo '&nbsp&nbsp&nbsp<span class="pointer" title="Disable User ' .$getsecret[$i]['name']. '"  onclick="loadpage(' . $sprocess . ')"><i class="fa fa-unlock "></i></span>';
										}
										echo "&nbsp&nbsp&nbsp <a title='Edit User secret ".$getsecret[$i]['name']."' href='./?secret=".$getsecret[$i]['.id']."&schedulerbyname=".$getsecret[$i]['name']."&session=".$session."'><i class='fa fa-edit'></i></a>";
										echo "&nbsp&nbsp&nbsp <a title='Billing User Secret\nusername ".$getsecret[$i]['name']."\nprice [ ".rupiah(caridtharga1($getsecret[$i]['profile']))." ]' href='./?ppp=billing&idsecret=".$getsecret[$i]['.id']."&session=" . $session . "'><i class='fa fa-money' aria-hidden='true'></i></a>&nbsp";
										?>
									</td>
								<?php
									echo "<td>".$getsecret[$i]['name']."</td>";
									echo "<td>".$getsecret[$i]['password']."</td>";
									echo "<td>".$getsecret[$i]['service']."</td>";
									echo "<td>".$getsecret[$i]['last-caller-id']."</td>";
									$mprofile=$getsecret[$i]['profile'];
									if (strpos($getsecret[$i]['profile'] , '*' )) {
										$mprofile="unknown";
									}
									echo "<td>".$mprofile."</td>";
									echo "<td>".$getsecret[$i]['local-address']."</td>";
									echo "<td>".$getsecret[$i]['remote-address']."</td>";
									echo "<td>".$getsecret[$i]['last-logged-out']."</td>";
									echo "<td>" . (isset($getsecret[$i]['comment']) ? $getsecret[$i]['comment'] : caridtpelanggan($getsecret[$i]['name'],9)) . "</td>";
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