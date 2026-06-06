<?php
// hide all error
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$func	="settings/function.php";
	if (file_exists($func)) {
		include $func;
		
	}else{
		$mket="File ".$func." Tidak Ada";
	}
}

$dtuser	= lihatdata();
$userid=decrypt($_GET['id_u']);
$namauser=lihatuser($userid);
$dthistory=sethistoryidbymonth1($userid,explode("-",$_GET['bln'])[0],explode("-",$_GET['bln'])[1]);
$itrx=count($dthistory);

$ckpesan=$_GET['kpesan'];
if (!empty($ckpesan)) {
	if (substr($userid,0,3)=='628') {
		$status=sendWa($userid, str_replace("</b>","*",str_replace("<b>","*",$ckpesan)));	
	}else{
		$tbothot=explode('|',file_get_contents($ftoken))[3];
		$status="[".sendMessage($userid, $ckpesan, $tbothot)."]";	
		$status=json_decode($status,true);
		if ($status[0]['ok']=="ok") {
			$status=str_replace("\n","<br>",$status[0]['result']['text'])."<br>Pesan berhasil dikirim ke Username :<br>".$status[0]['result']['chat']['username']."<br>[".$status[0]['result']['chat']['first_name']." / ".$status[0]['result']['chat']['last_name']."]";
		}else{
			$status="Pesan gagal terkirim.<br><b>".$status[0]['description']."</b>";
		}
	}
	echo "<script>window.location='?id=history&id_u=".$_GET['id_u']."&bln=".$_GET['bln']."&status=".$status."'</script>";
}


?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<span style="float:right;"><a href="?id=<?=$_GET['back']?>&bln=<?=$_GET['bln']?>">BACK &nbsp <i style='cursor: pointer;' class='fa fa-money'></i></a></span>
								<h3 class="card-title"> &nbsp History  Transaksi Id <?=$userid." <u>".$namauser['nama_seller']."</u> ( ".count($dthistory)." Trx.)" ?>  <small id="loader" style="display: none;" ><i> Prosessing... <i class='fa fa-circle-o-notch fa-spin'></i></i></small></h3>
							</div>
							<table width="100%" style="border:1px solid white;">
							<tr><td width="40%">
								<input id="filterTable" type="text" class="form-control" placeholder="Search..">
							</td><td align="right" style="padding-right:10px;width:20%;">Bln-Thn : 
							</td><td align="right" style="padding-right:10px;">
								<select name="bulan" class="form-control" style="width:115px;" onchange="location = this.value; loader()">
								<?php
								$abln = array('1' => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des');
								for ($i = 0; $i > -6; $i--) {
									$mbln=manipulasiTanggal(date('d-M-Y'),$i,$format='months');
									$nbln=manipulasiTanggal1(date('d-M-Y'),$i,$format='months');
									if ($mbln==$_GET['bln']) {$pil1="selected";}else{$pil1="";}
									echo "<option value='?id=history&id_u=".$_GET['id_u']."&bln=".$mbln."&back=".$_GET['back']."' ".$pil1."> ".$nbln." </option>";
								}
								?>
								</select>
							</td><td align="right" style="padding-right:10px;width:20%;">Re-Seller : 
							</td><td align="right" style="padding-right:10px;width:20%;">
								<select name="pilih" class="form-control" style="width:250px;" onchange="location = this.value; loader()">
								<?php
								$no=0;
								for ($x=0;$x<count($dtuser);$x++) {
									$no++;
									if ($dtuser[$x]['id_user']==$userid) {$pil="selected";}else{$pil="";}
									echo "<option value='?id=history&id_u=".encrypt($dtuser[$x]['id_user'])."&bln=".$_GET['bln']."&back=".$_GET['back']."' ".$pil." > ".$no.". ".$dtuser[$x]['id_user']." ".$dtuser[$x]['nama_seller']."</option>";
								}
								?>
							</select>
							</td></tr></table>
							<div class="card">
							<div class="overflow box-bordered" style="max-height: 70vh">
								<table id="dataTable" class="table table-bordered table-hover text-nowrap">
									<thead>
									<tr>
										<th class="text-right">No.</th>
										<th class="pointer text-right" title="Click to sort"> Tanggal / Jam <i class="fa fa-sort"></i>&nbsp </th>
										<th class="text-right">Keterangan</th>
										<th class="text-right">S-Awal</th>
										<th class="text-right"></th>
										<th class="text-right">Mutasi</th>
										<th class="text-right">S-Akhir</th>
										<th class="text-right">Profit</th>
										<th class="text-right">Username</th>
										<th class="text-right">Password</th>
										<th class="text-right">Keterangan</th>
									</tr>
									</thead>
									<tbody>
									<?php
									if (count($dthistory)==0) {
										echo "<tr><td colspan='11' style='font-size:18px;font-family:times;font-weight:bold;' align='center'><hr>BELUM ADA TRANSAKSI BULAN ".$_GET['bln']."<br>YANG TERCATAT DALAM DATABASE.</td></tr>";
									}
									$ttopup=$tsales=$sawal=$sakhir=$profit=0;
									$no=0;
									$blok="0";
									$cblok=0;
									$tvcr=0;
									for ($x=0;$x<count($dthistory);$x++) {
										if (explode("-",$dthistory[$x]['Tanggal'])[1]==explode("-",$_GET['bln'])[0]) {
											$no++;
											if ($blok=="0") {
												$sakhir	=$dthistory[$x]['saldo_akhir'];
												$blok="1";
											}
											$ceku="";
											if ($dthistory[$x]['top_up']<>0) {
												$mutasi=rupiah($dthistory[$x]['top_up'])." TU ";
												$mut="+";
												$cek="'background:green;'";
												$cek1=$dthistory[$x]['keterangan']." FROM ".$dthistory[$x]['top_up_fromid'];
												$ceku=$cek;
												$ttopup = $ttopup+$dthistory[$x]['top_up'];
											}elseif ($dthistory[$x]['top_up_fromid']<>0) {
												$mutasi=rupiah($dthistory[$x]['top_up_fromid'])." TUF ";
												$mut="+";
												$cek1=$dthistory[$x]['keterangan']." FROM ".$dthistory[$x]['top_up_fromid'];
												$cek="'background:blue;'";
												$ttopup = $ttopup+$dthistory[$x]['top_up'];
											}elseif ($dthistory[$x]['beli_voucher']<>0) {
												$mutasi=rupiah($dthistory[$x]['beli_voucher'])." BV ";
												$tsales=$tsales+$dthistory[$x]['beli_voucher'];
												$mut="( - )";
												$cek="'background:black;'";
												$tvcr++;
											}
											echo "
											<tr>
												<td align='right' style=$ceku >".$no.". </td>
												<td align='right' style=$ceku >".$dthistory[$x]['Tanggal']." ".$dthistory[$x]['Waktu']."</td>";
											
												if ($mut=="+") {
													echo "<td align='right' style=$ceku >".strtoupper($dthistory[$x]['keterangan'])." / ".$dthistory[$x]['top_up_fromid']."</td>";
												}else{
													echo "<td align='right' style=$ceku >".$dthistory[$x]['keterangan']."</td>";
												}
												echo "
												<td align='right' style=$ceku >".rupiah($dthistory[$x]['saldo_awal'])."</td>
												<td align='center' style=$ceku ><b>".$mut."</b></td>
												<td align='right' style=$cek  >".$mutasi."</td>
												<td align='right' style=$ceku >".rupiah($dthistory[$x]['saldo_akhir'])."</td>";
												if ($mut=="+") {
													echo "
													<td align='center' style=$ceku colspan='4'>".pisahkan($cek1)."</td>";
												}else{
													$profit=$profit+$dthistory[$x]['markup_voucher'];
													echo "
													<td align='right' style=$ceku >".rupiah($dthistory[$x]['markup_voucher'])."</td>
													<td align='right' style=$ceku >".$dthistory[$x]['username_voucher']."</td>
													<td align='right' style=$ceku >".$dthistory[$x]['password_voucher']."</td>
													<td align='right' style=$ceku >".$dthistory[$x]['exp_voucher']."</td>";
												}
												echo "
											</tr>										
											";
											$sawal	=$dthistory[$x]['saldo_awal'];
										}
									}
									?>
									</tbody>
								</table>
							</div>
							</div>
								<table width="100%" style="border:1px solid white;"><?php
								$kpesan	= sapaan()." ".$namauser['nama_seller']." \n\n";
								$kpesan .="<b>LAPORAN</b> \nRingkasan Transaksi Anda \nBulan ".$_GET['bln']."\nID Trx : ".$userid." \n".garis();
								$kpesan	.="Saldo Awal : ".rupiah($sawal)." \n";
								$kpesan	.="Deposit : ".rupiah($ttopup)." \n";
								$kpesan	.="Penjualan : ".rupiah($tsales)." \n";
								$kpesan	.="Voucher : ".$tvcr." Lembar \n";
								$kpesan	.="Saldo Akhir : ".rupiah($sakhir)." \n".garis();
								$kpesan	.="Laba : ".rupiah($profit)." \n";
								$kpesan .="<b>".ucwords(terbilang($profit))." Rupiah.</b> \n";
								$kpesan .= garis();
								$kpesan	.="Terimakasih dan ".sapaan();
								$kpesan	= urlencode($kpesan);
								
								echo "<tr><td align='right' style='padding-right:20px;font-weight:bold;'>Saldo Awal : ".rupiah($sawal)." | Deposit : ".rupiah($ttopup)." | Penjualan : ".rupiah($tsales)." | Jumlah Vcr : ".$tvcr." Lembar | <span style='background:green;border:0px solid white;padding:2px 10px 2px 10px;'>Laba : ".rupiah($profit)."</span> | Saldo Akhir : ".rupiah($sakhir)."</td><td><a style='color:white;' href='?id=history&id_u=".$_GET['id_u']."&bln=".$_GET['bln']."&kpesan=".$kpesan."'><input class='form-control' style='cursor: pointer;background:black;color:white;width:100px;text-align:center;' title='Kirim Laporan ke \n ".$namauser['nama_seller']."' value='Kirim'></a></td></tr>";
								
								?></table>
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
