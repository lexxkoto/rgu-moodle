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
 * Theme functions.
 *
 * @package    theme_garthdee
 * @copyright  2022 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function theme_garthdee_get_main_scss_content($theme) {         
                                                                       
    global $CFG;                                                                                                                    
 
    $scss = '';
    
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : 'purple';
    $scss .= file_get_contents($CFG->dirroot . '/theme/garthdee/scss/preset-'.$filename.'.scss');
    
    $sheets = Array('config');
    
    foreach($sheets as $sheet) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/garthdee/scss/'.$sheet.'.scss');
    } 
                                                                                                                         
    $scss .= theme_boost_get_main_scss_content($theme);
    
    $sheets = Array('garthdee', 'custom-styles');
    
    foreach($sheets as $sheet) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/garthdee/scss/'.$sheet.'.scss');
    }                                                   
 
    return $scss;                                                                                                                   
}