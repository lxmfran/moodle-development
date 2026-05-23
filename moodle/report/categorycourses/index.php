<?php

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$systemcontext = context_system::instance();
require_capability('moodle/site:config', $systemcontext);

$PAGE->set_url('/report/categorycourses/index.php');
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('admin');

$PAGE->set_title(get_string('pluginname', 'report_categorycourses'));
$PAGE->set_heading(get_string('pluginname', 'report_categorycourses'));

echo $OUTPUT->header();

// sacamos las categorias principales
try {
    $categories = $DB->get_records(
        'course_categories',
        array('parent' => 0),
        'name ASC'
    );

    if (empty($categories)) {
        echo $OUTPUT->notification(
            get_string('no_categories', 'report_categorycourses'),
            'info'
        );
        echo $OUTPUT->footer();
        exit;
    }

} catch (Exception $e) {
    echo $OUTPUT->notification(
        get_string('database_error', 'report_categorycourses'),
        'error'
    );
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading(get_string('pluginname', 'report_categorycourses'), 2);

echo html_writer::tag(
    'p',
    get_string('report_description', 'report_categorycourses'),
    array('class' => 'text-muted', 'style' => 'margin-bottom: 20px;')
);

foreach ($categories as $category) {

    try {
        $courses = $DB->get_records(
            'course',
            array('category' => $category->id, 'visible' => 1),
            'fullname ASC'
        );

    } catch (Exception $e) {
        echo $OUTPUT->notification(
            get_string('database_error', 'report_categorycourses'),
            'error'
        );
        continue;
    }

    $course_count = count($courses);

    echo html_writer::tag(
        'h3',
        $category->name . ' (' .
        get_string('courses_count', 'report_categorycourses', $course_count) . ')',
        array(
            'style' => 'margin-top: 30px; margin-bottom: 15px; color: #0066cc; border-bottom: 2px solid #0066cc; padding-bottom: 10px;'
        )
    );

    if ($course_count == 0) {
        echo html_writer::tag(
            'p',
            get_string('no_courses', 'report_categorycourses'),
            array('class' => 'text-muted')
        );
        continue;
    }

    $course_student_counts = array();

    foreach ($courses as $course) {

        $student_count = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT ra.userid)
               FROM {role_assignments} ra
               JOIN {context} ctx ON ra.contextid = ctx.id
              WHERE ctx.contextlevel = ?
                AND ctx.instanceid = ?
                AND ra.roleid = (SELECT id FROM {role} WHERE shortname = 'student')",
            array(CONTEXT_COURSE, $course->id)
        );

        $course_student_counts[$course->id] = (int)$student_count;
    }

    $student_count_frequencies = array_count_values($course_student_counts);

    $table = new html_table();
    $table->attributes = array('class' => 'courses-table generaltable');

    $table->head = array(
        get_string('course_name', 'report_categorycourses'),
        get_string('description', 'report_categorycourses'),
        get_string('start_date', 'report_categorycourses'),
        get_string('end_date', 'report_categorycourses'),
        get_string('duration', 'report_categorycourses'),
        get_string('students_enrolled', 'report_categorycourses'),
        get_string('courses_same_count', 'report_categorycourses')
    );

    $table->align = array('left', 'left', 'center', 'center', 'center', 'center', 'center');
    $table->data = array();

    foreach ($courses as $course) {

        $student_count = $course_student_counts[$course->id];
        $courses_with_same_count = $student_count_frequencies[$student_count];

        $course_url = new moodle_url('/course/view.php', array('id' => $course->id));
        $course_name = html_writer::link($course_url, format_string($course->fullname));

        $description = format_string($course->summary, true);
        $description = strip_tags($description);

        if (strlen($description) > 100) {
            $description = substr($description, 0, 100) . '...';
        }

        if (empty($description)) {
            $description = html_writer::tag(
                'em',
                get_string('no_description', 'report_categorycourses'),
                array('class' => 'text-muted')
            );
        }

        if ($course->startdate > 0) {
            $start_date = userdate($course->startdate, get_string('strftimedatetime', 'langconfig'));
        } else {
            $start_date = html_writer::tag(
                'em',
                get_string('no_date', 'report_categorycourses'),
                array('class' => 'text-muted')
            );
        }

        if ($course->enddate > 0) {
            $end_date = userdate($course->enddate, get_string('strftimedatetime', 'langconfig'));
        } else {
            $end_date = html_writer::tag(
                'em',
                get_string('no_date', 'report_categorycourses'),
                array('class' => 'text-muted')
            );
        }

        if ($course->startdate > 0 && $course->enddate > 0 && $course->enddate >= $course->startdate) {
            $duration = format_time($course->enddate - $course->startdate);
        } else {
            $duration = html_writer::tag(
                'em',
                get_string('no_duration', 'report_categorycourses'),
                array('class' => 'text-muted')
            );
        }

        $students_cell = html_writer::tag(
            'span',
            $student_count,
            array('class' => 'badge badge-info', 'style' => 'font-size: 14px; padding: 5px 10px;')
        );

        $same_count_cell = html_writer::tag(
            'span',
            $courses_with_same_count,
            array('class' => 'badge badge-secondary', 'style' => 'font-size: 14px; padding: 5px 10px;')
        );

        $table->data[] = array(
            $course_name,
            $description,
            $start_date,
            $end_date,
            $duration,
            $students_cell,
            $same_count_cell
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
