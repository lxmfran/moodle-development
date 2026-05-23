<?php

defined('MOODLE_INTERNAL') || die();

// strings del bloque de usuarios inactivos

$string['pluginname'] = 'Inactive Users Block';
$string['inactive_users:addinstance'] = 'Add a new Inactive Users block';
$string['inactive_users:viewblock'] = 'View the Inactive Users block';

$string['blocktitle'] = 'Inactive Users Report';
$string['inactive_header'] = 'Users with No Recent Activity';

$string['user_name'] = 'User Name';
$string['last_access'] = 'Last Access';
$string['time_inactive'] = 'Time Inactive';

$string['no_inactive_users'] = 'All users have accessed the academy recently';
$string['loading_users'] = 'Loading inactive users...';
$string['error_loading'] = 'Error loading inactive users from database';

$string['total_inactive'] = 'Total Inactive Users Shown';
$string['showing_top_ten'] = 'Showing the 10 users with the oldest last access time';

$string['never_accessed'] = 'Never accessed';
$string['days_inactive'] = '{$a} days inactive';
$string['months_inactive'] = '{$a} months inactive';

// nota sobre privacidad del listado
$string['inactive_note'] = 'This report shows users who have not accessed the academy for extended periods';
$string['view_profile'] = 'View User Profile';

// ayuda del bloque
$string['blocktitle_help'] = 'This block displays a list of the 10 users with the oldest last access timestamp. This helps administrators and instructors identify potentially inactive accounts that may need follow-up or account recovery.';
