<?php

defined('MOODLE_INTERNAL') || die();

class block_inactive_users extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_inactive_users');
    }

    public function applicable_formats() {
        return array('site' => true);
    }

    public function has_required_capability() {
        return has_capability('block/inactive_users:viewblock', context_system::instance());
    }

    public function get_content() {

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        global $DB, $OUTPUT;

        // miramos permisos antes de tocar nada
        if (!$this->has_required_capability()) {
            $this->content->text = $OUTPUT->notification(
                get_string('error_loading', 'block_inactive_users'),
                'error'
            );
            return $this->content;
        }

        try {
            $select = 'lastaccess > ? AND deleted = ? AND suspended = ?';
            $params = array(0, 0, 0);

            $inactive_users = $DB->get_records_select(
                'user',
                $select,
                $params,
                'lastaccess ASC',
                'id, firstname, lastname, email, lastaccess',
                0,
                10
            );

        } catch (Exception $e) {
            $this->content->text = $OUTPUT->notification(
                get_string('error_loading', 'block_inactive_users'),
                'error'
            );
            return $this->content;
        }

        if (empty($inactive_users)) {
            $this->content->text = $OUTPUT->notification(
                get_string('no_inactive_users', 'block_inactive_users'),
                'success'
            );
            return $this->content;
        }

        $html = '';

        $html .= '<div class="inactive-users-block">';
        $html .= '<h3 style="margin-top: 0; color: #d9534f;">' .
                 get_string('inactive_header', 'block_inactive_users') . '</h3>';

        $html .= '<p style="font-size: 12px; color: #666; margin: 10px 0;">' .
                 get_string('showing_top_ten', 'block_inactive_users') . '</p>';

        $html .= '<table style="width: 100%; border-collapse: collapse; margin: 15px 0;">';

        $html .= '<thead>';
        $html .= '<tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">';
        $html .= '<th style="padding: 10px; text-align: left; font-weight: bold;">' .
                 get_string('user_name', 'block_inactive_users') . '</th>';
        $html .= '<th style="padding: 10px; text-align: left; font-weight: bold;">' .
                 get_string('time_inactive', 'block_inactive_users') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';

        foreach ($inactive_users as $user) {

            $time_since_access = time() - $user->lastaccess;
            $time_formatted = format_time($time_since_access);

            $profile_url = new moodle_url('/user/view.php', array(
                'id' => $user->id,
                'course' => 1
            ));

            $fullname = fullname($user);

            $user_link = html_writer::link(
                $profile_url,
                $fullname,
                array(
                    'title' => get_string('view_profile', 'block_inactive_users'),
                    'style' => 'color: #0066cc; text-decoration: none;'
                )
            );

            $row_style = '';

            if ($time_since_access > (30 * 24 * 60 * 60)) {
                $row_style = 'background-color: #ffe6e6;';
            } elseif ($time_since_access > (14 * 24 * 60 * 60)) {
                $row_style = 'background-color: #fff3cd;';
            }

            $html .= '<tr style="border-bottom: 1px solid #ddd; ' . $row_style . '">';

            $html .= '<td style="padding: 10px; vertical-align: middle;">';
            $html .= $user_link;
            $html .= '</td>';

            $html .= '<td style="padding: 10px; vertical-align: middle;">';
            $html .= '<span style="color: #d9534f; font-weight: 500;">' . $time_formatted . '</span>';
            $html .= '</td>';

            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '<div style="padding: 10px; background-color: #f9f9f9; border-left: 3px solid #d9534f; font-size: 12px; margin-top: 15px;">';
        $html .= '<strong>' . get_string('inactive_note', 'block_inactive_users') . '</strong>';
        $html .= '</div>';

        $html .= '</div>';

        $this->content->text = $html;

        $this->content->footer =
            '<div style="text-align: center; font-size: 12px; color: #666; margin-top: 10px;">' .
            get_string('total_inactive', 'block_inactive_users') . ': ' .
            count($inactive_users) .
            ' / 10</div>';

        return $this->content;
    }

    public function has_config() {
        return false;
    }
}
