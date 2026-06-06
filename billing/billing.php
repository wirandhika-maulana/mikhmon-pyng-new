<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
}
include "./ppp/function.php";
$fdtpell="./ppp/csv/dtpelanggan.txt";
$item=0;
if (file_exists($fdtpell)) {
	$dtpell=count(explode("#",file_get_contents($fdtpell)))-1;
	if ($dtpell<2 ) {
		$mitem=$dtpell." - Item.";
	}else{
		$mitem=$dtpell." - Items.";
	}
}
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3><h4 style="float:right;"><a href="./?ppp=addsecret&session=<?= $session; ?>" title="Add Secrets"><i class="fa fa-user-plus"></i> Add PPP Secret </a></h4>
				<i class="fa fa-money"></i> &nbsp Daftar Pelanggan User Ppppoe </h3>
			</div>
			<div class="card">
				<div class="w-4">
					<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder="<?= $_search ?>">
				</div>
			</div>
			<div class="overflow box-bordered" style="max-height: 75vh">
				<table id="dataTable" class="table table-bordered table-hover text-nowrap">
					<thead>
						<tr>
							<th class="text-right"> No. </th>
							<th class="text-center"><?=$mitem?></th>
							<th class="pointer" title="Click to sort">Nama&nbsp&nbsp<i class="fa fa-sort"></i> </th>
							<th class="pointer" title="Click to sort">Secret&nbsp&nbsp<i class="fa fa-sort"></i></th>
							<th class="text-right"><i class="pointer" title="Click to sort"><i class="fa fa-sort"></i></i>&nbsp&nbsp No Hp/Telegram </th>
							<th class="pointer" title="Click to sort">Profile&nbsp&nbsp<i class="fa fa-sort"></i></th>
							<th class="text-right"> Harga </th>
							<th class="text-center">Notifikasi</th>
							<th> Keterangan </th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (file_exists($fdtpell)) {
						$dtpell=explode("#",file_get_contents($fdtpell));
						if (!empty($dtpell)) {
							for ($x=0;$x<count($dtpell)-1;$x++) {
								$no=$x+1;
								echo "<tr>";
								
									echo "<td class='text-right'>".$no.".</td>
									<td class='text-center'>
									<a href='./?ppp=history&idhistory=".explode("^",$dtpell[$x])[0]."&session=".$session."' style='cursor:pointer;' title='History.' class='fa fa-address-card-o fa-lg'></a>&nbsp&nbsp&nbsp
									<a href='./?ppp=billing&idsecret=".explode("^",$dtpell[$x])[0]."&session=".$session."' style='cursor:pointer;' title='Pembayaran.' class='fa fa-money fa-lg'></i></a>&nbsp&nbsp&nbsp&nbsp";
									echo "<a href='./?secret=".explode("^",$dtpell[$x])[0]."&schedulerbyname=".explode("^",$dtpell[$x])[2]."&session=".$session."' style='cursor:pointer;' title='Edit data pelangan ".explode("^",$dtpell[$x])[12]."\nSecret ".explode("^",$dtpell[$x])[2]."' class='fa fa-edit fa-lg text-white'></a>&nbsp&nbsp&nbsp";
									echo "</td>
									<td>".explode("^",$dtpell[$x])[12]."</td>
									<td>".explode("^",$dtpell[$x])[2]."</td>
									<td class='text-right'>".explode("^",$dtpell[$x])[11]."</td>
									<td>".explode("^",$dtpell[$x])[5]."</td>
									<td class='text-right' style='cursor:pointer;' title='".ucwords(terbilang(explode("^",$dtpell[$x])[6]))." Rupiah'><span style='color:orange;'>".rupiah(explode("^",$dtpell[$x])[6])."</span></td>
									<td class='text-center'>";
									if (explode("^",$dtpell[$x])[14]=="1") {
										echo "<i class='fa fa-toggle-on text-green fa-lg' aria-hidden='true'></i>&nbsp&nbsp&nbsp&nbsp<b style='font-family:times;font-size:16px;'>ON</b>";
									}else{
										echo "<i class='fa fa-toggle-off text-red fa-lg' aria-hidden='true'></i>&nbsp&nbsp&nbsp&nbsp<b style='font-family:times;font-size:16px;'>OFF</b>";
									}
									echo "</td><td>".explode("^",$dtpell[$x])[9]."</td>";
								echo "</tr>";
							}
						}else{
							echo "<tr><td colspan='8' >File data pelanggan masih kosong</td></tr>";
						}
					}else{
						echo "<tr><td colspan='8' class='text-center' style='color:blue;'>File data pelanggan tidak ditemukan</td></tr>";
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
