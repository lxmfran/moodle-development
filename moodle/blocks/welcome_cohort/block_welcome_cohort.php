<?php

defined('MOODLE_INTERNAL') || die();

class block_welcome_cohort extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_welcome_cohort');
    }

    public function applicable_formats() {
        return array('site' => true);
    }

    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        global $USER, $DB, $OUTPUT;

        // pillamos el usuario y vemos si esta logueado
        if (isguestuser()) {
            $this->content->text = $OUTPUT->notification(
                get_string('notloggedin', 'block_welcome_cohort'),
                'info'
            );
            $this->content->text .= '<p>' . get_string('pleaselogin', 'block_welcome_cohort') . '</p>';
            return $this->content;
        }

        $userid = $USER->id;
        $fullname = fullname($USER);

        // sacamos la cohorte de la base
        $cohort = $DB->get_record_sql(
            "SELECT c.id, c.name, c.idnumber, c.description
               FROM {cohort} c
               INNER JOIN {cohort_members} cm ON c.id = cm.cohortid
              WHERE cm.userid = ?",
            array($userid),
            IGNORE_MULTIPLE
        );

        if (empty($cohort)) {
            $this->content->text = $OUTPUT->notification(
                get_string('error_cohort_not_found', 'block_welcome_cohort'),
                'warning'
            );
            return $this->content;
        }

        $welcome_text = get_string('welcome', 'block_welcome_cohort') . ', ' . $fullname . '.';

        $cohort_message = $this->get_cohort_message($cohort->idnumber);

        $cohort_member_count = $DB->count_records(
            'cohort_members',
            array('cohortid' => $cohort->id)
        );

        if ($cohort_member_count == 1) {
            $member_message = get_string('cohort_member_singular', 'block_welcome_cohort');
        } else {
            $member_message = get_string('cohort_size', 'block_welcome_cohort', $cohort_member_count);
        }

        // montamos el html del bloque
        $html = '';

        $html .= '<div class="welcome-cohort-container">';

        $html .= '<div class="welcome-message" style="margin-bottom: 20px; padding: 15px; background-color: #f0f4f7; border-left: 4px solid #0066cc; border-radius: 4px;">';
        $html .= '<h3 style="margin-top: 0; color: #0066cc;">' . $welcome_text . '</h3>';
        $html .= '<p style="margin: 10px 0; font-size: 16px; color: #333;">' . $cohort_message . '</p>';
        $html .= '</div>';

        $html .= '<div class="cohort-info" style="padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= '<h4 style="margin-top: 0; color: #333;">' . get_string('cohort_info', 'block_welcome_cohort') . '</h4>';

        $html .= '<p style="margin: 8px 0;">';
        $html .= '<strong>' . get_string('cohort_name', 'block_welcome_cohort') . ':</strong> ';
        $html .= $cohort->name;
        $html .= '</p>';

        $html .= '<p style="margin: 8px 0;">';
        $html .= '<strong>' . get_string('cohort_members', 'block_welcome_cohort') . ':</strong> ';
        $html .= $member_message;
        $html .= '</p>';

        $html .= '</div>';

        $html .= '</div>';

        $this->content->text = $html;

        return $this->content;
    }

    private function get_cohort_message($cohort_idnumber) {

        $messages = array(
            'cohort_directors' => 'director_welcome',
            'cohort_admin' => 'admin_welcome',
            'cohort_instructors' => 'instructor_welcome',
            'cohort_ai_students' => 'ai_student_welcome',
            'cohort_cybersec_students' => 'cybersec_student_welcome',
            'cohort_forensics_students' => 'forensics_student_welcome',
        );

        $message_key = isset($messages[$cohort_idnumber])
            ? $messages[$cohort_idnumber]
            : 'notloggedin';

        return get_string($message_key, 'block_welcome_cohort');
    }

    public function has_config() {
        return false;
    }
}
