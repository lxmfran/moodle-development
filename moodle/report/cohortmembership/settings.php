<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // añadimos el enlace del reporte en administración

    $ADMIN->add(
        'reports',
        new admin_externalpage(
            'report_cohortmembership',
            get_string('pluginname', 'report_cohortmembership'),
            new moodle_url('/report/cohortmembership/index.php'),
            'moodle/site:config'
        )
    );
}
