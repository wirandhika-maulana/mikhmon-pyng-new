<?php
// hide all error
//error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
}

if (isset($_POST['save'])) {
	$iphost=$_POST['iphost'];
	$jam=$_POST['jam'];
	$menit=$_POST['menit'];
	$detik=$_POST['detik'];
	$timeout=$_POST['timeout'];
	$comm=$_POST['comm'];
	$minterval=$jam.":".$menit.":".$detik;
	$idnetw=$_POST['idnetw'];
	if (strlen($_POST['iphost'])<7) {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,...\nFormat Ip Belum Benar......."); } 
		</script>';
	}elseif ($_POST['iphost']=='0.0.0.0') {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,...\nFormat IP host belum benar......."); } 
		</script>';
	}elseif (count(explode(".",trim($_POST['iphost'])))<>4) {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,...\nFormat host belum benar......."); } 
		</script>';
	}elseif ($_POST['jam']=="0" and $_POST['menit']=="0" and $_POST['detik']=="0" ) {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,...\nFormat interval belum benar......."); } 
		</script>';
	}elseif (!preg_match('/^[0-9]+$/', $_POST['timeout'])) {
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,...\nFormat timeout belum benar.......\n[ angka saja ] [ milisecond ]"); } 
		</script>';
	}else{
		if ($timeout<1) {
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,...\nTimeout tidak valid......."); } 
			</script>';
		}else{
			$timeout=$timeout/1000;
			if ($_GET['flag']=="C") {
				$add_netwatch = $API->comm("/tool/netwatch/set", array(
					".id"		=> "$idnetw",
					"host"		=> "$iphost",
					"interval"	=> "$minterval",
					"timeout"	=> "$timeout",
					"comment"	=> "$comm",
				));
			}else{
				$add_netwatch = $API->comm("/tool/netwatch/add", [
					"host"		=> $_POST['iphost'],
					"interval" 	=> $minterval,
					"timeout" 	=> "$timeout",
					"comment" 	=> $_POST['comm'],
				]);
			}
			$cek = json_encode($add_netwatch);
			if (strpos(strtolower($cek), '!trap')) {
				echo '<script type="text/javascript">
				window.onload = function () { alert("INFO,...\nGAGAL Proses add Netwatch......."); } 
				</script>';
			}
			echo "<script>window.location='./?interface=netwatch&session=".$session."'</script>";
		}
	}
}
if ($_GET['flag']=="A") {
	$comm		= "Add Netwatch, Start in ".date('M/d/Y H:i:s');
	$timeout	= 0;
	$linkback="./?interface=netwatch&session=".$session;
}elseif ($_GET['flag']=="B") {
	$getuser 	= $API->comm("/ip/hotspot/ip-binding/print",["?to-address" => "$iphost"]);
	$comm		= $getuser[0]['comment'].", Netwatch, Start in ".date('M/d/Y H:i:s');
	$timeout	= 0;
	$linkback="./?hotspot=ipbinding&session=".$session;
}elseif ($_GET['flag']=="C") {
	$getuser 	= $API->comm("/tool/netwatch/print",["?host" => "$iphost"]);
	$idnetw		= $getuser[0]['.id'];
	$comm		= $getuser[0]['comment'];
	$timeout	= str_replace("m","",str_replace("s","",$getuser[0]['timeout']));
	$linkback="./?interface=netwatch&session=".$session;
}elseif ($_GET['flag']=="D") {
	include "ppp/function.php";
	$getuser 	= $API->comm("/ppp/secret/print",["?remote-address" => "$iphost"]);
	$comm		= "User secret [".$getuser[0]['name']."], Netwatch Start in ".date('M/d/Y H:i:s');
	$timeout	= 0;
	$linkback="./?ppp=secrets&session=".$session;
}elseif ($_GET['flag']=="E") {
	$getuser 	= $API->comm("/ip/hotspot/host/print",["?to-address" => "$iphost"]);
	$comm		= $getuser[0]['comment'];
	$timeout	= 0;
	$linkback="./?hotspot=hosts&session=".$session;
}


?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
				<h3> <i class=" fa fa-pie-chart"></i> Input Netwatch Monitoring. &nbsp &nbsp &nbsp 
				 | &nbsp<a href="./?interface=netwatch&session=<?= $session; ?>" title="Input Netwatch"><i class="fa fa-sliders"></i> List </a>
				</h3>
			</div>
		</div>
		<div class="card-body">
			<form autocomplete="off" method="post" action="">
				<div>
					<a class="btn bg-warning" style="color:black;font-weight:bold;" href="<?=$linkback?>" <i class="fa fa-close btn-mrg"></i> <?= $_close ?></a>
					
					<button style="color:black;font-weight:bold;" type="submit" name="save" class="btn bg-primary btn-mrg"><i class="fa fa-save btn-mrg"></i> <?= $_save ?></button>
				</div>
				<div class="w-4" style="margin-top:50px;">
					<div class="overflow box-bordered mr-t-10" style="max-height: 75vh">  	   
						<table id="dataTable" class="table table-bordered table-hover text-nowrap"> 
							<tr>
								<td class="text-middle">IP HOST</td>
								<td>
								<input type="hidden" name="idnetw" value="<?=$idnetw?>">
								<?php
								if ($_GET['flag']=='A') {
									echo "<input class='form-control'  type='text' name='iphost' value='".$iphost."' required='1' autofocus>";
								}else{
									echo "<input class='form-control'  type='text' name='iphost' value='".$iphost."' readonly>";
								}
								?>
								</td>
							</tr>
							<tr>
								<td class="text-middle">Interval</td>
								<td>
								<div class="input-group-3 col-box-6" style="margin-right:10px;">
									<select style="padding:5px;" class="group-item group-item-m"  name ="jam" title="Jam" >
										<option value="0">Jam</option>
										<?php
										for ($jam=0;$jam<24;$jam++) {
											echo "<option>".$jam."</option>";
										}
										?>
									</select>
								</div>
								<div class="input-group-3 col-box-6" style="margin-right:10px;">
									<select style="padding:5px;" class="group-item group-item-m"  name ="menit" title="Menit">
										<option value="0">Menit</option>
										<?php
										for ($menit=0;$menit<60;$menit++) {
											echo "<option>".$menit."</option>";
										}
										?>
									</select>
								</div>
								<div class="input-group-3 col-box-6" style="margin-right:10px;">
									<select style="padding:5px;" class="group-item group-item-m"  name ="detik" title="Detik">
										<option value="0">Detik</option>
										<?php
										for ($detik=0;$detik<60;$detik++) {
											echo "<option>".$detik."</option>";
										}
										?>
									</select>
								</div>
							</div>
						</tr>
						<tr>
							<td class="text-middle">Timeout</td>
							<td>
								<div class="input-group-3 col-box-6" style="margin-right:10px;">
									<input style="text-align:right;font-weight:bold;background-color:silver;color:black;" class="form-control" type="text" name="timeout" value="<?=$timeout?>" required="1">
								</div>
								<div class="input-group-3 col-box-6" style="margin-right:10px;">
									ms
								</div>
							</td>
						</tr>
						<tr>
							<td valign="top">Comment</td>
							<td><?php
								if ($_GET['flag']=='A' || $_GET['flag']=='C') {
									echo "<textarea style='height:100px;max-width:400px;font-weight:bold;background-color:silver;color:black;' class='form-control' type='text' name='comm' >".$comm."</textarea>";
								}else{
									echo "<textarea style='height:100px;max-width:400px;font-weight:bold;background-color:silver;color:black;' class='form-control' type='text' name='comm' readonly>".$comm."</textarea>";
								}
							?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
