<?php
    
    echo '<div style="padding: 20px;">';
    
    $url = new moodle_url('/enrol/sits/rules.php', array('id'=>$_GET['id']));
    
    echo '<a href="'.$url->out().'">Back to SITS Sync Rules</a>';
    
    echo '</a>';