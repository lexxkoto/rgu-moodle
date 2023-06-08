<?php

    require('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once('manifest.php');
    require_once('filters.php');
    
    $report = optional_param('report', false, PARAM_TEXT);
    
    $context = context_system::instance();
    require_capability('report/rgu:view', $context);
    $PAGE->set_context($context);
    
    admin_externalpage_setup('reportrgu', '', null, '', array('pagelayout' => 'report'));
    $renderer = $PAGE->get_renderer('report_rgu');
    $PAGE->set_title($SITE->shortname .': '.get_string('pluginname', 'report_rgu'));
    $PAGE->set_primary_active_tab('siteadminnode');
    
    echo $OUTPUT->header();

    if(isset($report) && $report !== false) {
    
        $showPlaceholdersPage = false;
        $placeholders = Array();
    
        if(isset($reports[$report]['placeholders'])) {
            foreach($reports[$report]['placeholders'] as $placeholderName=>$placeholderData) {
                $placeholders[$placeholderName] = optional_param('ph_'.$placeholderName, false, PARAM_TEXT);
                if(empty($placeholders[$placeholderName])) {
                    $showPlaceholdersPage = true;
                }
            }
        }
        
        if($showPlaceholdersPage) {
            echo '<form method="get" id="adminsettings">';
            echo '<input type="hidden" name="report" value="'.$report.'" />';
            foreach($reports[$report]['placeholders'] as $code=>$data) {
                echo '<div class="form-item row">';
                echo '<div class="form-label col-sm-3 text-sm-right"><label for="id_ph_'.$code.'">'.$data['label'].'</label></div>';
                echo '<div class="form-setting col-sm-9"><div class="form-text defaultsnext"><input id="id_ph_'.$code.'" type="text" name="ph_'.$code.'" value="'.$data['default'].'" class="form-control" size="30" /></div><div class="form-description mt-3"><p>'.$data['hint'].'</p></div></div>';
                echo '</div>';
            }
            echo '<div class="row"><div class="offset-sm-3 col-sm-3"><button type="submit" class="btn btn-primary">Show Report</button></div></div>';
            echo '</form>';
        } else {
        
            $fixedQuery = $reports[$report]['query'];
            
            foreach($placeholders as $code=>$value) {
                $fixedQuery = str_replace('__'.$code.'__', $value, $fixedQuery);
            }
        
            $r = $DB->get_records_sql($fixedQuery);
            
            echo $OUTPUT->heading($reports[$report]['name'].' ('.count($r).')');
            
            echo '<p>'.$reports[$report]['desc'].'</p>';
            
            echo '<table class="table">';
            echo '<thead><tr>';
            foreach($reports[$report]['titles'] as $title) {
                echo '<th>'.$title.'</th>';
            }
            echo '</tr></thead>';
            echo '<tbody>';
            foreach($r as $record) {
                echo '<tr>';
                foreach($reports[$report]['titles'] as $code=>$friendly) {
                    if(isset($reports[$report]['filters'][$code])) {
                        echo '<td>';
                        switch($reports[$report]['filters'][$code]) {
                            case 'relative-time':
                                echo time_elapsed_string($record->$code);
                                break;
                            case 'user-link':
                                user_link($record->$code);
                                break;
                            case 'course-link':
                                course_link($record->$code);
                                break;
                            case 'assign-link':
                                assign_link($record->$code);
                                break;
                            case 'module-link':
                                module_link($record->$code);
                                break;
                            case 'date-long':
                                pretty_date($record->$code, 'long');
                                break;
                            case 'yes-no':
                                yes_no($record->$code);
                                break;
                            case 'number-format':
                                echo number_format($record->$code, 0);
                                break;
                            default:
                                echo 'Unknown Filter: '.$record->$code;
                        }
                        echo '</td>';
                    } else {
                        echo '<td>'.$record->$code.'</td>';
                    }
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        
    } else {
        echo $OUTPUT->heading(get_string('pluginname', 'report_rgu'));
        foreach ($categories as $categoryName=>$categoryData) {
            $outputHeading = '<h3>'.$categoryName.'</h3>';
            $outputText = '<ul>';
            foreach ($categoryData as $report) {
                if(has_capability($reports[$report]['capability'], $context)) {
                    $outputText .= '<li>';
                    $outputText .= '<h4><a href="?report='.$report.'">'.$reports[$report]['name'].'</a></h4>';
                    $outputText .= '<p>'.$reports[$report]['desc'].'</p>';
                    $outputText .= '</li>';
                }
            }
            $outputText .= '</ul>';
            
            if ($outputText != '<ul></ul>') {
                echo $outputHeading;
                echo $outputText;
            }
        }
    }
    
    echo $OUTPUT->footer();

?>
