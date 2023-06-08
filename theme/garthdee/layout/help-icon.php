<?php

    $helpIconVisible = get_config('theme_garthdee', 'help_show');
    $helpIconLink = get_config('theme_garthdee', 'help_location');
    
    $helpText = '';
    
    if($helpIconVisible === 'enabled' && !empty($helpIconLink)) {
        $helpText = '<a href="'.$helpIconLink.'"><i class="floating-help-icon fa fa-question-circle"></i></a>';
    }

?>
