<?php

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$PAGE->set_url('/report/cohortmembership/index.php');
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('admin');

$PAGE->set_title(get_string('pluginname', 'report_cohortmembership'));
$PAGE->set_heading(get_string('pluginname', 'report_cohortmembership'));

echo $OUTPUT->header();

$PAGE->navbar->add(
    get_string('pluginname', 'report_cohortmembership'),
    new moodle_url('/report/cohortmembership/index.php')
);

echo $OUTPUT->heading(get_string('pluginname', 'report_cohortmembership'), 2);

echo html_writer::tag(
    'p',
    get_string('report_description', 'report_cohortmembership'),
    array('class' => 'text-muted')
);

try {
    $cohorts = $DB->get_records(
        'cohort',
        array(),
        'name ASC'
    );

    if (empty($cohorts)) {
        echo $OUTPUT->notification(
            get_string('no_cohorts_found', 'report_cohortmembership'),
            'warning'
        );
        echo $OUTPUT->footer();
        exit;
    }

} catch (Exception $e) {
    echo $OUTPUT->notification(
        get_string('database_error', 'report_cohortmembership'),
        'error'
    );
    echo $OUTPUT->footer();
    exit;
}

foreach ($cohorts as $cohort) {

    $member_count = $DB->count_records('cohort_members', array('cohortid' => $cohort->id));

    echo html_writer::tag(
        'h3',
        $cohort->name . ' (' .
        get_string('members_count', 'report_cohortmembership', $member_count) . ')',
        array('class' => 'cohort-header', 'style' => 'margin-top: 30px; color: #0066cc;')
    );

    if ($member_count == 0) {
        echo html_writer::tag(
            'p',
            get_string('no_members', 'report_cohortmembership'),
            array('class' => 'text-muted')
        );
        continue;
    }

    try {
        $cohort_members = $DB->get_records_sql(
            "SELECT u.id, u.firstname, u.lastname, u.email, u.picture, u.imagealt
               FROM {user} u
               INNER JOIN {cohort_members} cm ON u.id = cm.userid
              WHERE cm.cohortid = ?
              ORDER BY u.lastname ASC, u.firstname ASC",
            array($cohort->id)
        );

    } catch (Exception $e) {
        $cohort_members = array();
    }

    $table = new html_table();
    $table->attributes = array('class' => 'cohort-members-table generaltable');

    $table->head = array(
        get_string('photo', 'report_cohortmembership'),
        get_string('full_name', 'report_cohortmembership'),
        get_string('email_address', 'report_cohortmembership'),
        get_string('courses_enrolled', 'report_cohortmembership')
    );

    $table->headattr = array(
        array('class' => 'photo-column'),
        array('class' => 'name-column'),
        array('class' => 'email-column'),
        array('class' => 'courses-column')
    );

    $table->align = array('center', 'left', 'left', 'center');
    $table->data = array();

    foreach ($cohort_members as $user) {

        $user_object = $DB->get_record('user', array('id' => $user->id));

        $course_count = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT e.courseid)
               FROM {user_enrolments} ue
               INNER JOIN {enrol} e ON ue.enrolid = e.id
              WHERE ue.userid = ?
                AND ue.status = 0
                AND e.status = 0",
            array($user->id)
        );

        $photo_cell = $OUTPUT->user_picture(
            $user_object,
            array('size' => 35)
        );

        $fullname = fullname($user);

        $profile_url = new moodle_url('/user/view.php', array(
            'id' => $user->id,
            'course' => SITEID
        ));

        $name_cell = html_writer::link($profile_url, $fullname);

        $email_cell = $user->email;

        $courses_cell = html_writer::tag(
            'span',
            $course_count,
            array('class' => 'badge badge-primary')
        );

        $table->data[] = array(
            $photo_cell,
            $name_cell,
            $email_cell,
            $courses_cell
        );
    }

    echo html_writer::tag(
        'div',
        html_writer::table($table),
        array('class' => 'table-responsive', 'style' => 'margin: 20px 0;')
    );

    echo html_writer::tag('hr', '', array('style' => 'margin: 40px 0;'));
}

echo $OUTPUT->footer();
