<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    if ($ADMIN->fulltree) {
    
        $settings = new theme_boost_admin_settingspage_tabs('themesettinggarthdee', get_string('configtitle', 'theme_garthdee'));
        
        $page = new admin_settingpage('theme_garthdee_preset', get_string('page_presets', 'theme_garthdee'));
        
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
        
        $page = new admin_settingpage('theme_hillhead_notifications', get_string('page_notifications', 'theme_garthdee')); 
        
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
    }