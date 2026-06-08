<?php
/**
 * WhatsApp Gateway Functions for Payung.Net
 * Menu handlers for WA Bot interactions
 */

require_once __DIR__ . '/config.php';

/**
 * Menu Utama
 */
function waMenuUtama() {
    $config = waGetConfig();
    $brand = $config['brand_name'];
    
    $msg  = "🌐 *Selamat Datang di Layanan $brand*\n\n";
    $msg .= "Silahkan Pilih Menu berikut:\n\n";
    $msg .= "1️⃣ *Lihat Dashboard*\n";
    $msg .= "    _Info voucher, hotspot aktif & PPPoE_\n\n";
    $msg .= "2️⃣ *Generate Hotspot*\n";
    $msg .= "    _Generate voucher dari preset_\n\n";
    $msg .= "3️⃣ *Add PPPoE*\n";
    $msg .= "    _Tambah akun PPPoE baru_\n\n";
    $msg .= "4️⃣ *Print Voucher*\n";
    $msg .= "    _Lihat voucher yang sudah digenerate_\n\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Ketik *angka* (1-4) untuk memilih menu\n";
    $msg .= "Ketik *menu* untuk kembali ke menu utama\n";
    
    return $msg;
}

/**
 * 1. Dashboard
 */
function waDashboard($API) {
    $config = waGetConfig();
    $brand = $config['brand_name'];
    
    // Hotspot data
    $countUsers = $API->comm("/ip/hotspot/user/print", ["count-only" => ""]);
    $countActive = $API->comm("/ip/hotspot/active/print", ["count-only" => ""]);
    
    // PPPoE data
    $countSecrets = count($API->comm("/ppp/secret/print") ?: []);
    $pppActive = $API->comm("/ppp/active/print");
    if (!is_array($pppActive)) $pppActive = [];
    $countPPPActive = count($pppActive);
    $countPPPNonActive = $countSecrets - $countPPPActive;
    
    // System info
    $resource = $API->comm("/system/resource/print");
    $identity = $API->comm("/system/identity/print");
    $routerName = isset($identity[0]['name']) ? $identity[0]['name'] : 'Unknown';
    
    $msg  = "📊 *DASHBOARD $brand*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    
    $msg .= "🖥️ *Router:* $routerName\n";
    if (isset($resource[0])) {
        $msg .= "⏱️ *Uptime:* " . $resource[0]['uptime'] . "\n";
        $msg .= "💻 *CPU Load:* " . $resource[0]['cpu-load'] . "%\n\n";
    }
    
    $msg .= "📶 *HOTSPOT*\n";
    $msg .= "├ Total Voucher: *$countUsers*\n";
    $msg .= "└ Hotspot Aktif: *$countActive*\n\n";
    
    $msg .= "🔌 *PPPoE*\n";
    $msg .= "├ Total Pelanggan: *$countSecrets*\n";
    $msg .= "├ PPPoE Aktif: *$countPPPActive*\n";
    $msg .= "└ PPPoE Non-Aktif: *$countPPPNonActive*\n\n";
    
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Ketik *menu* untuk kembali\n";
    
    return $msg;
}

/**
 * 2. Generate Hotspot - Show presets
 */
function waGenerateHotspotMenu($API) {
    $presets = waGetPresets();
    $profiles = $API->comm("/ip/hotspot/user/profile/print");
    if (!is_array($profiles)) $profiles = [];
    
    $msg  = "🎫 *GENERATE HOTSPOT VOUCHER*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    
    if (count($presets) > 0) {
        $msg .= "*Preset Tersedia:*\n";
        foreach ($presets as $idx => $preset) {
            $no = $idx + 1;
            $msg .= "$no. {$preset['name']} (Profile: {$preset['profile']}, Qty: {$preset['qty']})\n";
        }
        $msg .= "\n";
    } else {
        $msg .= "_Belum ada preset._\n\n";
    }
    
    $msg .= "*Pilihan:*\n";
    if (count($presets) > 0) {
        $msg .= "• Ketik nomor preset (1-" . count($presets) . ") untuk generate\n";
    }
    $msg .= "• Ketik *tambah* untuk buat preset baru\n";
    $msg .= "• Ketik *menu* untuk kembali\n\n";
    
    $msg .= "*Profile yang tersedia:*\n";
    foreach ($profiles as $p) {
        if ($p['default'] != 'true') {
            $msg .= "• " . $p['name'] . "\n";
        }
    }
    
    return $msg;
}

/**
 * Generate hotspot voucher from preset
 */
function waDoGenerateHotspot($API, $presetIndex) {
    $presets = waGetPresets();
    if (!isset($presets[$presetIndex])) {
        return "❌ Preset tidak ditemukan.";
    }
    
    $preset = $presets[$presetIndex];
    $profile = $preset['profile'];
    $qty = intval($preset['qty']);
    $prefix = isset($preset['prefix']) ? $preset['prefix'] : 'VC';
    
    $generated = [];
    for ($i = 0; $i < $qty; $i++) {
        $username = $prefix . rand(100000, 999999);
        $password = rand(100000, 999999);
        
        $result = $API->comm("/ip/hotspot/user/add", [
            "name"     => $username,
            "password" => (string)$password,
            "profile"  => $profile,
            "comment"  => "WA-GEN " . date('d/m/Y H:i'),
        ]);
        
        if (!empty($result) && !isset($result['!trap'])) {
            $generated[] = ['user' => $username, 'pass' => $password];
        }
    }
    
    $msg  = "✅ *GENERATE BERHASIL*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Preset: *{$preset['name']}*\n";
    $msg .= "Profile: *$profile*\n";
    $msg .= "Berhasil: *" . count($generated) . "/$qty*\n\n";
    
    if (count($generated) > 0) {
        $msg .= "*Voucher:*\n";
        foreach ($generated as $v) {
            $msg .= "👤 {$v['user']} | 🔑 {$v['pass']}\n";
        }
    }
    
    $msg .= "\nKetik *menu* untuk kembali\n";
    return $msg;
}

/**
 * 3. Add PPPoE - Step by step
 */
function waAddPPPoEMenu($API) {
    $profiles = $API->comm("/ppp/profile/print");
    if (!is_array($profiles)) $profiles = [];
    
    $msg  = "👤 *ADD PPPoE SECRET*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    $msg .= "*Pilih Profile:*\n";
    
    $no = 1;
    foreach ($profiles as $p) {
        if ($p['default'] != 'true') {
            $msg .= "$no. *{$p['name']}*\n";
            $no++;
        }
    }
    
    $msg .= "\nKetik *nomor* profile yang dipilih\n";
    $msg .= "Ketik *menu* untuk kembali\n";
    
    return $msg;
}

function waAddPPPoEUsername($profileName, $API) {
    // Auto-generate recommendation
    $prefix = "";
    $profUpper = strtoupper($profileName);
    if (strpos($profUpper, 'BRONZE') !== false) $prefix = "1010";
    elseif (strpos($profUpper, 'SILVER') !== false) $prefix = "1020";
    elseif (strpos($profUpper, 'GOLD') !== false) $prefix = "1030";
    elseif (strpos($profUpper, 'DIAMOND') !== false) $prefix = "1040";
    
    $recommendation = "";
    if ($prefix != "") {
        $dtsecret = $API->comm("/ppp/secret/print");
        if (!is_array($dtsecret)) $dtsecret = [];
        
        $maxId = 0;
        foreach ($dtsecret as $sec) {
            $sname = $sec['name'];
            if (strlen($sname) == 8 && strpos($sname, $prefix) === 0) {
                $idNum = (int)substr($sname, 4, 4);
                if ($idNum > $maxId) $maxId = $idNum;
            }
        }
        
        $autoUsername = ($maxId == 0) ? $prefix . "1001" : $prefix . str_pad($maxId + 1, 4, "0", STR_PAD_LEFT);
        $recommendation = "\n💡 *Rekomendasi:* $autoUsername\n_Ketik username atau kirim rekomendasi di atas_\n";
    }
    
    $msg  = "📝 *STEP 2: Username*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Profile: *$profileName*\n";
    $msg .= $recommendation;
    $msg .= "\nKetik *username* untuk akun PPPoE baru:\n";
    $msg .= "_Username tidak boleh ada spasi_\n";
    
    return $msg;
}

function waAddPPPoEPassword($profileName, $username) {
    $msg  = "🔐 *STEP 3: Password*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Profile: *$profileName*\n";
    $msg .= "Username: *$username*\n\n";
    $msg .= "💡 *Rekomendasi:* $username\n";
    $msg .= "_Biasanya password sama dengan username_\n\n";
    $msg .= "Ketik *password* atau kirim *ok* untuk gunakan rekomendasi:\n";
    
    return $msg;
}

function waAddPPPoEComment($profileName, $username, $password) {
    $msg  = "💬 *STEP 4: Nama Pelanggan*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n";
    $msg .= "Profile: *$profileName*\n";
    $msg .= "Username: *$username*\n";
    $msg .= "Password: *$password*\n\n";
    $msg .= "Ketik *nama pelanggan* atau *skip* untuk lewati:\n";
    
    return $msg;
}

function waAddPPPoEConfirm($profileName, $username, $password, $comment) {
    $msg  = "✅ *KONFIRMASI ADD PPPoE*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    $msg .= "Profile  : *$profileName*\n";
    $msg .= "Username : *$username*\n";
    $msg .= "Password : *$password*\n";
    $msg .= "Pelanggan: *" . ($comment ?: '-') . "*\n\n";
    $msg .= "Ketik *ya* untuk menyimpan\n";
    $msg .= "Ketik *batal* untuk membatalkan\n";
    
    return $msg;
}

function waDoAddPPPoE($API, $profile, $username, $password, $comment = '') {
    $data = [
        "name"     => $username,
        "password" => $password,
        "service"  => "pppoe",
        "profile"  => $profile,
        "disabled" => "no",
    ];
    if (!empty($comment)) {
        $data["comment"] = $comment;
    }
    
    $result = $API->comm("/ppp/secret/add", $data);
    $cek = json_encode($result);
    
    if (strpos(strtolower($cek), '!trap')) {
        $error = str_replace(":", "\n", explode('"', $cek)[5]);
        return "❌ *GAGAL MENAMBAHKAN PPPoE*\n\nError: $error\n\nKetik *menu* untuk kembali";
    }
    
    $msg  = "✅ *PPPoE BERHASIL DITAMBAHKAN*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    $msg .= "Profile  : *$profile*\n";
    $msg .= "Username : *$username*\n";
    $msg .= "Password : *$password*\n";
    $msg .= "Pelanggan: *" . ($comment ?: '-') . "*\n\n";
    $msg .= "Ketik *menu* untuk kembali\n";
    
    return $msg;
}

/**
 * 4. Print Voucher - List vouchers by comment
 */
function waPrintVoucherMenu($API) {
    $users = $API->comm("/ip/hotspot/user/print");
    if (!is_array($users)) $users = [];
    
    // Group by comment
    $comments = [];
    foreach ($users as $u) {
        $comment = isset($u['comment']) ? $u['comment'] : 'Tanpa Komentar';
        if (!isset($comments[$comment])) {
            $comments[$comment] = 0;
        }
        $comments[$comment]++;
    }
    
    $msg  = "🖨️ *PRINT VOUCHER*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    
    if (count($comments) > 0) {
        $msg .= "*Voucher berdasarkan Comment:*\n";
        $no = 1;
        foreach ($comments as $comment => $count) {
            $msg .= "$no. *$comment* ($count voucher)\n";
            $no++;
        }
        $msg .= "\nKetik *nomor* untuk melihat voucher\n";
    } else {
        $msg .= "_Belum ada voucher._\n";
    }
    
    $msg .= "Ketik *menu* untuk kembali\n";
    
    return $msg;
}

function waPrintVoucherByComment($API, $commentFilter) {
    $users = $API->comm("/ip/hotspot/user/print");
    if (!is_array($users)) $users = [];
    
    $filtered = [];
    foreach ($users as $u) {
        $comment = isset($u['comment']) ? $u['comment'] : 'Tanpa Komentar';
        if ($comment == $commentFilter) {
            $filtered[] = $u;
        }
    }
    
    $msg  = "🎫 *VOUCHER: $commentFilter*\n";
    $msg .= "━━━━━━━━━━━━━━━━━━\n\n";
    
    $count = 0;
    foreach ($filtered as $v) {
        $count++;
        if ($count > 30) {
            $msg .= "\n_...dan " . (count($filtered) - 30) . " voucher lainnya_\n";
            break;
        }
        $msg .= "👤 *{$v['name']}*";
        if (isset($v['password'])) $msg .= " | 🔑 {$v['password']}";
        $msg .= "\n";
    }
    
    $msg .= "\nTotal: *" . count($filtered) . "* voucher\n";
    $msg .= "Ketik *menu* untuk kembali\n";
    
    return $msg;
}
