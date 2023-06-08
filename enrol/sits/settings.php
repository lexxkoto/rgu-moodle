<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Database enrolment plugin settings and presets.
 *
 * @package    enrol
 * @subpackage gudatabase
 * @copyright  2012 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings->add(new admin_setting_heading('enrol_sits/sitsdb_title', get_string('page_sits_db', 'enrol_sits'), ''));
        
    $name = 'enrol_sits/sits_db_enabled';                                                                                                   
    $title = get_string('sits_db_enabled', 'enrol_sits');                                                                                   
    $description = get_string('sits_db_enabled_desc', 'enrol_sits');
    
    $choices = Array(
        'enabled' => get_string('option_on', 'enrol_sits'),
        'disabled' => get_string('option_off', 'enrol_sits')
    );                                                                     
    $default = 'disabled';
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
    $settings->add($setting);
            
    $name = 'enrol_sits/sits_db_host';
    $title = get_string('sits_db_host', 'enrol_sits');
    $desc = get_string('sits_db_host_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'sits-mood-db-01.rgu.ac.uk', PARAM_TEXT);

    $settings->add($setting);
    
    $name = 'enrol_sits/sits_db_username';
    $title = get_string('sits_db_username', 'enrol_sits');
    $desc = get_string('sits_db_username_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'moodlero', PARAM_TEXT);

    $settings->add($setting);
    
    $name = 'enrol_sits/sits_db_password';
    $title = get_string('sits_db_password', 'enrol_sits');
    $desc = get_string('sits_db_password_desc', 'enrol_sits');
    $setting = new admin_setting_configpasswordunmask($name, $title, $desc, '', PASSWORD_LOWER);

    $settings->add($setting);
    
    $name = 'enrol_sits/sits_db_database';
    $title = get_string('sits_db_database', 'enrol_sits');
    $desc = get_string('sits_db_database_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'intuit', PARAM_TEXT);

    $settings->add($setting);
    
    $settings->add(new admin_setting_heading('enrol_sits/parameters_title', get_string('page_parameters', 'enrol_sits'), ''));
    
    $name = 'enrol_sits/sits_current_year';
    $title = get_string('sits_current_year', 'enrol_sits');
    $desc = get_string('sits_current_year_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, '2022', PARAM_TEXT);

    $settings->add($setting);
    
    $name = 'enrol_sits/allowed_codes';
    $title = get_string('sits_allowed_codes', 'enrol_sits');
    $desc = get_string('sits_allowed_codes_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, '"C","PF","AC","PL","PWR","R","COM","PM"', PARAM_TEXT);

    $settings->add($setting);
    
    $settings->add(new admin_setting_heading('enrol_sits/cron_title', get_string('page_cron', 'enrol_sits'), ''));
    
    $name = 'enrol_sits/cron_num_courses';                       
    $title = get_string('cron_num_courses', 'enrol_sits');                                                                                   
    $description = get_string('cron_num_courses_desc', 'enrol_sits');
    
    $choices = Array(
        '5' => '5',
        '10' => '10',
        '25' => '25',
        '50' => '50',
        '100' => '100',
    );                                                                     
    $default = '10';
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
    $settings->add($setting);
    
    $name = 'enrol_sits/cron_inactive_time';                       
    $title = get_string('cron_inactive_time', 'enrol_sits');                                                                                   
    $description = get_string('cron_inactive_time_desc', 'enrol_sits');
    
    $choices = Array(
        '86400' => get_string('cron_inactive_1_day', 'enrol_sits'),
        '604800' => get_string('cron_inactive_1_week', 'enrol_sits'),
        '1209600' => get_string('cron_inactive_2_weeks', 'enrol_sits'),
        '2419200' => get_string('cron_inactive_4_weeks', 'enrol_sits'),
        '4838400' => get_string('cron_inactive_8_weeks', 'enrol_sits'),
        '7257600' => get_string('cron_inactive_12_weeks', 'enrol_sits'),
        '14515200' => get_string('cron_inactive_24_weeks', 'enrol_sits'),
        '29030400' => get_string('cron_inactive_48_weeks', 'enrol_sits'),
    );                                                                     
    $default = '2419200';
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                               
    $settings->add($setting);
    
    $name = 'enrol_sits/trigger_inactive_time';                       
    $title = get_string('trigger_inactive_time', 'enrol_sits');                                                                                   
    $description = get_string('trigger_inactive_time_desc', 'enrol_sits');
    
    $choices = Array(
        '300' => get_string('trigger_5_minutes', 'enrol_sits'),
        '900' => get_string('trigger_15_minutes', 'enrol_sits'),
        '1800' => get_string('trigger_30_minutes', 'enrol_sits'),
        '3600' => get_string('trigger_1_hour', 'enrol_sits'),
        '7200' => get_string('trigger_2_hours', 'enrol_sits'),
        '14400' => get_string('trigger_4_hours', 'enrol_sits'),
        '21600' => get_string('trigger_6_hours', 'enrol_sits'),
        '43200' => get_string('trigger_12_hours', 'enrol_sits'),
        '86400' => get_string('trigger_1_day', 'enrol_sits'),
    );                                                                     
    $default = '2419200';
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);     
    
    $settings->add($setting);
    
}
