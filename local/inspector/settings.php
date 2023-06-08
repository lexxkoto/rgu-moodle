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
 * GUID Enrolment sync
 *
 * @package    local_backupcleaner
 * @copyright  2021 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage(
            'local_inspector', get_string('pluginname', 'local_inspector'));
    $ADMIN->add('localplugins', $settings);
    
    $name = 'local_inspector/security_key';
    $title = get_string('security_key', 'local_inspector');
    $desc = get_string('security_key_desc', 'local_inspector');
    $setting = new admin_setting_configtext($name, $title, $desc, '74833e911605ee374986da0e41874371', PARAM_TEXT);

    $settings->add($setting);
    
    $name = 'local_inspector/prefix';
    $title = get_string('prefix', 'local_inspector');
    $desc = get_string('prefix', 'local_inspector');
    $setting = new admin_setting_configtext($name, $title, $desc, 'mdlv', PARAM_TEXT);

    $settings->add($setting);
}
