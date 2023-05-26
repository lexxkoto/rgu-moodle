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

require_once($CFG->dirroot.'/blocks/rgu_contacts/locallib.php');

class block_rgu_contacts extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_rgu_contacts');
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('site-index' => false, 'course' => true, 'my' => false);
    }

    public function specialization() {
        $this->title = !empty($this->config->title) ? $this->config->title :
                get_string('pluginname', 'block_rgu_contacts');
    }

    public function get_content() {
        global $DB, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return null;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (!isloggedin() || isguestuser()) {
            return $this->content;
        }

        $renderer = $this->page->get_renderer('block_rgu_contacts');

        $accordiondata = array();
        if ($this->config) {
            foreach (array_keys($this->config->tabuser) as $key) {
                $accordiondata[$key] = new stdClass();
                $accordiondata[$key]->id = $key;
                $accordiondata[$key]->tabuser = (isset($this->config->tabuser[$key])) ?
                        $this->config->tabuser[$key] : '';
                $accordiondata[$key]->tabtitletype = (isset($this->config->tabtitletype[$key])) ?
                        $this->config->tabtitletype[$key] : '';
                $accordiondata[$key]->tabtitle = (isset($this->config->tabtitle[$key])) ?
                        $this->config->tabtitle[$key] : '';
                $accordiondata[$key]->tabcontent = (isset($this->config->tabcontent[$key]['text'])) ?
                        $this->config->tabcontent[$key]['text'] : '';
            }
        }

        if (!empty($accordiondata)) {
            $blockinstance = $this->instance;
            $content = $renderer->display_accordion($accordiondata, $blockinstance);
        } else {
            $content = $renderer->display_no_contacts();
        }

        $this->content->text = $content;

        return $this->content;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function instance_config_save($data, $nolongerused = false) {

        if (isset($data->tabuser) && !empty($data->tabuser)) {
            foreach ($data->tabuser as $key => $value) {
                if (isset($data->delete[$key]) && $data->delete[$key]) {
                	unset($data->delete[$key]);
                    unset($data->tabuser[$key]);
                    unset($data->tabtitle[$key]);
                    unset($data->tabtitletype[$key]);
                    unset($data->tabcontent[$key]);
                }
            }
        }

        $keys = array_keys($data->tabuser);
        $tabusers = $data->tabuser;
        $tabtitles = $data->tabtitle;
        if (isset($data->tabtitletype)) {
            $tabtitletypes = $data->tabtitletype;
        }
        $tabcontents = $data->tabcontent;

        foreach ($keys as $key => $value) {
            $data->tabuser[$key] = $tabusers[$value];
            if (isset($tabtitles[$value])) {
                $data->tabtitle[$key] = $tabtitles[$value];
            } else {
                $data->tabtitle[$key] = '';
            }
            if (isset($tabtitletypes[$value])) {
                $data->tabtitletype[$key] = $tabtitletypes[$value];
            } else {
                $data->tabtitletype[$key] = '';
            }
            $data->tabcontent[$key]['text'] = $tabcontents[$value]['text'];
        }

        ksort($data->tabuser);
        ksort($data->tabtitle);
        ksort($data->tabtitletype);
        ksort($data->tabcontent);

        $tabno = count($keys);
        $data->tabuser = array_slice($data->tabuser, 0, $tabno);
        $data->tabtitle = array_slice($data->tabtitle, 0, $tabno);
        $data->tabtitletype = array_slice($data->tabtitletype, 0, $tabno);
        $data->tabcontent = array_slice($data->tabcontent, 0, $tabno);

        parent::instance_config_save($data);
    }
}
