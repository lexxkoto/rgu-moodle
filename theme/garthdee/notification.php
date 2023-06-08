<?php
    
    require_once('../../config.php');
    
    $notificationHash = required_param('h', PARAM_RAW);
    
    require_login();
    
    if(!isset($_SESSION['SESSION']->garthdee_notifications)) {
        $_SESSION['SESSION']->garthdee_notifications = Array();
    }
    $_SESSION['SESSION']->garthdee_notifications[$notificationHash] = 1;
    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    
?>
