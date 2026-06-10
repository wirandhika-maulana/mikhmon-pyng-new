<?php
/**
 * Telegram Bot Webhook Handler for Mikhmon
 * Receives POST requests from Telegram API
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Include MikroTik API
require_once __DIR__ . '/../lib/routeros_api.class.php';
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/readcfg.php';

// Get JSON input
$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (!$update) {
    http_response_code(400);
    echo "Bad Request";
    exit;
}

$config = tgGetConfig();
if (!$config['enabled']) {
    exit; // Silently ignore if disabled
}

// Variables
$chatId = null;
$messageText = null;
$messageId = null;
$callbackQuery = null;
$callbackData = null;
$callbackId = null;
$fromId = null;

// Parse update type (Message or Callback Query)
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $messageText = trim($update['message']['text']);
    $messageId = $update['message']['message_id'];
    $fromId = $update['message']['from']['id'];
} elseif (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $messageId = $callbackQuery['message']['message_id'];
    $callbackData = $callbackQuery['data'];
    $callbackId = $callbackQuery['id'];
    $fromId = $callbackQuery['from']['id'];
} else {
    exit; // Not a supported update type
}

// Check admin
$isAdmin = true;
if (!empty($config['admin_ids'])) {
    $admins = array_map('trim', explode(',', $config['admin_ids']));
    if (!in_array($fromId, $admins)) {
        $isAdmin = false;
    }
}

if (!$isAdmin) {
    if ($messageText) {
        tgSendMessage($chatId, "⛔ *Akses Ditolak*\nAnda tidak memiliki izin menggunakan bot ini.");
    } elseif ($callbackId) {
        tgAnswerCallback($callbackId, "Akses Ditolak!");
    }
    exit;
}

// Connect to MikroTik
$API = new RouterosAPI();
$API->debug = false;

// Setup session
$sessions = explode("\n", file_get_contents(__DIR__ . '/../setup.set'));
$default_session = $config['session']; // from config

// Fallback to first session if config is empty
if (empty($default_session)) {
    foreach ($sessions as $s) {
        if (trim($s) != '') {
            $parts = explode('=', $s);
            if (isset($parts[0])) {
                $default_session = trim($parts[0]);
                break;
            }
        }
    }
}

$connected = false;
if (!empty($default_session)) {
    $auth_file = __DIR__ . '/../session/' . $default_session . '.php';
    if (file_exists($auth_file)) {
        include($auth_file);
        $connected = $API->connect($iphost, $userhost, decrypt($passwdhost));
    }
}

if (!$connected) {
    $errorMsg = "❌ *SYSTEM ERROR*\nTidak dapat terhubung ke MikroTik Router.\nPastikan Mikhmon berjalan normal.";
    if ($messageText) tgSendMessage($chatId, $errorMsg);
    if ($callbackId) tgAnswerCallback($callbackId, "Gagal koneksi ke Router!");
    exit;
}

// ---------------------------------------------------------
// Process Callback Query (Button Clicks)
// ---------------------------------------------------------
if ($callbackQuery) {
    $response = null;
    
    switch ($callbackData) {
        case 'menu_utama':
            $response = tgMenuUtama();
            break;
            
        case 'menu_dashboard':
            $response = tgDashboard($API);
            break;
            
        case 'menu_info':
            $response = tgInfo($API);
            break;
            
        case 'menu_hotspot':
            $response = tgHotspotMenu();
            break;
            
        case 'hs_generate':
            $response = tgHsGenerateMenu($API);
            break;
            
        case 'menu_pppoe':
            $response = tgPPPoEMenu();
            break;
            
        case 'menu_ipbinding':
            $response = tgIPBindingMenu();
            break;
    }
    
    // Check dynamic callback data
    if (strpos($callbackData, 'hs_gen_prof_') === 0) {
        $profName = str_replace('hs_gen_prof_', '', $callbackData);
        $response = tgHsDoGenerate($API, $profName);
    }
    
    if ($response) {
        // Edit existing message instead of sending a new one
        tgEditMessage($chatId, $messageId, $response['text'], isset($response['markup']) ? $response['markup'] : null);
    } else {
        // Feature not implemented yet
        tgAnswerCallback($callbackId, "Fitur ini sedang dalam pengembangan!");
    }
    
    // Always answer callback to remove loading state on button
    tgAnswerCallback($callbackId);
    
    $API->disconnect();
    exit;
}

// ---------------------------------------------------------
// Process Text Messages
// ---------------------------------------------------------
if ($messageText) {
    $cmd = strtolower($messageText);
    
    if ($cmd == '/start' || $cmd == '/menu' || $cmd == 'menu') {
        tgClearState($chatId);
        $response = tgMenuUtama();
        tgSendMessage($chatId, $response['text'], isset($response['markup']) ? $response['markup'] : null);
    }
    else {
        // Process State Machine for multi-step inputs (e.g., adding user via text)
        $state = tgGetState($chatId);
        if ($state) {
            // Future implementation: Add PPPoE text flow
            tgClearState($chatId);
        } else {
            // Default reply
            $response = tgMenuUtama();
            tgSendMessage($chatId, $response['text'], isset($response['markup']) ? $response['markup'] : null);
        }
    }
}

$API->disconnect();
