<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
}

if (isset($_POST['save'])) {
	if ($_POST['type']=='Pilih') {
		$text	= "Pesan Kesalahan :<br>Tentukan Type Ip Binding,<br>";
		$kirim="Gagal|".$text."|";
		echo "<script>window.location='./?info=".$kirim."&session=".$session."'</script>";
	}else{
//		$removeuser	= $API->comm("/ip/hotspot/active/remove",array("?.id" => "$idbinding"));
//		$cek	= json_encode($addipbind);
		$cek	= "ok";
		if (strpos(strtolower($cek), '!trap')) {
			$text	= "Pesan Kesalahan :<br>".str_replace(":","\n",explode('"',$cek)[5])."<br>";
			$kirim="Gagal|Gagal remove ".$_POST['user']."|".$text;
			echo "<script>window.location='./?info=".$kirim."&session=".$session."'</script>";
		}else{
			$addipbind 	= $API->comm("/ip/hotspot/ip-binding/add",array(
				"mac-address" 	=> $_POST['mac'],
				"address" 		=> $_POST['address'],
				"to-address" 	=> $_POST['toaddress'],
				"server" 		=> $_POST['server'],
				"type" 			=> $_POST['type'],
				"comment"		=> $_POST['comm'],
			));
			$cek	= json_encode($addipbind);
			if (strpos(strtolower($cek), '!trap')) {
				$text	= "Pesan Kesalahan :<br>".str_replace(":","\n",explode('"',$cek)[5])."<br>";
				$kirim="Gagal|Ip Binding  ".$_POST['user']."|".$text;
			}else{
				$kirim="Berhasil|Ip Binding  ".$_POST['user']."|Mac Address : ".$_POST['mac']."<br>Address : ".$_POST['address']."<br>To Address : ".$_POST['toaddress']."<br>Server : ".$_POST['server']."<br>Type : ".$_POST['type'];
			}
			echo "<script>window.location='./?info=".$kirim."&session=".$session."'</script>";
		}
	}
}

if ($_GET['flag']=='A') {
	$getuser 	= $API->comm("/ip/hotspot/active/print",array("?.id" => "$idbinding"));
	$macuser	= $getuser[0]['mac-address'];
	$comm		= "Binding user ".$getuser[0]['user']." Start in ".date('M/d/Y H:i:s');
	$gethost 	= $API->comm("/ip/hotspot/host/print",array("?mac-address" => "$macuser"));
}else{
	
}

?>
<div class="row">
    <div class="col-6">
		<div class="card">
			<div class="card-header">
				<h3><i class=" fa fa-address-book bg-red"></i> &nbsp&nbsp<?= $_ip_bindings ?> <span class="text-yellow"><?=$getuser[0]['server']."-".$getuser[0]['user']?></span>
				</h3>
			</div>
			<div class="card-body">	   
				<form autocomplete="off" method="post" action="">
					<a style="color:black;" class="btn bg-green" href="./?hotspot=active&profile=all&session=<?= $session; ?>"> <i class="fa fa-close btn-mrg"></i> <?= $_close ?></a>
					<button style="color:black;"  type="submit" name="save" class="btn bg-primary btn-mrg"><i class="fa fa-save btn-mrg"></i> <?= $_save ?></button>
					<table class="table" style="margin-top:50px;">
						<tr>
							<td class="align-middle">User Name</td>
							<td>
								<input class="form-control" type="text" autocomplete="off" name="muser" value="<?=$getuser[0]['user']?>" readonly>
							</td>
						</tr>
						<tr>
							<td class="align-middle">MAC Address</td>
							<td>
								<input class="form-control" type="hidden" autocomplete="off" name="user" value="<?=$getuser[0]['server']."-".$getuser[0]['user']?>">
								<input class="form-control" type="text" autocomplete="off" name="mac" value="<?=$getuser[0]['mac-address']?>" readonly>
							</td>
						</tr>
						<tr>
							<td class="align-middle">Address</td>
							<td><input class="form-control" type="text" autocomplete="off" name="address" value="<?=$getuser[0]['address']?>" readonly></td>
						</tr>
						<tr>
							<td class="align-middle">To Address</td>
							<td><input class="form-control" type="text" autocomplete="off" name="toaddress" value="<?=$gethost[0]['to-address']?>" readonly></td>
						</tr>
						<tr>
							<td class="align-middle">Server</td>
							<td><input class="form-control" type="text" autocomplete="off" name="server" value="<?=$getuser[0]['server']?>" readonly></td>
						</tr>
						<tr>
							<td class="align-middle">Type</td>
							<td>
								<select class="form-control" name="type">
									<option>blocked</option>
									<option>regular</option>
									<option>bypassed</option>
									<option selected>Pilih</option>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top">Comment</td>
							<td><textarea  style="max-height:100px;max-width:400px;" class="form-control" type="text" autocomplete="off" name="comm" readonly><?=$comm?></textarea></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>
