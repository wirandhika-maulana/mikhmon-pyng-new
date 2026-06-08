<?php
/**
 * WhatsApp Gateway Webhook Handler for Payung.Net
 * File ini dipanggil oleh webhook dari MPWA
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Include MikroTik API class
require_once __DIR__ . '/../lib/routeros_api.class.php';
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/readcfg.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['message']) || !isset($data['from'])) {
    http_response_code(400);
    echo "Bad Request";
    exit;
}

$config = waGetConfig();
if (!$config['enabled']) {
    exit; // Silently ignore if disabled
}

$message = trim(strtolower($data['message']));
$from = explode('@', $data['from'])[0]; // Extract phone number

// Cek apakah pengirim adalah admin (jika ada config admin_number)
$isAdmin = true;
if (!empty($config['admin_number'])) {
    $admins = array_map('trim', explode(',', $config['admin_number']));
    if (!in_array($from, $admins)) {
        $isAdmin = false;
    }
}

if (!$isAdmin) {
    // Abaikan pesan dari non-admin
    exit;
}

// Connect to MikroTik
$API = new RouterosAPI();
$API->debug = false;

// Setup session (gunakan session pertama yang ditemukan di setup.set jika tidak spesifik)
// Idealnya, webhook harus tahu session mana, tapi untuk simplifikasi kita pakai session default/pertama
$sessions = explode("\n", file_get_contents(__DIR__ . '/../setup.set'));
$default_session = '';
foreach ($sessions as $s) {
    if (trim($s) != '') {
        $parts = explode('=', $s);
        if (isset($parts[0])) {
            $default_session = trim($parts[0]);
            break;
        }
    }
}

$connected = false;
if (!empty($default_session)) {
    // Load auth info
    $auth_file = __DIR__ . '/../session/' . $default_session . '.php';
    if (file_exists($auth_file)) {
        include($auth_file);
        $connected = $API->connect($iphost, $userhost, decrypt($passwdhost));
    }
}

if (!$connected) {
    waSendMessage($data['from'], "❌ *SYSTEM ERROR*\nTidak dapat terhubung ke MikroTik Router.\nPastikan Mikhmon berjalan normal.");
    exit;
}

// Handle conversation states
$state = waGetState($from);

// Jika ada perintah "batal" atau "menu", clear state
if ($message == 'batal' || $message == 'menu' || $message == 'cancel') {
    waClearState($from);
    $reply = waMenuUtama();
    waSendMessage($data['from'], $reply);
    exit;
}

// State Machine Processing
if ($state) {
    $reply = "";
    
    // GENERATE HOTSPOT FLOW
    if ($state['step'] == 'generate_hotspot') {
        if (is_numeric($message)) {
            $idx = intval($message) - 1;
            $reply = waDoGenerateHotspot($API, $idx);
            waClearState($from);
        } else {
            $reply = "❌ Pilihan tidak valid. Ketik angka preset atau *batal*.";
        }
    }
    
    // ADD PPPOE FLOW - STEP 1 (Select Profile)
    elseif ($state['step'] == 'add_pppoe_profile') {
        if (is_numeric($message)) {
            $profiles = $API->comm("/ppp/profile/print");
            if (!is_array($profiles)) $profiles = [];
            
            $validProfiles = [];
            foreach ($profiles as $p) {
                if ($p['default'] != 'true') $validProfiles[] = $p['name'];
            }
            
            $idx = intval($message) - 1;
            if (isset($validProfiles[$idx])) {
                $selectedProfile = $validProfiles[$idx];
                waSetState($from, 'add_pppoe_username', ['profile' => $selectedProfile]);
                $reply = waAddPPPoEUsername($selectedProfile, $API);
            } else {
                $reply = "❌ Pilihan tidak valid. Ketik angka profile atau *batal*.";
            }
        } else {
            $reply = "❌ Pilihan tidak valid. Ketik angka profile atau *batal*.";
        }
    }
    
    // ADD PPPOE FLOW - STEP 2 (Input Username)
    elseif ($state['step'] == 'add_pppoe_username') {
        $username = str_replace(' ', '', $message); // Remove spaces
        $profile = $state['data']['profile'];
        waSetState($from, 'add_pppoe_password', ['profile' => $profile, 'username' => $username]);
        $reply = waAddPPPoEPassword($profile, $username);
    }
    
    // ADD PPPOE FLOW - STEP 3 (Input Password)
    elseif ($state['step'] == 'add_pppoe_password') {
        $profile = $state['data']['profile'];
        $username = $state['data']['username'];
        $password = ($message == 'ok') ? $username : $message;
        
        waSetState($from, 'add_pppoe_comment', ['profile' => $profile, 'username' => $username, 'password' => $password]);
        $reply = waAddPPPoEComment($profile, $username, $password);
    }
    
    // ADD PPPOE FLOW - STEP 4 (Input Comment)
    elseif ($state['step'] == 'add_pppoe_comment') {
        $profile = $state['data']['profile'];
        $username = $state['data']['username'];
        $password = $state['data']['password'];
        $comment = ($message == 'skip') ? '' : $message;
        
        waSetState($from, 'add_pppoe_confirm', [
            'profile' => $profile, 
            'username' => $username, 
            'password' => $password,
            'comment' => $comment
        ]);
        $reply = waAddPPPoEConfirm($profile, $username, $password, $comment);
    }
    
    // ADD PPPOE FLOW - STEP 5 (Confirm)
    elseif ($state['step'] == 'add_pppoe_confirm') {
        if ($message == 'ya' || $message == 'y') {
            $d = $state['data'];
            $reply = waDoAddPPPoE($API, $d['profile'], $d['username'], $d['password'], $d['comment']);
            waClearState($from);
        } else {
            $reply = "❌ Dibatalkan.\n\nKetik *menu* untuk kembali";
            waClearState($from);
        }
    }
    
    // PRINT VOUCHER FLOW
    elseif ($state['step'] == 'print_voucher') {
        if (is_numeric($message)) {
            $users = $API->comm("/ip/hotspot/user/print");
            if (!is_array($users)) $users = [];
            
            $comments = [];
            foreach ($users as $u) {
                $c = isset($u['comment']) ? $u['comment'] : 'Tanpa Komentar';
                if (!in_array($c, $comments)) $comments[] = $c;
            }
            
            $idx = intval($message) - 1;
            if (isset($comments[$idx])) {
                $reply = waPrintVoucherByComment($API, $comments[$idx]);
                waClearState($from);
            } else {
                $reply = "❌ Pilihan tidak valid. Ketik angka atau *batal*.";
            }
        } else {
            $reply = "❌ Pilihan tidak valid. Ketik angka atau *batal*.";
        }
    }
    
    if (!empty($reply)) {
        waSendMessage($data['from'], $reply);
        exit;
    }
}

// Stateless Processing (Menu Utama)
$reply = "";
switch ($message) {
    case '1':
    case 'dashboard':
        $reply = waDashboard($API);
        break;
        
    case '2':
    case 'generate hotspot':
    case 'generate':
        waSetState($from, 'generate_hotspot');
        $reply = waGenerateHotspotMenu($API);
        break;
        
    case '3':
    case 'add pppoe':
    case 'pppoe':
        waSetState($from, 'add_pppoe_profile');
        $reply = waAddPPPoEMenu($API);
        break;
        
    case '4':
    case 'print voucher':
    case 'print':
        waSetState($from, 'print_voucher');
        $reply = waPrintVoucherMenu($API);
        break;
        
    case 'menu':
    case 'start':
    case 'halo':
    case 'hi':
    case 'ping':
        $reply = waMenuUtama();
        break;
}

if (!empty($reply)) {
    waSendMessage($data['from'], $reply);
}

// Disconnect API
$API->disconnect();
