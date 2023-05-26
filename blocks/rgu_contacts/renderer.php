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

defined('MOODLE_INTERNAL') || die();

class block_rgu_contacts_renderer extends plugin_renderer_base {

    /**
     * Return HTML for RGU contacts block
     *
     * @param array $accordiondata
     * @param obj $blockinstance
     * @return type
     */
    public function display_accordion($accordiondata, $blockinstance) {

        $this->page->requires->js('/blocks/rgu_contacts/js/rgu_contacts.js');
	$this->page->requires->jquery_plugin('ui');
        $html = html_writer::start_div('', array('id' => 'rgu_contacts_accordion'));
        foreach ($accordiondata as $accordionpanel) {
            if ($accordionpanel->tabuser == BLOCK_RGU_CONTACTS_MANUAL) {
                $html .= html_writer::tag('h3', $accordionpanel->tabtitle);
                $html .= html_writer::div($accordionpanel->tabcontent);
            } else {
                $html .= $this->automatic_panel($accordionpanel);
            }
        }
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Returns HTML for RGU contacts block 'auto' format panel
     *
     * @param obj $accordionpanel
     * @return string
     */
    public function automatic_panel($accordionpanel) {
        global $OUTPUT, $PAGE;

        $userid = $accordionpanel->tabuser;
        $user = blocks_rgu_contacts_get_user_details($userid);
        $fullname = fullname($user[$userid]);
        $userroles = blocks_rgu_contacts_get_user_roles($PAGE->context, $userid);

        $tabtitle = '';
        if ($accordionpanel->tabtitletype == BLOCK_RGU_CONTACTS_TAB_COURSEROLE) {
            $tabtitle = implode(',', $userroles);
        } else {
            $tabtitle = $fullname;
        }

        $profileurl = new moodle_url('/user/profile.php', array('id' => $userid));
        $profileimage = $OUTPUT->user_picture((object)$user[$userid], array('size' => BLOCK_RGU_CONTACTS_PHOTO_SIZE));
        $profilelink = html_writer::link($profileurl, $profileimage);
        $fullprofilelink = html_writer::link($profileurl, get_string('fullprofile','block_rgu_contacts'));
        $profiledesc = shorten_text($user[$userid]->description, BLOCK_RGU_CONTACTS_DESCRIPTION_PREVIEW_LENGTH);

        $tabcontent = html_writer::div($profilelink, 'profileimage');
        $tabcontent .= html_writer::div($fullname, 'fullname');
        $tabcontent .= html_writer::div(get_string('userroles', 'block_rgu_contacts', implode(',', $userroles)), 'userroles');
        $tabcontent .= html_writer::div($profiledesc, 'profiledesc');
        $tabcontent .= html_writer::div($fullprofilelink, 'viewfullprofile');
        $tabcontent .= html_writer::div(get_string('emaillink', 'block_rgu_contacts', $user[$userid]->email), 'email');

        $html = html_writer::tag('h3', $tabtitle);
        $html .= html_writer::div($tabcontent);

        return $html;
    }

    /**
     * Display 'no contacts' information in block
     *
     * @return string HTML
     */
    public function display_no_contacts() {
        return html_writer::tag('p', get_string('nocontacts', 'block_rgu_contacts'), array('class' => 'no-contacts'));
    }
}
