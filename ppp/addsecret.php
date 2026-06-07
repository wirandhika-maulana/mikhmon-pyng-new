<?php
session_start();
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {
    include "ppp/function.php";
    $getprofile = $API->comm("/ppp/profile/print");
    
    if (isset($_POST['save'])) {
        $name 		= (preg_replace('/\s+/', '', $_POST['name']));
        $password 	= ($_POST['password']);
        $profile 	= ($_POST['profile']);
        $comment    = ($_POST['comment']);
        $service    = "pppoe"; // Force to PPPoE
        
        // Pastikan semua field required tidak kosong
        if (empty($name) || empty($password) || empty($profile)) {
            echo "<script>window.location='./?info=Gagal|Data tidak lengkap|Nama, password, dan profile harus diisi&session=" . $session . "'</script>";
            exit;
        }
        
        // Cek koneksi API
        if (!$API->connected) {
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
    
    // Auto-Generate Logic
    $selectedProfile = "";
    $autoUsername = "";
    $hintMsg = "";
    $prefix = "";

    if (isset($_GET['idprof'])) {
        $selectedProfile = $_GET['nprof'];
        
        // Deteksi kata kunci pada nama profile
        $profUpper = strtoupper($selectedProfile);
        if (strpos($profUpper, 'BRONZE') !== false) {
            $prefix = "1010";
        } elseif (strpos($profUpper, 'SILVER') !== false) {
            $prefix = "1020";
        } elseif (strpos($profUpper, 'GOLD') !== false) {
            $prefix = "1030";
        } elseif (strpos($profUpper, 'DIAMOND') !== false) {
            $prefix = "1040";
        }
        
        if ($prefix != "") {
            $dtsecret = $API->comm("/ppp/secret/print");
            if (!is_array($dtsecret)) $dtsecret = [];
            
            $maxId = 0;
            foreach ($dtsecret as $sec) {
                $sname = $sec['name'];
                // Cari secret yang 8 karakter dan diawali prefix VLAN
                if (strlen($sname) == 8 && strpos($sname, $prefix) === 0) {
                    $idNum = (int)substr($sname, 4, 4);
                    if ($idNum > $maxId) {
                        $maxId = $idNum;
                    }
                }
            }
            
            if ($maxId == 0) {
                $autoUsername = $prefix . "1001"; // Default pertama jika kosong
            } else {
                $nextId = $maxId + 1;
                $autoUsername = $prefix . str_pad($nextId, 4, "0", STR_PAD_LEFT);
            }
            $hintMsg = "<span class='text-success'><i class='fa fa-check-circle'></i> Auto-Generate VLAN Prefix: <b>$prefix</b>. Rekomendasi Akun: <b>$autoUsername</b></span>";
        } else {
            $hintMsg = "<span class='text-warning'><i class='fa fa-info-circle'></i> Profile tidak mengandung kata BRONZE, SILVER, GOLD, atau DIAMOND.</span>";
        }
    }
}
?>
<div class="row">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card box-bordered">
            <div class="card-header align-middle">
                <h3><i class="fa fa-user-plus"></i> Add PPP Secret 
                <small id="loader" style="display: none;"><i> <i class='fa fa-circle-o-notch fa-spin'></i> Processing... </i></small></h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" method="post" action="">
                    <div class="row">
                        <!-- Profile Selection -->
                        <!-- Profile Selection -->
                        <div class="col-12 pd-b-15">
                            <label class="font-weight-bold">Quick Profile <span class="text-danger">*</span></label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;">
                                <?php
                                $TotalReg = count($getprofile);
                                for ($i = 0; $i < $TotalReg; $i++) {
                                    if ($getprofile[$i]['default']=='false') {
                                        $pname = $getprofile[$i]['name'];
                                        $pid = $getprofile[$i]['.id'];
                                        $isSelected = ($pname == $selectedProfile);
                                        $bg = $isSelected ? 'linear-gradient(135deg, #6f42c1, #e83e8c)' : '#f8f9fa';
                                        $color = $isSelected ? '#fff' : '#333';
                                        $border = $isSelected ? 'none' : '1px solid #ddd';
                                        $shadow = $isSelected ? '0 4px 8px rgba(111,66,193,0.3)' : 'none';
                                        
                                        echo "<button type='button' class='btn' onclick=\"location='./?ppp=addsecret&session=$session&nprof=$pname&idprof=$pid'; loader();\" style=\"background: $bg; color: $color; border: $border; border-radius: 8px; padding: 10px; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; box-shadow: $shadow; width: 100%;\" onmouseover=\"this.style.transform='scale(1.05)'\" onmouseout=\"this.style.transform='scale(1)'\">
                                            <i class='fa ".($isSelected ? "fa-check-circle" : "fa-tag")."'></i> $pname
                                        </button>";
                                    }
                                }
                                ?>
                            </div>
                            <input type="hidden" name="profile" value="<?= $selectedProfile ?>" required="1">
                            <?php if($selectedProfile != ""): ?>
                                <div class="mr-t-10"><small><?= $hintMsg ?></small></div>
                            <?php else: ?>
                                <div class="mr-t-10"><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> Silakan klik salah satu profile di atas terlebih dahulu.</small></div>
                            <?php endif; ?>
                        </div>

                        <!-- Username Input -->
                        <div class="col-6 pd-b-15">
                            <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="uname" autocomplete="off" name="name" value="<?= $autoUsername ?>" required="1" style="height:45px; font-size:18px; font-weight:bold; letter-spacing:1px;" placeholder="Username" <?= ($selectedProfile=="")?"disabled":"" ?> autofocus>
                        </div>

                        <!-- Password Input -->
                        <div class="col-6 pd-b-15">
                            <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="upass" autocomplete="off" name="password" value="<?= $autoUsername ?>" required="1" style="height:45px; font-size:18px; font-weight:bold; letter-spacing:1px;" placeholder="Password" <?= ($selectedProfile=="")?"disabled":"" ?>>
                        </div>

                        <!-- Comment Input -->
                        <div class="col-12 pd-b-15">
                            <label class="font-weight-bold">Comment / Nama Pelanggan</label>
                            <input class="form-control" type="text" autocomplete="off" name="comment" placeholder="Keterangan tambahan (Opsional)" style="height:45px; font-size:16px;" <?= ($selectedProfile=="")?"disabled":"" ?>>
                        </div>
                        
                    </div>
                    <div class="mr-t-15 pd-t-10" style="border-top:1px solid #eee;">
                        <button type="submit" name="save" class="btn bg-primary btn-mrg" <?= ($selectedProfile=="")?"disabled":"" ?>><i class="fa fa-save mr-1"></i> Save Secret</button>
                        <a class="btn bg-warning btn-mrg" href="./?ppp=secrets&session=<?= $session; ?>"> <i class="fa fa-close mr-1"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    // Mirror username ke password agar selalu sama & hindari spasi
    $("#uname").on("input", function() {
        var cleanVal = $(this).val().replace(/\s/g, "");
        $(this).val(cleanVal);
        $("#upass").val(cleanVal);
    });
});
</script>