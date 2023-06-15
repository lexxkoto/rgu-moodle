<?php

    $reports = Array(
        'test' => Array(
            'name'              => 'All Site Users',
            'desc'              => 'Gets a list of all users on the site.',
            'query'             => 'SELECT id, firstname, lastname, email
                                    FROM {user}',
            'capability'        => 'moodle/user:viewalldetails',
            'titles'            => Array(
                'id'            => 'User ID',
                'firstname'     => 'First Name',
                'lastname'      => 'Last Name',
                'email'         => 'Email Address'
            )
        ),
        'assignpdfqueue' => Array(
            'name'              => 'Assignment PDF Queue',
            'desc'              => 'Shows the queue of files waiting to be converted by Unoconv',
            'query'             => 'SELECT {assign_submission}.timemodified uploaded,
                                    {user}.id userid, {course}.id courseid,
                                    {assign}.id assignid
                                    FROM {assignfeedback_editpdf_queue}
                                    INNER JOIN {assign_submission}
                                    ON {assignfeedback_editpdf_queue}.submissionid =
                                    {assign_submission}.id
                                    INNER JOIN {user}
                                    ON {assign_submission}.userid = {user}.id
                                    INNER JOIN {assign}
                                    ON {assign}.id = {assign_submission}.assignment
                                    INNER JOIN {course}
                                    ON {assign}.course = {course}.id
                                    ORDER BY {assignfeedback_editpdf_queue}.id',
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'uploaded'      => 'Upload Time',
                'userid'        => 'User',
                'courseid'      => 'Course',
                'assignid'      => 'Assignment'
                
            ),
            'filters'           => Array(
                'uploaded'      => 'relative-time',
                'userid'        => 'user-link',
                'courseid'      => 'course-link',
                'assignid'      => 'assign-link'
            )
        ),
        'turnitinqueue' => Array(
            'name'              => 'Turnitin Queue',
            'desc'              => 'Shows the files waiting to be sent to Turnitin for processing',
            'query'             => 'SELECT {plagiarism_turnitin_files}.lastmodified uploaded,
                                    {plagiarism_turnitin_files}.statuscode,
                                    {plagiarism_turnitin_files}.userid,
                                    {course_modules}.course courseid,
                                    {plagiarism_turnitin_files}.cm,
                                    {plagiarism_turnitin_files}.attempt,
                                    {plagiarism_turnitin_files}.submissiontype
                                    FROM {plagiarism_turnitin_files} 
                                    INNER JOIN {user} ON
                                    {plagiarism_turnitin_files}.userid = {user}.id
                                    INNER JOIN {course_modules} ON
                                    {course_modules}.id = {plagiarism_turnitin_files}.cm
                                    WHERE
                                    {plagiarism_turnitin_files}.externalid IS NULL
                                    AND ( {plagiarism_turnitin_files}.statuscode = "pending"
                                    OR {plagiarism_turnitin_files}.statuscode = "queued" )
                                    ORDER BY {plagiarism_turnitin_files}.id',
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'uploaded'      => 'Upload Time',
                'userid'        => 'User',
                'courseid'      => 'Course',
                'cm'            => 'Activity',
            ),
            'filters'           => Array(
                'uploaded'      => 'relative-time',
                'userid'        => 'user-link',
                'courseid'      => 'course-link',
                'cm'            => 'module-link'
            ),
        ),
        'deltahits' => Array(
            'name'              => 'DELTA Course Visitors',
            'desc'              => 'Shows the number of users that have visited courses run by DELTA over the past 90 days.',
            'query'             => 'SELECT FROM_UNIXTIME({logstore_standard_log}.timecreated,"%Y-%m") month,
                                    {course}.id courseid,
                                    count({logstore_standard_log}.id) as hits,
                                    count(distinct({logstore_standard_log}.userid)) as visitors
                                    FROM {logstore_standard_log}
                                    INNER JOIN {course} ON
                                    {logstore_standard_log}.courseid = {course}.id 
                                    WHERE courseid IN (74843,85986,86458,88430,88343,88436,88431,83710,83736,79470,79471,79469,79475,79474,79473,79472,81485,79168,79173,79182,79176,88464,88466,88465)
                                    and component = "core" 
                                    and action = "viewed"
                                    and userid not in (
                                    select userid from {user_info_data} 
                                    inner join {user_info_field} on
                                    {user_info_data}.fieldid = {user_info_field}.id
                                    where trim(data) = "DELTA"
                                    and shortname = "rgu_school")
                                    and {logstore_standard_log}.timecreated >
                                    unix_timestamp()-(60*60*24*100)
                                    GROUP BY FROM_UNIXTIME(timecreated,"%Y-%m"),
                                    {course}.id
                                    ORDER BY FROM_UNIXTIME(timecreated,"%Y-%m"),
                                    {course}.shortname',
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'month'         => 'Month',
                'userid'        => 'User',
                'courseid'      => 'Course',
                'cm'            => 'Activity',
            ),
            'filters'           => Array(
                'uploaded'      => 'relative-time',
                'userid'        => 'user-link',
                'courseid'      => 'course-link',
                'cm'            => 'module-link'
            ),
        ),
        'assignmentclose' => Array(
            'name'              => 'Assignment Deadline Dates',
            'desc'              => 'Shows a list of all the assignments in Moodle with a deadline between two dates.',
            'query'             => 'SELECT a.id assignid, a.course courseid, a.duedate FROM {assign} a
                                    WHERE FROM_UNIXTIME(a.duedate) > "__startdate__"
                                    AND FROM_UNIXTIME(a.duedate) < "__enddate__"',
            'placeholders'      => Array(
                'startdate'     => Array(
                    'label'     => 'Start Date',
                    'default'   => '2023-02-14',
                    'hint'      => 'The start date we want to look for, in the form YYYY-MM-DD',
                ),
                'enddate'       => Array(
                    'label'     => 'End Date',
                    'default'   => '2023-02-28',
                    'hint'      => 'The end date we want to look for, in the form YYYY-MM-DD',
                ),
            ),
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'courseid'      => 'Course',
                'assignid'      => 'Assignment',
                'duedate'       => 'Deadline',
            ),
            'filters'           => Array(
                'courseid'      => 'course-link',
                'assignid'      => 'assign-link',
                'duedate'       => 'date-long'
            ),
        ),
        'opencourses' => Array(
            'name'              => 'Courses with Self Enrolment',
            'desc'              => 'Shows courses that users can add themselves to using the self-enrolment option.',
            'query'             => 'SELECT {course}.id courseid,
                                    {course}.visible, {enrol}.enrol,
                                    {enrol}.enrolperiod DIV (60*60*24) enrol_days,
                                    COUNT({user_enrolments}.id) AS user_count
                                    FROM {enrol}
                                    INNER JOIN {course}
                                    ON {enrol}.courseid = {course}.id
                                    INNER JOIN {user_enrolments}
                                    ON {user_enrolments}.enrolid = {enrol}.id
                                    WHERE (enrol = "self" OR enrol = "selfdirect")
                                    AND {enrol}.status = 0
                                    GROUP BY {enrol}.id
                                    ORDER BY {course}.shortname',
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'courseid'      => 'Course',
                'visible'       => 'Visible to Students',
                'enrol'         => 'Enrolment Method',
                'enrol_days'    => 'Enrolment in Days',
                'user_count'    => 'Number of Users',
            ),
            'filters'           => Array(
                'courseid'      => 'course-link',
                'visible'       => 'yes-no',
                'user_count'    => 'number-format'
            ),
        ),
        'rollover' => Array(
            'name'              => 'Annual Module Rollover',
            'desc'              => 'Generates a list of module codes to be used in the annual rollover process.',
            'query'             => 'SELECT DISTINCT REPLACE({course}.shortname,"__oldyear__","__newyear__") AS shortname, '.
                                   'REPLACE({course}.fullname,"__oldyear__","__newyear__") AS fullname, '.
                                   '{course}.category, "modelcourse" AS templatecourse '.
                                   'FROM {course} '.
                                   'INNER JOIN {context} ON {course}.id = {context}.instanceid '.
                                   'INNER JOIN {role_assignments} ON {context}.id = {role_assignments}.contextid '.
                                   'INNER JOIN {role} ON {role_assignments}.roleid = {role}.id '.
                                   'WHERE ({role}.shortname = "student" OR {role}.shortname = "rgu_student") '.
                                   'AND {course}.shortname LIKE "%module%__oldyear__%" '.
                                   'ORDER BY shortname',
            'placeholders'      => Array(
                'oldyear'       => Array(
                    'label'     => 'Previous Year',
                    'default'   => '2023/2024',
                    'hint'      => 'The previous academic year in the format 2023/2024',
                ),
                'newyear'       => Array(
                    'label'     => 'Upcoming Year',
                    'default'   => '2024/2025',
                    'hint'      => 'The previous academic year in the format 2024/2025',
                ),
            ),
            'capability'        => 'moodle/site:viewreports',
            'titles'            => Array(
                'shortname'     => 'Course Shortname',
                'fullname'      => 'Course Fullname',
                'category'      => 'Course Category',
                'templatecourse'=> 'Template Course'
            ),
        ),
    );
    
    $categories = Array(
        'Assessment Processing Queues' => Array(
            'assignpdfqueue',
            'turnitinqueue',
            'assignmentclose'
        ),
        'DELTA Engagement' => Array(
            'deltahits',
        ),
        'Course Auditing' => Array(
            'opencourses',
        ),
        'Annual Rollover' => Array(
            'rollover'
        ),
        'Test Category' => Array(
            'test'
        ),
    );

?>
