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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
            new admin_setting_configtext('block_rgu_course_overview/defaultpaginationvalue',
                    new lang_string('defaultpaginationvalue', 'block_rgu_course_overview'),
            new lang_string('defaultpaginationvaluedesc', 'block_rgu_course_overview'),
            BLOCKS_RGU_COURSE_OVERVIEW_PAGESIZE,
            PARAM_INT
        )
    );
    $settings->add(
            new admin_setting_configcheckbox('block_rgu_course_overview/hideheader',
                    new lang_string('hideheader', 'block_rgu_course_overview'),
            new lang_string('hideheaderdesc', 'block_rgu_course_overview'),
            1, PARAM_INT
        )
    );
    $settings->add(
    		new admin_setting_configtext('block_rgu_course_overview/currentyearofstudy',
    				new lang_string('currentyearofstudy', 'block_rgu_course_overview'),
    				new lang_string('currentyearofstudydesc', 'block_rgu_course_overview'),
    				'2015', PARAM_INT
    		)
    );

    $settings->add(new admin_setting_configtext('block_rgu_course_overview_hide_recent',
    		get_string('hide_recent', 'block_rgu_course_overview'),
    		get_string('hide_recent_desc', 'block_rgu_course_overview'), 0, PARAM_INT));
    

    $settings->add(new admin_setting_configtext('block_rgu_course_overview_recent_min_courses',
    		get_string('min_recent', 'block_rgu_course_overview'),
    		get_string('min_recent_desc', 'block_rgu_course_overview'), 5, PARAM_INT));
    

    $settings->add(new admin_setting_configtext('block_rgu_course_overview_recent_limit',
    		get_string('show_x_recent_courses', 'block_rgu_course_overview'),
    		get_string('show_x_recent_courses_desc', 'block_rgu_course_overview'), 10, PARAM_INT));
    
    $linkurl = new moodle_url($CFG->wwwroot . '/blocks/rgu_course_overview/bannereditor.php');
    $link = html_writer::link($linkurl,new lang_string('showbannerseditorlink', 'block_rgu_course_overview'));
     
    
    $settings->add(
    		new admin_setting_configcheckbox('block_rgu_course_overview/showbanners',
    				new lang_string('showbanners', 'block_rgu_course_overview'),
    				$link,
    				1, PARAM_INT
    		)
    );
        
    
    
    
}
