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
 * get_report_data.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_confidence\external;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Class get_report_data.
 */
class get_report_data extends external_api {
    /**
     * Method execute_parameters.
     *
     * @return external_function_parameters Return value.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            "cmid" => new external_value(PARAM_INT, "Course module id"),
        ]);
    }

    /**
     * Method execute.
     *
     * @param int $cmid Parameter cmid.
     * @return array Return value.
     */
    public static function execute(int $cmid): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), ["cmid" => $cmid]);
        $cm = get_coursemodule_from_id("confidence", $params["cmid"], 0, false, MUST_EXIST);
        $confidence = $DB->get_record("confidence", ["id" => $cm->instance], "*", MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability("mod/confidence:viewreport", $context);

        return \mod_confidence\manager::report_data($confidence);
    }

    /**
     * Method execute_returns.
     *
     * @return external_single_structure Return value.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            "firstcounts" => new external_multiple_structure(new external_value(PARAM_INT, "First response count")),
            "latestcounts" => new external_multiple_structure(new external_value(PARAM_INT, "Latest response count")),
            "participants" => new external_value(PARAM_INT, "Participants"),
            "submissions" => new external_value(PARAM_INT, "Submissions"),
            "improved" => new external_value(PARAM_INT, "Improved participants"),
            "unchanged" => new external_value(PARAM_INT, "Unchanged participants"),
            "declined" => new external_value(PARAM_INT, "Declined participants"),
            "rows" => new external_multiple_structure(new external_single_structure([
                "name" => new external_value(PARAM_TEXT, "Participant name"),
                "first" => new external_value(PARAM_INT, "First level"),
                "latest" => new external_value(PARAM_INT, "Latest level"),
                "submissions" => new external_value(PARAM_INT, "Number of submissions"),
                "timemodified" => new external_value(PARAM_INT, "Last update timestamp"),
            ])),
        ]);
    }
}
