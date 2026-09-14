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
 * register_reference.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_confidence\external;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Class register_reference.
 */
class register_reference extends external_api {
    /**
     * Method execute_parameters.
     *
     * @return external_function_parameters Return value.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            "cmid" => new external_value(PARAM_INT, "Course module id"),
            "latitude" => new external_value(PARAM_FLOAT, "Latitude", VALUE_DEFAULT, 0.0),
            "longitude" => new external_value(PARAM_FLOAT, "Longitude", VALUE_DEFAULT, 0.0),
            "accuracy" => new external_value(PARAM_FLOAT, "Accuracy in meters", VALUE_DEFAULT, 0.0),
        ]);
    }

    /**
     * Method execute.
     *
     * @param int $cmid Parameter cmid.
     * @param float $latitude Parameter latitude.
     * @param float $longitude Parameter longitude.
     * @param float $accuracy Parameter accuracy.
     * @return array Return value.
     */
    public static function execute(int $cmid, float $latitude = 0.0, float $longitude = 0.0, float $accuracy = 0.0): array {
        global $DB, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            "cmid" => $cmid,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "accuracy" => $accuracy,
        ]);

        $cm = get_coursemodule_from_id("confidence", $params["cmid"], 0, false, MUST_EXIST);
        $confidence = $DB->get_record("confidence", ["id" => $cm->instance], "*", MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability("mod/confidence:managereference", $context);

        $lat = (int)$confidence->presencevalidation === \mod_confidence\manager::PRESENCE_LOCATION
            ? $params["latitude"] : null;
        $lon = (int)$confidence->presencevalidation === \mod_confidence\manager::PRESENCE_LOCATION
            ? $params["longitude"] : null;
        $acc = (int)$confidence->presencevalidation === \mod_confidence\manager::PRESENCE_LOCATION
            ? $params["accuracy"] : null;

        $record = \mod_confidence\manager::register_reference($confidence, $cm, $USER->id, $lat, $lon, $acc);

        return [
            "message" => get_string("referencesaved", "mod_confidence"),
            "ipaddress" => $record->ipaddress,
            "latitude" => $record->latitude === null ? "" : (string)$record->latitude,
            "longitude" => $record->longitude === null ? "" : (string)$record->longitude,
            "accuracy" => $record->accuracy === null ? "" : (string)$record->accuracy,
            "timemodified" => (int)$record->timemodified,
        ];
    }

    /**
     * Method execute_returns.
     *
     * @return external_single_structure Return value.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            "message" => new external_value(PARAM_TEXT, "Status message"),
            "ipaddress" => new external_value(PARAM_TEXT, "Reference IP"),
            "latitude" => new external_value(PARAM_TEXT, "Reference latitude"),
            "longitude" => new external_value(PARAM_TEXT, "Reference longitude"),
            "accuracy" => new external_value(PARAM_TEXT, "Location accuracy"),
            "timemodified" => new external_value(PARAM_INT, "Updated timestamp"),
        ]);
    }
}
