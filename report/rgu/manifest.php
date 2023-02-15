<?php

    $reports = Array(
        'test' => Array(
            'name'          => 'All Site Users',
            'desc'          => 'Gets a list of all users on the site.',
            'query'         => 'SELECT id, firstname, lastname, email
                                FROM {user}',
            'capability'    => 'moodle/user:viewalldetails',
            'titles'        => Array(
                'id'        => 'User ID',
                'firstname' => 'First Name',
                'lastname'  => 'Last Name',
                'email'     => 'Email Address'
            )
        ),
        'assignpdfqueue' => Array(
            'name'          => 'Assignment PDF Queue',
            'desc'          => 'Shows the queue of files waiting to be converted by Unoconv',
            'query'         => 'SELECT {assign_submission}.timemodified uploaded,
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
            'capability'    => 'moodle/site:viewreports',
            'titles'        => Array(
                'uploaded'  => 'Upload Time',
                'userid'    => 'User',
                'courseid'  => 'Course',
                'assignid'  => 'Assignment'
                
            ),
            'filters'       => Array(
                'uploaded'  => 'relative-time',
                'userid'    => 'user-link',
                'courseid'  => 'course-link',
                'assignid'  => 'assign-link'
            )
        ),
        'turnitinqueue' => Array(
            'name'          => 'Turnitin Queue',
            'desc'          => 'Shows the files waiting to be sent to Turnitin for processing',
            'query'         => 'SELECT {plagiarism_turnitin_files}.lastmodified uploaded,
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
            'capability'    => 'moodle/site:viewreports',
            'titles'        => Array(
                'uploaded'  => 'Upload Time',
                'userid'    => 'User',
                'courseid'  => 'Course',
                'cm'        => 'Activity',
            ),
            'filters'       => Array(
                'uploaded'  => 'relative-time',
                'userid'    => 'user-link',
                'courseid'  => 'course-link',
                'cm'        => 'module-link'
            ),
        ),
    );
    
    $categories = Array(
        'Assignment Processing' => Array(
            'assignpdfqueue',
            'turnitinqueue',
        ),
        'Test Category' => Array(
            'test'
        ),
    );

?>
