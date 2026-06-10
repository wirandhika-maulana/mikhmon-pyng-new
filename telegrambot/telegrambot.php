<?php
/**
 * Telegram Bot Settings UI
 * Integrates with Mikhmon Web UI
 */
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
    exit;
}

require_once __DIR__ . '/config.php';

// Handle Form Submission
if (isset($_POST['save_tg_config'])) {
    $newConfig = [
        'bot_token'   => $_POST['bot_token'],
        'admin_ids'   => $_POST['admin_ids'],
        'session'     => $_POST['session'],
        'brand_name'  => $_POST['brand_name'],
        'enabled'     => isset($_POST['enabled']),
        'webhook_url' => $_POST['webhook_url']
    ];
    
    tgSaveConfig($newConfig);
    
    // Attempt to set webhook if enabled
    if (isset($_POST['enabled']) && !empty($_POST['webhook_url']) && !empty($_POST['bot_token'])) {
        $whResult = tgSetWebhook($_POST['webhook_url']);
        if (isset($whResult['ok']) && $whResult['ok']) {
            echo "<script>window.location='./?telegrambot=settings&session=" . $session . "&info=Berhasil|Pengaturan disimpan dan Webhook berhasil diset!'</script>";
        } else {
            $desc = isset($whResult['description']) ? $whResult['description'] : 'Unknown error';
            echo "<script>window.location='./?telegrambot=settings&session=" . $session . "&info=Gagal|Pengaturan disimpan tapi Webhook GAGAL diset|" . addslashes($desc) . "'</script>";
        }
    } else {
        tgDeleteWebhook();
        echo "<script>window.location='./?telegrambot=settings&session=" . $session . "&info=Berhasil|Pengaturan Telegram Bot berhasil disimpan!'</script>";
    }
    exit;
}

$config = tgGetConfig();

// Detect Webhook URL candidate based on current host
$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/');
$suggestedWebhook = $scheme . '://' . $host . $uri . '/telegrambot/handler.php';

// If config webhook is empty, use suggestion
if (empty($config['webhook_url'])) {
    $config['webhook_url'] = $suggestedWebhook;
}

// Get Webhook Info
$whInfo = tgGetWebhookInfo();
$whStatus = "Not Set";
$whStatusClass = "text-danger";

if (isset($whInfo['ok']) && $whInfo['ok']) {
    if (!empty($whInfo['result']['url'])) {
        $whStatus = "Active";
        $whStatusClass = "text-success";
        if (isset($whInfo['result']['last_error_message'])) {
            $whStatus = "Error: " . $whInfo['result']['last_error_message'];
            $whStatusClass = "text-danger";
        }
    }
}
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.tg-container {
    font-family: 'Inter', sans-serif;
    width: 100%;
    margin: 0 auto;
}

.tg-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9));
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    overflow: hidden;
}

.tg-header {
    background: linear-gradient(135deg, #0088cc 0%, #005580 100%);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: white;
}

.tg-header i {
    font-size: 20px;
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 12px;
}

.tg-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
}

.tg-header p {
    margin: 4px 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.tg-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #334155;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #fff;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #0088cc;
    box-shadow: 0 0 0 3px rgba(0,136,204,0.1);
}

.switch-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #0088cc;
}

input:checked + .slider:before {
    transform: translateX(24px);
}

.btn-primary {
    background: #0088cc;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #006699;
}

.alert-info {
    background: #e0f2fe;
    border-left: 4px solid #0ea5e9;
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 24px;
    font-size: 14px;
    color: #0369a1;
}

.alert-info strong {
    color: #0c4a6e;
}
</style>

<div class="tg-container">
    <div class="tg-card">
        <div class="tg-header">
            <i class="fa fa-telegram"></i>
            <div>
                <h3>Telegram Bot Settings</h3>
                <p>Konfigurasi Inline Keyboard Bot untuk Mikhmon</p>
            </div>
        </div>
        
        <div class="tg-body">
            <div class="alert-info">
                <strong>Cara Penggunaan:</strong><br>
                1. Buka <strong>@BotFather</strong> di Telegram, buat bot baru dan dapatkan Token.<br>
                2. Masukkan Token di bawah.<br>
                3. Pastikan URL Webhook bisa diakses dari internet (Public IP/Domain atau Tunneling).
            </div>
            
            <form method="post" action="">
                
                <div class="form-group switch-wrap" style="margin-bottom: 30px;">
                    <label class="switch">
                        <input type="checkbox" name="enabled" <?= $config['enabled'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span style="font-weight: 600; color: #334155;">Aktifkan Telegram Bot</span>
                </div>
                
                <div class="form-group">
                    <label>Bot Token dari @BotFather</label>
                    <input type="text" class="form-control" name="bot_token" value="<?= htmlspecialchars($config['bot_token']) ?>" placeholder="1234567890:AAHxxxxxxxxxxxxxxxxxxxxxx">
                </div>
                
                <div class="form-group">
                    <label>Admin User ID (Pisahkan dengan koma)</label>
                    <input type="text" class="form-control" name="admin_ids" value="<?= htmlspecialchars($config['admin_ids']) ?>" placeholder="123456789, 987654321">
                    <small style="color: #64748b; margin-top: 4px; display: block;">Bot hanya akan merespon pesan dari ID ini. Anda bisa dapatkan ID dari @userinfobot</small>
                </div>
                
                <div class="form-group">
                    <label>Webhook URL <span class="<?= $whStatusClass ?>" style="float:right; font-size: 12px;">Status: <?= $whStatus ?></span></label>
                    <input type="text" class="form-control" name="webhook_url" value="<?= htmlspecialchars($config['webhook_url']) ?>">
                    <small style="color: #64748b; margin-top: 4px; display: block;">Otomatis diisi, tapi pastikan ini adalah URL publik yang bisa diakses Telegram.</small>
                </div>
                
                <div class="form-group" style="display: flex; gap: 16px;">
                    <div style="flex: 1;">
                        <label>Nama Brand (Tampil di Bot)</label>
                        <input type="text" class="form-control" name="brand_name" value="<?= htmlspecialchars($config['brand_name']) ?>">
                    </div>
                    <div style="flex: 1;">
                        <label>Session Name (Router)</label>
                        <input type="text" class="form-control" name="session" value="<?= htmlspecialchars($config['session']) ?>" placeholder="Kosongkan untuk default session">
                    </div>
                </div>
                
                <div style="margin-top: 32px;">
                    <button type="submit" name="save_tg_config" class="btn-primary">
                        <i class="fa fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
