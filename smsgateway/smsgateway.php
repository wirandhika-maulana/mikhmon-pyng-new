<?php
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
}
include "function.php";

$fdtprofil="smsgateway/Api/telerivet.txt";
$cek="required";
if (isset($_POST['send'])) {
	if (empty($_POST['nomor'])) {
		$_GET['nomor']="";
		$cek="readonly";
		echo '<script type="text/javascript">
		window.onload = function () { alert("INFO,\nNomor tujuan harus diisi."); } 
		</script>';
	}else{
		if (substr(ceknomor($_POST['nomor']),0,2)=="62") {
			$hasil=kirimsms($_POST['nomor'],$_POST['pesan']);
			$_GET['nomor']="";
			$pesan=$hasil;
			$cek="readonly";
		}else{
			echo '<script type="text/javascript">
			window.onload = function () { alert("INFO,\nFormat Nomor tujuan belum benar."); } 
			</script>';
		}
	}
}
if (isset($_POST['save'])) {
	$writecev=$_POST['p_id'].'|'.$_POST['api_k']."|".$_POST['f_core']."|\n";
	$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $fdtprofil);
	fwrite($handle, $writecev);
	fclose($handle);
	echo '<script type="text/javascript">
	window.onload = function () { alert("INFO,\nData api, berhasil disimpan."); } 
	</script>';
	
}
if (file_exists($fdtprofil)) {
	$isi=explode("|",file_get_contents($fdtprofil));
	$p_id =$isi[0];
	$api_k=$isi[1];
	$f_core=$isi[2];
}else{
	$p_id ="";
	$api_k="";
	$f_core="";
}
$style1="style='font-family:times;font-size:16px;font-weight:bold;'";
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
                <h3><i class=" fa fa-envelope-o"></i> SMS Gateway Telerivet.com
                    <?php if ($page=='') {
					echo '<a href="./admin.php?id=websms&page=update" style="cursor: pointer;" title="Ambil data server"> &nbsp&nbsp || &nbsp&nbsp Reload..&nbsp&nbsp <i class="fa fa-refresh"></i></a>';
					}?>	
					<h4 style="float:right;font-family:times;font-size:18px;margin-right:20px;">
						<a href="./admin.php?id=websms&page=" style="margin-right:30px;"> <i class="fa fa-sun-o" aria-hidden="true"></i> Core </a>
						<a href="./admin.php?id=websms&page=history" style="margin-right:30px;"> <i class="fa fa-list" aria-hidden="true"></i> History </a>
						<a href="./admin.php?id=websms&page=send&nomor=" style="margin-right:30px;"> <i class="fa fa-envelope-o" aria-hidden="true"></i> Send </a>
						<a href="./admin.php?id=websms&page=setapi" > <i class="fa fa-code" aria-hidden="true"></i> Api </a>
						</h4>
						<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h4>
					</h3>
			</div>
			<form autocomplete="off" method="post" action="">
			<?php
			if ($page=='setapi') {
				echo '
				<div class="card-body">
                    <div>
                        <button type="submit" name="save" class="btn bg-primary btn-mrg" style="color:black;font-weight:bold;">
						<i class="fa fa-save btn-mrg"></i> &nbsp&nbsp SAVE </button>
                    </div>
				
					<div style="margin-top:20px;">
						<h3 style="font-size:18px;font-weight:bold;"> Setting Api. </h3>
						<table class="table" style="width:600px;">
							<tr><td '.$style1.'> Project Id </td><td> : </td><td> <div class=w-6><input  '.$style1.' class="form-control" name="p_id" value="'.$p_id.'" required></div> </td></tr>
							<tr><td '.$style1.'> Api Key </td><td> : </td><td><input '.$style1.' class="form-control" name="api_k" value="'.$api_k.'" required></td></tr>
							<tr><td '.$style1.'> File Core </td><td> : </td><td><input '.$style1.' class="form-control" name="f_core" value="'.$f_core.'" required></td></tr>
							<tr><td colspan="3">&nbsp</td></tr>
							<tr><td '.$style1.'  valign="top"> Info </td><td valign="top"> : </td><td '.$style1.'>Untuk mendapatkan akses, silahkan untuk ke tkp <a href="https://telerivet.com" target="_nlankNew">Telerivet.com</a></td></tr>
						</table>
					</div>
				</div>'; 
			}elseif ($page=='update') {
				bcore("update");
			}elseif ($page=='send') {				
				echo '
				<div class="card-body">
                    <div>
                        <button type="submit" name="send" class="btn bg-primary btn-mrg" style="color:black;font-weight:bold;">
						<i class="fa fa-save btn-mrg"></i> &nbsp&nbsp SEND </button>
                    </div>
				
					<div style="margin-top:20px;">
						<h3 style="font-size:18px;font-weight:bold;"> SMS SEND. </h3>
						<table class="table" style="width:600px;">
							<tr><td '.$style1.'> Nomor </td><td> : </td><td> <input  '.$style1.' name="nomor" value="'.$_GET['nomor'].'" style="width:150px;padding:5px;border:1px solid green;border-radius:3px;font-weight:bold;font-size:16px;" required> </td></tr>
							<tr><td '.$style1.' valign="top"> Pesan </td><td '.$style1.' valign="top"> : </td><td><textarea class="form-control" name="pesan" style="height:100px;" '.$cek.' >'.$pesan.'</textarea></td></tr>
						</table>
					</div>
				</div>
				'; 
			}elseif ($page=='history') {				
				echo '
				<div class="card-body">
					<div class="w-4" style="margin-top:10px;">
						<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder='.$_search.'
					</div>
				</div>
				<br>
				<div class="card-body">
					<h3>Total : '.bcore(hitung).' Pesan.</h3>
					<div class="overflow box-bordered" style="max-height: 65vh">
						<table id="dataTable" class="table table-bordered table-hover text-nowrap">
							<thead>
								<tr>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> No. </th>
									<th style="width:12%;" class="pointer" title="Click to sort"> Tanggal <i class="fa fa-sort"></i></th>
									<th style="width:12%;" class="pointer" title="Click to sort"> Nomor <i class="fa fa-sort"></i></th>
									<th class="pointer" title="Click to sort"> Pesan <i class="fa fa-sort"></i></th>
								</tr>
							</thead>
							<tbody>
								'.bcore("list").'
							</tbody>
						</table>
					</div>
				</div>
				';
			}else{
				echo '
				<br>
				<div class="card-header">
					<h3>Total : '.bcore(hitung).' Pesan.</h3>
				</div>		
				<div class="raw">
					<div class="card-body">
						<div class="overflow box-bordered" style="max-height: 65vh">
							<table id="dataTable" class="table table-bordered table-hover text-nowrap">
								<thead>
									<tr>
										<th align="right"> No. </th>
										<th> Tanggal </th>
										<th> Nomor </th>
										<th> Pesan </th>
									</tr>
								</thead>
								<tbody>
									'.bcore("list").'
								</tbody>
							</table>
						</div>
					</div>
				</div>
				';
			}
			?>
			</form>
		</div>
	</div>
</div>
<?php
?>