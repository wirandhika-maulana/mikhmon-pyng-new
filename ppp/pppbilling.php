<?php
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
}
include "ppp/function.php";
$cnama	= $API->comm("/ppp/secret/print", ["?.id" => $idpell, ]);
$csche	= $cnama[0]['name'];
$cidsc	= $API->comm("/system/schedule/print", ["?name" => $csche, ]);
//$idsch	= $cidsc[0]['.id'];
$maktif	= $cidsc[0]['next-run'];

?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header align-middle">
                <h3><i class=" fa fa-money"></i> PPP Billing Secrets <span style="color:yellow;"><?=caridtpelanggan1($idpell,2)?></span>&nbsp&nbsp&nbsp|&nbsp&nbsp&nbsp
				<a href="./?ppp=history&idhistory=<?=$idpell?>&session=<?= $session; ?>" title="Rincian transaksi a/n <?=caridtpelanggan1($idpell,2)?> ">
				<i class="fa fa-history"></i> History Secret <span style="color:yellow;"><?=caridtpelanggan1($idpell,2)?></span> </a>
				<small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small>
				</h3>
			</div>
			<div class="card-body">
				<form autocomplete="off" method="post" action="">
					<div>
						<i style="color:black;" class="btn bg-green"  onclick="if(confirm('Are you sure to BAYAR ppp secret ( <?=caridtpelanggan1($idpell,2)?> )..? ')){loadpage('./?bayar=<?=$idpell?>&session=<?= $session; ?>')}else{}" title='Bayar <?=caridtpelanggan1($idpell,2)?>'><i class='fa fa-money fa-lg'>&nbsp&nbspBayar</i></i>
						<a style="color:black;" class="btn bg-warning" href="./?ppp=secrets&session=<?= $session; ?>"> <i class="fa fa-close fa-lg btn-mrg"></i> Batal</a>
					</div>
					<div class="overflow box-bordered" style="max-height: 75vh">
						<table id="dataTable" class="table table-bordered table-hover text-nowrap">
							<tr><td><?=$_user_name?></td><td><input class="form-control" type="text" name="user" value="<?=caridtpelanggan1($idpell,2)?>" readonly </td></tr>
							<tr><td>Profile</td><td><input class="form-control" type="text" name="mprofile" value="<?=caridtpelanggan1($idpell,5)?>" readonly </td></tr>
							<tr><td>Service</td><td><input class="form-control" type="text" name="service" value="<?=caridtpelanggan1($idpell,4)?>" readonly </td></tr>
							<tr><td >Nama Pelanggan </td><td><input class="form-control" type="text" name="pelanggan" value="<?=caridtpelanggan1($idpell,12)?>" readonly </td></tr>
							<tr><td style="font-size:16px;font-family:times;color:orange;">Status Account</td><td><input style="color:orane;" class="form-control" type="text" name="renew" value="<?="Disable in ".str_replace("/"," / ",ucwords($maktif))?>" readonly </td></tr>
							<tr><td >No WA / Teleram </td><td><input class="form-control" type="text" name="idpel" value="<?=caridtpelanggan1($idpell,11)?>" readonly </td></tr>
							<tr><td style="font-size:16px;font-family:times;color:orange;">Last Transaksi</td><td style="font-size:16px;font-family:times;color:orange;padding-left:8px;"><?=str_replace("/"," / ",caridtpelanggan1($idpell,9))?></td></tr>
							<tr><td>Piutang</td><td><input class="form-control" type="text" name="mhrg" value="<?=rupiah(caridtpelanggan1($idpell,6))?>" readonly </td></tr>
							<tr><td></td><td style="font-size:20px;font-family:times;color:orange;padding-left:8px;"><?=ucwords(terbilang(caridtpelanggan1($idpell,6)))?> Rupiah.</td></tr>
							<tr><td style="font-size:16px;font-family:times;">Tanggal Pembayaran</td><td style="font-size:16px;font-family:times;padding-left:8px;"><?=date('d/M/Y'); ?></td></tr>
						</table>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>