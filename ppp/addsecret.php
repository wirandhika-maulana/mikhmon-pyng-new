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
            $hintMsg = "<span class='hint-success'><i class='fa fa-check-circle'></i> Auto-Generate Prefix: <b>$prefix</b> — Rekomendasi: <b>$autoUsername</b></span>";
        } else {
            $hintMsg = "<span class='hint-warning'><i class='fa fa-info-circle'></i> Profile tidak mengandung kata BRONZE, SILVER, GOLD, atau DIAMOND.</span>";
        }
    }
}
?>

<style>
/* ============================================
   MODERN ADD SECRET - PREMIUM DESIGN
   ============================================ */

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.secret-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    max-width: 720px;
    margin: 0 auto;
}

/* Glassmorphism Card */
.secret-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(248,250,252,0.9));
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 
        0 8px 32px rgba(0,0,0,0.08),
        0 2px 8px rgba(0,0,0,0.04),
        inset 0 1px 0 rgba(255,255,255,0.8);
    overflow: hidden;
    animation: cardSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes cardSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Card Header */
.secret-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.secret-header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
    backdrop-filter: blur(10px);
}

.secret-header-text h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.3px;
}

.secret-header-text small {
    color: rgba(255,255,255,0.75);
    font-size: 13px;
    font-weight: 400;
}

#loader {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: #fff;
    margin-left: auto;
}

/* Card Body */
.secret-body {
    padding: 28px;
}

/* Section Label */
.section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #667eea;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #667eea20, transparent);
}

/* Profile Grid */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 10px;
    margin-bottom: 8px;
}

.profile-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 14px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    text-decoration: none !important;
    font-family: 'Inter', sans-serif;
}

.profile-btn:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.profile-btn.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.35);
    transform: translateY(-2px);
}

.profile-btn .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.4);
    transform: scale(0);
    animation: rippleEffect 0.6s ease-out;
    pointer-events: none;
}

@keyframes rippleEffect {
    to { transform: scale(4); opacity: 0; }
}

/* Hint Messages */
.hint-box {
    margin-top: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    animation: hintFadeIn 0.3s ease;
}

@keyframes hintFadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.hint-success {
    color: #059669;
    font-weight: 500;
}

.hint-warning {
    color: #d97706;
    font-weight: 500;
}

.hint-box.success-box {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border: 1px solid #a7f3d0;
}

.hint-box.warning-box {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1px solid #fde68a;
}

.hint-box.required-box {
    background: linear-gradient(135deg, #fef2f2, #fee2e2);
    border: 1px solid #fecaca;
    color: #dc2626;
}

/* Modern Input Groups */
.input-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.input-row.full {
    grid-template-columns: 1fr;
}

.modern-input-group {
    position: relative;
}

.modern-input-group .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    z-index: 2;
    transition: all 0.3s ease;
}

.modern-input-group .input-icon.user-icon {
    background: linear-gradient(135deg, #667eea20, #764ba220);
    color: #667eea;
}

.modern-input-group .input-icon.pass-icon {
    background: linear-gradient(135deg, #f59e0b20, #ef444420);
    color: #f59e0b;
}

.modern-input-group .input-icon.name-icon {
    background: linear-gradient(135deg, #10b98120, #059669 20);
    color: #10b981;
}

.modern-input-group .modern-input {
    width: 100%;
    height: 56px;
    padding: 16px 16px 8px 62px;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    background: #fff;
    outline: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.5px;
}

.modern-input-group .modern-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modern-input-group .modern-input:focus + .floating-label,
.modern-input-group .modern-input:not(:placeholder-shown) + .floating-label {
    top: 8px;
    font-size: 10px;
    color: #667eea;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.modern-input-group .floating-label {
    position: absolute;
    left: 62px;
    top: 18px;
    font-size: 14px;
    color: #94a3b8;
    font-weight: 500;
    pointer-events: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-input-group .floating-label .required-star {
    color: #ef4444;
    margin-left: 2px;
}

/* Password Toggle */
.pass-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    background: #f1f5f9;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    transition: all 0.2s ease;
    z-index: 2;
}

.pass-toggle:hover {
    background: #e2e8f0;
    color: #334155;
}

/* Modern Input Disabled State */
.modern-input:disabled {
    background: #f8fafc;
    color: #cbd5e1;
    cursor: not-allowed;
    border-color: #f1f5f9;
}

.modern-input:disabled + .floating-label {
    color: #cbd5e1;
}

/* Divider */
.secret-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
    margin: 24px 0;
}

/* Action Buttons */
.secret-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-save:active {
    transform: translateY(0);
}

.btn-save:disabled {
    background: #cbd5e1;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
}

.btn-save .btn-icon {
    width: 32px;
    height: 32px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 24px;
    background: #fff;
    color: #64748b;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none !important;
}

.btn-cancel:hover {
    border-color: #f87171;
    color: #ef4444;
    background: #fef2f2;
    text-decoration: none;
}

/* Responsive */
@media (max-width: 640px) {
    .secret-body { padding: 20px; }
    .input-row { grid-template-columns: 1fr; }
    .profile-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
    .secret-actions { flex-direction: column; }
    .btn-save, .btn-cancel { width: 100%; justify-content: center; }
}

/* Dark theme support */
[data-theme="dark"] .secret-card,
.dark .secret-card {
    background: linear-gradient(135deg, rgba(30,41,59,0.95), rgba(15,23,42,0.9));
    border-color: rgba(255,255,255,0.1);
}
[data-theme="dark"] .modern-input,
.dark .modern-input {
    background: #1e293b;
    border-color: #334155;
    color: #e2e8f0;
}
[data-theme="dark"] .profile-btn,
.dark .profile-btn {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
</style>

<div class="secret-container">
    <div class="secret-card">
        <!-- Header -->
        <div class="secret-header">
            <div class="secret-header-icon">
                <i class="fa fa-user-plus"></i>
            </div>
            <div class="secret-header-text">
                <h3>Add PPP Secret</h3>
                <small>Tambah akun PPPoE pelanggan baru</small>
            </div>
            <small id="loader" style="display: none;"><i class='fa fa-circle-o-notch fa-spin'></i> Processing...</small>
        </div>

        <!-- Body -->
        <div class="secret-body">
            <form autocomplete="off" method="post" action="" id="secretForm">

                <!-- Profile Selection -->
                <div class="section-label">
                    <i class="fa fa-tag"></i> Pilih Profile PPPoE
                </div>
                <div class="profile-grid">
                    <?php
                    $TotalReg = count($getprofile);
                    for ($i = 0; $i < $TotalReg; $i++) {
                        if ($getprofile[$i]['default']=='false') {
                            $pname = $getprofile[$i]['name'];
                            $pid = $getprofile[$i]['.id'];
                            $isSelected = ($pname == $selectedProfile);
                            $activeClass = $isSelected ? 'active' : '';
                            $iconClass = $isSelected ? 'fa-check-circle' : 'fa-tag';
                            
                            echo "<button type='button' class='profile-btn $activeClass' onclick=\"selectProfile(this, '$pname', '$pid');\">
                                <i class='fa $iconClass'></i> $pname
                            </button>";
                        }
                    }
                    ?>
                </div>
                <input type="hidden" name="profile" id="profileInput" value="<?= $selectedProfile ?>" required="1">
                
                <?php if($selectedProfile != ""): ?>
                    <div class="hint-box <?= ($prefix != '') ? 'success-box' : 'warning-box' ?>">
                        <?= $hintMsg ?>
                    </div>
                <?php else: ?>
                    <div class="hint-box required-box">
                        <i class="fa fa-exclamation-triangle"></i> Silakan pilih salah satu profile di atas terlebih dahulu.
                    </div>
                <?php endif; ?>

                <div class="secret-divider"></div>

                <!-- Username & Password -->
                <div class="section-label">
                    <i class="fa fa-key"></i> Kredensial Akun
                </div>
                
                <div class="input-row">
                    <!-- Username -->
                    <div class="modern-input-group">
                        <div class="input-icon user-icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input class="modern-input" type="text" id="uname" autocomplete="off" name="name" value="<?= $autoUsername ?>" required="1" placeholder=" " <?= ($selectedProfile=="")?'disabled':'' ?> autofocus>
                        <label class="floating-label">Username <span class="required-star">*</span></label>
                    </div>

                    <!-- Password -->
                    <div class="modern-input-group">
                        <div class="input-icon pass-icon">
                            <i class="fa fa-lock"></i>
                        </div>
                        <input class="modern-input" type="password" id="upass" autocomplete="off" name="password" value="<?= $autoUsername ?>" required="1" placeholder=" " <?= ($selectedProfile=="")?'disabled':'' ?>>
                        <label class="floating-label">Password <span class="required-star">*</span></label>
                        <button type="button" class="pass-toggle" onclick="togglePassword()" title="Toggle Password">
                            <i class="fa fa-eye" id="passEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Nama Pelanggan -->
                <div class="input-row full">
                    <div class="modern-input-group">
                        <div class="input-icon name-icon">
                            <i class="fa fa-id-card"></i>
                        </div>
                        <input class="modern-input" type="text" autocomplete="off" name="comment" placeholder=" " style="font-weight: 500;" <?= ($selectedProfile=="")?'disabled':'' ?>>
                        <label class="floating-label">Nama Pelanggan</label>
                    </div>
                </div>

                <div class="secret-divider"></div>

                <!-- Actions -->
                <div class="secret-actions">
                    <button type="submit" name="save" class="btn-save" <?= ($selectedProfile=="")?'disabled':'' ?>>
                        <span class="btn-icon"><i class="fa fa-save"></i></span>
                        Simpan Secret
                    </button>
                    <a class="btn-cancel" href="./?ppp=secrets&session=<?= $session; ?>">
                        <i class="fa fa-arrow-left"></i> Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Mirror username ke password & hindari spasi
    $("#uname").on("input", function() {
        var cleanVal = $(this).val().replace(/\s/g, "");
        $(this).val(cleanVal);
        $("#upass").val(cleanVal);
    });
});

// Toggle password visibility
function togglePassword() {
    var passInput = document.getElementById('upass');
    var eyeIcon = document.getElementById('passEyeIcon');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeIcon.className = 'fa fa-eye-slash';
    } else {
        passInput.type = 'password';
        eyeIcon.className = 'fa fa-eye';
    }
}

// Profile selection with ripple
function selectProfile(btn, profileName, profileId) {
    // Ripple effect
    var rect = btn.getBoundingClientRect();
    var ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.width = ripple.style.height = Math.max(rect.width, rect.height) + 'px';
    ripple.style.left = '50%';
    ripple.style.top = '50%';
    ripple.style.marginLeft = -Math.max(rect.width, rect.height) / 2 + 'px';
    ripple.style.marginTop = -Math.max(rect.width, rect.height) / 2 + 'px';
    btn.appendChild(ripple);
    setTimeout(function() { ripple.remove(); }, 600);

    // Navigate
    loader();
    location = './?ppp=addsecret&session=<?= $session ?>&nprof=' + profileName + '&idprof=' + profileId;
}
</script>