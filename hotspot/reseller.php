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

if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
// time zone
date_default_timezone_set($_SESSION['timezone']);

	// Get reseller list
	$getreseller = $API->comm("/ip/hotspot/user/profile/print");
	
	// For simple implementation, we'll use local storage
	// Create a simple JSON file for reseller management
	$resellerFile = './voucher/reseller.json';
	
	// Initialize reseller data
	if (!file_exists($resellerFile)) {
		file_put_contents($resellerFile, json_encode(array()));
	}
	
	$resellerData = json_decode(file_get_contents($resellerFile), true);
	if (!is_array($resellerData)) {
		$resellerData = array();
	}

	// Handle add reseller
	if (isset($_POST['addreseller'])) {
		$resellername = $_POST['resellername'];
		$resellerprefix = $_POST['resellerprefix'];
		
		$newReseller = array(
			'id' => time(),
			'name' => $resellername,
			'prefix' => $resellerprefix,
			'created' => date('Y-m-d H:i:s')
		);
		
		array_push($resellerData, $newReseller);
		file_put_contents($resellerFile, json_encode($resellerData, JSON_PRETTY_PRINT));
		echo "<script>window.location='./?hotspot=reseller&session=" . $session . "'</script>";
	}

	// Handle delete reseller
	if (isset($_GET['delete-reseller']) && $_GET['delete-reseller'] != "") {
		$deleteId = $_GET['delete-reseller'];
		$resellerData = array_filter($resellerData, function($item) use ($deleteId) {
			return $item['id'] != $deleteId;
		});
		$resellerData = array_values($resellerData);
		file_put_contents($resellerFile, json_encode($resellerData, JSON_PRETTY_PRINT));
		echo "<script>window.location='./?hotspot=reseller&session=" . $session . "'</script>";
	}

	// Handle edit reseller
	if (isset($_POST['editreseller'])) {
		$editId = $_POST['editid'];
		$resellername = $_POST['resellername'];
		$resellerprefix = $_POST['resellerprefix'];
		
		foreach ($resellerData as &$reseller) {
			if ($reseller['id'] == $editId) {
				$reseller['name'] = $resellername;
				$reseller['prefix'] = $resellerprefix;
				break;
			}
		}
		file_put_contents($resellerFile, json_encode($resellerData, JSON_PRETTY_PRINT));
		echo "<script>window.location='./?hotspot=reseller&session=" . $session . "'</script>";
	}

	// Get edit reseller data
	$editReseller = null;
	if (isset($_GET['edit-reseller']) && $_GET['edit-reseller'] != "") {
		$editId = $_GET['edit-reseller'];
		foreach ($resellerData as $reseller) {
			if ($reseller['id'] == $editId) {
				$editReseller = $reseller;
				break;
			}
		}
	}

?>
<div class="row">
	<div class="col-12">
		<div class="card box-bordered">
			<div class="card-header">
				<h3><i class="fa fa-store"></i> Reseller List</h3>
			</div>
			<div class="card-body">
				<button class="btn btn-lg" style="background-color: #28a745; color: white; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onclick="toggleForm();" onmouseover="this.style.backgroundColor='#218838'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';" title="Add New Reseller">
					<i class="fa fa-plus"></i> Add Reseller
				</button>
				<hr>
				
				<!-- Add Form -->
				<div id="addForm" style="display:none; margin-bottom:20px;">
					<div class="card" style="border: 2px solid #28a745; border-radius: 10px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15); background-color: #ffffff;">
						<div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 8px 8px 0 0; padding: 15px;">
							<h5 style="margin: 0; font-weight: 600;"><i class="fa fa-plus-circle"></i> Add New Reseller</h5>
						</div>
						<div class="card-body" style="padding: 20px; background-color: #ffffff;">
							<form method="post" action="">
								<div style="margin-bottom: 15px;">
									<label style="display: block; margin-bottom: 8px; color: #000000; font-weight: 700; font-size: 14px;">Reseller Name</label>
									<input type="text" class="form-control" name="resellername" required="1" placeholder="e.g., Reseller A" style="border-radius: 6px; border: 2px solid #d0d0d0; padding: 10px 12px; font-size: 14px; color: #000000; background-color: #f8f9fa; transition: all 0.3s ease;" onfocus="this.style.borderColor='#28a745'; this.style.boxShadow='0 0 8px rgba(40, 167, 69, 0.3)'; this.style.backgroundColor='#ffffff';" onblur="this.style.borderColor='#d0d0d0'; this.style.boxShadow='none'; this.style.backgroundColor='#f8f9fa';">
								</div>
								<div style="margin-bottom: 15px;">
									<label style="display: block; margin-bottom: 8px; color: #000000; font-weight: 700; font-size: 14px;">Prefix <span style="color: #dc3545; font-size: 12px;">(Max 6 characters)</span></label>
									<input type="text" class="form-control" name="resellerprefix" required="1" maxlength="6" placeholder="e.g., RSL" title="Max 6 characters" style="border-radius: 6px; border: 2px solid #d0d0d0; padding: 10px 12px; font-size: 14px; color: #000000; background-color: #f8f9fa; text-transform: uppercase; transition: all 0.3s ease;" onfocus="this.style.borderColor='#28a745'; this.style.boxShadow='0 0 8px rgba(40, 167, 69, 0.3)'; this.style.backgroundColor='#ffffff';" onblur="this.style.borderColor='#d0d0d0'; this.style.boxShadow='none'; this.style.backgroundColor='#f8f9fa';">
								</div>
								<div style="display: flex; gap: 10px; padding-top: 10px; border-top: 2px solid #e0e0e0;">
									<button type="submit" name="addreseller" class="btn btn-lg" style="background-color: #28a745; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#218838'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
										<i class="fa fa-save"></i> Save
									</button>
									<button type="button" class="btn btn-lg" style="background-color: #ffc107; color: #000000; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onclick="toggleForm();" onmouseover="this.style.backgroundColor='#ffb300'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
										<i class="fa fa-close"></i> Cancel
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!-- Edit Form -->
				<div id="editForm" style="display:<?= ($editReseller !== null) ? 'block' : 'none'; ?>; margin-bottom:20px;">
					<div class="card" style="border: 2px solid #007bff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15); background-color: #ffffff;">
						<div class="card-header" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border-radius: 8px 8px 0 0; padding: 15px;">
							<h5 style="margin: 0; font-weight: 600;"><i class="fa fa-edit"></i> Edit Reseller</h5>
						</div>
						<div class="card-body" style="padding: 20px; background-color: #ffffff;">
							<form method="post" action="">
								<input type="hidden" name="editid" value="<?= ($editReseller !== null) ? $editReseller['id'] : ''; ?>">
								<div style="margin-bottom: 15px;">
									<label style="display: block; margin-bottom: 8px; color: #000000; font-weight: 700; font-size: 14px;">Reseller Name</label>
									<input type="text" class="form-control" name="resellername" required="1" placeholder="e.g., Reseller A" value="<?= ($editReseller !== null) ? htmlspecialchars($editReseller['name']) : ''; ?>" style="border-radius: 6px; border: 2px solid #d0d0d0; padding: 10px 12px; font-size: 14px; color: #000000; background-color: #f8f9fa; transition: all 0.3s ease;" onfocus="this.style.borderColor='#007bff'; this.style.boxShadow='0 0 8px rgba(0, 123, 255, 0.3)'; this.style.backgroundColor='#ffffff';" onblur="this.style.borderColor='#d0d0d0'; this.style.boxShadow='none'; this.style.backgroundColor='#f8f9fa';">
								</div>
								<div style="margin-bottom: 15px;">
									<label style="display: block; margin-bottom: 8px; color: #000000; font-weight: 700; font-size: 14px;">Prefix <span style="color: #dc3545; font-size: 12px;">(Max 6 characters)</span></label>
									<input type="text" class="form-control" name="resellerprefix" required="1" maxlength="6" placeholder="e.g., RSL" title="Max 6 characters" value="<?= ($editReseller !== null) ? htmlspecialchars($editReseller['prefix']) : ''; ?>" style="border-radius: 6px; border: 2px solid #d0d0d0; padding: 10px 12px; font-size: 14px; color: #000000; background-color: #f8f9fa; text-transform: uppercase; transition: all 0.3s ease;" onfocus="this.style.borderColor='#007bff'; this.style.boxShadow='0 0 8px rgba(0, 123, 255, 0.3)'; this.style.backgroundColor='#ffffff';" onblur="this.style.borderColor='#d0d0d0'; this.style.boxShadow='none'; this.style.backgroundColor='#f8f9fa';">
								</div>
								<div style="display: flex; gap: 10px; padding-top: 10px; border-top: 2px solid #e0e0e0;">
									<button type="submit" name="editreseller" class="btn btn-lg" style="background-color: #007bff; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
										<i class="fa fa-save"></i> Save Changes
									</button>
									<button type="button" class="btn btn-lg" style="background-color: #6c757d; color: white; border: none; border-radius: 6px; padding: 10px 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" onclick="closeEditForm();" onmouseover="this.style.backgroundColor='#5a6268'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#6c757d'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
										<i class="fa fa-close"></i> Cancel
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>

				<!-- Reseller List Table -->
				<table class="table table-bordered table-hover" style="margin-top: 20px; border-radius: 8px; overflow: hidden;">
					<thead style="background: linear-gradient(135deg, #007bff, #0056b3); color: white;">
						<tr style="border-bottom: 3px solid #003d82;">
							<th width="50" style="font-weight: 700; letter-spacing: 0.5px; padding: 12px; font-size: 13px; text-transform: uppercase;">No</th>
							<th style="font-weight: 700; letter-spacing: 0.5px; padding: 12px; font-size: 13px; text-transform: uppercase;">Reseller Name</th>
							<th width="120" style="font-weight: 700; letter-spacing: 0.5px; padding: 12px; font-size: 13px; text-transform: uppercase;">Prefix</th>
							<th style="font-weight: 700; letter-spacing: 0.5px; padding: 12px; font-size: 13px; text-transform: uppercase;">Created Date</th>
							<th width="100" style="font-weight: 700; letter-spacing: 0.5px; padding: 12px; font-size: 13px; text-transform: uppercase;">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (count($resellerData) > 0) {
							$no = 1;
							foreach ($resellerData as $reseller) {
								$rowBg = ($no % 2 == 0) ? '#ffffff' : '#f8f9fa';
								echo "<tr style='background-color: " . $rowBg . "; border-bottom: 1px solid #e0e0e0; transition: all 0.3s ease;' onmouseover=\"this.style.backgroundColor='#e7f3ff'; this.style.boxShadow='inset 0 0 8px rgba(0,123,255,0.1)';\" onmouseout=\"this.style.backgroundColor='" . $rowBg . "'; this.style.boxShadow='none';\">";
								echo "<td class='text-center' style='font-weight: 700; color: #0056b3; padding: 12px; font-size: 14px;'>" . $no . "</td>";
								echo "<td style='color: #333; font-weight: 600; padding: 12px; font-size: 14px;'>" . htmlspecialchars($reseller['name']) . "</td>";
								echo "<td class='text-center' style='background-color: #e7f3ff; color: #0056b3; font-weight: 700; letter-spacing: 2px; padding: 12px; font-size: 16px; border: 2px solid #007bff; border-radius: 6px;'>" . htmlspecialchars($reseller['prefix']) . "</td>";
								echo "<td style='color: #666; font-size: 13px; padding: 12px;'>" . $reseller['created'] . "</td>";
								echo "<td class='text-center' style='padding: 12px; display: flex; gap: 6px; justify-content: center;'>";
								echo "<a href='./?hotspot=reseller&edit-reseller=" . $reseller['id'] . "&session=" . $session . "' class='btn btn-sm' style='background-color: #007bff; color: white; border: none; border-radius: 6px; padding: 8px 14px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,123,255,0.3); text-decoration: none;' onmouseover=\"this.style.backgroundColor='#0056b3'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 8px rgba(0,123,255,0.5)';\" onmouseout=\"this.style.backgroundColor='#007bff'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,123,255,0.3)';\">";
								echo "<i class='fa fa-edit'></i> Edit";
								echo "</a>";
								echo "<a href='./?hotspot=reseller&delete-reseller=" . $reseller['id'] . "&session=" . $session . "' class='btn btn-sm' style='background-color: #dc3545; color: white; border: none; border-radius: 6px; padding: 8px 14px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(220,53,69,0.3); text-decoration: none;' onclick=\"return confirm('Delete this reseller?');\" onmouseover=\"this.style.backgroundColor='#c82333'; this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 8px rgba(220,53,69,0.5)';\" onmouseout=\"this.style.backgroundColor='#dc3545'; this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(220,53,69,0.3)';\">";
								echo "<i class='fa fa-trash'></i> Delete";
								echo "</a>";
								echo "</td>";
								echo "</tr>";
								$no++;
							}
						} else {
							echo "<tr style='background-color: #f8f9fa; border-bottom: 1px solid #e0e0e0;'><td colspan='5' class='text-center' style='padding: 30px; color: #999; font-style: italic; font-size: 14px;'>No Reseller Data</td></tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
function toggleForm() {
	var form = document.getElementById('addForm');
	if (form.style.display === 'none') {
		form.style.opacity = '0';
		form.style.display = 'block';
		form.style.transition = 'opacity 0.3s ease';
		setTimeout(function() {
			form.style.opacity = '1';
		}, 10);
	} else {
		form.style.opacity = '0';
		form.style.transition = 'opacity 0.3s ease';
		setTimeout(function() {
			form.style.display = 'none';
		}, 300);
	}
}

function closeEditForm() {
	window.location = './?hotspot=reseller&session=<?= $session; ?>';
}
</script>

<?php
}
?>
