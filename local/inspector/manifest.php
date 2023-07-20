<?php

$runtime = time();
$roundedTimestamp = $runtime - ($runtime%60);

$excludeEvents = Array(
    '"\\core\\event\\message_sent"',
    '"\\core\\event\message_viewed"'
);

$cronTasks = Array(
    '"\\core\\task\\refresh_mod_calendar_events_task"'      => 'task-calendar',
    '"\\core\\task\\search_optimize_task"'                  => 'task-search',
    '"\\core\\task\\asynchronous_backup_task"'              => 'task-coursebackup',
    '"\\mod_forum\\task\\send_user_digests"'                => 'task-forumdigest',
    '"\\mod_forum\\task\\send_user_notifications"'          => 'task-forumpost',
    '"\\core_course\\task\\course_delete_modules"'          => 'task-moddelete',
    '"\\core\\task\\asynchronous_restore_task"'             => 'task-courserestore',
    '"\\tool_dataprivacy\\task\\process_data_request_task"' => 'task-gdpr',
    '"\\assignfeedback_editpdf\\task\\convert_submission"'  => 'task-unoconv',
    '"\\plagiarism_turnitin\\task\\send_submissions"'       => 'task-turnitin-send',
    '"\\plagiarism_turnitin\\task\\update_reports"'         => 'task-turnitin-get',
    '"\\enrol_sits\\task\\sync_course"'                     => 'task-sits-sync',
);

$logEvents = Array(
    '"\\core\\event\\user_loggedin"'                        => 'act-login',
    '"\\core\\event\\course_viewed"'                        => 'act-courseview',
    '"\\core\\event\\dashboard_viewed"'                     => 'act-dashboard',
    '"\assignsubmission_file\event\assessable_uploaded"'    => 'act-assupload',
    '"\\mod_forum\\event\\discussion_created"'              => 'act-forumpost',
    '"\\mod_forum\\event\\discussion_viewed"'               => 'act-forumview',
    '"\\mod_quiz\\event\\attempt_started"'                  => 'act-quizstart',
    '"\\mod_quiz\\event\\attempt_submitted"'                => 'act-quizsubmit',
    '"\\core\\event\\course_created"'                       => 'act-coursecreated',
    '"\\core\\event\\course_deleted"'                       => 'act-coursedeleted',
    '"\\core\\event\\course_backup_created"'                => 'act-coursebackup',
    '"\\core\\event\\course_restored"'                      => 'act-courserestore',
    '"\\core\\event\\course_module_created"'                => 'act-modcreate',
    '"\\core\\event\\course_module_updated"'                => 'act-moddupdate',
    '"\\core\\event\\course_module_deleted"'                => 'act-moddelete',
    '"\\core\\event\\user_login_failed"'                    => 'act-failedlogin',
    '"\\mod_assign\\event\\assessable_submitted"'           => 'act-asssubmit',
    '"\\mod_turnitintooltwo\\event\\add_submission"'        => 'act-tiisubmit',
);

$queries = Array(
    'tii-queue'         => 'select count(*) ttl from {plagiarism_turnitin_files} where statuscode in ("queued", "pending")',
    'task-adhoc'        => 'select count(*) ttl from {task_adhoc}',
    'task-adhoc-due'    => 'select count(*) ttl from {task_adhoc} where nextruntime <= '.($roundedTimestamp+300),
    'task-adhoc-late'   => 'select count(*) ttl from {task_adhoc} where nextruntime < '.($roundedTimestamp-3600),
    'task-adhoc-future' => 'select count(*) ttl from {task_adhoc} where nextruntime > '.($roundedTimestamp+300),
    'users-now'         => 'select count(distinct userid) ttl from {logstore_standard_log} where eventname not in ('.str_replace("\\", "\\\\", implode(', ', $excludeEvents)).') and timecreated >= '.strtotime('-5 minutes', $roundedTimestamp).' and timecreated < '.$roundedTimestamp,
    'ass-unoconv'       => 'select count(*) ttl from {assignfeedback_editpdf_queue}'
);

?>
