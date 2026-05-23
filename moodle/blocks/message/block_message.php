<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the message block class, based upon block_base.
 *
 * @package    block_message
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class block_message
 *
 * @package    block_message
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_message extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_message');
    }

    public function get_content() {
        global $CFG, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $text = '';

        if ($USER->firstname) {
            $results_membership = $DB->get_records_select(
                'cohort_members',
                'userid = ?',
                array($USER->id),
                'id'
            );

            if ($results_membership) {
                foreach ($results_membership as $result_membership) {
                    $results = $DB->get_records_select(
                        'cohort',
                        'id = ?',
                        array($result_membership->cohortid),
                        'id'
                    );

                    if ($results) {
                        foreach ($results as $result) {
                            $text .= $result->id . ',' . $result->name . '<br>';
                        }
                    }
                }
            } else {
                $text .= 'Not in a cohort.<br>';
            }

            $text .= get_string(
                'message:hello',
                'block_message'
            ) . $USER->firstname;
        }

        $this->content->text = $text;

        return $this->content;
    }
}
