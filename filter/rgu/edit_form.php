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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

require_once($CFG->libdir.'/formslib.php');

class content_items_edit_form extends moodleform {

    /**
     * Define the form for editing notes
     */
    public function definition() {
        $mform =& $this->_form; 

    	$mform->addElement('text', 'contentkey', get_string('contentkey','filter_rgu'), ' size="255" ');
        $mform->setType('contentkey', PARAM_ALPHANUM);
    
        $mform->addRule('contentkey', get_string('nokey','filter_rgu'), 'required', null, 'client');

        $mform->addElement('textarea', 'text', get_string('content', 'notes'), array('rows' => 15, 'cols' => 80));
        $mform->setType('text', PARAM_RAW);
        $mform->addRule('text', get_string('nocontent', 'filter_rgu'), 'required', null, 'client');
        $mform->setForceLtr('text', false);
        
        $audiences = Array(
            'all'               => 'Everybody',
            'staff'             => 'All Staff',
            'student'           => 'All Students',
            'student-grays'     => 'All Students in Gray\'s School of Art',
            'student-notgrays'  => 'All Students except Gray\'s School of Art',
        );

    	$mform->addElement('select', 'audience', get_string('audience','filter_rgu'), $audiences);
     	$mform->setType('audience', PARAM_RAW);

        $this->add_action_buttons();

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
    }
}


class content_item_delete_form extends moodleform {
	
	public function definition() {
		$mform =& $this->_form;
		$mform->addElement('hidden','id');
		$mform->addElement('hidden','action','delete');
		$this->add_action_buttons(TRUE,get_string('delete','filter_rgu')); 
	}
} 
