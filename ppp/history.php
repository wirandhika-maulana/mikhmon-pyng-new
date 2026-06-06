<?php
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
}

if (isset($_POST['save'])) {
	echo '<script type="text/javascript">
	window.onload = function () { alert("INFO,\nPembayaran BERHASIL disimpan."); } 
	</script>';
}
include "ppp/function.php";
?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
                <h3><i class=" fa fa-history"></i> History <span style="color:yellow;"><?=caridtpelanggan1($idpell,2)?></span> &nbsp&nbsp&nbsp|&nbsp&nbsp&nbsp
				<a href="./?ppp=billing&idsecret=<?=$idpell?>&session=<?= $session; ?>" title="Back to ppp Billing"><i style="color:yellow;" class="fa fa-backward  "></i></a>&nbsp&nbspBack To Billing 
				<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small>
				</h3>
			</div>
			<div class="card-header align-middle">
				<h3><i class="fa fa-book" aria-hidden="true"></i> History  Transaksi </h3><p>
				<div class="w-4">
					<input id="filterTable" type="text" style="padding:5.8px;" class="group-item group-item-l" placeholder="<?= $_search ?>">
				</div>
			</div>
			<div class="card-body">
				<form autocomplete="off" method="post" action="">
					<div class="overflow box-bordered" style="max-height: 75vh">
						<table id="dataTable" class="table table-bordered table-hover text-nowrap">
							<thead>
								<tr>
									<th style="width:5%;" class="pointer" title="Click to sort"><i class="fa fa-sort"></i>&nbsp&nbsp No. </th>
									<th style="width:10%;" class="pointer" title="Click to sort"><i class="fa fa-sort"></i>&nbsp&nbsp Tanggal </th>
									<th> Keterangan </th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php
									if (empty(caridtpelanggan1($idpell,9))) {
										echo "<td align='right'>1.</td><td>".date('d-M-Y H:i:s')."</td><td>Scret User dibuat sebelum System Billing diterapkan.</td>";
									}else{
										echo "<td align='right'>1.</td>
										<td>".date('d-M-Y H:i:s',caridtpelanggan1($idpell,1))."</td>
										<td>".caridtpelanggan1($idpell,9).", User Secret <span style='font-weight:bold;color:yellow;'>".caridtpelanggan1($idpell,2)."</span>, Profile ".caridtpelanggan1($idpell,5).", Harga Langganan ".rupiah(caridtpelanggan1($idpell,6))." ( <i style='color:yellow;'>".ucwords(terbilang(caridtpelanggan1($idpell,6)))." Rupiah.</i> ).</td>";
									}
								echo "</tr>";
									$fdtprofil="ppp/csv/dthistory.txt";
									if (!file_exists($fdtprofil)) {
										echo "<tr><td align='right'>2.</td><td>".date('d-M-Y H:i:s')."</td><td>Belum ada history data yang tercatat dalam data.</td><tr>";
									}else{
										$dtfile=explode("#",file_get_contents($fdtprofil));
										$no=1;
										for ($x=0;$x<count($dtfile)-1;$x++) {
											if (trim(explode("^",$dtfile[$x])[0])==$idpell) {
												if (explode("^",$dtfile[$x])[2]=='bayar') {
													$no++;
													echo "<tr><td align='right'>".$no.".</td><td>".date('d-M-Y H:i:s',explode("^",$dtfile[$x])[1])."</td><td>".explode("^",$dtfile[$x])[3]." <b>".rupiah(explode("^",$dtfile[$x])[4])."</b> ( <i style='color:yellow;'>".ucwords(terbilang(explode("^",$dtfile[$x])[4]))." Rupiah.</i> )</td></tr>";
												}
											}
										}
										if ($no<2) {
											echo "<tr><td align='right'>2.</td><td>".date('d-M-Y H:i:s')."</td><td>Belum ada history data yang tercatat dalam data.</td></tr>";
										}
									}
									?>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>