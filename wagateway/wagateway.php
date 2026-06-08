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

<style>
/* ============================================
   MODERN WA GATEWAY - PREMIUM DESIGN
   ============================================ */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.wa-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    width: 100%;
    margin: 0 auto;
}

/* Glassmorphism Card */
.wa-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9));
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 
        0 8px 32px rgba(0,0,0,0.08),
        0 2px 8px rgba(0,0,0,0.04),
        inset 0 1px 0 rgba(255,255,255,0.8);
    overflow: hidden;
    animation: cardSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    margin-bottom: 24px;
}

@keyframes cardSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Header */
.wa-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    padding: 24px 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    overflow: hidden;
}

.wa-header::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNykiLz48L3N2Zz4=');
    mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1), rgba(0,0,0,0));
}

.wa-header-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
    backdrop-filter: blur(10px);
    z-index: 1;
}

.wa-header-text {
    z-index: 1;
}

.wa-header-text h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
}

.wa-header-text small {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    font-weight: 500;
}

.wa-body {
    padding: 30px;
}

/* Modern Form Controls */
.modern-form-group {
    margin-bottom: 24px;
}

.modern-form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
}

.modern-input {
    width: 100%;
    padding: 14px 18px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    color: #1e293b;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

.modern-input:focus {
    outline: none;
    border-color: #10b981;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}

.modern-help {
    margin-top: 6px;
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Buttons */
.btn-modern {
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
}

.btn-modern-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
}

.btn-modern-test {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.btn-modern-test:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
}

/* Toggle Switch */
.modern-switch-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 24px;
}

.modern-switch-wrapper .text-part {
    display: flex;
    flex-direction: column;
}

.modern-switch-wrapper .title {
    font-size: 15px;
    font-weight: 600;
    color: #1e293b;
}

.modern-switch-wrapper .desc {
    font-size: 13px;
    color: #64748b;
}

.switch {
  position: relative;
  display: inline-block;
  width: 54px;
  height: 28px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #cbd5e1;
  transition: .4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
input:checked + .slider { background-color: #10b981; }
input:checked + .slider:before { transform: translateX(26px); }

/* Section Separator */
.wa-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
    margin: 30px 0;
}

.wa-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.wa-section-title i {
    color: #10b981;
}

/* Webhook copy box */
.webhook-box {
    background: #1e293b;
    padding: 16px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    border: 1px solid #334155;
}
.webhook-url {
    flex: 1;
    color: #a7f3d0;
    font-family: monospace;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.btn-copy {
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
}
.btn-copy:hover {
    background: #10b981;
}
</style>

<div class="wa-container">
    <div class="row">
        <!-- Main Settings Column -->
        <div class="col-12 col-md-8">
            <div class="wa-card">
                <div class="wa-header">
                    <div class="wa-header-icon">
                        <i class="fa fa-whatsapp"></i>
                    </div>
                    <div class="wa-header-text">
                        <h3>WhatsApp Gateway</h3>
                        <small>Konfigurasi koneksi MPWA Bot Engine</small>
                    </div>
                </div>
                
                <div class="wa-body">
                    <form method="post" action="">
                        <!-- Status Toggle -->
                        <div class="modern-switch-wrapper">
                            <div class="text-part">
                                <span class="title">Status Gateway</span>
                                <span class="desc">Aktifkan untuk merespon pesan otomatis</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" name="enabled" <?= $config['enabled'] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>
                        
                        <div class="modern-form-group">
                            <label>Brand Name</label>
                            <input type="text" class="modern-input" name="brand_name" value="<?= htmlspecialchars($config['brand_name']) ?>" required placeholder="Misal: Payung.Net">
                            <div class="modern-help"><i class="fa fa-info-circle"></i> Nama layanan yang akan tampil di pesan.</div>
                        </div>

                        <div class="modern-form-group">
                            <label>Admin Number</label>
                            <input type="text" class="modern-input" name="admin_number" value="<?= htmlspecialchars($config['admin_number']) ?>" placeholder="628xxxx">
                            <div class="modern-help"><i class="fa fa-shield"></i> Hanya nomor ini yang memiliki akses bot.</div>
                        </div>

                        <div class="wa-divider"></div>
                        <div class="wa-section-title"><i class="fa fa-server"></i> MPWA Connection API</div>
                        
                        <div class="modern-form-group">
                            <label>API URL</label>
                            <input type="url" class="modern-input" name="api_url" value="<?= htmlspecialchars($config['api_url']) ?>" required placeholder="https://api.wiran.my.id">
                        </div>

                        <div class="modern-form-group">
                            <label>API Key</label>
                            <input type="password" class="modern-input" name="api_key" value="<?= htmlspecialchars($config['api_key']) ?>" required placeholder="••••••••••••••">
                        </div>

                        <div class="modern-form-group">
                            <label>Device Number (Sender)</label>
                            <input type="text" class="modern-input" name="device" value="<?= htmlspecialchars($config['device']) ?>" placeholder="628xxxx" required>
                            <div class="modern-help"><i class="fa fa-mobile-phone"></i> Nomor WhatsApp bot (pengirim pesan).</div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" name="save_config" class="btn-modern btn-modern-primary">
                                <i class="fa fa-save"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Column -->
        <div class="col-12 col-md-4">
            <!-- Webhook Card -->
            <div class="wa-card">
                <div class="wa-header" style="background: linear-gradient(135deg, #6366f1, #4f46e5); padding: 20px;">
                    <div class="wa-header-icon" style="width: 40px; height: 40px; font-size: 20px;">
                        <i class="fa fa-link"></i>
                    </div>
                    <div class="wa-header-text">
                        <h3 style="font-size: 17px;">Webhook Router</h3>
                    </div>
                </div>
                <div class="wa-body" style="padding: 24px;">
                    <p style="font-size: 14px; color: #475569; margin-bottom: 12px; font-weight: 500;">
                        Copy URL di bawah ini ke pengaturan Webhook MPWA:
                    </p>
                    <div class="webhook-box">
                        <div class="webhook-url" id="webhookText"><?= $webhookUrl ?></div>
                        <button class="btn-copy" type="button" onclick="copyWebhook()">Copy</button>
                    </div>
                    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 12px; border-radius: 10px; font-size: 12px; font-weight: 600; display: flex; gap: 8px;">
                        <i class="fa fa-exclamation-triangle" style="margin-top: 2px;"></i>
                        <span>Pastikan server Mikhmon bisa diakses via internet public (IP Public/Domain).</span>
                    </div>
                </div>
            </div>
            
            <!-- Test Connection Card -->
            <div class="wa-card">
                <div class="wa-header" style="background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 20px;">
                    <div class="wa-header-icon" style="width: 40px; height: 40px; font-size: 20px;">
                        <i class="fa fa-paper-plane"></i>
                    </div>
                    <div class="wa-header-text">
                        <h3 style="font-size: 17px;">Test Connection</h3>
                    </div>
                </div>
                <div class="wa-body" style="padding: 24px;">
                    <form method="post" action="">
                        <div class="modern-form-group" style="margin-bottom: 16px;">
                            <label>Nomor Tujuan Test</label>
                            <input type="text" class="modern-input" name="test_number" placeholder="628xxxx" required>
                        </div>
                        <button type="submit" name="test_msg" class="btn-modern btn-modern-test">
                            <i class="fa fa-send"></i> Send Test Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyWebhook() {
    var text = document.getElementById("webhookText").innerText;
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    alert("Webhook URL disalin: " + text);
}
</script>
