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
    $page = new admin_settingpage('enrol_enrol_sits', get_string('configtitle', 'enrol_sits'));
    $ADMIN->add('enrolments', $page);

    $page->add(new admin_setting_heading('enrol_sits/sitsdb_title', get_string('page_sits_db', 'enrol_sits'), ''));
        
    $name = 'enrol_sits/sits_db_enabled';                                                                                                   
    $title = get_string('sits_db_enabled', 'enrol_sits');                                                                                   
    $description = get_string('sits_db_enabled_desc', 'enrol_sits');
    
    $choices = Array(
        'enabled' => get_string('option_on', 'enrol_sits'),
        'disabled' => get_string('option_off', 'enrol_sits')
    );                                                                     
    $default = 'disabled';
    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                                                                                                                                                                     
    $page->add($setting);
            
    $name = 'enrol_sits/sits_db_host';
    $title = get_string('sits_db_host', 'enrol_sits');
    $desc = get_string('sits_db_host_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'sits-db-mood-01.rgu.ac.uk', PARAM_TEXT);

    $page->add($setting);
    
    $name = 'enrol_sits/sits_db_username';
    $title = get_string('sits_db_username', 'enrol_sits');
    $desc = get_string('sits_db_username_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'moodlero', PARAM_TEXT);

    $page->add($setting);
    
    $name = 'enrol_sits/sits_db_password';
    $title = get_string('sits_db_password', 'enrol_sits');
    $desc = get_string('sits_db_password_desc', 'enrol_sits');
    $setting = new admin_setting_configpasswordunmask($name, $title, $desc, '', PASSWORD_LOWER);

    $page->add($setting);
    
    $name = 'enrol_sits/sits_db_database';
    $title = get_string('sits_db_database', 'enrol_sits');
    $desc = get_string('sits_db_database_desc', 'enrol_sits');
    $setting = new admin_setting_configtext($name, $title, $desc, 'intuit', PARAM_TEXT);

    $page->add($setting);

}
