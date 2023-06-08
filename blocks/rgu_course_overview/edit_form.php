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
 * @subpackage rgu_course_overview
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

class block_rgu_course_overview_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        $mform->addElement('header', 'blockinstancesettings', get_string('blockinstancesettings', 'block_rgu_course_overview'));

        $mform->addElement('text', 'config_instancepaginationvalue', get_string('instancepaginationvalue', 'block_rgu_course_overview'));
        $mform->setType('config_instancepaginationvalue', PARAM_INT);
        $mform->addHelpButton('config_instancepaginationvalue', 'instancepaginationvalue', 'block_rgu_course_overview');
        $mform->setDefault('instancepaginationvalue', block_rgu_course_overview_get_pagesize());
    }

}
