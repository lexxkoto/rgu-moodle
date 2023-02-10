<?php
require_once("$CFG->dirroot/enrol/locallib.php");
    
    $downtimeTime = get_config('theme_garthdee', 'garthdee_downtime_datetime');
    $downtimeLength = get_config('theme_garthdee', 'garthdee_downtime_length');
    $downtimeDetails = get_config('theme_garthdee', 'garthdee_downtime_details');
    
    $downtimeText = '';
    
    if(!empty($downtimeTime)) {
        if(empty($_SESSION['SESSION']->garthdee_notifications) || !array_key_exists(md5('downtimeWarning'.$downtimeTime.$downtimeLength), $_SESSION['SESSION']->garthdee_notifications)) {
        $countdown = strtotime($downtimeTime);
        $finishtime = $countdown + ($downtimeLength * 60);
        if($countdown !== false) {
            $timetildowntime = $countdown - time();
            if($timetildowntime > 1209600) {
                // Downtime is over two weeks away. Do nothing.
            } else if ($timetildowntime > 172800) {
                // Downtime is between 2 and 7 days away.
                $downtimeText .= '<div class="message-information message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5('downtimeWarning'.$downtimeTime.$downtimeLength).'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span class="d-flex-item"><strong>Moodle is being upgraded</strong> on '.date('l jS F', $countdown).' between '.date('H:i', $countdown).' and '.date('H:i', $finishtime).'. '.$downtimeDetails.'</span></div>';
            } else if ($timetildowntime > 10800) {
                // Downtime is between 3 hours and 2 days away.
                $downtimeText .= '<div class="message-notification message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5('downtimeWarning'.$downtimeTime.$downtimeLength).'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span class="d-flex-item"><strong>Moodle is being upgraded</strong> on '.date('l jS F', $countdown).' between '.date('H:i', $countdown).' and '.date('H:i', $finishtime).'. '.$downtimeDetails.'</span></div>';
            } else if ($timetildowntime > 900) {
                // Downtime is between 30 minutes and 3 hours away
                $downtimeText .= '<div class="message-warning message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5('downtimeWarning'.$downtimeTime.$downtimeLength).'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span class="d-flex-item"><strong>Moodle is being upgraded</strong> on '.date('l jS F', $countdown).' between '.date('H:i', $countdown).' and '.date('H:i', $finishtime).'. '.$downtimeDetails.'</span></div>';
            } else if ($timetildowntime > -7200) {
                // Downtime is less than 30 minutes away
                $downtimeText .= '<div class="message-warning message-closable message-outside-content"><a class="close d-flex-item ml-auto" href="'.$CFG->wwwroot.'/theme/garthdee/notification.php?h='.md5('downtimeWarning'.$downtimeTime.$downtimeLength).'" aria-label="Close"><span aria-hidden="true">&times;</span></a><span class="d-flex-item"><strong>Moodle is being upgraded</strong> on '.date('l jS F', $countdown).' between '.date('H:i', $countdown).' and '.date('H:i', $finishtime).'. '.$downtimeDetails.'</span></div>';
            }
        }
    }
}
    
?>
