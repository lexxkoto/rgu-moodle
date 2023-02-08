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
 * Form for editing module instances.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_tabbedcontent_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        // Header: General
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required');

        // Show name of course page
        $mform->addElement('checkbox', 'showname', get_string('showname', 'tabbedcontent'));

        // Tabs and Accordion introduction
        $this->standard_intro_elements(get_string('tabbedcontentintro', 'tabbedcontent'));

        // Display type
        $options = array(
            TABBEDCONTENT_HORIZONTAL => get_string('horizontaltype', 'tabbedcontent'),
            TABBEDCONTENT_VERTICAL => get_string('verticaltype', 'tabbedcontent')
        );
        $mform->addElement('select', 'type', get_string('displaytype', 'tabbedcontent'), $options);

        
	        
	        
	        // Header: Tab 1
	        $mform->addElement('header', 'tab1', get_string('panel1', 'tabbedcontent'));
	
	        // Tab 1 ID
	        $mform->addElement('hidden', 'tab1id');
	        $mform->setType('tab1id', PARAM_INT);
	
	        // Tab 1 title
	        $mform->addElement('text', 'tabtitle1', get_string('paneltitle','tabbedcontent'));
	        $mform->setType('tabtitle1', PARAM_TEXT);
	        $mform->addRule('tabtitle1', get_string('required'), 'required', null, 'client');
	
	        // Content editor options
	        $editoroptions = array(
	            'maxfiles' => EDITOR_UNLIMITED_FILES,
	            'noclean' => true,
	            'context' => $this->context,
	            'subdirs' => true
	        );
	
	        // Tab 1 content
	        $mform->addElement('editor', 'tabcontent1', get_string('panelcontent','tabbedcontent'), null, $editoroptions);
	        $mform->setType('tabcontent1', PARAM_RAW);
	        $mform->addRule('tabcontent1', get_string('required'), 'required', null, 'client');
	
	        // Header: Tab 2
	        $mform->addElement('header', 'tab2', get_string('panel2', 'tabbedcontent'));
	
	        // Tab 2 ID
	        $mform->addElement('hidden', 'tab2id');
	        $mform->setType('tab2id', PARAM_INT);
	
	        // Tab 2 title
	        $mform->addElement('text', 'tabtitle2', get_string('paneltitle','tabbedcontent'));
	        $mform->setType('tabtitle2', PARAM_TEXT);
	        $mform->addRule('tabtitle2', get_string('required'), 'required', null, 'client');
	
	        // Tab 2 content
	        $mform->addElement('editor', 'tabcontent2', get_string('panelcontent','tabbedcontent'), null, $editoroptions);
	        $mform->setType('tabcontent2', PARAM_RAW);
	        $mform->addRule('tabcontent2', get_string('required'), 'required', null, 'client');

        
	        if(!empty($this->_cm->instance)){
	        	$html = $this->render_edit_more_tabs();
	        	if(!empty($html)){
	        		$mform->addElement('header', 'further', get_string('editfurthertabs', 'tabbedcontent'));
	        		$mform->setExpanded('further');
	        		$mform->addElement('html',$html);
	        	}
	        }
	        	 
	        
	        
        // Common module settings
        $this->standard_coursemodule_elements();

        // Save / Cancel buttons
        $this->add_action_buttons();
    }

    /**
     * Overriding moodleform_mod's add_action_buttons() method, to remove "save changes and display" button.
     *
     * @param bool $cancel show cancel button
     * @param string $submitlabel null means default, false means none, string is label text
     * @param string $submit2label  null means default, false means none, string is label text
     * @return void
     */
    public function add_action_buttons($cancel=TRUE, $submitlabel=NULL, $submit2label=NULL) {

        $submitlabel = get_string('savechangesandreturntocourse');

        $mform =& $this->_form;

        // Elements in a row need a group
        $buttonarray = array();

        $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param mixed $default_values object or array of default values
     */
    function set_data($default_values) {
        global $DB;

        if ($default_values->coursemodule) {
        	
        	correct_null_tab_positions($default_values->id);

            $context = context_module::instance($default_values->coursemodule);

            $sql = "SELECT tabposition AS id, id AS tabid, tabtitle, tabcontent, tabcontentformat
                      FROM {tabbedcontent_content}
                     WHERE instance = ?
                  ORDER BY tabposition";
            $params = array($default_values->id);

            $tabs = $DB->get_records_sql($sql, $params, 0, 2);

            if(!empty($tabs)){
            	$this->tabs = $tabs;
            }
            
            
            if (array_key_exists(0, $tabs)) {
                $draftid_editor = file_get_submitted_draft_itemid('tabcontent1');
                $default_values->tab1id = $tabs[0]->tabid;
                $default_values->tabtitle1 = $tabs[0]->tabtitle;
                $default_values->tabcontent1['text'] = file_prepare_draft_area(
                    $draftid_editor,
                    $context->id,
                    'mod_tabbedcontent',
                    'content',
                    $tabs[0]->tabid,
                    array(),
                    $tabs[0]->tabcontent
                );
                $default_values->tabcontent1['format'] = $tabs[0]->tabcontentformat;
                $default_values->tabcontent1['itemid'] = $draftid_editor;
            }

            if (array_key_exists(1, $tabs)) {
                $draftid_editor = file_get_submitted_draft_itemid('tabcontent2');
                $default_values->tab2id = $tabs[1]->tabid;
                $default_values->tabtitle2 = $tabs[1]->tabtitle;
                $default_values->tabcontent2['text'] = file_prepare_draft_area(
                    $draftid_editor,
                    $context->id,
                    'mod_tabbedcontent',
                    'content',
                    $tabs[1]->tabid,
                    array(),
                    $tabs[1]->tabcontent
                );
                $default_values->tabcontent2['format'] = $tabs[1]->tabcontentformat;
                $default_values->tabcontent2['itemid'] = $draftid_editor;
            }
        }
        
        parent::set_data($default_values);
    }
    
    
    function render_edit_more_tabs(){
    	global $DB;
    	if(empty($this->_cm->id)||empty($this->_cm->instance)){
    		return false;
    	}
    	
    	
    	@correct_null_tab_positions($default_values->id);
    
    	$sql = "SELECT tabposition AS id, id AS tabid, tabtitle, tabcontent, tabcontentformat
                      FROM {tabbedcontent_content}
                     WHERE instance = ?
                  ORDER BY tabposition";
    	$params = array($this->_cm->instance);
    	
    	$tabs = $DB->get_records_sql($sql, $params);
    	
    	
    	
    	if(count($tabs)<3){
    		return false;
    	}	
    	$html = html_writer::start_tag('div', array('id' => 'rgu_tab_links'));
    	for($i=2;$i<count($tabs);$i++){
			$url = new moodle_url('/mod/tabbedcontent/edit.php', array('cmid' => $this->_cm->id, 'tab' => $tabs[$i]->tabid));
			$tabnumber = $tabs[$i]->id + 1;
			$text =  get_string('edittabinline', 'tabbedcontent').$tabnumber.': '.$tabs[$i]->tabtitle;
			$html .= html_writer::tag('div', html_writer::link($url,$text));
		}    	

		$html .= html_writer::end_tag('div');
		
		return $html; 
    }
    
    
    //
    
}
