<?php
$fcek='../notif/kirim.txt';
$flog="../notif/notifwa.log";
$cek=" Api-".explode('-',file_get_contents($fcek))[1];
$hasil="
<div class='col-12'>
	<div class='card'>
		<div class='card-header'>
			<h3 class='card-title'><i class='fa fa-whatsapp'></i> STATUS LOG CORE WHATSAPP &nbsp &nbsp &nbsp <i stle='font-family:times;'> [ ".explode('/',$flog)[2]." ] </i> &nbsp ".date('d-M-Y H:i:s')."</h3>
		</div>
		<div class='card-body'>
			<div class='overflow box-bordered mr-t-10' style='max-height: 150vh;'>
				<table id='dataTable' class='table table-bordered table-hover text-nowrap' style='width:650px;'>";
				
					if (!file_exists($flog)) {
						$hasil .= "<caption style='font-size:20px;'>File ".$flog." Tidak Ditemukan / belum tersedia.</caption>";
					}else{
						$misi= explode("#",file_get_contents($flog));
						$nowa= explode("|",file_get_contents('../notif/setup.set'))[0];
						$no=count($misi);
						$hasil .="<tr><th colspan='3'>Log WhatsApp $nowa </th></tr>";
						$hasil .="<tr><th align='right'> No. </th><th> Tanggal transaksi</th><th> Status </th><th> Nomor </th><th> Pesan </th></tr>";
						for ($a=count($misi)-2;$a>-1;$a--) {
							$no--;
							if (explode('|-|',$misi[$a])[2]=='2') {
								if (strpos(explode('|-|',$misi[$a])[3],'kwid')!==false) {
									if (strpos(explode('|-|',$misi[$a])[3],'webhook_type')!==false) {
										$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
										$hasil .="
										<tr>
											<td align='right' valign='top'>".$no.".</td>
											<td  valign='top'>".explode('|-|',$misi[$a])[0]."</td>
											<td valign='top'>".$dtjson['status']."</td>
											<td valign='top'><a href='?id=wawebhook&notuj=".$dtjson['payload']['phone_number']."' title='Kirim pesan'>".$dtjson['payload']['phone_number']."</a></td>
											<td valign='top'>".str_replace("\n","<br>",str_replace('*','',$dtjson['payload']['message']))."</td>
										</tr>";
									}else{
										$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
										$hasil .="
										<tr>
											<td align='right' valign='top'>".$no.".</td>
											<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
											<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
											<td colspan='2' valign='top'>".$dtjson['message']."</td>
										</tr>";
									}
								}else{
									$dtjson=json_decode(explode('|-|',$misi[$a])[3],true);
									$mket="";
									if ($dtjson['payload']['from_me']=='1') {
										$mket=" &nbsp -=> Tidak diproses <=-";
									}else{
										if (substr($dtjson['payload']['text'],0,1)<>'/') {
											$mket=" &nbsp -=> Diabaikan <=-";
										}
									}
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".$dtjson['payload']['sender']."' title='Kirim pesan'>".$dtjson['payload']['sender']."</a></td>
										<td valign='top'>".str_replace('*','',$dtjson['payload']['text'])." ".$mket."</td>
									</tr>";
								}
							}else{
//parshing data mpwa
								$cekpesan=explode('|-|',$misi[$a])[3];
								if (explode('|-|',$misi[$a])[1]=='recive') {
									$json=json_decode($cekpesan,true);
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".explode('@',$json['from'])[0]."' title='Kirim pesan'>".explode('@',$json['from'])[0]."</a></td>
										<td valign='top'>".$json['message']."</td>
									</tr>";
								}elseif (explode('|-|',$misi[$a])[1]=='report') {
									$hasil .="
									<tr>
										<td align='right' valign='top'>".$no.".</td>
										<td valign='top'>".explode('|-|',$misi[$a])[0]."</td>
										<td valign='top'>".explode('|-|',$misi[$a])[1]."</td>
										<td valign='top'><a href='?id=wawebhook&notuj=".explode('|-|',$misi[$a])[4]."' title='Kirim pesan'>".explode('|-|',$misi[$a])[4]."</a></td>
										<td valign='top'>".str_replace("*","",explode('|-|',$misi[$a])[5])."</td>
									</tr>";
								}elseif (explode('|-|',$misi[$a])[1]=='error') {

								}else{
									
								}
							}
						}
					}
				$hasil .=  
				"</table>
			</div>	
		</div>
	</div>
</div>";
	print $hasil;
?>
