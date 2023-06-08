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
 * RGU Tabbed Content module.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2013 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2013 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * Form for editing module instances.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

class tabbedcontent_form extends moodleform {

    function definition() {
        global $CFG;
        $mform = $this->_form;

        $title = $this->_customdata['title'];
        $cmid = $this->_customdata['cmid'];
        $tab = $this->_customdata['tab'];
        $tabposition = $this->_customdata['tabposition'];

        $mform->addElement('header', 'general', $title);

        $mform->addElement('text', 'tabtitle', get_string('paneltitle','tabbedcontent'));
        $mform->setType('tabtitle', PARAM_TEXT);
        $mform->addRule('tabtitle', get_string('required'), 'required', null, 'client');

        $positionoptions = mod_tabbedcontent_position_options($this->_customdata['cmid'], $this->_customdata['tab']);
        $position = $mform->addElement('select', 'tabposition', get_string('panelposition', 'tabbedcontent'), $positionoptions);
        $position->setSelected($tabposition);

        $editoroptions = array(
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true
        );
        $mform->addElement('editor', 'tabcontent', get_string('panelcontent','tabbedcontent'), null, $editoroptions);
        $mform->setType('tabcontent', PARAM_RAW);
        $mform->addRule('tabcontent', get_string('required'), 'required', null, 'client');

        $mform->addElement('hidden', 'cmid', $this->_customdata['cmid']);
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'tab', $this->_customdata['tab']);
        $mform->setType('tab', PARAM_INT);

        $this->add_action_buttons();
    }
}
