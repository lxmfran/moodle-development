<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {


    $ADMIN->add(
        'reports',
        new admin_externalpage(
            'report_categorycourses',
            get_string('pluginname', 'report_categorycourses'),
            new moodle_url('/report/categorycourses/index.php'),
            'moodle/site:config'
        )
    );
    
}
