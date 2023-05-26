<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    if ($ADMIN->fulltree) {
    
        $settings = new theme_boost_admin_settingspage_tabs('themesettinggarthdee', get_string('configtitle', 'theme_garthdee'));
        
        $page = new admin_settingpage('theme_garthdee_preset', get_string('page_presets', 'theme_garthdee'));
        
        $page->add(new admin_setting_heading('theme_garthdee/smart_alerts_title', get_string('section_preset', 'theme_garthdee'), ''));
        
        // Replicate the preset setting from boost.                                                                                     
        $name = 'theme_garthdee/preset';                                                                                                   
        $title = get_string('preset', 'theme_garthdee');                                                                                   
        $description = get_string('preset_desc', 'theme_garthdee');                                                                        
        $default = 'purple.scss';                                                                                                      
                                                                                                                                
        $choices = Array();                                                                                  
        $choices['purple'] = 'Mulled Wine';   
        $choices['blue'] = 'Curious Blue';                                                                                    
        $choices['green'] = 'Elf Green';
        $choices['red'] = 'Shiraz Red';
        $choices['orange'] = 'Rust Orange';
        $choices['teal'] = 'Deep Sea Teal';
        $choices['grey'] = 'Slate Grey';
     
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                     
        $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
        $page->add($setting);
        
        $settings->add($page);
        
        $page = new admin_settingpage('theme_garthdee_notifications', get_string('page_notifications', 'theme_garthdee'));
        
        $page->add(new admin_setting_heading('theme_garthdee/smart_alerts_title', get_string('section_smart_alerts', 'theme_garthdee'), ''));
        
        $name = 'theme_garthdee/garthdee_smart_alerts';                                                                                                   
        $title = get_string('garthdee_smart_alerts', 'theme_garthdee');                                                                                   
        $description = get_string('garthdee_smart_alerts_desc', 'theme_garthdee');
        
        $choices = Array(
            'enabled' => get_string('garthdee_smart_alerts_on', 'theme_garthdee'),
            'disabled' => get_string('garthdee_smart_alerts_off', 'theme_garthdee')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
        
        $page->add(new admin_setting_heading('theme_garthdee/course_alert_title', get_string('section_course_alert', 'theme_garthdee'), ''));
        
        $name = 'theme_garthdee/garthdee_student_course_alert';                                                                                                   
        $title = get_string('garthdee_student_course_alert', 'theme_garthdee');                                                                                   
        $description = get_string('garthdee_student_course_alert_desc', 'theme_garthdee');
        
        $choices = Array(
            'enabled' => get_string('garthdee_student_course_alert_on', 'theme_garthdee'),
            'disabled' => get_string('garthdee_student_course_alert_off', 'theme_garthdee')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
        
        $setting = new admin_setting_configtextarea('theme_garthdee/garthdee_student_course_alert_text',                                                              
            get_string('garthdee_student_course_alert_text', 'theme_garthdee'), get_string('garthdee_student_course_alert_text_desc', 'theme_garthdee'), '<p>If you can\'t see a course that you expect to see, please contact your lecturer.</p>', PARAM_RAW);                                                                                            
        $page->add($setting);
        
        $page->add(new admin_setting_heading('theme_garthdee/downtime_title', get_string('section_downtime', 'theme_garthdee'), ''));
        
        $setting = new admin_setting_configtext('theme_garthdee/garthdee_downtime_datetime',                                                              
        get_string('garthdee_downtime_datetime', 'theme_garthdee'), get_string('garthdee_downtime_datetime_desc', 'theme_garthdee'), '2021-02-08 00:00:00', PARAM_RAW);                                                                                            
        $page->add($setting);
        
        $name = 'theme_garthdee/garthdee_downtime_length';                                                                                                   
        $title = get_string('garthdee_downtime_length', 'theme_garthdee');                                                                                   
        $description = get_string('garthdee_downtime_length_desc', 'theme_garthdee');
        
        $choices = Array(
            '30'  => get_string('garthdee_downtime_length_30', 'theme_garthdee'),
            '60'  => get_string('garthdee_downtime_length_60', 'theme_garthdee'),
            '90'  => get_string('garthdee_downtime_length_90', 'theme_garthdee'),
            '120' => get_string('garthdee_downtime_length_120', 'theme_garthdee'),
            '180' => get_string('garthdee_downtime_length_180', 'theme_garthdee'),
            '240' => get_string('garthdee_downtime_length_240', 'theme_garthdee'),
            '480' => get_string('garthdee_downtime_length_480', 'theme_garthdee'),
            '600' => get_string('garthdee_downtime_length_600', 'theme_garthdee'),
            '720' => get_string('garthdee_downtime_length_720', 'theme_garthdee'),
        );                                                                     
        $default = '180';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                    
        $page->add($setting);
        
        $setting = new admin_setting_configtextarea('theme_garthdee/garthdee_downtime_details',                                                              
            get_string('garthdee_downtime_details', 'theme_garthdee'), get_string('garthdee_downtime_details_desc', 'theme_garthdee'), 'The site may be unavailable or slower than usual during the upgrade.', PARAM_RAW);                                                                                            
        $page->add($setting);
        
        $page->add(new admin_setting_heading('theme_garthdee/general_notification_title', get_string('section_notification', 'theme_garthdee'), ''));
        
        $name = 'theme_garthdee/garthdee_notification_type';                                                                                                   
        $title = get_string('garthdee_notification_type', 'theme_garthdee');                                                                                   
        $description = get_string('garthdee_notification_type_desc', 'theme_garthdee');
        
        $choices = Array(
            'alert-none' => get_string('garthdee_notification_none', 'theme_garthdee'),
            'alert-danger' => get_string('garthdee_notification_danger', 'theme_garthdee'),
            'alert-warning' => get_string('garthdee_notification_warning', 'theme_garthdee'),
            'alert-success' => get_string('garthdee_notification_success', 'theme_garthdee'),
            'alert-info' => get_string('garthdee_notification_info', 'theme_garthdee'),
            'alert-grey' => get_string('garthdee_notification_grey', 'theme_garthdee')
        );
        $default = 'alert-none';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                    
        $page->add($setting);
                                                                               
        $setting = new admin_setting_configtextarea('theme_garthdee/garthdee_notification',                                                              
            get_string('garthdee_notification', 'theme_garthdee'), get_string('garthdee_notification_desc', 'theme_garthdee'), '', PARAM_RAW);                                                                                            
        $page->add($setting);
        
        $settings->add($page);
        
        $page = new admin_settingpage('theme_garthdee_content_warning', get_string('page_content_warning', 'theme_garthdee')); 
        
        $page->add(new admin_setting_heading('theme_garthdee/content_warning', get_string('section_content_warning', 'theme_garthdee'), ''));
        
        $name = 'theme_garthdee/content_warning_enabled';                                                                                                   
        $title = get_string('content_warning_enabled', 'theme_garthdee');                                                                                   
        $description = get_string('content_warning_enabled_desc', 'theme_garthdee');
        
        $choices = Array(
            'enabled' => get_string('option_on', 'theme_garthdee'),
            'disabled' => get_string('option_off', 'theme_garthdee')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
        
        $setting = new admin_setting_configtextarea('theme_garthdee/content_warning_text',                                                              
        get_string('content_warning_text', 'theme_garthdee'), get_string('content_warning_text_desc', 'theme_garthdee'), '<h3>Content Advice</h3><p>The content and discussion in this module may cover themes and topics that, for some students, are more difficult to deal with. The online learning and in-person classrooms have been designed to provide an open space to engage sensitively and empathetically with the core topics set in the course syllabus. However, students finding particular challenges with the content will be supported appropriately via <a class="alert-link" href="https://www.rgu.ac.uk/life-at-rgu/support-advice-services">Support & Advice Services</a>.</p>', PARAM_RAW); 
        
        $page->add($setting);
        
        $settings->add($page);
        /*
        $page = new admin_settingpage('theme_garthdee_navigation', get_string('page_navigation', 'theme_garthdee'));
        
        $page->add(new admin_setting_heading('theme_garthdee/help_icon', get_string('section_help_icon', 'theme_garthdee'), ''));
        
        $name = 'theme_garthdee/help_show';                                                                                                   
        $title = get_string('help_icon_enabled', 'theme_garthdee');                                                                                   
        $description = get_string('help_icon_enabled_desc', 'theme_garthdee');
        
        $choices = Array(
            'enabled' => get_string('option_on', 'theme_garthdee'),
            'disabled' => get_string('option_off', 'theme_garthdee')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        
        $page->add($setting);
        
        $setting = new admin_setting_configtext('theme_garthdee/help_location',                                                              
        get_string('help_icon_link', 'theme_garthdee'), get_string('help_icon_link_desc', 'theme_garthdee'), '', PARAM_RAW);                                                                                            
        $page->add($setting);
        
        $settings->add($page);
        */
    }
