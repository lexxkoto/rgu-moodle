<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    if ($hassiteconfig) {
        $page = new admin_settingpage('local_rgu_core_services', get_string('configtitle', 'local_rgu_core_services'));
        $ADMIN->add('localplugins', $page);
        
        $page->add(new admin_setting_heading('local_rgu_core_services/general_title', get_string('page_general', 'local_rgu_core_services'), ''));
        
        $name = 'local_rgu_core_services/number_in_lastname_enabled';                                                                                                   
        $title = get_string('number_in_lastname_enabled', 'local_rgu_core_services');                                                                                   
        $description = get_string('number_in_lastname_enabled_desc', 'local_rgu_core_services');
        
        $choices = Array(
            'enabled' => get_string('option_on', 'local_rgu_core_services'),
            'disabled' => get_string('option_off', 'local_rgu_core_services')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);

        
        $page->add(new admin_setting_heading('local_rgu_core_services/sitsdb_title', get_string('page_sits_db', 'local_rgu_core_services'), ''));
        
        $name = 'local_rgu_core_services/sits_db_enabled';                                                                                                   
        $title = get_string('sits_db_enabled', 'local_rgu_core_services');                                                                                   
        $description = get_string('sits_db_enabled_desc', 'local_rgu_core_services');
        
        $choices = Array(
            'enabled' => get_string('option_on', 'local_rgu_core_services'),
            'disabled' => get_string('option_off', 'local_rgu_core_services')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
                
        $name = 'local_rgu_core_services/sits_db_host';
        $title = get_string('sits_db_host', 'local_rgu_core_services');
        $desc = get_string('sits_db_host_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'sits-db-mood-01.rgu.ac.uk', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/sits_db_username';
        $title = get_string('sits_db_username', 'local_rgu_core_services');
        $desc = get_string('sits_db_username_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'moodlero', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/sits_db_password';
        $title = get_string('sits_db_password', 'local_rgu_core_services');
        $desc = get_string('sits_db_password_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configpasswordunmask($name, $title, $desc, '', PASSWORD_LOWER);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/sits_db_database';
        $title = get_string('sits_db_database', 'local_rgu_core_services');
        $desc = get_string('sits_db_database_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'intuit', PARAM_TEXT);
    
        $page->add($setting);
        
        $page->add(new admin_setting_heading('local_rgu_core_services/avatar_title', get_string('page_avatar', 'local_rgu_core_services'), ''));
        
        $name = 'local_rgu_core_services/avatar_enabled';                                                                                                   
        $title = get_string('avatar_enabled', 'local_rgu_core_services');                                                                                   
        $description = get_string('avatar_enabled_desc', 'local_rgu_core_services');
        
        $choices = Array(
            'enabled' => get_string('option_on', 'local_rgu_core_services'),
            'disabled' => get_string('option_off', 'local_rgu_core_services')
        );                                                                     
        $default = 'disabled';
        
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
        $page->add($setting);
        
        $name = 'local_rgu_core_services/avatar_host';
        $title = get_string('avatar_host', 'local_rgu_core_services');
        $desc = get_string('avatar_host_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'matricphoto.rgu.ac.uk', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/avatar_port';
        $title = get_string('avatar_port', 'local_rgu_core_services');
        $desc = get_string('avatar_port_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, '21', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/avatar_username';
        $title = get_string('avatar_username', 'local_rgu_core_services');
        $desc = get_string('avatar_username_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'photoupload', PARAM_TEXT);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/avatar_password';
        $title = get_string('avatar_password', 'local_rgu_core_services');
        $desc = get_string('avatar_password_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configpasswordunmask($name, $title, $desc, '', PASSWORD_LOWER);
    
        $page->add($setting);
        
        $name = 'local_rgu_core_services/avatar_folder';
        $title = get_string('avatar_folder', 'local_rgu_core_services');
        $desc = get_string('avatar_folder_desc', 'local_rgu_core_services');
        $setting = new admin_setting_configtext($name, $title, $desc, 'Photos', PARAM_TEXT);
    
        $page->add($setting);
    }