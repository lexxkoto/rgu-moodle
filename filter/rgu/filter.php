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
 * Filter main class for the filter_pluginname plugin.
 *
 * @package   filter_rgu
 * @copyright 2023 Alex Walker a.walker43@rgu.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class filter_rgu extends moodle_text_filter {
	 
	function filter($text, array $options = []) {
		
		if (!is_string($text) or empty($text)) {
	        return $text;
	    }
		
		$pattern = '/\{RGU::([^}]*)\}/';
		$newtext =  preg_replace_callback($pattern, fn($m)=>filter_rgu::rewrite($m), $text);
		 
		if(is_null($newtext)){
			return $text;
		}	
		return $newtext;
	}
	
	function rewrite($array) {
		$filterText = $array[1];
		$parts = explode(',', $filterText);
		
		$filterType = $parts[0];
		
		$text = '';
		
		switch($filterType) {
			case 'content_item':
				$filterItem = str_replace('key=', '', $parts[1]);
				$text = 'I am looking for content item '.$filterItem;
				break;
			default:
				$text = 'I don\'t know what I am: '.$filterType;
				break;
		}
		
		return $text;
	}
	 
 }