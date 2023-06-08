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

class block_rgu_contacts_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_rgu_contacts'));
        $mform->setType('config_title', PARAM_MULTILANG);

        $courseusers = block_rgu_contacts_get_course_users_select($this->page->context->id);
        $tabtitletypes = array(
            BLOCK_RGU_CONTACTS_TAB_FULLNAME => get_string('fullname'),
            BLOCK_RGU_CONTACTS_TAB_COURSEROLE => get_string('courserole', 'block_rgu_contacts')
        );
        $repeatarray = array();
        $repeatarray[] = $mform->createElement('header', '', get_string('coursecontact', 'block_rgu_contacts').' {no}');
        $repeatarray[] = $mform->createElement('select', 'config_tabuser', get_string('contenttype', 'block_rgu_contacts'), $courseusers);
        $repeatarray[] = $mform->createElement('select', 'config_tabtitletype', get_string('tabtitle', 'block_rgu_contacts'), $tabtitletypes);
        $repeatarray[] = $mform->createElement('text', 'config_tabtitle', get_string('contactname', 'block_rgu_contacts'));
        $mform->setType('config_tabtitle', PARAM_TEXT);
        $repeatarray[] = $mform->createElement('editor', 'config_tabcontent', get_string('contactdetail', 'block_rgu_contacts'), 'rows=4 cols=30',  array('maxfiles' => EDITOR_UNLIMITED_FILES));
        $repeatarray[] = $mform->createElement('checkbox', 'config_delete', get_string('delete', 'block_rgu_contacts'));

        if (!empty($this->block->config->tabuser)) {
            $repeatno = count($this->block->config->tabuser);
            if ($repeatno == 1) {
                $repeatno = 2;
            }
        } else {
            $repeatno = 2;
        }

        $repeatedoptions = array();
        $repeatedoptions['config_tabtitle']['disabledif'] = array('config_tabuser', 'neq', BLOCK_RGU_CONTACTS_MANUAL);
        $repeatedoptions['config_tabtitletype']['disabledif'] = array('config_tabuser', 'eq', BLOCK_RGU_CONTACTS_MANUAL);

        // Note: disabledif does not appear to work correctly with elements of type 'editor'
        $repeatedoptions['config_tabcontent']['disabledif'] = array('config_tabuser', 'neq', BLOCK_RGU_CONTACTS_MANUAL);

        $this->repeat_elements($repeatarray, $repeatno, $repeatedoptions, 'option_repeats', 'option_add_fields', 1);
    }

    public function set_data($defaultvalues) {
        global $DB;

        parent::set_data($defaultvalues);

        $defaultvalues->tabuser = array();
        $defaultvalues->tabtitle = array();
        $defaultvalues->tabcontent = array();

        if (!empty($this->block->config) && is_object($this->block->config)) {
            if (!empty($this->block->config->tabuser) && is_array($this->block->config->tabuser)) {
                foreach ($this->block->config->tabuser as $key => $tabuser) {
                    $defaultvalues->config_tabuser[$key] = $tabuser;
                    if (isset($this->block->config->tabtitle[$key])) {
                        $defaultvalues->config_tabtitle[$key] = $this->block->config->tabtitle[$key];
                    }
                    // Text and embedded files
                    $text = '';
                    if (isset($this->block->config->tabcontent[$key]) && !empty($this->block->config->tabcontent[$key])) {
                        $text = $this->block->config->tabcontent[$key];
                    }
                    $defaultvalues->config_tabcontent[$key]['text'] = $text;
                }
            }
            parent::set_data($defaultvalues);
        }
    }
}
