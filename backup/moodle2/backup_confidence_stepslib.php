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
 * backup_confidence_stepslib.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * backup_confidence_activity_structure_step
 */
class backup_confidence_activity_structure_step extends backup_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value("userinfo");

        $confidence = new backup_nested_element("confidence", ["id"], [
            "name", "intro", "introformat", "question", "questionformat", "anonymous", "anonymoussalt",
            "allowchange", "showresults", "timeopen", "timeclose", "presencevalidation", "locationradius",
            "timecreated", "timemodified",
        ]);
        $responses = new backup_nested_element("responses");
        $response = new backup_nested_element("response", ["id"], [
            "userid", "participantkey", "level", "timecreated", "timemodified",
        ]);

        $confidence->add_child($responses);
        $responses->add_child($response);
        $confidence->set_source_table("confidence", ["id" => backup::VAR_ACTIVITYID]);

        if ($userinfo) {
            $response->set_source_table("confidence_response", ["confidenceid" => backup::VAR_PARENTID]);
            $response->annotate_ids("user", "userid");
        }

        $confidence->annotate_files("mod_confidence", "intro", null);
        return $this->prepare_activity_structure($confidence);
    }
}
