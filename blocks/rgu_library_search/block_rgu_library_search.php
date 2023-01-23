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
 * A block which displays text with a user selected background image
 *
 * @package    block
 * @subpackage rgu_notepad
 * @copyright  2012 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2012 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

class block_rgu_library_search extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_rgu_library_search');
    }

    public function get_content() {
        
        $this->page->requires->js_call_amd('block_rgu_library_search/library_search', 'init');
        
        $this->content = new stdClass;

        $this->content->text = '<form id="rguls-form" name="searchForm" method="get" target="_self" action="https://librarysearch.rgu.ac.uk/discovery/search" enctype="application/x-www-form-urlencoded; charset=utf-8" onsubmit="searchPrimo()">
<input type="hidden" name="vid" value="44RGU_INST:VU1">
<input type="hidden" name="tab" value="Everything">
<input type="hidden" name="search_scope" value="MyInst_and_CI">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="query" id="primoQuery">
<!-- Fixed parameters -->
<div class="input-group">
    <input type="text" id="primoQueryTemp" value="" />
    <div class="input-group-append">
        <button type="submit" class="btn btn-info" id="rguls-go"><i class="fa fa-search"></i><span class="sr-only">Search</span></button>
    </div>
</div>
<!-- Search Button -->

</form>';

        return $this->content;
    }

    public function instance_allow_multiple() {
        return false;
    }

}
