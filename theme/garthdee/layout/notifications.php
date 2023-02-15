<?php
require_once("$CFG->dirroot/enrol/locallib.php");

$notificationType = get_config('theme_garthdee', 'garthdee_notification_type');
$notificationText =  get_config('theme_garthdee', 'garthdee_notification');

if(!empty($notificationText) && (empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($notificationText), $_SESSION['SESSION']->garthdee_notifications))) {
    switch($notificationType) {
        case 'alert-danger':
            $pageBannerText = '<div class="message-outside-content message-warning message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($notificationText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$notificationText.'</div>';
            break;
        case 'alert-warning':
             $pageBannerText = '<div class="message-outside-content message-notification message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($notificationText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$notificationText.'</div>';
            break;
        case 'alert-success':
             $pageBannerText = '<div class="message-outside-content message-success message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($notificationText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$notificationText.'</div>';
            break;
        case 'alert-info':
             $pageBannerText = '<div class="message-outside-content message-information message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($notificationText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$notificationText.'</div>';
            break;
        case 'alert-grey':
             $pageBannerText = '<div class="message-outside-content message-grey message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($notificationText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$notificationText.'</div>';
            break;
        default:
            $pageBannerText = '';
    }
} else {
    $pageBannerText = '';
}

$dashboardNotification = get_config('theme_garthdee', 'garthdee_student_course_alert');
$dashboardAlertText = get_config('theme_garthdee', 'garthdee_student_course_alert_text');

if((substr($PAGE->pagetype, 0, 8) == 'my-index') && ($dashboardNotification == 'enabled')) {
    if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($dashboardAlertText), $_SESSION['SESSION']->garthdee_notifications)) {
        $pageBannerText .= '<div class="message-outside-content message-grey message-closable"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($dashboardAlertText).'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$dashboardAlertText.'</div>';
    }
}
    
?>
