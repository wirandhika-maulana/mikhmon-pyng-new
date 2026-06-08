<?php
/**
 * WhatsApp Gateway Configuration for Payung.Net
 * Menggunakan MPWA API di api.wiran.my.id
 */

// Config file path
define('WA_GW_CONFIG_FILE', dirname(__FILE__) . '/config.json');
define('WA_GW_STATES_FILE', dirname(__FILE__) . '/states.json');
define('WA_GW_PRESETS_FILE', dirname(__FILE__) . '/presets.json');

/**
 * Get WA Gateway config
 */
function waGetConfig() {
    $default = [
        'api_url'      => 'https://api.wiran.my.id',
        'api_key'      => '',
        'device'       => '', // nomor bot yang terdaftar di MPWA
        'admin_number' => '', // nomor admin (owner)
        'brand_name'   => 'Payung.Net',
        'enabled'      => false,
    ];
    
    if (file_exists(WA_GW_CONFIG_FILE)) {
        $data = json_decode(file_get_contents(WA_GW_CONFIG_FILE), true);
        if (is_array($data)) {
            return array_merge($default, $data);
        }
    }
    return $default;
}

/**
 * Save WA Gateway config
 */
function waSaveConfig($config) {
    file_put_contents(WA_GW_CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
}

/**
 * Send WA message via MPWA API
 */
function waSendMessage($number, $message) {
    $config = waGetConfig();
    if (empty($config['api_key']) || empty($config['device'])) {
        return ['status' => false, 'message' => 'API Key atau Device belum dikonfigurasi'];
    }
    
    $url = rtrim($config['api_url'], '/') . '/api/send-message';
    $postData = [
        'api_key' => $config['api_key'],
        'sender'  => $config['device'],
        'number'  => $number,
        'message' => $message,
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result ?: ['status' => false, 'message' => 'No response (HTTP ' . $httpCode . ')'];
}

/**
 * Get/Set conversation state for multi-step flows
 */
function waGetState($phone) {
    $states = [];
    if (file_exists(WA_GW_STATES_FILE)) {
        $states = json_decode(file_get_contents(WA_GW_STATES_FILE), true) ?: [];
    }
    // Clean expired states (> 30 minutes)
    foreach ($states as $key => $state) {
        if (isset($state['timestamp']) && (time() - $state['timestamp']) > 1800) {
            unset($states[$key]);
        }
    }
    return isset($states[$phone]) ? $states[$phone] : null;
}

function waSetState($phone, $step, $data = []) {
    $states = [];
    if (file_exists(WA_GW_STATES_FILE)) {
        $states = json_decode(file_get_contents(WA_GW_STATES_FILE), true) ?: [];
    }
    $states[$phone] = [
        'step'      => $step,
        'data'      => $data,
        'timestamp' => time(),
    ];
    file_put_contents(WA_GW_STATES_FILE, json_encode($states, JSON_PRETTY_PRINT));
}

function waClearState($phone) {
    $states = [];
    if (file_exists(WA_GW_STATES_FILE)) {
        $states = json_decode(file_get_contents(WA_GW_STATES_FILE), true) ?: [];
    }
    unset($states[$phone]);
    file_put_contents(WA_GW_STATES_FILE, json_encode($states, JSON_PRETTY_PRINT));
}

/**
 * Get/Save hotspot presets
 */
function waGetPresets() {
    if (file_exists(WA_GW_PRESETS_FILE)) {
        $data = json_decode(file_get_contents(WA_GW_PRESETS_FILE), true);
        return isset($data['presets']) ? $data['presets'] : [];
    }
    return [];
}

function waSavePresets($presets) {
    file_put_contents(WA_GW_PRESETS_FILE, json_encode(['presets' => $presets], JSON_PRETTY_PRINT));
}
