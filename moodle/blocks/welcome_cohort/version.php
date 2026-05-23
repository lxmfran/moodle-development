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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

/**
 * Version information for the Welcome Cohort block
 *
 * @package   block_welcome_cohort
 * @author    Moodle Developer
 * @copyright 2026 AI & Cybersecurity Defense Academy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'block_welcome_cohort';
$plugin->version = 2026051400;                    // YYYYMMDDNN (Year, Month, Day, Sequence Number)
$plugin->requires = 2024041600;                   // Moodle 4.4+
$plugin->maturity = MATURITY_BETA;
$plugin->release = '1.0 beta';
