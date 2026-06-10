<?php
/**
 * Telegram Bot Functions for Mikhmon
 * Logic and UI for bot commands
 */

require_once __DIR__ . '/config.php';

/**
 * Menu Utama
 */
function tgMenuUtama() {
    $config = tgGetConfig();
    $brand = $config['brand_name'];
    
    $text = "🌐 *Selamat Datang di {$brand}*\n\nSilakan pilih menu di bawah ini:";
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '📊 Dashboard', 'callback_data' => 'menu_dashboard'],
            ['text' => 'ℹ️ Info Router', 'callback_data' => 'menu_info']
        ],
        [
            ['text' => '👤 PPPoE', 'callback_data' => 'menu_pppoe'],
            ['text' => '🎫 Hotspot', 'callback_data' => 'menu_hotspot']
        ],
        [
            ['text' => '📋 IP Binding', 'callback_data' => 'menu_ipbinding']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Menu Dashboard
 */
function tgDashboard($API) {
    $config = tgGetConfig();
    $brand = $config['brand_name'];
    
    // Hotspot data
    $countUsers = $API->comm("/ip/hotspot/user/print", ["count-only" => ""]);
    $countActive = $API->comm("/ip/hotspot/active/print", ["count-only" => ""]);
    
    // PPPoE data
    $countSecrets = count($API->comm("/ppp/secret/print") ?: []);
    $pppActive = $API->comm("/ppp/active/print");
    if (!is_array($pppActive)) $pppActive = [];
    $countPPPActive = count($pppActive);
    
    $text  = "📊 *DASHBOARD {$brand}*\n";
    $text .= "━━━━━━━━━━━━━━━━━━\n\n";
    
    $text .= "📶 *HOTSPOT*\n";
    $text .= "├ Total Voucher: *$countUsers*\n";
    $text .= "└ Hotspot Aktif: *$countActive*\n\n";
    
    $text .= "🔌 *PPPoE*\n";
    $text .= "├ Total Pelanggan: *$countSecrets*\n";
    $text .= "└ PPPoE Aktif: *$countPPPActive*\n\n";
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '🔄 Refresh', 'callback_data' => 'menu_dashboard'],
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Menu Info Router
 */
function tgInfo($API) {
    $resource = $API->comm("/system/resource/print");
    $identity = $API->comm("/system/identity/print");
    $routerName = isset($identity[0]['name']) ? $identity[0]['name'] : 'Unknown';
    
    $text  = "ℹ️ *INFO ROUTER*\n";
    $text .= "━━━━━━━━━━━━━━━━━━\n\n";
    $text .= "🖥️ *Identity:* $routerName\n";
    
    if (isset($resource[0])) {
        $text .= "⏱️ *Uptime:* " . $resource[0]['uptime'] . "\n";
        $text .= "💻 *CPU Load:* " . $resource[0]['cpu-load'] . "%\n";
        $text .= "💾 *Free Memory:* " . formatBytes($resource[0]['free-memory']) . "\n";
        $text .= "💿 *Free HDD:* " . formatBytes($resource[0]['free-hdd-space']) . "\n";
        $text .= "🏷️ *Version:* " . $resource[0]['version'] . "\n";
        $text .= "📦 *Board:* " . $resource[0]['board-name'] . "\n";
    }
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '🔄 Refresh', 'callback_data' => 'menu_info'],
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Submenu Hotspot
 */
function tgHotspotMenu() {
    $text  = "🎫 *MENU HOTSPOT*\n";
    $text .= "Silakan pilih aksi:";
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '➕ Generate Voucher', 'callback_data' => 'hs_generate']
        ],
        [
            ['text' => '📋 List Voucher', 'callback_data' => 'hs_list_user']
        ],
        [
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Submenu PPPoE
 */
function tgPPPoEMenu() {
    $text  = "👤 *MENU PPPoE*\n";
    $text .= "Silakan pilih aksi:";
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '➕ Add Secret', 'callback_data' => 'ppp_add']
        ],
        [
            ['text' => '🟢 List Active', 'callback_data' => 'ppp_list_active'],
            ['text' => '🔴 List Non-Active', 'callback_data' => 'ppp_list_nonactive']
        ],
        [
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Submenu IP Binding
 */
function tgIPBindingMenu() {
    $text  = "📋 *MENU IP BINDING*\n";
    $text .= "Silakan pilih aksi:";
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '➕ Add Binding', 'callback_data' => 'ipb_add'],
            ['text' => '📋 List Binding', 'callback_data' => 'ipb_list']
        ],
        [
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Hotspot - Generate Voucher (Select Preset)
 */
function tgHsGenerateMenu($API) {
    // Ambil User Profiles sebagai "Presets"
    $profiles = $API->comm("/ip/hotspot/user/profile/print");
    if (!is_array($profiles)) $profiles = [];
    
    $text  = "🎫 *GENERATE VOUCHER*\n";
    $text .= "Pilih Profile/Preset (Otomatis generate 5 voucher):\n";
    
    $buttons = [];
    foreach ($profiles as $prof) {
        $name = $prof['name'];
        if ($name == 'default') continue;
        $buttons[] = [['text' => "📦 $name", 'callback_data' => 'hs_gen_prof_' . substr($name, 0, 20)]];
    }
    
    if (empty($buttons)) {
        $text .= "\n_Belum ada user profile selain default._";
    }
    
    $buttons[] = [['text' => '🔙 Kembali', 'callback_data' => 'menu_hotspot']];
    $keyboard = tgInlineKeyboard($buttons);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Hotspot - Process Generate
 */
function tgHsDoGenerate($API, $profileName) {
    $qty = 5; // Default 5 voucher
    $prefix = "VC";
    
    $generated = [];
    for ($i = 0; $i < $qty; $i++) {
        $username = $prefix . rand(100, 999) . chr(rand(65,90));
        $password = rand(1000, 9999);
        
        $result = $API->comm("/ip/hotspot/user/add", [
            "name"     => $username,
            "password" => (string)$password,
            "profile"  => $profileName,
            "comment"  => "TG-GEN " . date('d/m/y'),
            "server"   => "all"
        ]);
        
        if (!empty($result) && !isset($result['!trap'])) {
            $generated[] = ['user' => $username, 'pass' => $password];
        }
    }
    
    $text  = "✅ *GENERATE BERHASIL*\n";
    $text .= "━━━━━━━━━━━━━━━━━━\n";
    $text .= "Profile: *$profileName*\n";
    $text .= "Berhasil: *" . count($generated) . "/$qty*\n\n";
    
    if (count($generated) > 0) {
        $text .= "*Voucher:*\n";
        foreach ($generated as $v) {
            $text .= "👤 `{$v['user']}` | 🔑 `{$v['pass']}`\n";
        }
    }
    
    $keyboard = tgInlineKeyboard([
        [
            ['text' => '🔙 Kembali ke Generate', 'callback_data' => 'hs_generate']
        ],
        [
            ['text' => '🏠 Menu Utama', 'callback_data' => 'menu_utama']
        ]
    ]);
    
    return ['text' => $text, 'markup' => $keyboard];
}

/**
 * Helper Format Bytes
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
