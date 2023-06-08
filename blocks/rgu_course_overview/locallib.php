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

define('BLOCKS_RGU_COURSE_OVERVIEW_PAGESIZE', 15);



function rgu_dummy_return($shortname,$fullname){
	$return = array();
	$object = new stdClass();
	$object->id = 18313;
	$object->shortname = $shortname;
	$object->fullname = $fullname;
	$object->visible = 1;
	$return[9999] = $object;

	return $return;
}

/**
 * Returns active courses currently logged in user has an enrolment in
 *
 * @return stdClass
 */
function block_rgu_course_overview_get_courses() {

    $panels = block_rgu_course_overview_get_panels();

    $courses = new stdClass();
    foreach ($panels as $panel) {
        $courses->{$panel} = block_rgu_course_overview_get_course_group($panel);
    }

    return $courses;
}

/**
 * Returns previous courses currently logged in user has an enrolment in
 *
 * @param type $search
 * @return stdClass
 */
function block_rgu_course_overview_get_previous_courses() {

    $subpanels = block_rgu_course_overview_get_previous_subpanels();

    $courses = new stdClass();
    foreach ($subpanels as $subpanel) {
        if ($previouscourses = block_rgu_course_overview_get_course_sub_group($subpanel)) {
            $courses->{$subpanel} = $previouscourses;
        }
    }

    return $courses;
}

/**
 * Returns array of panels
 *
 * @return array
 */
function block_rgu_course_overview_get_panels() {

    return array(
        'modulestudyareas',
        'futurestudyareas',
        'generalstudyarea',
        'teamworkareas',
        'allmystudyareas',
        'previousstudyareas'
    );
}

/**
 * Returns array of years for previous study area panel
 *
 * @return array
 */
function block_rgu_course_overview_get_previous_subpanels() {

    $subpanels = array();
    list($currentyear, $lastyear, $nextyear) = block_rgu_course_overview_get_years();
    for ($year = $currentyear-1; $year > 2006; $year--) {
        $subpanels[] = $year;
    }

    return $subpanels;
}

/**
 * Returns records for specified panel
 *
 * @param string $panel
 * @return array
 */
function block_rgu_course_overview_get_course_group($panel) {
    global $DB;

    $query = block_rgu_course_overview_get_query($panel);

    return $DB->get_records_sql($query->sql, $query->params);
}

/**
 * Returns records for specified sub panel
 *
 * @param string $subpanel
 * @return array
 */
function block_rgu_course_overview_get_course_sub_group($subpanel) {
    global $DB;

    $query = block_rgu_course_overview_get_sub_panel_query($subpanel);

    return $DB->get_records_sql($query->sql, $query->params);
}

/**
 * Returns SQL and params to return records for specified panel 
 *
 * @param string $panel Panel name
 * @return stdClass
 */
function block_rgu_course_overview_get_query($panel) {
    global $USER, $DB;

    list($currentyear, $lastyear, $nextyear) = block_rgu_course_overview_get_years();

    $now = time();

    $params = array(
        'siteid'       => SITEID,
        'contextlevel' => CONTEXT_COURSE,
        'userid'       => $USER->id,
        'active'       => '0',
        'enabled'      => '0',
        'now1'         => $now,
        'now2'         => $now
    );

    switch ($panel) {
        case 'modulestudyareas':
            $extrawhere = " AND (
                (" . $DB->sql_like('c.shortname', ':shortname1', FALSE) . " AND " .
                    $DB->sql_like('c.shortname', ':shortname2', FALSE) . ")
                OR
                (" . $DB->sql_like('c.shortname', ':shortname3', FALSE) . " AND " .
                    $DB->sql_like('c.shortname', ':shortname4', FALSE, TRUE, TRUE) . ")
                )";
            $params['shortname1'] = '%module study area%';
            $params['shortname2'] = "%{$currentyear}%";
            $params['shortname3'] = '%module study area%';
            $params['shortname4'] = "%{$lastyear}%";
            break;
        case 'futurestudyareas':
            $extrawhere = " AND (" . $DB->sql_like('c.shortname', ':shortname1') . ")";
            $params['shortname1'] = "%{$nextyear}/%";
            break;
        case 'generalstudyarea':
            $extrawhere = " AND ( " .
                $DB->sql_like('c.shortname', ':shortname1', FALSE) . " OR " .
                $DB->sql_like('c.shortname', ':shortname2' , FALSE) . " OR " .
                $DB->sql_like('c.shortname', ':shortname3', FALSE) .
                " )";
            $params['shortname1'] = '%course study area%';
            $params['shortname2'] = '%general study area%';
            $params['shortname3'] = '%school study area%';
            break;
        case 'teamworkareas':
            $extrawhere = " AND (" . $DB->sql_like('c.shortname', ':shortname1', FALSE) . ")";
            $params['shortname1'] = '%team%';
            break;
        case 'allmystudyareas':
            $extrawhere = '';
            break;
        case 'previousstudyareas':
            $extrawhere = '';
            $previousyears = array();
            for ($year = $currentyear-1; $year > 2006; $year--) {
                $previousyears[] = $DB->sql_like('c.shortname', ":rgu_course_year_{$year}", FALSE);
                $params["rgu_course_year_{$year}"] = "%{$year}%";
            }
            if (!empty($previousyears)) {
                $yearssearch = implode(' OR ', $previousyears);
                $extrawhere = " AND ({$yearssearch})";
            }
            break;
        default:
            $extrawhere = '';
            break;
    }

    $sql = "SELECT c.id,
                   c.fullname
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                     WHERE ue.status = :active
                       AND e.status = :enabled
                       AND ue.timestart < :now1
                       AND (ue.timeend = 0 OR ue.timeend > :now2)
                   ) en ON (en.courseid = c.id)
         LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)
             WHERE c.id <> :siteid
                    {$extrawhere}
          ORDER BY c.visible DESC, c.sortorder ASC";

    $query = new stdClass();
    $query->sql = $sql;
    $query->params = $params;

    return $query;
}

/**
 * Returns SQL and params to return records for specified previous study area panel 
 *
 * @param string $year
 * @return stdClass
 */
function block_rgu_course_overview_get_sub_panel_query($year) {
    global $USER, $DB;

    list($currentyear, $lastyear, $nextyear) = block_rgu_course_overview_get_years();

    $now = time();

    $params = array(
        'siteid'       => SITEID,
        'contextlevel' => CONTEXT_COURSE,
        'userid'       => $USER->id,
        'year'         => "%{$year}/%"
    );

    $sql = "SELECT c.id,
                   c.fullname
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                   ) en ON (en.courseid = c.id)
         LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)
             WHERE c.id <> :siteid
               AND (" . $DB->sql_like('c.shortname', ':year') . ")
          ORDER BY c.visible DESC, c.sortorder ASC";

    $query = new stdClass();
    $query->sql = $sql;
    $query->params = $params;

    return $query;
}

/**
 * Returns SQL and params to return records for search results panel 
 *
 * @param string $searchtext
 * @return stdClass
 */
function block_rgu_course_overview_get_search_query($searchtext) {
    global $USER, $DB;

    list($currentyear, $lastyear, $nextyear) = block_rgu_course_overview_get_years();

    $now = time();

    $params = array(
        'siteid'       => SITEID,
        'contextlevel' => CONTEXT_COURSE,
        'userid'       => $USER->id,
        'searchtext'   => "%{$searchtext}%"
    );
    
    //RGU Alteration to (a) search full name (a) make sure searches like 'term1 term2' return all results
    //containing term1 and term2, regardless of where the  terms are found.
    $searcharray = array();
    foreach(explode(' ',$searchtext) as $term){
    	$term = trim($term);
    	if(!empty($term)){
    		$searcharray[] = '(shortname LIKE \'%'.$term.'%\' or fullname LIKE \'%'.$term.'%\' )';
    	}
    }
    
    $searchsql = implode(' AND ',$searcharray);
    
    

    $sql = "SELECT c.id,
                   c.fullname
              FROM {course} c
              JOIN (SELECT DISTINCT e.courseid
                      FROM {enrol} e
                      JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)
                   ) en ON (en.courseid = c.id)
         LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)
             WHERE c.id <> :siteid
               AND " . $searchsql . "
          ORDER BY c.visible DESC, c.sortorder ASC";

    $query = new stdClass();
    $query->sql = $sql;
    $query->params = $params;

    return $query;
}

/**
 * Returns values for current, last and next year
 *
 * @return array
 */
function block_rgu_course_overview_get_years() {

    $currentmonth = date('n');
    $currentyear = date('Y');

    // Start new school year in September
    if ($currentmonth < 8 ) {
        $currentyear--;
    }

    $lastyear = $currentyear - 1;
    $nextyear = $currentyear + 1;

    return array($currentyear, $lastyear, $nextyear);
}

/**
 * Returns either configured or defined value for total records in paginated set
 *
 * @return int
 */
function block_rgu_course_overview_get_pagesize() {

    $config = get_config('block_rgu_course_overview');

    return (empty($config->defaultpaginationvalue)) ?
            BLOCKS_RGU_COURSE_OVERVIEW_PAGESIZE : $config->defaultpaginationvalue;
}
