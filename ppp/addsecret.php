<?php
session_start();
error_reporting(0);
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {
    include "ppp/function.php";
    $getprofile = $API->comm("/ppp/profile/print");
    date_default_timezone_set('Asia/Jakarta');

    if (isset($_POST['save'])) {
        
        $name 		= (preg_replace('/\s+/', '-', $_POST['name']));
        $password 	= ($_POST['password']);
        $service 	= ($_POST['service']);
        $profile 	= ($_POST['profile']);
        $comment    = ($_POST['comment']);
        
        // Pastikan semua field required tidak kosong
        if (empty($name) || empty($password) || empty($profile)) {
            echo "<script>alert('Data tidak lengkap: Name=" . $name . ", Password=" . $password . ", Profile=" . $profile . "');</script>";
            echo "<script>window.location='./?info=Gagal|Data tidak lengkap|Nama, password, dan profile harus diisi&session=" . $session . "'</script>";
            exit;
        }
        
        // Cek koneksi API
        if (!$API->connected) {
            echo "<script>alert('API tidak terhubung ke MikroTik');</script>";
            echo "<script>window.location='./?info=Gagal|Tidak dapat terhubung ke MikroTik&session=" . $session . "'</script>";
            exit;
        }
        
        // Siapkan data untuk dikirim, hanya masukkan field yang tidak kosong
        $dataToSend = array(
            "name" 				=> $name,
            "password" 			=> $password,
            "service" 			=> $service,
            "profile" 			=> $profile,
            "disabled"			=> "no"
        );
        
        // Tambahkan local-address hanya jika tidak kosong dan valid IP
        if (!empty($_POST['locadd']) && filter_var($_POST['locadd'], FILTER_VALIDATE_IP)) {
            $dataToSend["local-address"] = $_POST['locadd'];
        }
        
        // Tambahkan remote-address hanya jika tidak kosong dan valid IP
        if (!empty($_POST['remadd']) && filter_var($_POST['remadd'], FILTER_VALIDATE_IP)) {
            $dataToSend["remote-address"] = $_POST['remadd'];
        }
        
        // Tambahkan comment hanya jika tidak kosong
        if (!empty($comment)) {
            $dataToSend["comment"] = $comment;
        }
        
        $addppp	= $API->comm("/ppp/secret/add", $dataToSend);
        
        $cek = json_encode($addppp);
        
        if (strpos(strtolower($cek), '!trap')) {
            $text	= str_replace(":","\n",explode('"',$cek)[5])."\n";
            echo "<script>window.location='./?info=Gagal|".$text."|Silahkan di cek ulang&session=" . $session . "'</script>";
        } elseif (empty($addppp) || $addppp === false) {
            echo "<script>window.location='./?info=Gagal|Tidak dapat terhubung ke MikroTik atau response kosong&session=" . $session . "'</script>";
        } else {
            echo "<script>window.location='./?ppp=secrets&profile=all&session=" . $session . "&info=Berhasil|User " . $name . " berhasil ditambahkan'</script>";
        }
    }
    
    // Set default values
    $locadd = "";
    $remadd = "";
    $harga = 0;
    $selectedProfile = "";
    
    if (isset($_GET['idprof'])) {
        $dtprofile	= $API->comm("/ppp/profile/print",["?.id"=>$_GET['idprof'],]);
        $selectedProfile = $_GET['nprof'];
        
        $locadd		= isset($dtprofile[0]['local-address']) ? $dtprofile[0]['local-address'] : '';

        // Menggunakan fungsi caridtharga() yang sama seperti di pppprofile.php
        $harga = caridtharga($_GET['idprof']);
        
        // Auto generate remote address berdasarkan local address
        if (!empty($locadd)) {
            $dtsecret	= $API->comm("/ppp/secret/print");
            if (count($dtsecret)<1) {
                $remadd	= explode(".",$locadd)[0].".".explode(".",$locadd)[1].".".explode(".",$locadd)[2].".2";
            }else{
                $lastip3	= explode(".",$locadd)[0].".".explode(".",$locadd)[1].".".explode(".",$locadd)[2];
                
                for ($x=2;$x<255;$x++) {
                    $tul=$lastip3.".".$x;
                    $dtsecretest = $API->comm("/ppp/secret/print",["?remote-address"=>"$tul",]);
                    if (count($dtsecretest)==0) {
                        break;
                    }
                }
                if ($x>254) {
                    echo "<script>window.location='./?info=Ip Full&session=" . $session . "'</script>";
                }
                $remadd= trim($tul);
            }
        }
    } else {
        $harga = 0;
    }
}
?>
<div class="row">
    <div class="col-8">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-plus"></i> Add PPP Secrets <small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div>
                        <a class="btn bg-warning" href="./?ppp=secrets&session=<?= $session; ?>"> <i
                                class="fa fa-close btn-mrg"></i> Close</a>
                        <button type="submit" name="save" class="btn bg-primary btn-mrg"><i
                                class="fa fa-save btn-mrg"></i> Save</button>
                    </div>
                    <table class="table">
                        <tr>
                            <td class="align-middle">Profile</td>
                            <td>
                                <select class="form-control" name="profile_select" onchange="location = this.value; loader()" title="Filter by Profile" required="1">
                                    <option value="">Pilih Profile PPPoE</option>
                                    <?php
                                    $TotalReg = count($getprofile);
                                    for ($i = 0; $i < $TotalReg; $i++) {
                                        if ($getprofile[$i]['default']=='false') {
                                            $selected = ($getprofile[$i]['name'] == $selectedProfile) ? 'selected' : '';
                                            echo "<option value='./?ppp=addsecret&session=$session&nprof=".$getprofile[$i]['name']."&idprof=".$getprofile[$i]['.id']."' $selected>" . $getprofile[$i]['name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="profile" value="<?= $selectedProfile ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Price</td>
                            <td>
                            <input class="form-control" type="hidden" autocomplete="off" name="harga" value="<?=$harga?>">
                            <input class="form-control" type="text" autocomplete="off" name="mhrg" value="<?php echo rupiah($harga); ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Local Address</td>
                            <td>
                                <input class="form-control" type="text" autocomplete="off" name="locadd" value="<?=$locadd ?>" placeholder="Kosongkan jika tidak menggunakan static IP">
                                <!--<small class="text-muted">Kosongkan jika tidak menggunakan static local address</small>-->
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Remote Address</td>
                            <td>
                                <input class="form-control" type="text" autocomplete="off" name="remadd" value="<?=$remadd?>" placeholder="Kosongkan jika tidak menggunakan static IP">
                                <!--<small class="text-muted">Kosongkan jika tidak menggunakan static remote address</small>-->
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">User Name</td>
                            <td><input class="form-control" type="text" onchange="remSpace();" autocomplete="off"
                                    name="name" value="" required="1" autofocus></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Password</td>
                            <td><input class="form-control" type="text" size="4" autocomplete="off" name="password" required="1">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Comment</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="comment" placeholder="">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Service</td>
                            <td>
                                <select class="form-control" name="service" required="1">
                                    <option value="any">any</option>
                                    <option value="async">async</option>
                                    <option value="l2tp">l2tp</option>
                                    <option value="ovpn">ovpn</option>
                                    <option value="pppoe" selected>pppoe</option>
                                    <option value="pptp">pptp</option>
                                    <option value="sstp">sstp</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Interval</td>
                            <td valign="middle"><input type="number" size="4" autocomplete="off" placeholder="1" value="1" name="interva" style="width:70px;font-size:16px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;" required="1"> 
                                <select  name="msat" style="width:200px;font-size:15px;font-weight:bold;border-radius:5px;text-align:right;padding:2px;font-family:times;">
                                    <option value="1" style='text-align:left;'> Hari &nbsp&nbsp </option>
                                    <option value="2" style='text-align:left;'> Minggu &nbsp&nbsp </option>
                                    <option value="3" style='text-align:left;'> Bulan &nbsp&nbsp </option>
                                    <option value="4" style='text-align:left;' selected > Bulanan &nbsp&nbsp&nbsp </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">No WhatsApp / Id Telegram</td>
                            <td><input class="form-control" type="number" autocomplete="off" name="idwatele" value="" placeholder="Opsional">
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Nama Pelanggan</td>
                            <td><input class="form-control" type="text" autocomplete="off" name="npelanggan" value="" required>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">Notifikasi</td>
                            <td>
                                <div class="w-3">
                                    <select class="form-control" type="text" name="notif">
                                        <option value="1">On</option>
                                        <option value="2" selected>Off</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function remSpace() {
    var upName = document.getElementsByName("name")[0];
    var newUpName = upName.value.replace(/\s/g, "-");
    upName.value = newUpName;
    upName.focus();
}
</script>