<?php
/**
 * Info Notification Handler
 * Displays a notification banner when $_GET['info'] is present
 * Format: info=Title|Message|Detail (pipe-separated)
 */
if (isset($_GET['info']) && !empty($_GET['info'])) {
    $infoParts = explode('|', $_GET['info']);
    $infoTitle = isset($infoParts[0]) ? htmlspecialchars($infoParts[0]) : '';
    $infoMsg = isset($infoParts[1]) ? htmlspecialchars($infoParts[1]) : '';
    $infoDetail = isset($infoParts[2]) ? htmlspecialchars($infoParts[2]) : '';
    
    $isSuccess = (stripos($infoTitle, 'Berhasil') !== false || stripos($infoTitle, 'Success') !== false);
    $iconClass = $isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle';
    $bgGradient = $isSuccess 
        ? 'linear-gradient(135deg, #00b894, #00cec9)' 
        : 'linear-gradient(135deg, #e17055, #d63031)';
    
    echo '
    <style>
    .info-toast {
        position: fixed;
        top: 70px;
        right: 20px;
        z-index: 99999;
        min-width: 320px;
        max-width: 450px;
        background: ' . $bgGradient . ';
        color: #fff;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        font-family: "Inter", sans-serif;
        animation: toastSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1), toastFadeOut 0.4s ease 4.6s forwards;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    .info-toast-icon { font-size: 22px; margin-top: 2px; }
    .info-toast-body { flex: 1; }
    .info-toast-title { font-weight: 700; font-size: 15px; margin-bottom: 4px; }
    .info-toast-msg { font-size: 13px; opacity: 0.9; }
    .info-toast-close {
        background: none; border: none; color: rgba(255,255,255,0.7);
        font-size: 18px; cursor: pointer; padding: 0; line-height: 1;
    }
    .info-toast-close:hover { color: #fff; }
    @keyframes toastSlideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes toastFadeOut {
        to { transform: translateX(100%); opacity: 0; pointer-events: none; }
    }
    </style>
    <div class="info-toast" id="infoToast">
        <div class="info-toast-icon"><i class="fa ' . $iconClass . '"></i></div>
        <div class="info-toast-body">
            <div class="info-toast-title">' . $infoTitle . '</div>
            <div class="info-toast-msg">' . $infoMsg . ($infoDetail ? '<br>' . $infoDetail : '') . '</div>
        </div>
        <button class="info-toast-close" onclick="document.getElementById(\'infoToast\').style.display=\'none\'">&times;</button>
    </div>
    <script>setTimeout(function(){ var t=document.getElementById("infoToast"); if(t) t.style.display="none"; }, 5000);</script>';
}
?>
