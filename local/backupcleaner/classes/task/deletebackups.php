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
 * GUID Enrolment sync
 *
 * @package    local_backupcleaner
 * @copyright  2021 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_backupcleaner\task;

defined('MOODLE_INTERNAL') || die;

class deletebackups extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('deletebackups', 'local_backupcleaner');
    }

    public function execute() {
        global $DB;

        $days = get_config('local_backupcleaner', 'min_age');
        $limit = get_config('local_backupcleaner', 'max_delete');

        $files = $DB->get_records_sql(
            'SELECT * FROM {files} WHERE (component="backup" OR component="user") AND mimetype="application/vnd.moodle.backup" AND timecreated < :deletetime',
            ['deletetime' => (time() - ( $days * 86400))],
            0,
            $limit
        );

        $fs = get_file_storage();

        $identified = 0;
        $deleted = 0;
        $bytes = 0;

        foreach ($files as $thisfile) {

            $file = $fs->get_file(
                $thisfile->contextid, $thisfile->component, $thisfile->filearea,
                $thisfile->itemid, $thisfile->filepath, $thisfile->filename
            );

            $identified += 1;

            if ($file) {
                $bytes += $thisfile->filesize;
                $file->delete();
                $deleted += 1;
            }
        }

        if ($identified > 0) {
            mtrace('Identified '.$identified.' crusty old backups.');
            mtrace('Successfully deleted '.$deleted.' of them.');
            mtrace('Enjoy the '.number_format($bytes / 1048576).' megabytes you\'ve saved!');
        } else {
            mtrace('Didn\'t find anything to delete.');
        }
    }

}
