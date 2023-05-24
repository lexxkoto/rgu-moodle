<?php 

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

$records = 10000;

$users_page_hits = $DB->get_records_sql("SELECT (@cnt := @cnt + 1) AS rowNumber,userid,courseid
					FROM {logstore_standard_log}
					  CROSS JOIN (SELECT @cnt := 0) AS dummy
					WHERE ACTION = 'viewed' AND target = 'course' AND userid > 0 AND courseid > 100
					ORDER BY id DESC
					LIMIT {$records}");

for($i=$records;$i>0;$i--){
	
	mtrace('Updating user '.$users_page_hits[$i]->userid.' who visited course id '.$users_page_hits[$i]->courseid);
	add_to_recent($users_page_hits[$i]->courseid,$users_page_hits[$i]->userid);
} 