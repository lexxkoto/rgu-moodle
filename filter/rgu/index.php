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
 * This page shows all course enrolment options for current user.
 *
 * @package    core_enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$contentItems = $DB->get_records('filter_rgu_content', array());

$context = context_system::instance();

require_login();
require_capability('filter/rgu:manage', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_url('/filter/rgu/index.php');

$PAGE->set_title(get_string('managecontent', 'filter_rgu'));
$PAGE->set_heading(get_string('managecontent', 'filter_rgu'));

$output = $PAGE->get_renderer('filter_rgu');

echo $OUTPUT->header();

echo '<style>.table td { vertical-align: middle; } td pre { border: none; padding: 0; margin: 0; background: transparent; }</style>';

echo $output->render_content_table($contentItems);

echo $OUTPUT->footer();
