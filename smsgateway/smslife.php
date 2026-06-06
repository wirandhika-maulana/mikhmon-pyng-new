<?php
echo '<div class="card-body">
	<h3>Total : '.bcore("hitung").' Pesan.</h3>
	<div class="overflow box-bordered" style="max-height: 65vh">
		<table id="dataTable" class="table table-bordered table-hover text-nowrap">
			<thead>
				<tr>
					<th style="width:5%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> No. </th>
					<th style="width:12%;" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Tanggal </th>
					<th style="width:12%;" align="right" class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Nomor </th>
					<th class="pointer" title="Click to sort"><i class="fa fa-sort"></i> Pesan </th>
				</tr>
			</thead>
			<tbody>
				'.bcore("list").'
			</tbody>
		</table>
	</div>
</div>';

function bcore($cari) {
	
	$ftarget="https://mikhmon.mimoassist.online/telerivet/bacacore.php";
	$hasil=explode("#",urldecode(file_get_contents($ftarget)));
	$cek=explode("&",$hasil[0]);
	for ($x=0;$x<count($cek);$x++) {
		$tulis=$cek[$x];
//		file_put_contents("cek.log",$tulis."\n", FILE_APPEND | LOCK_EX);
	}
	$no=0;
	$tul="";
	if ($cari=="hitung") {
		$hasil=count($hasil)-1;
	}elseif ($cari=="list") {
		$tot=count($hasil)-2;
		for ($x=$tot; $x > -1 ;$x--) {
			$no++;
			$pecah=explode("&",$hasil[$x]);
			$tul .="<tr>
			<td align='right'>".$no.".</td>
			<td>".explode("secret",explode("&",$hasil[$x])[0])[0]."</td>
			<td align='right'><a href='./admin.php?id=websms&page=send&nomor=".explode("=",$pecah[5])[1]."'>".explode("=",$pecah[5])[1]."</a></td>
			<td>".explode("=",$pecah[37])[1]."</td>
			</tr>";
		}
		$hasil=$tul;
	}
	return $hasil;
}

?>