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

define('BLOCK_RGU_CONTACTS_MANUAL', 0);
define('BLOCK_RGU_CONTACTS_PHOTO_SIZE', 80);
define('BLOCK_RGU_CONTACTS_DESCRIPTION_PREVIEW_LENGTH', 250);
define('BLOCK_RGU_CONTACTS_TAB_FULLNAME', 1);
define('BLOCK_RGU_CONTACTS_TAB_COURSEROLE', 2);

/**
 * Return specific user details
 *
 * @param int $userid
 * @return array
 */
function blocks_rgu_contacts_get_user_details($userid) {
    global $DB;

    return $DB->get_records('user', array('id' => $userid), '',
            'id, picture, firstname, lastname, firstnamephonetic, lastnamephonetic,
            middlename, alternatename, imagealt, email, description');
}

/**
 * Return array of role names for passed user
 *
 * @param obj $context
 * @param int $userid
 * @return array
 */
function blocks_rgu_contacts_get_user_roles($context, $userid) {
    global $CFG;

    $courseroles = get_user_roles($context, $userid, false);
    $allowedroles = explode(',', $CFG->rgu_contacts_roles);
    $userroles = array();
    foreach ($courseroles as $courserole) {
        if (in_array($courserole->roleid, $allowedroles)) {
            $userroles[] = $courserole->name;
        }
    }

    return $userroles;
}

/**
 * Return all users select menu ready which match context and role criteria.
 *
 * @param integer $contextid a course context id.
 * @param boolean $students include students.
 * @return array list of users which match context and role criteria.
 */
function block_rgu_contacts_get_course_users_select($contextid) {
    global $DB, $CFG;

    $courseusers = array();
    $courseusers[BLOCK_RGU_CONTACTS_MANUAL] = get_string('manualcontent', 'block_rgu_contacts');

    if (!$CFG->rgu_contacts_roles) {
        return $courseusers; // No roles enabled - manual content option only.
    }

    $params = array($contextid);
    $roles = explode(',', $CFG->rgu_contacts_roles);
    list($rolesql, $roleparams) = $DB->get_in_or_equal(array_values($roles));
    $params = array_merge($params, $roleparams);
    $sql = "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
              FROM {role_assignments} ra
              JOIN {user} u ON u.id = ra.userid
             WHERE u.deleted = 0 AND u.confirmed = 1 AND ra.contextid = ? AND ra.roleid {$rolesql}
          ORDER BY ra.id DESC, u.firstname, u.lastname";

    if ($users = $DB->get_records_sql($sql, $params)) {
        foreach (array_keys($users) as $key) {
            $courseusers[$key] = get_string('autocontent', 'block_rgu_contacts').': '.fullname($users[$key]);
        }
    }

    return $courseusers;
}
