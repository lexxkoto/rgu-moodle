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
 * CLI update for manual enrolments expiration.
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    enrol_sits
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once("$CFG->libdir/clilib.php");

function out($message, $type='-') {
    echo date('[H:i:s]').' ['.$type.'] '.$message.PHP_EOL;
}

out('SITS Sync Migration Script by Alex Walker', '+');

if (!enrol_is_enabled('sits')) {
    out('SITS Sync is disabled system-wide. Enable before running this script.', '!');
}

$plugin = enrol_get_plugin('sits');

//$courses = $DB->get_records('course');
$courses = $DB->get_records_sql('select * from becourse');

$numCourses = count($courses);
out('Migrating '.$numCourses.' course'.$plugin->s($numCourses), '+');

$i = 1;

foreach($courses as $course) {
    
    out($i.'/'.$numCourses.' - '.$course->fullname.' - '.$course->id, '+');
    
    if($course->id > 1) {
        //$instance = $plugin->check_instance($course->id);
        
        if((strpos(strtolower($course->shortname), 'module study area') !== false) || (strpos(strtolower($course->shortname), 'module') !== false)) {
            out('Creating a SITS Sync rule for "module"');
            
            $record = new stdClass();
            $record->type = 'module';
            
            // Get the year
            $matches = Array();
            preg_match('/(20[0-9][0-9])\//', $course->shortname, $matches);
            
            $year = $matches[1];
            out('Modules is from academic year '.$year);
            
            // Get the module code
            
            $matches = Array();
            preg_match('/](.*)-/U', $course->shortname, $matches);
            
            if(count($matches) < 2) {
                out('No module code found.');
                break;
            }
            
            $code = trim($matches[1]);
            out('Module code is '.$code);
        }
        
        if(strpos(strtolower($course->shortname), 'course study area') !== false) {
            out('Creating a SITS Sync rule for "course"');
            
            $parts = explode('-', $course->shortname);
            
            if(count($parts) < 2) {
                out('No course name found');
            }
            
            $courseCodes = explode(',', $parts[1]);
            
            $parts = explode('-', $course->shortname);
            $courseCodes = explode(',', $parts[1]);
            
            $filters = array_slice($parts, 2);
            foreach($filters as $filter) {
                $value = trim($filter);
                
                // Is this a block filter?
                
                $match = preg_match('/Block [0-9]{1,2}/i');
                
                // Does this look like months of the year?
                
                $match = preg_match('/ja(?:nuary){0,1}|fe(?:bruary){0,1}|ma(?:rch){0,1}|ap(?:ril){0,1}|m(?:a){0,1}y|ju(?:ne){0,1}|j(?:u){0,1}l(?:y){0,1}|au(?:gust){0,1}|se(?:ptember){0,1}|oc(?!cur)(?:tober){0,1}|no(?:vember){0,1}|de(?:cember)/gi', $course->shortname);
                if($match) {
                    $months = Array();
                    
                    $searches = Array(
                        'ja' => '/ja(?:nuary){0,1/i',
                        'fe' => '/fe(?:bruary){0,1}/i',
                        'ma' => '/ma(?:rch){0,1}/i',
                        'ap' => '/ap(?:ril){0,1}/i',
                        'my' => '/m(?:a){0,1}y/i',
                        'ju' => '/ju(?:ne){0,1}/i',
                        'jl' => '/j(?:u){0,1}l(?:y){0,1}/i',
                        'au' => '/au(?:gust){0,1}/i',
                        'se' => '/se(?:ptember){0,1}/i',
                        'oc' => '/oc(?!cur)(?:tober){0,1}/i',
                        'no' => '/no(?:vember){0,1}/i',
                        'de' => '/de(?:cember){0,1}/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            out('Matched an occurrence month: '.$code);
                            $months[] = $code;
                        }
                    }
                    
                    if(count($months) !== 0) {
                        $record->occurrence = strtoupper(implode(':', $months));
                    }
                    break;
                }
                
                // is this a mode of delivery filter?
                
                // Let's hope that no occurrences or blocks have 'OD' in them.
                // Maybe check this one last and use a 'break'?
                
                $match = preg_match('/(F(ull){0,1}[ -]{0,1}T)|(P(art){0,1}[ -]{0,1}T)|(O(nline){0,1}[ -]{0,1}D(istance){0,1}[ -]{0,1}L{0,1})/gi', $course->shortname);
                if($match) {
                    $modes = Array();
                    
                    $searches = Array(
                        'ft' => '/(F(?:ull){0,1}[ -]{0,1}T)/i',
                        'pt' => '/(P(?:art){0,1}[ -]{0,1}T)/i',
                        'od' => '/(O(?:nline){0,1}[ -]{0,1}D(?:istance){0,1}[ -]{0,1}L{0,1})/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            out('Matched a mode of attendance: '.$code);
                            $modes[] = $code;
                        }
                    }
                    
                    if(count($modes) !== 0) {
                        $record->modes = strtoupper(implode(':', $modes));
                    }
                    break;  
                }
            }
            
            foreach($courseCodes as $code) {
                if(preg_match('/[A-Z]{7,12}/', $code) || preg_match('/[A-Z]{3,9}\*([A-Z\*]){0-6}/', $code)) {
                    out('Matched course code '.trim($code));
                    $record = new stdClass();
                    $record->type = 'school';
                    $record->code = trim($code);
                } else {
                    out('Doesn\'t look like a real course code: '.trim($code));
                }
            }
        }
        
        if(strpos(strtolower($course->shortname), 'school study area') !== false) {
            out('Creating a SITS Sync rule for "school"');
            
            foreach($courseCodes as $code) {
                if(preg_match('/[a-zA-Z]{5,10}/', trim($code))) {
                    out('Matched course code '.trim($code));
                    $record = new stdClass();
                    $record->type = 'course';
                    $record->code = trim($code);
                } else {
                    out('Doesn\'t look like a real course code: '.trim($code));
                }
            }
        }
        
        $DB->insert_record('enrol_sits_code', $record);
    }
    $i++;
}

exit($result);
