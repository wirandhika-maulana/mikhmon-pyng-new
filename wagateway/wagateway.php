<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
    exit;
}

require_once __DIR__ . '/config.php';

$session = $_GET['session'];

// Handle form submit
if (isset($_POST['save_config'])) {
    $config = [
        'api_url'      => $_POST['api_url'],
        'api_key'      => $_POST['api_key'],
        'device'       => $_POST['device'],
        'admin_number' => $_POST['admin_number'],
        'brand_name'   => $_POST['brand_name'],
        'enabled'      => isset($_POST['enabled']) ? true : false,
    ];
    waSaveConfig($config);
    echo "<script>window.location='./?wagateway=settings&session=$session&info=Berhasil|Konfigurasi WA Gateway disimpan'</script>";
    exit;
}

if (isset($_POST['test_msg'])) {
    $res = waSendMessage($_POST['test_number'], "🚀 Test pesan dari Mikhmon WA Gateway");
    if ($res['status']) {
        echo "<script>window.location='./?wagateway=settings&session=$session&info=Berhasil|Pesan test berhasil dikirim'</script>";
    } else {
        $err = isset($res['message']) ? $res['message'] : 'Unknown error';
        echo "<script>window.location='./?wagateway=settings&session=$session&info=Gagal|Gagal mengirim pesan: $err'</script>";
    }
    exit;
}

// Load current config
$config = waGetConfig();

// Webhook URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$webhookUrl = $protocol . $domainName . dirname($_SERVER['PHP_SELF']) . '/wagateway/handler.php';
?>

<div class="row">
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-whatsapp"></i> WhatsApp Gateway Settings</h3>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Status Gateway</label>
                        <div class="col-sm-8">
                            <label class="switch">
                                <input type="checkbox" name="enabled" <?= $config['enabled'] ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                            <small class="form-text text-muted">Aktifkan untuk merespon pesan WhatsApp masuk</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Brand Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="brand_name" value="<?= htmlspecialchars($config['brand_name']) ?>" required>
                            <small class="form-text text-muted">Nama yang akan tampil di pesan (Contoh: Payung.Net)</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Admin Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="admin_number" value="<?= htmlspecialchars($config['admin_number']) ?>" placeholder="628xxxx">
                            <small class="form-text text-muted">Hanya nomor ini yang bisa akses menu WA. Pisahkan dengan koma jika lebih dari satu.</small>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-4 mb-3">MPWA Connection Settings</h5>
                    
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">MPWA API URL</label>
                        <div class="col-sm-8">
                            <input type="url" class="form-control" name="api_url" value="<?= htmlspecialchars($config['api_url']) ?>" required>
                            <small class="form-text text-muted">Contoh: https://api.wiran.my.id</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">API Key</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" name="api_key" value="<?= htmlspecialchars($config['api_key']) ?>" required>
                            <small class="form-text text-muted">API Key dari dashboard MPWA</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Device Number (Sender)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="device" value="<?= htmlspecialchars($config['device']) ?>" placeholder="628xxxx" required>
                            <small class="form-text text-muted">Nomor bot yang sudah di-scan di MPWA</small>
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col-sm-12 text-right">
                            <button type="submit" name="save_config" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fa fa-link"></i> Webhook Info</h3>
            </div>
            <div class="card-body">
                <p>Copy URL di bawah ini dan paste di pengaturan Webhook dashboard MPWA Anda:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?= $webhookUrl ?>" id="webhookUrlInput" readonly>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyWebhook()">Copy</button>
                    </div>
                </div>
                <small class="text-danger">* Pastikan server Mikhmon bisa diakses dari internet (public IP/domain).</small>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fa fa-paper-plane"></i> Test Connection</h3>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label>Kirim pesan test ke nomor:</label>
                        <input type="text" class="form-control mb-2" name="test_number" placeholder="628xxxx" required>
                        <button type="submit" name="test_msg" class="btn btn-success btn-block"><i class="fa fa-send"></i> Send Test Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function copyWebhook() {
    var copyText = document.getElementById("webhookUrlInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Webhook URL disalin: " + copyText.value);
}
</script>

<style>
/* Switch toggle CSS */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}
.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
input:checked + .slider {
  background-color: #2196F3;
}
input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}
input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
.slider.round {
  border-radius: 34px;
}
.slider.round:before {
  border-radius: 50%;
}
</style>
