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
 * Renderer for rgu filter.
 * @copyright 2023 Alex Walker
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_rgu_renderer extends plugin_renderer_base {

    public function render_content_table($records) {
        
        $string = '';
        
        $string .= '<table class="table table-striped"><thead><tr><th>Content Key</th><th>Embed Code</th><th>Settings</th></tr></thead><tbody>';
        
        foreach($records as $record) {
	        $string .= '<tr>';
	        $string .= '<td>'.$record->contentkey.'</td>';
	        $string .= '<td><pre>{RGU::content_item,key='.$record->contentkey.'}</pre></td>';
	        $string .= '<td><a class="btn btn-success mr-2" href="edit.php?id='.$record->id.'"><i class="fa fa-pencil"></i> Edit</a><a class="btn btn-danger" href="delete.php?id='.$record->id.'"><i class="fa fa-trash"></i> Delete</a></td>';
	        $string .= '</tr>';
        }
        
        $string .= '</tbody></table>';
        
		return $string;
    }
}
