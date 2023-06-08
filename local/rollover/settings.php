<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    if ($hassiteconfig) {
        $page = new admin_settingpage('local_rollover', get_string('configtitle', 'local_rollover'));
        $ADMIN->add('localplugins', $page);
        
        $page->add(new admin_setting_heading('local_rollover/matching_title', get_string('page_matching', 'local_rollover'), ''));
        
        $name = 'local_rollover/new_string';
        $title = get_string('new_string', 'local_rollover');
        $desc = get_string('new_string_desc', 'local_rollover');
        $setting = new admin_setting_configtext($name, $title, $desc, '2023/2024', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rollover/old_string';
        $title = get_string('old_string', 'local_rollover');
        $desc = get_string('old_string_desc', 'local_rollover');
        $setting = new admin_setting_configtext($name, $title, $desc, '2022/2023', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rollover/cron_num_courses';                       
        $title = get_string('cron_num_courses', 'local_rollover');                                                                                   
        $description = get_string('cron_num_courses_desc', 'local_rollover');
        
        $choices = Array(
            '50' => '50',
            '100' => '100',
            '250' => '250',
            '500' => '500',
            '1000' => '1,000',
        );                                                                     
        $default = '100';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
        
    }
