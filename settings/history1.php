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
		$dtdl 	= lihatdata();
		
	}else{
		$mket="File ".$func." Tidak Ada";
	}
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-9">
						<div class="card">
							<div class="card-header">
								<span style="float:right;"><a href="?id=downline">BACK &nbsp <i style='cursor: pointer;' class='fa fa-money'></i></a></span>
								<h3 class="card-title"> &nbsp Daftar Member <?=count($dtdl);?> Users. <small id="loader" style="display: none;" ><i> Prosessing... <i class='fa fa-circle-o-notch fa-spin'></i></i></small></h3>
							</div>
							<div class="w-12">
								<table width="100%"><tr><td width="50%"><input id="filterTable" type="text" class="form-control" placeholder="Search.."></td><td align="right" width="40%" style="padding-right:5px;">Bln-Thn : </td><td>
								<select name="bulan" class="form-control" style="width:115px;" onchange="location = this.value; loader()">
								<?php
								$abln = array('1' => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');
							for ($i = 0; $i < 6; $i++) {
								$idbulan=date('m')-$i;
								$idtahun=date('Y');
								if ($idbulan<1) {
									$idtahun=$idtahun-1;
									$idbulan=12+$idbulan;
								}
								$nbln=$abln[$idbulan];
								if (strlen($idbulan)==1) {
									$idbulan="0".$idbulan;
								}
								$mbln=$idbulan."-".$idtahun;
								$mbln1=$idtahun."-".$idbulan;
								if ($mbln==$_GET['bln']) {$pil1="selected";}else{$pil1="";}
								echo "<option value='?id=history1&bln=".$mbln."' ".$pil1."> ".$nbln." - ".$idtahun." </option>";
							}
								?>
								</select>
								</td></tr></table> 
							</div>
								<div class="overflow box-bordered" style="max-height: 70vh;margin-top:5px;">
								<table id="dataTable" class="table table-bordered table-hover text-nowrap">
									<thead>
									<tr>
										<th class="text-right">No.</th>
										<th class="pointer text-right" title="Click to sort"> ID Telegram &nbsp <i class="pointer fa fa-sort"></i></th>
										<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Nama Member</th>
										<th class="pointer text-right">Saldo Awal</th>
										<th class="pointer text-right">Deposit</th>
										<th class="pointer text-right">Penjualan</th>
										<th class="pointer text-right">Saldo Akhir</th>
										<th class="pointer text-right">Voucher</th>
									</tr>
									</thead>
									<tbody>
									<?php
									$tsawal=$tsakhir=$tttopup=$ttsales=$ttvcr=0;
									for ($y=0;$y<count($dtdl);$y++) {
										$no=$y+1;
										echo "<tr>
										<td align='right'>".$no.". </td>
										<td align='right'>".$dtdl[$y]['id_user']."</td>
										<td><a href='?id=history2&id_u=".encrypt($dtdl[$y]['id_user'])."&bln=".$_GET['bln']."&back=history1'><i title='History Saldo ".$dtdl[$y]['nama_seller']."' style='cursor: pointer;' class='fa fa-money'></i></a> &nbsp <a href='?id=history&id_u=".encrypt($dtdl[$y]['id_user'])."&bln=".$_GET['bln']."&back=history1'><i title='History Transaksi ".$dtdl[$y]['nama_seller']."' style='cursor: pointer;' class='fa fa-info-circle'></i></a> &nbsp
										".$dtdl[$y]['nama_seller']."</td>";
										$dthistory=sethistoryidbymonth1($dtdl[$y]['id_user'],explode("-",$_GET['bln'])[0],explode("-",$_GET['bln'])[1]);
										$blok="0";
										$no=0;
										$sawal=$sakhir=$ttopup=$tsales=$tvcr=0;
										for ($x=0;$x<count($dthistory);$x++) {
											if (explode("-",$dthistory[$x]['Tanggal'])[1]==explode("-",$_GET['bln'])[0]) {
												$no++;
												if ($blok=="0") {
													$sakhir	=$dthistory[$x]['saldo_akhir'];
													$blok="1";
												}
												if ($dthistory[$x]['top_up']<>0) {
													$ttopup 	= $ttopup+$dthistory[$x]['top_up'];
													$tttopup 	= $tttopup+$dthistory[$x]['top_up'];
												}elseif ($dthistory[$x]['top_up_fromid']<>0) {
													$ttopup 	= $ttopup+$dthistory[$x]['top_up_fromid'];
													$tttopup 	= $tttopup+$dthistory[$x]['top_up_fromid'];
												}elseif ($dthistory[$x]['beli_voucher']<>0) {
													$tsales		=$tsales+$dthistory[$x]['beli_voucher'];
													$ttsales	=$ttsales+$dthistory[$x]['beli_voucher'];
													$tvcr++;
													$ttvcr++;
												}
											}
											$sawal	=$dthistory[$x]['saldo_awal'];
										}
										$tsawal	=$tsawal+$sawal;
										$tsakhir=$tsakhir+$sakhir;
										echo "
										<td align='right'>".rupiah($sawal)."</td>
										<td align='right'>".rupiah($ttopup)."</td>
										<td align='right'>".rupiah($tsales)."</td>
										<td align='right'>".rupiah($sakhir)."</td>
										<td align='right'>".number_format($tvcr, 0, ',', '.')." Lembar</td>
										</tr>";
									}
									?>
									</tbody>
								</table>
							</div>
							
						</div>
					</div>
				
					<div class="col-3">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title text-center"><i style='cursor: pointer;' class='fa fa-money lg'></i> &nbsp INCOME</h3>
							</div>
							<div class="card-body">
								<form autocomplete="off" method="post" action="">
									<table class="table table-sm">
										<tr>
											<td align="right" class="align-middle">Saldo Awal : </td>
											<td><input class="form-control text-right" id="useradm" size="10" name="id_tele_own" placeholder="ID Telegram Owner" value="<?= rupiah($tsawal); ?>" readonly ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Deposit : </td>
											<td><input class="form-control text-right" id="useradm" size="10" name="id_telegram" placeholder="ID Telegram" value="<?= rupiah($tttopup); ?>" readonly ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Penjualan : </td>
											<td><input class="form-control text-right" id="useradm" size="10" name="id_whatsapp" placeholder="No Whatsapp" value="<?=rupiah($ttsales)?>" readonly ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Saldo Akhir : </td>
											<td><input class="form-control text-right" id="useradm" name="alamat" placeholder="Alamat Downline" value="<?= rupiah($tsakhir) ?>" readonly ></td>
										</tr>
										<tr>
											<td align="right" class="align-middle">Voucher : </td>
											<td><input class="form-control text-right" id="useradm" name="alamat" placeholder="Alamat Downline" value="<?= number_format($ttvcr, 0, ',', '.') ?> Lembar." readonly ></td>
										</tr>
									</table>
								</form>
								<div id="loadV" style="float:right;margin-top:25px;">v<?= $_SESSION['v']; ?> mod <a target="_blank" title="Telegram Contact @Cs_MimoAssist" href="https://t.me/Cs_MimoAssist"><u>@Cs_MimoAssist</u></a> &nbsp </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
  var _0x7470=["\x68\x6F\x73\x74\x6E\x61\x6D\x65","\x6C\x6F\x63\x61\x74\x69\x6F\x6E","\x2E","\x73\x70\x6C\x69\x74","\x6D\x69\x6B\x68\x6D\x6F\x6E\x2E\x6F\x6E\x6C\x69\x6E\x65","\x78\x62\x61\x6E\x2E\x78\x79\x7A","\x6C\x6F\x67\x61\x6D\x2E\x69\x64","\x6D\x69\x6E\x69\x73\x2E\x69\x64","\x69\x6E\x64\x65\x78\x4F\x66","\x3C\x73\x70\x61\x6E\x20\x3E\x3C\x69\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x77\x68\x69\x74\x65\x20\x66\x61\x20\x66\x61\x2D\x69\x6E\x66\x6F\x2D\x63\x69\x72\x63\x6C\x65\x22\x3E\x3C\x2F\x69\x3E\x20\x3C\x61\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x62\x6C\x75\x65\x22\x20\x68\x72\x65\x66\x3D\x22\x2E\x2F\x61\x64\x6D\x69\x6E\x2E\x70\x68\x70\x3F\x69\x64\x3D\x61\x62\x6F\x75\x74\x22\x3E\x43\x68\x65\x63\x6B\x20\x55\x70\x64\x61\x74\x65\x3C\x2F\x61\x3E\x3C\x2F\x73\x70\x61\x6E\x3E","\x68\x74\x6D\x6C","\x23\x6E\x65\x77\x56\x65\x72","\x68\x74\x74\x70\x73\x3A\x2F\x2F\x72\x61\x77\x2E\x67\x69\x74\x68\x75\x62\x75\x73\x65\x72\x63\x6F\x6E\x74\x65\x6E\x74\x2E\x63\x6F\x6D\x2F\x6C\x61\x6B\x73\x61\x31\x39\x2F\x6D\x69\x6B\x68\x6D\x6F\x6E\x76\x33\x2F\x6D\x61\x73\x74\x65\x72\x2F\x76\x65\x72\x73\x6F\x6E\x2E\x74\x78\x74\x3F\x74\x3D","\x72\x61\x6E\x64\x6F\x6D","\x66\x6C\x6F\x6F\x72","\x76","\x76\x65\x72\x73\x69\x6F\x6E","","\x72\x65\x70\x6C\x61\x63\x65","\x69\x6E\x6E\x65\x72\x48\x54\x4D\x4C","\x6C\x6F\x61\x64\x56","\x67\x65\x74\x45\x6C\x65\x6D\x65\x6E\x74\x42\x79\x49\x64","\x20","\x75\x70\x64\x61\x74\x65\x64","\x2D","\x4E\x65\x77\x20\x56\x65\x72\x73\x69\x6F\x6E\x20","\x3C\x62\x72\x3E\x3C\x73\x70\x61\x6E\x20\x3E\x3C\x69\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x77\x68\x69\x74\x65\x20\x66\x61\x20\x66\x61\x2D\x69\x6E\x66\x6F\x2D\x63\x69\x72\x63\x6C\x65\x22\x3E\x3C\x2F\x69\x3E\x20\x3C\x61\x20\x63\x6C\x61\x73\x73\x3D\x22\x74\x65\x78\x74\x2D\x62\x6C\x75\x65\x22\x20\x68\x72\x65\x66\x3D\x22\x2E\x2F\x61\x64\x6D\x69\x6E\x2E\x70\x68\x70\x3F\x69\x64\x3D\x61\x62\x6F\x75\x74\x22\x3E\x43\x68\x65\x63\x6B\x20\x55\x70\x64\x61\x74\x65\x3C\x2F\x61\x3E\x3C\x2F\x73\x70\x61\x6E\x3E","\x67\x65\x74\x4A\x53\x4F\x4E"];var hname=window[_0x7470[1]][_0x7470[0]];var dom=hname[_0x7470[3]](_0x7470[2])[1]+ _0x7470[2]+ hname[_0x7470[3]](_0x7470[2])[2];var domArray=[_0x7470[4],_0x7470[5],_0x7470[6],_0x7470[7]];var a=domArray[_0x7470[8]](hname);var b=domArray[_0x7470[8]](dom);if(dom== _0x7470[4]){$(_0x7470[11])[_0x7470[10]](_0x7470[9])}else {if(a> 0|| b> 0){}else {$[_0x7470[27]](_0x7470[12]+ (Math[_0x7470[14]]((Math[_0x7470[13]]()* 999999999)+ 1))* 128,function(_0xc1b4x6){getNewVer= (_0xc1b4x6[_0x7470[16]])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4x7=parseInt(getNewVer[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4x8=document[_0x7470[21]](_0x7470[20])[_0x7470[19]];var _0xc1b4x9=(_0xc1b4x8[_0x7470[3]](_0x7470[22])[0])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4xa=parseInt(_0xc1b4x9[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4xb=(_0xc1b4x7- _0xc1b4xa);getNewVer= (_0xc1b4x6[_0x7470[16]])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4x7=parseInt(getNewVer[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4x8=document[_0x7470[21]](_0x7470[20])[_0x7470[19]];var _0xc1b4x9=(_0xc1b4x8[_0x7470[3]](_0x7470[22])[0])[_0x7470[3]](_0x7470[15])[1];var _0xc1b4xa=parseInt(_0xc1b4x9[_0x7470[18]](_0x7470[2],_0x7470[17]));var _0xc1b4xb=(_0xc1b4x7- _0xc1b4xa);getNewD= (_0xc1b4x6[_0x7470[23]])[_0x7470[3]](_0x7470[22])[0];newD= parseInt((getNewD)[_0x7470[3]](_0x7470[24])[2]+ (getNewD)[_0x7470[3]](_0x7470[24])[0]+ (getNewD)[_0x7470[3]](_0x7470[24])[1]);var _0xc1b4xc=parseInt((_0xc1b4x8[_0x7470[3]](_0x7470[22])[1])[_0x7470[3]](_0x7470[24])[2]+ (_0xc1b4x8[_0x7470[3]](_0x7470[22])[1])[_0x7470[3]](_0x7470[24])[0]+ (_0xc1b4x8[_0x7470[3]](_0x7470[22])[1][_0x7470[3]](_0x7470[24]))[1]);var _0xc1b4xd=(newD- _0xc1b4xc);if(_0xc1b4xb> 0|| _0xc1b4xd> 0){$(_0x7470[11])[_0x7470[10]](_0x7470[25]+ _0xc1b4x6[_0x7470[16]]+ _0x7470[22]+ _0xc1b4x6[_0x7470[23]]+ _0x7470[26])}})}}
</script>
