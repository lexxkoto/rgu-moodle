<?php
require_once("$CFG->dirroot/enrol/locallib.php");

if(substr($PAGE->pagetype, 0, 11) == 'course-view') {
    
    $courseDetails = $PAGE->course;
    
    $contentWarningEnabled = get_config('theme_garthdee', 'content_warning_enabled');
    $contentWarningText = get_config('theme_garthdee', 'content_warning_text');
    
    $contentWarning = '';
    
    if($contentWarningEnabled == 'enabled' && !empty($contentWarningText)) {
        
        if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($courseDetails->id.'contentwarning'), $_SESSION['SESSION']->garthdee_notifications)) {
            $contentWarning .= '<div class="message-notification message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($courseDetails->id.'contentwarning').'" aria-label="Close"><span aria-hidden="true">&times;</span></a>'.$contentWarningText.'</div>';
        }
    }
    
    $courseDetails = $PAGE->course;
    $context = context_course::instance($courseDetails->id);
    $canEditCourse = has_capability('moodle/course:visibility', $context);
    
    if((!empty($courseDetails->id)) && $courseDetails->id != 1) {
        if($courseDetails->visible=='0') {
            if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($courseDetails->id.'courseinvisible'), $_SESSION['SESSION']->garthdee_notifications) && $canEditCourse) {
                $contentWarning .= '<div class="message-grey message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($courseDetails->id.'courseinvisible').'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span><strong>This course is currently hidden.</strong> You can see it, but students can\'t. When you\'re ready, <a class="alert-link" href="'.$CFG->wwwroot.'/theme/garthdee/course-visible.php?id='.$courseDetails->id.'">make this course available to students</a>.</span></div>';
                $automaticEnrolmentsDisabled = true;
                $automaticEnrolmentsReason = 'this course has been hidden from students.';
            }
        }
        
        $canConfigureEnrolments = has_capability('enrol/sits:manage', $context);
        $studentyUsers = count_role_users(5, $PAGE->context);
                
        if($studentyUsers === 0) {
            if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($courseDetails->id.'coursenostudents'), $_SESSION['SESSION']->garthdee_notifications) && $canConfigureEnrolments) {
                $contentWarning .= '<div class="message-grey message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($courseDetails->id.'coursenostudents').'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span><strong>There are no students on this course.</strong> Students need to be added to a course before they can see it. <a class="alert-link" href="'.$CFG->wwwroot.'/enrol/sits/rules.php?id='.$courseDetails->id.'">Manage SITS Sync Rules</a></span></div>';
            }
        }
        
        $enrolCodes = enrol_sits_plugin::getAllCodesForCourse($courseDetails->id);
        if(count($enrolCodes) == 0) {
            if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5($courseDetails->id.'nositsrules'), $_SESSION['SESSION']->garthdee_notifications)) {
                $contentWarning .= '<div class="message-grey message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5($courseDetails->id.'nositsrules').'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span><strong>You haven\'t added any rules to SITS Sync for this course.</strong> SITS Sync is the easiest way to add students to your course. <a class="alert-link" href="'.$CFG->wwwroot.'/enrol/sits/rules.php?id='.$courseDetails->id.'">Manage SITS Sync Rules</a></span></div>';
            }
        }
    }
}
    
?>
