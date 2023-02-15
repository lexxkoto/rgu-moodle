<?php

    require('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once('manifest.php');
    
    $PAGE->set_url('/report/rgu/index.php');
    $PAGE->set_pagelayout('report');
    $PAGE->set_context($context);
    admin_externalpage_setup('reportrgu', '', null, '', array('pagelayout' => 'report'));
    $PAGE->set_title($SITE->shortname .': '.get_string('pluginname', 'report_rgu'));
    $PAGE->set_primary_active_tab('siteadminnode');
    
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'report_rgu'));
    
    $context = context_system::instance();
    require_capability('report/rgu:view', $context);

    die('test');
    
    

?>
