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
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * restore_confidence_activity_task.class.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . "/restore_confidence_stepslib.php");

/**
 * Class restore_confidence_activity_task.
 */
class restore_confidence_activity_task extends restore_activity_task {
    /**
     * Method define_my_settings.
     *
     * @return mixed Return value.
     */
    protected function define_my_settings() {
    }

    /**
     * Method define_my_steps.
     *
     * @return mixed Return value.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_confidence_activity_structure_step("confidence_structure", "confidence.xml"));
    }

    /**
     * Method define_decode_contents.
     *
     * @return array Return value.
     */
    public static function define_decode_contents() {
        return [];
    }

    /**
     * Method define_decode_rules.
     *
     * @return array Return value.
     */
    public static function define_decode_rules() {
        return [];
    }

    /**
     * Define the restore log rules for this activity.
     *
     * @return array Restore log rules.
     */
    public static function define_restore_log_rules() {
        return [];
    }

    /**
     * Define the restore log rules for course-level logs.
     *
     * @return array Restore log rules.
     */
    public static function define_restore_log_rules_for_course() {
        return [];
    }
}
