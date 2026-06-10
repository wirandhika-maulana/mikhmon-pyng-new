<?php
/**
 * Telegram Bot Configuration for Mikhmon
 * Inline Keyboard Bot for managing Hotspot & PPPoE
 */

define('TG_BOT_CONFIG_FILE', dirname(__FILE__) . '/config.json');
define('TG_BOT_STATES_FILE', dirname(__FILE__) . '/states.json');

/**
 * Get Telegram Bot config
 */
function tgGetConfig() {
    $default = [
        'bot_token'    => '',
        'admin_ids'    => '', // comma-separated Telegram user IDs
        'session'      => '', // Mikhmon session name (router)
        'brand_name'   => 'Mikhmon Bot',
        'enabled'      => false,
        'webhook_url'  => '',
    ];
    
    if (file_exists(TG_BOT_CONFIG_FILE)) {
        $data = json_decode(file_get_contents(TG_BOT_CONFIG_FILE), true);
        if (is_array($data)) {
            return array_merge($default, $data);
        }
    }
    return $default;
}

/**
 * Save Telegram Bot config
 */
function tgSaveConfig($config) {
    file_put_contents(TG_BOT_CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
}

/**
 * Send message via Telegram Bot API
 */
function tgSendMessage($chatId, $text, $replyMarkup = null) {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return false;
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/sendMessage";
    $postData = [
        'chat_id'    => $chatId,
        'text'       => $text,
        'parse_mode' => 'Markdown',
    ];
    if ($replyMarkup) {
        $postData['reply_markup'] = json_encode($replyMarkup);
    }
    
    return tgApiRequest($url, $postData);
}

/**
 * Edit existing message (for callback query responses)
 */
function tgEditMessage($chatId, $messageId, $text, $replyMarkup = null) {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return false;
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/editMessageText";
    $postData = [
        'chat_id'    => $chatId,
        'message_id' => $messageId,
        'text'       => $text,
        'parse_mode' => 'Markdown',
    ];
    if ($replyMarkup) {
        $postData['reply_markup'] = json_encode($replyMarkup);
    }
    
    return tgApiRequest($url, $postData);
}

/**
 * Answer callback query (removes loading indicator on button)
 */
function tgAnswerCallback($callbackId, $text = '') {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return false;
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/answerCallbackQuery";
    $postData = [
        'callback_query_id' => $callbackId,
        'text' => $text,
    ];
    
    return tgApiRequest($url, $postData);
}

/**
 * Set webhook URL
 */
function tgSetWebhook($webhookUrl) {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return ['ok' => false, 'description' => 'Bot token kosong'];
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/setWebhook";
    return tgApiRequest($url, ['url' => $webhookUrl]);
}

/**
 * Delete webhook
 */
function tgDeleteWebhook() {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return ['ok' => false];
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/deleteWebhook";
    return tgApiRequest($url, []);
}

/**
 * Get webhook info
 */
function tgGetWebhookInfo() {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return ['ok' => false];
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/getWebhookInfo";
    return tgApiRequest($url, [], 'GET');
}

/**
 * Get bot info
 */
function tgGetMe() {
    $config = tgGetConfig();
    if (empty($config['bot_token'])) return ['ok' => false];
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/getMe";
    return tgApiRequest($url, [], 'GET');
}

/**
 * Generic Telegram API request
 */
function tgApiRequest($url, $postData = [], $method = 'POST') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true) ?: ['ok' => false];
}

/**
 * Conversation state management
 */
function tgGetState($chatId) {
    $states = [];
    if (file_exists(TG_BOT_STATES_FILE)) {
        $states = json_decode(file_get_contents(TG_BOT_STATES_FILE), true) ?: [];
    }
    // Clean expired states (> 30 minutes)
    foreach ($states as $key => $state) {
        if (isset($state['timestamp']) && (time() - $state['timestamp']) > 1800) {
            unset($states[$key]);
        }
    }
    return isset($states[$chatId]) ? $states[$chatId] : null;
}

function tgSetState($chatId, $step, $data = []) {
    $states = [];
    if (file_exists(TG_BOT_STATES_FILE)) {
        $states = json_decode(file_get_contents(TG_BOT_STATES_FILE), true) ?: [];
    }
    $states[$chatId] = [
        'step'      => $step,
        'data'      => $data,
        'timestamp' => time(),
    ];
    file_put_contents(TG_BOT_STATES_FILE, json_encode($states, JSON_PRETTY_PRINT));
}

function tgClearState($chatId) {
    $states = [];
    if (file_exists(TG_BOT_STATES_FILE)) {
        $states = json_decode(file_get_contents(TG_BOT_STATES_FILE), true) ?: [];
    }
    unset($states[$chatId]);
    file_put_contents(TG_BOT_STATES_FILE, json_encode($states, JSON_PRETTY_PRINT));
}

/**
 * Build inline keyboard markup
 */
function tgInlineKeyboard($buttons) {
    return ['inline_keyboard' => $buttons];
}
