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
 * @package    block
 * @subpackage rgu_contacts
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die;

$roleoptions = array();
if ($roles = $DB->get_records('role')) {
    foreach($roles as $role) {
        $roleoptions[$role->id] = s($role->name);
    }
}

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_configmultiselect(
                'rgu_contacts_roles',
                get_string('settings', 'block_rgu_contacts'),
                '',
                array(),
                $roleoptions
            )
        );
}
