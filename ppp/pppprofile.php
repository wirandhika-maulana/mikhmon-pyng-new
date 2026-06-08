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
    header("Location:../admin.php?id=login");
} else {
	include "ppp/function.php";
	// get ppp profile
	$getprofile = $API->comm("/ppp/profile/print");
	$TotalReg = count($getprofile);
	// count ppp profile (no need for extra API call)
	$countprofile = $TotalReg;
}

?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
				<h3><i class=" fa fa-pie-chart"></i> PPP Profile
					&nbsp; | &nbsp; <a href="./?ppp=add-profile&session=<?= $session; ?>" title="Add Profile"><i class="fa fa-user-plus"></i> Add Profile </a>
				</h3>
			</div>
			<!-- /.card-header -->
			<div class="card-body">
				<!-- /.card-header -->
				<div class="card-body">
					<div class="input-group">
						<div class="input-group-3 col-box-3">
							<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder="<?= $_search ?>">
						</div>
					</div>
					<br>
					<div class="overflow box-bordered" style="max-height: 75vh">
						<table id="dataTable" class="table table-bordered table-hover text-nowrap">
							<thead>
								<tr>
									<th style="min-width:50px;" class="text-center">
										<?php
										if ($countprofile < 2) {
											echo "$countprofile item  ";
										} elseif ($countprofile > 1) {
											echo "$countprofile items   ";
										}
										?></th>
									<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i>&nbsp&nbsp&nbsp<?= $_name ?> </th>
									<th class="align-middle">Loc Address</th>
									<th class="align-middle">Rem Address</th>
									<th class="align-middle">Dns</th>
									<th class="align-middle">Bridge</th>
									<th class="align-middle">Rate Limit</th>
									<th class="align-middle">Only One</th>
									<th class="text-right">Harga / Bulan</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php
									for ($i = 0; $i < $TotalReg; $i++) {
										if ($getprofile[$i]['default']<>'true') {
									?>
											<td style='text-align:center;'><i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Are you sure to delete profile (<?= $getprofile[$i]['name']; ?>)?')){loadpage('./?remove-pprofile=<?= $getprofile[$i]['.id'] ?>&pname=<?= $getprofile[$i]['name'] ?>&session=<?= $session; ?>')}else{}" title='Delete/Hapus <?= $getprofile[$i]['name'] ?>'></i>
									<?php
											echo "&nbsp&nbsp&nbsp<a title='Edit User Profile ".$getprofile[$i]['name']."\nId ".$getprofile[$i]['.id']."' href='./?ppp=edit-profile&idpppp=".$getprofile[$i]['.id']."&session=" . $session . "'><i class='fa fa-edit'></i></a>";
											echo "&nbsp&nbsp&nbsp<a title='Add User Secret By Profile ".$getprofile[$i]['name']."' href='./?ppp=add-secret-by-profile&idprof=".$getprofile[$i]['.id']."&session=" . $session . "'><i class='fa fa-users' aria-hidden='true'></i></b>";
											echo "</td>";
											echo "<td>".$getprofile[$i]['name']."</td>";
											echo "<td>".$getprofile[$i]['local-address']."</td>";
											echo "<td>".$getprofile[$i]['remote-address']."</td>";
											echo "<td>".$getprofile[$i]['dns-server']."</td>";
											echo "<td>".$getprofile[$i]['bridge']."</td>";
											echo "<td>".$getprofile[$i]['rate-limit']."</td>";
											echo "<td>".$getprofile[$i]['only-one']."</td>";
											echo "<td align='right'>".rupiah(caridtharga($getprofile[$i]['.id']))." &nbsp</td>";
											echo "</tr>";
										}
									}
									?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

