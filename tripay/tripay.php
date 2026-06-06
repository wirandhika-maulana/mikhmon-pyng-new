<?php
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
}

include "function.php";

$fdtprofil="tripay/api/tripay.txt";
if (isset($_POST['save'])) {
	$tulis	= "Nama|".$_POST['nama']."||*\n";
	$tulis	.="NoWa|".$_POST['nowa']."||*\n";
	$tulis	.="Email|".$_POST['email']."||*\n";
	$tulis	.="Url Return|".$_POST['urlback']."||*\n";
	$tulis	.="F Core|".$_POST['fcore']."||*\n";
	$tulis	.="Kode Merhand|".$_POST['merhand0']."|".$_POST['merhand1']."*\n";
	$tulis	.="Private Key|".$_POST['pkey0']."|".$_POST['pkey1']."*\n";
	$tulis	.="Api Key|".$_POST['akey0']."|".$_POST['akey1']."*\n";
	$tulis	.="Detail Trx|".$_POST['dtrx0']."|".$_POST['dtrx1']."*\n";
	$tulis	.="Intruksi|".$_POST['intruksi0']."|".$_POST['intruksi1']."*\n";
	$tulis	.="Url Trx|".$_POST['urltrx0']."|".$_POST['urltrx1']."*\n";
	$tulis	.="List Merchan|".$_POST['lmerchand0']."|".$_POST['lmerchand1']."*\n";
	$tulis	.="Biaya Trx|".$_POST['btrx0']."|".$_POST['btrx1']."*\n";
	$tulis	.="Status|".$_POST['aktif']."|".$_POST['mode']."*\n";
	$tulis	.="Fee|".$_POST['mfee']."|".$_POST['notiftrx']."*\n";
	$tulis	.="Harga|".$_POST['harga']."|".$_POST['norek']."*\n";
	$handle = fopen($fdtprofil, 'w') or die('Cannot open file:  ' . $fdtprofil);
	fwrite($handle, $tulis);
	fclose($handle);
	echo '<script type="text/javascript">
	window.onload = function () { alert("INFO,\nData api, berhasil disimpan."); } 
	</script>';
}

if (file_exists($fdtprofil)) {
	$nama=capi(0,1);
	$nowa=capi(1,1);
	$email=capi(2,1);
	$urlback=capi(3,1);
	$fcore=capi(4,1);
	$merhand0=capi(5,1);
	$merhand1=capi(5,2);
	$pkey0=capi(6,1);
	$pkey1=capi(6,2);
	$akey0=capi(7,1);
	$akey1=capi(7,2);
	$dtrx0=capi(8,1);
	$dtrx1=capi(8,2);
	$intruksi0=capi(9,1);
	$intruksi1=capi(9,2);
	$urltrx0=capi(10,1);
	$urltrx1=capi(10,2);
	$lmerchand0=capi(11,1);
	$lmerchand1=capi(11,2);
	$btrx0=capi(12,1);
	$btrx1=capi(12,2);

	$maktif1=$mmode1=$maktif2=$mmode2="";
	if (capi(13,1)=="2") {$maktif2="selected";}else{$maktif1="selected";}
	if (capi(13,2)=="2") {$mmode2="selected";}else{$mmode1="selected";}
	
	$mfee1=$mfee2=$mfee3="";
	if (capi(14,1)=="1") {
		$mfee1="selected";
	}elseif(capi(14,1)=="2") {
		$mfee2="selected";
	}else{
		$mfee3="selected";
	}
	$mnotiftrx1=$mnotiftrx2="";
	if (capi(14,2)=="1") {
		$mnotiftrx1="selected";
	}else{
		$mnotiftrx2="selected";
	}
	$notiftrx=capi(14,2);
	
	$harga=capi(15,1);
	$norek=capi(15,2);
}else{
	$nama=$nowa=$email=$urlback=$fcore=$merhand0=$merhand1=$pkey0=$pkey1="";
	$akey0=$akey1=$dtrx0=$dtrx1=$intruksi0=$intruksi1=$urltrx0=$urltrx1=$norek="DANA 089633033332 Irawan Akbar Maulana";
	$lmerchand0=$lmerchand1=$btrx0=$btrx1=$maktif1=$mmode1=$maktif2=$mmode2=$mfee="";
	$harga=10000;
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
                <h3><i class=" fa fa-credit-card-alt"></i> Payment Gateway TriPay.co.id 
                    <?php 
					if ($page=='history-merchand-all' || $page=='history-status-all' || $page=='update' ) {
						echo '<a href="./admin.php?id=payment&page=update" style="cursor: pointer;" title="Download from server oore"> &nbsp&nbsp || &nbsp&nbsp Update History..&nbsp&nbsp <i class="fa fa-refresh"></i></a>';
					}elseif ($page=='merchand') {
						echo '<a href="./admin.php?id=payment&page=upmerchand" style="cursor: pointer;" title="Download daftar merchand"> &nbsp&nbsp || &nbsp&nbsp Update Merchand..&nbsp&nbsp <i class="fa fa-refresh"></i></a>';
					}
					?>	
					<span style="float:right;font-family:times;font-size:18px;margin-right:20px;">
						<a href="./admin.php?id=payment&page=" style="margin-right:30px;"> <i class="fa fa-desktop" aria-hidden="true"></i> Dashboard </a>
						<a href="./admin.php?id=payment&page=trx" > <i class="fa fa-shopping-cart" aria-hidden="true"></i> Transaksi </a> &nbsp&nbsp
						<a href="./admin.php?id=payment&page=merchand" > <i class="fa fa-window-restore" aria-hidden="true"></i> Merchand </a> &nbsp&nbsp
						<a href="./admin.php?id=payment&page=history-status-all" > <i class="fa fa-list-ol" aria-hidden="true"></i> History </a> &nbsp&nbsp
						<a href="./admin.php?id=payment&page=tripay" > <i class="fa fa-code" aria-hidden="true"></i> Api </a> &nbsp&nbsp
						<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></span>
				</h3>
			</div>
		</div>	
	</div>	
	<div class="col-12">
		<form autocomplete="off" method="post" action="">
			<div class="card">
			<?php
			if ($page=='tripay') {
				echo '
				<div class="card-body">
					<div class="row">
                    <div style="float:right;">
                        <button type="submit" name="save" class="btn bg-primary btn-mrg" style="color:black;font-weight:bold;">
						<i class="fa fa-save btn-mrg"></i> &nbsp&nbsp SAVE </button>
                    </div>
                    </div>
                    </div>
				<div class="card-body">
					<div class="row">
						<div class="col-6">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Setting API</h3>
									</div>
									<div class="card-body">
										<table class="table">
											<tr>
												<td class="text-bold" >Nama</td><td>:</td><td><input type="text" class="form-control" name="nama" value="'.$nama.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >WhatsApp</td><td>:</td><td><input type="text" class="form-control" name="nowa" value="'.$nowa.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Email</td><td>:</td><td><input type="text" class="form-control" name="email" value="'.$email.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Url Return</td><td>:</td><td><input type="text" class="form-control" name="urlback" value="'.$urlback.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Url Callback</td><td>:</td><td><input type="text" class="form-control" name="fcore" value="'.$fcore.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Notifiksi Trx</td><td>:</td><td>
												<select name="notiftrx" class="form-control" >
													<option value="1" '.$mnotiftrx1.' >WhatsApp</option>
													<option value="2" '.$mnotiftrx2.' >Telegram</option>
												</select>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Penggunaan</h3>
									</div>
									<div class="card-body">
										<table class="table">
											<tr>
												<td class="text-bold" >Harga Sewa Mikhmon </td><td>:</td><td><input type="number" class="form-control" name="harga" value="'.$harga.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Nomor Rekening </td><td>:</td><td><textarea class="form-control" name="norek" >'.$norek.'</textarea></td>
											</tr>
											<tr>
												<td class="text-bold" >Mode Aktif</td><td>:</td><td>
													<select  class="form-control" name="mode" >
														<option value="1" '.$mmode1.' >SANDBOX</option>
														<option value="2" '.$mmode2.' >Production</option>
													</select>
												</td>
											</tr>
											<tr>
												<td class="text-bold" >Status Gateway</td><td>:</td><td>
													<select  class="form-control" name="aktif" >
														<option value="1" '.$maktif1.' >OFF</option>
														<option value="2" '.$maktif2.' >ON</option>
													</select>
												</td>
											</tr>
											<tr>
												<td class="text-bold" >Fee Status</td><td>:</td><td>
													<select  class="form-control" name="mfee" >
														<option value="1" '.$mfee1.' > Merchand </option>
														<option value="2" '.$mfee2.' > Customer </option>
														<option value="3" '.$mfee3.' > Share Fee </option>
													</select>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Mode Sandbox</h3>
									</div>
									<div class="card-body">
										<table class="table">
											<tr>
												<td class="text-bold" >Kode Merchand </td><td>:</td><td><input type="text" class="form-control" name="merhand0" value="'.$merhand0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Private Key </td><td>:</td><td><input type="text" class="form-control" name="pkey0" value="'.$pkey0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Api Key </td><td>:</td><td><input type="text" class="form-control" name="akey0" value="'.$akey0.'" required></td>
											</tr>
											<tr>
												<td></td><td></td><td align="center" class="text-bold" colspan="3" >End Point </td></td>
											</tr>
											<tr>
												<td class="text-bold" >Detail Trx</td><td>:</td><td><input type="text" class="form-control" name="dtrx0" value="'.$dtrx0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Intruksi</td><td>:</td><td><input type="text" class="form-control" name="intruksi0" value="'.$intruksi0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Url Trx</td><td>:</td><td><input type="text" class="form-control" name="urltrx0" value="'.$urltrx0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >List Merhand</td><td>:</td><td><input type="text" class="form-control" name="lmerchand0" value="'.$lmerchand0.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Biaya Trx</td><td>:</td><td><input type="text" class="form-control" name="btrx0" value="'.$btrx0.'" required></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Mode Produksi</h3>
									</div>
									<div class="card-body">    
										<table class="table table-sm">
											<tr>
												<td class="text-bold" >Kode Merchand </td><td>:</td><td><input type="text" class="form-control" name="merhand1" value="'.$merhand1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Private Key </td><td>:</td><td><input type="text" class="form-control" name="pkey1" value="'.$pkey1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Api Key </td><td>:</td><td><input type="text" class="form-control" name="akey1" value="'.$akey1.'" required></td>
											</tr>
											<tr>
												<td></td><td></td><td align="center" class="text-bold" colspan="3" >End Point </td></td>
											</tr>
											<tr>
												<td class="text-bold" >Detail Trx</td><td>:</td><td><input type="text" class="form-control" name="dtrx1" value="'.$dtrx1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Intruksi</td><td>:</td><td><input type="text" class="form-control" name="intruksi1" value="'.$intruksi1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Url Trx</td><td>:</td><td><input type="text" class="form-control" name="urltrx1" value="'.$urltrx1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >List Merhand</td><td>:</td><td><input type="text" class="form-control" name="lmerchand1" value="'.$lmerchand1.'" required></td>
											</tr>
											<tr>
												<td class="text-bold" >Biaya Trx</td><td>:</td><td><input type="text" class="form-control" name="btrx1" value="'.$btrx1.'" required></td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>'; 
			}elseif ($page=='1' || $page=='2' || $page=='3' || $page=='4' || $page=='5' || $page=='6' || $page=='7') {
				editapi($page);
			}elseif ($page=='update') {
				bcore(update);
			}elseif ($page=='upmerchand') {
				listmerchand("update");
			}elseif ($page=='merchand') {
				echo '
				<div class="card-body">
					<div class="w-4" style="margin-top:10px;">
						<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder='.$_search.'
					</div>
				</div>
				<br>
				<div class="card-body">
					<h3>Total : '.listmerchand("hitung").' Merchand.</h3>
					<div class="card">
						<div class="card-body">
							<div class="overflow box-bordered" style="max-height: 65vh">
								<table id="dataTable" class="table table-bordered table-hover text-nowrap">
									<thead>
										<tr>
											<th style="width:5%;"> No. </th>
											<th align="left" class="pointer" title="Click to sort"> Status <i class="fa fa-sort"></i></th>
											<th align="left" class="pointer" title="Click to sort"> Group <i class="fa fa-sort"></i></th>
											<th align="left" class="pointer" title="Click to sort"> Code <i class="fa fa-sort"></i></th>
											<th align="left" class="pointer" title="Click to sort"> Nama <i class="fa fa-sort"></i></th>
											<th align="left" class="pointer" title="Click to sort"> Type <i class="fa fa-sort"></i></th>
											<th align="left" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Fee (Rp.)</th>
											<th align="left" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Fee (%)</th>
										</tr>
									</thead>
									<tbody>
									'.listmerchand("list").'
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				';
			}elseif ($page=='trx') {
				echo '
				<div class="card-body">
					<h3>TRANSAKSI</h3>
					<div class="card">
						<div class="card-body">
						Tidak Ada Transaksi<p>
						'.listmerchand("logo").'
						</div>
					</div>
				</div>
				';
			}elseif (explode("-",$page)[0]=='history') {
				$dtdispl=bcore("list-".ltrim(explode("-",$page)[1])."-".ltrim(explode("-",$page)[2]));
				echo '
					<div class="row">
						<div class="col-6">
							<div class="col-12">
								<div class="card-body">
									<div style="margin-top:10px;">
										<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder='.$_search.'>
									</div>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="col-12">
								<div class="card-body">
									<div class="col-8 pd-t-10 pd-b-5" style="float:right;"> 
										<div class="input-group-5 col-box-3" style="margin-right:20px;">
											<select name="pilah" class="form-control" onchange="location = this.value; loader()">
												<option value=""> Filter By Merchand </option>
												'.listmerchand("select").'
											</select>
										</div>
										<div class="input-group-5 col-box-3">
											<select name="palih" class="form-control" onchange="location = this.value; loader()">
												<option value="">Filter By Status</option>
												<option value="./admin.php?id=payment&page=history-status-all">ALL</option>
												<option value="./admin.php?id=payment&page=history-status-paid">PAID</option>
												<option value="./admin.php?id=payment&page=history-status-okk">EXPIRED</option>
												<option value="./admin.php?id=payment&page=history-status-dev">SIMULASI</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="card-body">
					<h3>Total : '.explode("#",$dtdispl)[0].' Trx.</h3>
					<div class="overflow box-bordered" style="max-height: 65vh">
						<table id="dataTable" class="table table-bordered table-hover text-nowrap">
							<thead>
								<tr>
									<th style="width:5%;"> No. </th>
									<th style="width:5%;" align="left" class="pointer" title="Click to sort"> Tanggal <i class="fa fa-sort"></i></th>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Merchand </th>
									<th style="width:5%;" align="left" class="pointer" title="Click to sort"> Ref Merchand <i class="fa fa-sort"></i></th>
									<th style="width:5%;" align="left" class="pointer" title="Click to sort"> Code <i class="fa fa-sort"></i></th>
									<th style="width:5%;" align="left" class="pointer" title="Click to sort"> Methode <i class="fa fa-sort"></i></th>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Total </th>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Fee Merc </th>
									<th style="width:5%;" align="left" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Fee Cust </th>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Recive </th>
									<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Status </th>
								</tr>
							</thead>
							<tbody>
							'.explode("#",$dtdispl)[1].'
							</tbody>
						</table>
					</div>
				</div>
				';
//									<th style="width:5%;" align="left" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> asli </th>

			}else{
					$stt='style="font-family:times;font-size:16px;padding:5px 25px 5px 25px;border:1px white solid;border-radius:5px;background:green;color:white;"';
					if ($page !="") {
					echo '
					<div class="row">
						<div class="card">
							<div class="card-body">
								<img style="float:right;height:100px;margin-top:20px;width:200px;background:white;border-radius:5px;" src='.$_GET['logo'].'>
								<a href="./admin.php?id=payment"><b '.$stt.'>BACK</b></a>
								<select '.$stt.' onchange="location = this.value; loader()">
								'.listmerchand("selecti").'
								</select>
								<table>
									'.intruksi($page).'
								</table>
							</div>
						</div>
					</div>
					';
				}else{

				if (capi(13,1)=="1"){$color="color:green;";}else{$color="color:black;";}
				if (capi(13,2)=="1"){$color1="color:blue;";}else{$color1="color:black;";}
				$mstyle='style="font-family:times;font-weight:bold;font-size:16px;cursor:pointer;'.$color.';"';
				$mstyle1='style="font-family:times;font-weight:bold;font-size:16px;cursor:pointer;'.$color1.';"';
				$mstyletd='style="font-family:times;font-weight:bold;font-size:16px;cursor:pointer;"';
				$mstyleth='style="font-family:times;font-weight:bold;font-size:16px;cursor:pointer;background:white;color:black;border:1px solid yellow;border-radius:2px;padding:2px 10px 2px 20px;"';
				if (capi(13,1)=="2") {$mstatus='<a href="./admin.php?id=payment&page=1"><input title="Aktif" '.$mstyle.' type="button" value=" ON "></a>';}else{$mstatus='<a href="./admin.php?id=payment&page=2"><input title="Mati"  '.$mstyle.' type="button" value=" OFF "></a>';}
				if (capi(13,2)=="1") {$mmode='<a href="./admin.php?id=payment&page=3"><input title="Mode untuk test" '.$mstyle1.' type="button" value=" SANDBOX "></a>';}else{$mmode='<a href="./admin.php?id=payment&page=4"><input title="Mode operational"  '.$mstyle1.' type="button" value=" Production "></a>';}

				if (capi(14,1)=="1") {
					$color1="color:green;";
					$mstyle1='style="text-align:left;width:125px;font-family:times;font-weight:bold;font-size:16px;cursor:pointer;'.$color1.';"';
					$mmfee='<a href="./admin.php?id=payment&page=5"><input title="Fee oleh merchand" '.$mstyle1.' type="button" value=" Merchand "></a>';
				}elseif (capi(14,1)=="2") {
					$color1="color:blue;";
					$mstyle1='style="text-align:left;width:125px;font-family:times;font-weight:bold;font-size:16px;cursor:pointer;'.$color1.';"';
					$mmfee='<a href="./admin.php?id=payment&page=6"><input title="Fee oleh customer" '.$mstyle1.' type="button" value=" Customer "></a>';
				}elseif (capi(14,1)=="3") {
					$color1="color:red;";
					$mstyle1='style="text-align:left;width:125px;font-family:times;font-weight:bold;font-size:16px;cursor:pointer;'.$color1.';"';
					$mmfee='<a href="./admin.php?id=payment&page=7"><input title="Fee merchand dan customer" '.$mstyle1.' type="button" value=" Share Fee "></a>';
				}
				echo '
					<div class="row">
						<div class="col-6">
							<div class="col-12">
								<div class="card">
									<table class="table">
										<tr><th></th><th></th><th >STATUS SYSTEM</th></tr>
										<tr>
											<td '.$mstyletd.' >Status Payment Gateway </td><td>:</td><td>'.$mstatus.'</td>
										</tr>
										<tr>
											<td '.$mstyletd.' >Mode Payment Gateway </td><td>:</td><td>'.$mmode.'</td>
										</tr>
										<tr>
											<td '.$mstyletd.' >Fee Status </td><td>:</td><td>'.$mmfee.'</td>
										</tr>
										<tr>
											<td '.$mstyletd.' >Url File Webhook </td><td>:</td><td '.$mstyletd.' >'.capi(4,1).'</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<div class="col-6">
							<div class="col-12">
								<div class="card">
								<div class="w-8">
									<table class="table">
										<tr><th colspan="3" align="center">STATUS TRANSAKSI</th></tr>
										<tr>
											<td '.$mstyletd.' width="35%">	Transaksi PAID </td><td>:</td><td align="right"><a href="./admin.php?id=payment&page=history-status-paid" ><b '.$mstyleth.'>'.explode("|",bcore("detail"))[0].'</b></a></td>
										</tr>
										<tr>
											<td '.$mstyletd.' > Transaksi Expired </td><td>:</td><td align="right"><a href="./admin.php?id=payment&page=history-status-okk" ><b '.$mstyleth.'>'.explode("|",bcore("detail"))[1].'</b></a></td>
										</tr>
										<tr>
											<td '.$mstyletd.' >Transaksi Simulasi</td><td>:</td><td align="right"><a href="./admin.php?id=payment&page=history-status-dev" ><b '.$mstyleth.'>'.explode("|",bcore("detail"))[2].'</b></a></td>
										</tr>
										<tr>
											<td '.$mstyletd.' >Jumlah Transaksi </td><td>:</td><td align="right"><a href="./admin.php?id=payment&page=history-status-all"><b '.$mstyleth.'>'.explode("|",bcore("detail"))[3].'</b></a></td>
										</tr>
									</table>
								</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="card">
						<div class="card-body">
						'.listmerchand("logo").'
						</div>
					</div>
				</div>
				';
				}
			}
			?>
			</div>
		</form>
	</div>
</div>
<?php
?>