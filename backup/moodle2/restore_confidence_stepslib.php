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
 * restore_confidence_stepslib.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * restore_confidence_activity_structure_step
 */
class restore_confidence_activity_structure_step extends restore_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $paths = [new restore_path_element("confidence", "/activity/confidence")];
        if ($this->get_setting_value("userinfo")) {
            $paths[] = new restore_path_element("confidence_response", "/activity/confidence/responses/response");
        }
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Method process_confidence.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_confidence($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $newitemid = $DB->insert_record("confidence", $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Method process_confidence_response.
     *
     * @param mixed $data Parameter data.
     * @return mixed Return value.
     */
    protected function process_confidence_response($data) {
        global $DB;

        $data = (object)$data;
        $data->confidenceid = $this->get_new_parentid("confidence");
        if (!empty($data->userid)) {
            $data->userid = $this->get_mappingid("user", $data->userid);
        }
        $DB->insert_record("confidence_response", $data);
    }

    /**
     * Method after_execute.
     *
     * @return mixed Return value.
     */
    protected function after_execute() {
        $this->add_related_files("mod_confidence", "intro", null);
    }
}
