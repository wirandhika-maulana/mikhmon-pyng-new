<?php
$file = 'webhookk.php';
$mtgl=date('d/m/Y');
$mtime=date('H:i:s');
$fdtvcr	= "data/dt".substr(date('d/m/Y'),3,2).substr(date('d/m/Y'),6,4).".txt";
if (file_exists($file)) {
	$misi		= file_get_contents($file);
	$misi0		= explode("|",$misi);
	$idtele		= $misi0[0];
	$namatele	= $misi0[1];
	$bottele	= $misi0[2];
	$tokentele	= $misi0[3];
	$murlb		= $misi0[4];
}else{
	$bottele	= "file tidak ada.";
}

if (file_exists($fdtvcr)) {
	$misi=explode("#",file_get_contents($fdtvcr));
	$jdata=count($misi)-2;
	$no=1;
	$hasil	= "<div class='col-12'>";
	$hasil	.="<div class='card'>";
	$hasil	.="<div class='card-header'>";
	$hasil	.="<b style='font-family:timesnewrman;font-size:18px;'> <i class=' fa fa-sitemap'></i> [".$mtime."] - Aktivitas  @<a href='https://t.me/".$bottele."' target='_New'><u>".$bottele."</u></a></b>";
	$hasil	.="</div>";
	$hasil	.="<div class='card-body'>";
	$hasil	.="<div class='overflow box-bordered mr-t-10' style='max-height: 29vh'>";
	$hasil	.="<table id='dataTable' class='table table-bordered table-hover text-nowrap' style='font-size:14px;'>";
	$hasil	.="<th>No.</th><th>ID Telegram</th><th>User</th><th>Tanggal/Jam</th></th><th>Kode Vcr</th><th>H_Modal</th><th>H_Jual</th>";
	for ($a=$jdata ;$a>-1 ; $a--) {
		$hasil	.= "<tr><td align='right'>".$no.".</td><td>".explode("|",$misi[$a])[0]."</td><td>".explode("|",$misi[$a])[1]."</td><td>".explode("|",$misi[$a])[2]." / ".explode("|",$misi[$a])[3]."</td><td> ".explode("|",$misi[$a])[6]."</td><td> ".rupiah(explode("|",$misi[$a])[7])."</td><td> ".rupiah(explode("|",$misi[$a])[8])."</td></tr>";
		$no++;
	}
	$hasil	.="</table></div></div></div></div>";
	echo $hasil;

}else{
	echo '
<div class="col-12">
	<div class="card">
		<div class="card-header">
			<b style="font-family:timesnewrman;font-size:18px;"> <i class=" fa fa-sitemap"></i> ['.$mtime.'] - Aktivitas  @<a href="https://t.me/'.$bottele.'" target="_New"><u>'.$bottele.'</u></a>
		</div>
		<center>'.$fdtvcr.'<br>BELUM ADA DATA PEMBUATAN VOUCHER YANG BISA DI TAMPILKAN...</b></center>
	</div>
</div>
';
}

function rupiah($angka) {
	$hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}

?>
