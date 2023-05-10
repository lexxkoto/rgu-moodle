<?php

$roundedTimestamp = time()%60;

$excludeEvents = Array(
    '"\\core\\event\\message_sent"',
    '"\\core\\event\message_viewed"'
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
