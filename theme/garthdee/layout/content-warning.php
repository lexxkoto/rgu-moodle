<?php
require_once("$CFG->dirroot/enrol/locallib.php");

if(substr($PAGE->pagetype, 0, 11) == 'course-view') {
    
    $courseDetails = $PAGE->course;
    
    $contentWarningEnabled = get_config('theme_garthdee', 'content_warning_enabled');
    $contentWarningText = get_config('theme_garthdee', 'content_warning_text');
    
    $contentWarning = '';
    
    if($contentWarningEnabled == 'enabled' && !empty($contentWarningText)) {
        
        if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($courseDetails->id.'contentwarning'), $_SESSION['SESSION']->garthdee_notifications)) {
            $contentWarning .= '<div class="message-notification message-closable message-content-warning"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($courseDetails->id.'contentwarning').'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$contentWarningText.'</span></div>';
        }
    }
}
    
?>