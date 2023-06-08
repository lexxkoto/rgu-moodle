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

require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/blocks/rgu_course_overview/locallib.php');
require_once($CFG->dirroot . '/blocks/rgu_course_overview/rgulocallib.php');
require_once($CFG->dirroot . '/local/course/lib.php');

//$rgucourses = rgu_sort_courses(enrol_get_my_courses());


class block_rgu_course_overview extends block_base {

    /**
     * Block initialization
     */
    public function init() {

        $this->title = get_string('blocktitle', 'block_rgu_course_overview');
    }

    /**
     * Allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {

        return true;
    }

    /**
     * Locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {

        return array('my' => true);
    }

    /**
     * Sets block header to be hidden or visible
     *
     * @return bool if true then header will be visible.
     */
    public function hide_header() {

        $config = get_config('block_rgu_course_overview');
        return !empty($config->hideheader);
    }

    /**
     * Return contents of rgu_course_overview block
     *
     * @return stdClass
     */
    public function get_content() {
        global $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->page->requires->js_call_amd('block_rgu_course_overview/course_overview', 'init', array());

        $currenttabargs = array(
            array(
                'updatepanel' => '/blocks/rgu_course_overview/ajax/updatepanel.php',
                'wwwroot' => $CFG->wwwroot
            )
        );

        $previoustabargs = array(
            array(
                'updatepreviouspanel' => '/blocks/rgu_course_overview/ajax/updatepreviouspanel.php',
                'wwwroot' => $CFG->wwwroot
            )
        );

        $searchtabargs = array(
            array(
                'updatesearchresults' => '/blocks/rgu_course_overview/ajax/updatesearchresults.php',
                'wwwroot' => $CFG->wwwroot
            )
        );

       	// $courses = block_rgu_course_overview_get_courses();
       	// $previouscourses = block_rgu_course_overview_get_previous_courses();
        $renderer = $this->page->get_renderer('block_rgu_course_overview');

       /*
        if (isset($this->config->instancepaginationvalue)) {
            $pagesize = $this->config->instancepaginationvalue;
        } else {
            $pagesize = block_rgu_course_overview_get_pagesize();
        }
        */
        $pagesize = block_rgu_course_overview_get_pagesize();
        
        
        $this->content = new stdClass();
        $this->content->text = $renderer->rgu_course_overview(array(), array(), $pagesize, $this->instance->id);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Allows the block to load any JS it requires into the page.
     *
     * By default this function simply permits the user to dock the block if it is dockable.
     */
    function get_required_javascript() {
        if ($this->instance_can_be_docked() && !$this->hide_header()) {
            user_preference_allow_ajax_update('docked_block_instance_'.$this->instance->id, PARAM_INT);
        }
        $this->page->requires->js('/blocks/rgu_course_overview/js/easyResponsiveTabs.js');
    }
}
