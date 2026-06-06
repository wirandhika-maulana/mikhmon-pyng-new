<!DOCTYPE html>
<html>
<head>
<title id="title">Buat Voucher</title>
<!-- $(if refresh-timeout) -->
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
<meta http-equiv="refresh" content="$(refresh-timeout-secs)">
<!--$(endif)-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<link rel="icon" href="favicon2.png" />
<link rel="stylesheet" href="assets/css/main.css" />
<style>
iframe  {float:left; height:22px; width:100%;}
</style>
<script type="text/javascript" src="data.js"></script>

<style type="text/css">
    .card {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        width: 80%;
        border-radius: 5px;
    }
</style>
</head>
<article id="elements">
    
        <!-- New Form for Voucher -->
        <?php
        date_default_timezone_set('Asia/Jakarta');
        $date = date('dHis');
        if(isset($_POST['profile'])){
            $profile = $_POST['profile'];
            $saldo = $_POST['saldo'];
            $mac = $_POST['mac'];
        }
        ?>
        
    <center>
    <div class="card">
        <h2 class="major">Form Order</h2>
        <p>&nbsp;KODE VOUCHER ANDA&nbsp;</p>
        <div id="user" style="text-align: center; font-weight:bold; margin-top:24px;">&nbsp;&nbsp;<h2>vcr-<?php echo $date; ?></h2></div>
        <form action="https://mimoassist.homes/beli/bayar.php" method="POST"> 
            <input class="form-control" type="text" placeholder="Nama Anda" required="" name="nama">
            <input class="form-control" type="text" placeholder="Email Anda" required="" name="email">
            <input class="form-control" type="text" placeholder="No Whatsapp" required="" name="phone">
            <input type="hidden" value="<?php echo $date; ?>" name="vc">
            <input type="hidden" value="<?php echo $profile; ?>" name="profile">
            <input type="hidden" value="<?php echo $saldo; ?>" name="saldo">
            <input type="hidden" value="<?php echo $mac; ?>" name="mac">
            <p style="color:red; text-align: center;">Kode voucher berhasil dibuat, silahkan melakukan pembayaran agar voucHer anda bisa digunakan.</p>
            <select name="rek" style="width: 100%;">
                <option value="QRIS" selected>Metode Pembayaran</option> 
                <option value="QRIS">QRIS by ShopeePay</option> 
                <option value="DANA">DANA</option>
                <option value="OVO">OVO</option>
            </select>
            <br>
            <center><button class="btn btn-success" type="submit" name="kirim">Lanjutkan Pembayaran</button></center>
        </form>
        <!-- End of New Form -->

    </div>
    </center>
</article>
</html>