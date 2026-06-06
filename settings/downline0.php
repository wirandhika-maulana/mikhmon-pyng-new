<?php
// hide all error
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$ftoken='webhook/webhookk.php';
	$idowner=explode('|',file_get_contents($ftoken))[0];
	$tbothot=explode('|',file_get_contents($ftoken))[3];

	if (strlen(ltrim($idowner))<8) {
	echo '
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body" style="margin-top:20px;">
					<div class="card-header">
						<h3 class="card-title text-center"> Id Telegram Owner, Belum Di Setting. Silahkan Klik <a href="?id=settapi"><br><u>Setting Id Telegram</u></a></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
	';
	return (0);
	}
	if (count(explode(":",$tbothot))<>2) {
	echo '
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body" style="margin-top:20px;">
					<div class="card-header">
						<h3 class="card-title text-center"> Token Untuk '.$ubothot.', Tidak Sesuai. Silahkan Klik <a href="?id=settapi"><br><u>Setting Id Telegram</u></a></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
	';
	return (0);
	}
	
	$func	="settings/function.php";
	$tombol	="save-add";
	$tombol1=" Save ";
	$judul	="New Member";
	$sts2=$sts3=$sts4=$sts5=$sts6=$sts7=$sts8="";
	$sts1=$sts3=$sts4=$sts5="required";
	if (file_exists($func)) {
		include $func;
		if (lihatowner()<>$idowner) {
			echo '
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body" style="margin-top:20px;">
							<div class="card-header">
								<h3 class="card-title text-center"> ID Telegram Owner Aplikasi Mikhmon ( '.$idowner.' ) dan Mikbotam ( '.lihatowner().' ) Tidak Sama.</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
			';
			return (0);
		} 
		$cdata=lihatuser($_GET['id_u']);
		$id_telegram=$cdata['id_user'];
		$id_whatsapp=$cdata['nomer_tlp'];
		$nama=$cdata['nama_seller'];
		$alamat=$cdata['keterangan'];
		$saldo=$cdata['saldo'];
		if ($_GET['pro']=='1') {
			$tombol="save-deposit";
			$tombol1=" Proses ";
			$judul	="Deposit Member";
			$sts1=$sts2=$sts3=$sts4="readonly";
		}elseif($_GET['pro']=='2') {
			$judul	="Edit Member";
			$tombol="save-edit";
			$tombol1=" Edit ";
			$sts1=$sts5="readonly";
			$sts2=$sts3=$sts4="required";
		}
		$dtdl 	= lihatdata();
		
	}else{
		$mket="File ".$func." Tidak Ada";
	}
	$status=$_GET['status'];
	if (isset($_POST['clear'])) {
		echo "<script>window.location='./admin.php?id=downline'</script>";
	}
	if (isset($_POST['save-edit'])) {
		$status=updateuser($_POST['id_telegram'], $_POST['id_whatsapp'], ucwords($_POST['nama']), ucwords($_POST['alamat']), ltrim($idowner));
		if (strtolower(explode(",",$status)[0])=="maaf") {
			$status=str_replace("\n","<br>",$status);
		}else{
			$status="[".sendMessage($_POST['id_telegram'], $status, $tbothot)."]";	
			$status=json_decode($status,true);
			if ($status[0]['ok']=="ok") {
				$status=str_replace("\n","<br>",$status[0]['result']['text'])."<br>Pesan berhasil dikirim ke Username :<br>".$status[0]['result']['chat']['username']."<br>[".$status[0]['result']['chat']['first_name']." / ".$status[0]['result']['chat']['last_name']."]";
			}else{
				$status="Pesan gagal terkirim.<br><b>".$status[0]['description']."</b>";
			}
		}
		echo "<script>window.location='./admin.php?id=downline&status=$status'</script>";
	}
	if (isset($_POST['save-deposit'])) {
		$status=topupresseller($_POST['id_telegram'], $_POST['nama'], $_POST['deposit'], $idowner);
		$status="[".sendMessage($_POST['id_telegram'], $status, $tbothot)."]";	
		$status=json_decode($status,true);
		if ($status[0]['ok']=="ok") {
			$status=str_replace("\n","<br>",$status[0]['result']['text'])."<br>Pesan berhasil dikirim ke Username :<br>".$status[0]['result']['chat']['username']."<br>[".$status[0]['result']['chat']['first_name']." / ".$status[0]['result']['chat']['last_name']."]";
		}else{
			$status="Pesan gagal terkirim.<br><b>".$status[0]['description']."</b>";
		}
		echo "<script>window.location='./admin.php?id=downline&status=$status'</script>";
	}
	if (isset($_POST['save-add'])) {
		if (has($_POST['id_telegram'])==!false) {
			$dtuser=lihatuser($_POST['id_telegram']);
			$status="Id Telegram Ini Sudah Terdaftar Atas<hr>ID : ".$dtuser['id_user']."<br>Nama : ".$dtuser['nama_seller']."<br>Alamat : ".$dtuser['keterangan']."<br>Saldo : ".rupiah($dtuser['saldo'])."<br>Tercatat Mulai <hr>Tanggal : ".date('d/m/Y',strtotime($dtuser['Tanggal']))."<br>Jam : ".$dtuser['Waktu']."<hr style='border:4px dashed white;'>";
		}else{
			$cidtele="[".sendMessage($_POST['id_telegram'], "Cek Id Telegram Dulu,...", $tbothot)."]";	
			$cidtele=json_decode($cidtele,true);
			if ($cidtele[0]['ok']=="ok") {
				$status=daftaridm($_POST['id_telegram'],$_POST['id_whatsapp'],ucwords($_POST['nama']),ucwords($_POST['alamat']),$_POST['saldo_awal']);
				$status="[".sendMessage($_POST['id_telegram'], $status, $tbothot)."]";	
				$status=json_decode($status,true);
				if ($status[0]['ok']=="ok") {
					$status=str_replace("\n","<br>",$status[0]['result']['text'])."<br>Pesan berhasil dikirim ke Username :<br>".$status[0]['result']['chat']['username']."<br>[".$status[0]['result']['chat']['first_name']." / ".$status[0]['result']['chat']['last_name']."]";
					echo "<script>window.location='./admin.php?id=downline&status=$status'</script>";
				}else{
					$status="Pesan gagal terkirim.<br><b>".$status[0]['description']."</b>";
				}
			}else{
				$status="Id Telegramm ".$_POST['id_telegram'].", Tidak Di Temukan.<br><b>Pesan Kesalahan.</b><br><b>".$cidtele[0]['description']."</b>";
			}
		}
	}
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-8">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title"> &nbsp Daftar Downline <?=count($dtdl);?> Users.</h3>
							</div>
							<div class="w-12">
								<table width="100%"><tr><td width="50%"><input id="filterTable" type="text" class="form-control" placeholder="Search.."></td><td align="right" ><i class='fa fa-minus-square text-danger pointer fa-lg'></i>-Delete &nbsp<i class='fa fa-info-circle fa-lg'></i>-History &nbsp<i style='cursor: pointer;' class='fa fa-pencil-square-o text-warning fa-lg'></i>-Edit &nbsp<i style='cursor: pointer;' class='fa fa-plus-circle fa-lg'></i>-Saldo &nbsp &nbsp</td></tr></table> 
							</div>
								<div class="overflow box-bordered" style="max-height: 70vh;margin-top:5px;">
								<table id="dataTable" class="table table-bordered table-hover text-nowrap">
									<thead>
									<tr>
										<th class="text-right">No.</th>
										<th class="pointer text-right" title="Click to sort"> ID Telegram &nbsp <i class="fa fa-sort"></i>&nbsp </th>
										<th class="text-right">No WhatsApp</th>
										<th class="text-left">Nama</th>
										<th class="text-left">Alamat</th>
										<th class="text-right">Saldo</th>
										<th class="text-right"></th>
									</tr>
									</thead>
									<tbody>
									<?php
									for ($x=0;$x<count($dtdl);$x++) {
										$no=$x+1;
										echo "<tr>
										<td align='right'>".$no.". &nbsp ";
										?>
										<i class='fa fa-minus-square text-danger pointer' onclick="if(confirm('Tindakan ini akan menghapus data ( <?= $dtdl[$x]['nama_seller']; ?> ) secara permanen. \nTermasuk data re-seller Mikbotam. \nAre you sure to delete user downline ( <?= $dtdl[$x]['nama_seller']; ?> ) ? ')){loadpage('./admin.php?id=downline&delete-downline=<?= $dtdl[$x]['id_user']; ?>')}else{}" title='Remove user <?= $dtdl[$x]['nama_seller']; ?>'></i>
										<?php
										echo "
										</td>
										<td align='right'>".$dtdl[$x]['id_user']." &nbsp <a href='?id=history&id_u=".encrypt($dtdl[$x]['id_user'])."&bln=".date('m-Y')."'><i title='History ".$dtdl[$x]['nama_seller']."' style='cursor: pointer;' class='fa fa-info-circle'></i></a></td>
										<td align='right'>".$dtdl[$x]['nomer_tlp']."</td>
										<td><a href='?id=downline&pro=2&id_u=".$dtdl[$x]['id_user']."'><i title='Edit Data User ".$dtdl[$x]['nama_seller']."' style='cursor: pointer;' class='fa fa-pencil-square-o text-warning'></i> &nbsp ".$dtdl[$x]['nama_seller']."</a></td>
										<td>".$dtdl[$x]['keterangan']."</td>
										<td align='right'>".rupiah($dtdl[$x]['saldo'])."</td>
										<td><a href='?id=downline&pro=1&id_u=".$dtdl[$x]['id_user']."'><i title='Tambah Saldo ".$dtdl[$x]['nama_seller']."' style='cursor: pointer;' class='fa fa-plus-circle'></i></a></td>
										</tr>";
									}
									?>
									</tbody>
								</table>
							</div>
							
						</div>
					</div>
				
					<div class="col-4">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title text-center"> &nbsp <?=$judul?></h3>
							</div>
							<div class="card-body">
								<form autocomplete="off" method="post" action="">
									<table class="table table-sm">
										<?php if (empty($status)) { ?>
										<tr>
											<td align="right" class="align-middle">ID Owner : </td>
											<td><input class="form-control" id="useradm" type="number" size="10" name="id_tele_own" placeholder="ID Telegram Owner" value="<?= $idowner; ?>" readonly ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">ID Member : </td>
											<td><input class="form-control" id="useradm" type="number" size="10" name="id_telegram" placeholder="ID Telegram" value="<?= $id_telegram; ?>" <?=$sts1;?> ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">NO WhatsApp : </td>
											<td><input class="form-control" id="useradm" type="number" size="10" name="id_whatsapp" placeholder="No Whatsapp" value="<?= $id_whatsapp; ?>" <?=$sts2;?> ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Nama : </td>
											<td><input class="form-control" id="useradm" type="text" size="10" name="nama" placeholder="Nama Downline" value="<?= $nama; ?>" <?=$sts3;?> ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Alamat : </td>
											<td><input class="form-control" id="useradm" type="text" name="alamat" placeholder="Alamat Downline" value="<?= $alamat; ?>" <?=$sts4;?> ></td>
										</tr>
										<?php
										if ($_GET['pro']=='1') {
										echo '
										<tr>
											<td align="right" class="align-middle">Saldo : </td>
											<td><input class="group-item group-item-l" id="useradm" size="10" name="saldo" placeholder="Saldo" value="'.rupiah($saldo).'" readonly></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Deposit : </td>
											<td><input class="group-item group-item-l" id="useradm" type="number" min="10000" size="10" name="deposit" placeholder="Ketik Deposit" value="'.$deposit.'" required ></td>
										</tr>
										';	
											
										}elseif ($_GET['pro']=='2') {
										echo '
										<tr>
											<td align="right" class="align-middle">Saldo : </td>
											<td><input class="group-item group-item-l" id="useradm" size="10" name="saldo" placeholder="Saldo" value="'.rupiah($saldo).'" readonly ></td>
										</tr>
										';	
										}else{
										echo '
										<tr>
											<td align="right" class="align-middle">Saldo Awal : </td>
											<td><input class="group-item group-item-l" id="useradm" type="number" min="10000" size="10" name="saldo_awal" placeholder="Saldo Awal" value="'.$saldo_awal.'" required ></td>
										</tr>
										';	
										}
										echo '
										<tr>
											<td></td>
											<td class="text-right">
												<div class="input-group-4">
												<input class="group-item group-item-4" type="submit" style="cursor: pointer;" name="'.$tombol.'" value="'.$tombol1.'"/>
												</div>
												<div class="input-group-2" style="margin-left:10px;">
													<a href="?id=downline" style="cursor: pointer;" class="group-item group-item-r pd-2p5 text-center" title="Reload Data"><i class="fa fa-refresh"></i></a>
												</div>
											</td>
										</tr>
										';
										?>

										<?php }else{ ?>
										<tr>
											<td class="align-middle text-left" colspan="2">
											<?= $status; ?>
											</td>
										</tr>
										<tr>
											<td></td>
											<td class="text-right">
												<?php if (empty($status)) {?>
												<div class="input-group-4">
												<input class="group-item group-item-4" type="submit" style="cursor: pointer;" name="Clear" value=" Clear "/>
												</div>
												<?php } ?>
												<div class="input-group-2" style="margin-left:10px;">
													<a href="?id=downline" style="cursor: pointer;" class="group-item group-item-r pd-2p5 text-center" title="Reload Data"><i class="fa fa-refresh"></i></a>
												</div>
											</td>
										</tr>

										<?php } ?>
									</table>
								</form>
							</div>
						</div>
					</div>
					<div id="loadV" style="float:right;">v<?= $_SESSION['v']; ?> mod <a target="_blank" title="Telegram Contact @Cs_MimoAssist" href="https://t.me/Cs_MimoAssist"><u>@Cs_MimoAssist</u></a> &nbsp </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
  var _0x7470=["\x68\x6F\x73\x74\x6E\x61\x6D\x65","\x6C\x6F\x63\x61\x74\x69\x6F\x6E","\x2E","\x73\x70\x6C\x69\x74","\x6D\x69\x6B\x68\x6D\x6F\x6E\x2E\x6F\x6E\x6C\x69\x6E\x65","\x78\x62\x61\x6E\x2E\x78\x79\x7A","\x6C\x6F\x67\x61\x6D\x2E\x69\x64","\x6D\x69\x6E\x69\x73\x2E\x69\x64","\x69\x6E\x64\x65\x78\x4F\x66","\x3C\x73\x70\x61\x6E\x20\x3E\x3C\x69\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x77\x68\x69\x74\x65\x20\x66\x61\x20\x66\x61\x2D\x69\x6E\x66\x6F\x2D\x63\x69\x72\x63\x6C\x65\x22\x3E\x3C\x2F\x69\x3E\x20\x3C\x61\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x62\x6C\x75\x65\x22\x20\x68\x72\x65\x66\x3D\x22\x2E\x2F\x61\x64\x6D\x69\x6E\x2E\x70\x68\x70\x3F\x69\x64\x3D\x61\x62\x6F\x75\x74\x22\x3E\x43\x68\x65\x63\x6B\x20\x55\x70\x64\x61\x74\x65\x3C\x2F\x61\x3E\x3C\x2F\x73\x70\x61\x6E\x3E","\x68\x74\x6D\x6C","\x23\x6E\x65\x77\x56\x65\x72","\x68\x74\x74\x70\x73\x3A\x2F\x2F\x72\x61\x77\x2E\x67\x69\x74\x68\x75\x62\x75\x73\x65\x72\x63\x6F\x6E\x74\x65\x6E\x74\x2E\x63\x6F\x6D\x2F\x6C\x61\x6B\x73\x61\x31\x39\x2F\x6D\x69\x6B\x68\x6D\x6F\x6E\x76\x33\x2F\x6D\x61\x73\x74\x65\x72\x2F\x76\x65\x72\x73\x6F\x6E\x2E\x74\x78\x74\x3F\x74\x3D","\x72\x61\x6E\x64\x6F\x6D","\x66\x6C\x6F\x6F\x72","\x76","\x76\x65\x72\x73\x69\x6F\x6E","","\x72\x65\x70\x6C\x61\x63\x65","\x69\x6E\x6E\x65\x72\x48\x54\x4D\x4C","\x6C\x6F\x61\x64\x56","\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64","\x20","\x75\x70\x64\x61\x74\x65\x64","\x2D","\x4E\x65\x77\x20\x56\x65\x72\x73\x69\x6F\x6E\x20","\x3C\x62\x72\x3E\x3C\x73\x70\x61\x6E\x20\x3E\x3C\x69\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x77\x68\x69\x74\x65\x20\x66\x61\x20\x66\x61\x2D\x69\x6E\x66\x6F\x2D\x63\x69\x72\x63\x6C\x65\x22\x3E\x3C\x2F\x69\x3E\x20\x3C\x61\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x62\x6C\x75\x65\x22\x20\x68\x72\x65\x66\x3D\x22\x2E\x2F\x61\x64\x6D\x69\x6E\x2E\x70\x68\x70\x3F\x69\x64\x3D\x61\x62\x6F\x75\x74\x22\x3E\x43\x68\x65\x63\x6B\x20\x55\x70\x64\x61\x74\x65\x3C\x2F\x61\x3E\x3C\x2F\x73\x70\x61\x6E\x3E","\x67\x65\x74\x4A\x53\x4F\x4E"];var hname=window[_0x7470[1]][_0x7470[0]];var dom=hname[_0x7470[3]](_0x7470[2])[1]+ _0x7470[2]+ hname[_0x7470[3]](_0x7470[2])[2];var domArray=[_0x7470[4],_0x7470[5],_0x7470[6],_0x7470[7]];var a=domArray[_0x7470[8]](hname);var b=domArray[_0x7470[8]](dom);if(dom== _0x7470[4]){$(_0x7470[11])[_0x7470[10]](_0x7470[9])}else {if(a> 0|| b> 0){}else {$[_0x7470[27]](_0x7470[12]+ (Math[_0x7470[14]]((Math[_0x7470[13]]()* 999999999)+ 1))* 128,function(_0xc1b4x6){getNewVer= (_0xc1b4x6[_0x7470[16]])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4x7=parseInt(getNewVer[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4x8=document[_0x7470[21]](_0x7470[20])[_0x7470[19]];var _0xc1b4x9=(_0xc1b4x8[_0x7470[3]](_0x7470[22])[0])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4xa=parseInt(_0xc1b4x9[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4xb=(_0xc1b4x7- _0xc1b4xa);getNewVer= (_0xc1b4x6[_0x7470[16]])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4x7=parseInt(getNewVer[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4x8=document[_0x7470[21]](_0x7470[20])[_0x7470[19]];var _0xc1b4x9=(_0xc1b4x8[_0x7470[3]](_0x7470[22])[0])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4xa=parseInt(_0xc1b4x9[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4xb=(_0xc1b4x7- _0xc1b4xa);getNewD= (_0xc1b4x6[_0x7470[23]])[_0x7470[3]](_0x7470[22])[0];newD= parseInt((getNewD)[_0x7470[3]](_0x7470[24])[2]+ (getNewD)[_0x7470[3]](_0x7470[24])[0]+ (getNewD)[_0x7470[3]](_0x7470[24])[1]);var _0xc1b4xc=parseInt((_0xc1b4x8[_0x7470[3]](_0x7470[22])[1])[_0x7470[3]](_0x7470[24])[2]+ (_0xc1b4x8[_0x7470[3]](_0x7470[22])[1])[_0x7470[3]](_0x7470[24])[0]+ (_0xc1b4x8[_0x7470[3]](_0x7470[22])[1][_0x7470[3]](_0x7470[24]))[1]);var _0xc1b4xd=(newD- _0xc1b4xc);if(_0xc1b4xb> 0|| _0xc1b4xd> 0){$(_0x7470[11])[_0x7470[10]](_0x7470[25]+ _0xc1b4x6[_0x7470[16]]+ _0x7470[22]+ _0xc1b4x6[_0x7470[23]]+ _0x7470[26])}})}}
</script>
