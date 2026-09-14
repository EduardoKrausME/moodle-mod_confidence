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
 * manager.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_confidence;

use context_module;
use core\lock\lock_config;
use moodle_exception;
use stdClass;

/**
 * Class manager.
 */
class manager {
    /** @var int */
    public const SHOWRESULTS_NEVER = 0;

    /** @var int */
    public const SHOWRESULTS_AFTER_RESPONSE = 1;

    /** @var int */
    public const SHOWRESULTS_AFTER_CLOSE = 2;

    /** @var int */
    public const PRESENCE_NONE = 0;

    /** @var int */
    public const PRESENCE_IP = 1;

    /** @var int */
    public const PRESENCE_LOCATION = 2;

    /**
     * Method add_instance.
     *
     * @param stdClass $data Parameter data.
     * @return int Return value.
     */
    public static function add_instance(stdClass $data): int {
        global $DB;

        self::prepare_editor_data($data);
        $data->anonymoussalt = bin2hex(random_bytes(32));
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;

        return (int)$DB->insert_record("confidence", $data);
    }

    /**
     * Method update_instance.
     *
     * @param stdClass $data Parameter data.
     * @return bool Return value.
     */
    public static function update_instance(stdClass $data): bool {
        global $DB;

        $data->id = $data->instance;
        self::prepare_editor_data($data);

        $existing = $DB->get_record("confidence", ["id" => $data->id], "*", MUST_EXIST);
        if ((int)$existing->anonymous !== (int)$data->anonymous
                && $DB->record_exists("confidence_response", ["confidenceid" => $data->id])) {
            throw new moodle_exception("erroranonymouslocked", "mod_confidence");
        }

        if (empty($data->anonymoussalt)) {
            $data->anonymoussalt = $existing->anonymoussalt ?: bin2hex(random_bytes(32));
        }
        $data->timemodified = time();

        return $DB->update_record("confidence", $data);
    }

    /**
     * Method delete_instance.
     *
     * @param int $id Parameter id.
     * @return bool Return value.
     */
    public static function delete_instance(int $id): bool {
        global $DB;

        if (!$DB->record_exists("confidence", ["id" => $id])) {
            return false;
        }

        $DB->delete_records("confidence_response", ["confidenceid" => $id]);
        $DB->delete_records("confidence_reference", ["confidenceid" => $id]);
        $DB->delete_records("confidence", ["id" => $id]);
        return true;
    }

    /**
     * Method prepare_editor_data.
     *
     * @param stdClass $data Parameter data.
     * @return void Return value.
     */
    private static function prepare_editor_data(stdClass $data): void {
        if (isset($data->question_editor) && is_array($data->question_editor)) {
            $data->question = $data->question_editor["text"] ?? "";
            $data->questionformat = $data->question_editor["format"] ?? FORMAT_HTML;
            unset($data->question_editor);
        }
    }

    /**
     * Method get_levels.
     *
     * @return array Return value.
     */
    public static function get_levels(): array {
        return [
            1 => get_string("level1", "mod_confidence"),
            2 => get_string("level2", "mod_confidence"),
            3 => get_string("level3", "mod_confidence"),
            4 => get_string("level4", "mod_confidence"),
        ];
    }

    /**
     * Method is_open.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param ?int $time Parameter time.
     * @return bool Return value.
     */
    public static function is_open(stdClass $confidence, ?int $time = null): bool {
        $time = $time ?? time();
        if (!empty($confidence->timeopen) && $time < (int)$confidence->timeopen) {
            return false;
        }
        if (!empty($confidence->timeclose) && $time > (int)$confidence->timeclose) {
            return false;
        }
        return true;
    }

    /**
     * Method response_identity.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param int $userid Parameter userid.
     * @return array Return value.
     */
    public static function response_identity(stdClass $confidence, int $userid): array {
        if (!empty($confidence->anonymous)) {
            return [0, hash_hmac("sha256", (string)$userid, $confidence->anonymoussalt)];
        }
        return [$userid, ""];
    }

    /**
     * Method get_user_responses.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param int $userid Parameter userid.
     * @return array Return value.
     */
    public static function get_user_responses(stdClass $confidence, int $userid): array {
        global $DB;

        [$storeduserid, $participantkey] = self::response_identity($confidence, $userid);
        $params = ["confidenceid" => $confidence->id];
        if (!empty($confidence->anonymous)) {
            $params["participantkey"] = $participantkey;
        } else {
            $params["userid"] = $storeduserid;
        }

        return $DB->get_records("confidence_response", $params, "timecreated ASC, id ASC");
    }

    /**
     * Method get_latest_user_response.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param int $userid Parameter userid.
     * @return ?stdClass Return value.
     */
    public static function get_latest_user_response(stdClass $confidence, int $userid): ?stdClass {
        $responses = self::get_user_responses($confidence, $userid);
        if (!$responses) {
            return null;
        }
        return end($responses) ?: null;
    }

    /**
     * Method submit_response.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param stdClass $cm Parameter cm.
     * @param int $userid Parameter userid.
     * @param int $level Parameter level.
     * @param ?float $latitude Parameter latitude.
     * @param ?float $longitude Parameter longitude.
     * @param ?float $accuracy Parameter accuracy.
     * @return int Return value.
     */
    public static function submit_response(stdClass $confidence, stdClass $cm, int $userid, int $level,
            ?float $latitude, ?float $longitude, ?float $accuracy): int {
        global $DB;

        if ($level < 1 || $level > 4) {
            throw new moodle_exception("invalidlevel", "mod_confidence");
        }
        if (!self::is_open($confidence)) {
            throw new moodle_exception("activityclosed", "mod_confidence");
        }

        $context = context_module::instance($cm->id);
        require_capability("mod/confidence:respond", $context, $userid);

        $lockfactory = lock_config::get_lock_factory("mod_confidence_response");
        $lock = $lockfactory->get_lock("confidence:" . $confidence->id . ":user:" . $userid, 10);
        if (!$lock) {
            throw new moodle_exception("cannotlock", "mod_confidence");
        }

        try {
            $existing = self::get_user_responses($confidence, $userid);
            if ($existing && empty($confidence->allowchange)) {
                throw new moodle_exception("responsealreadyexists", "mod_confidence");
            }

            $ipaddress = (string)getremoteaddr();
            self::validate_presence($confidence, $ipaddress, $latitude, $longitude);

            [$storeduserid, $participantkey] = self::response_identity($confidence, $userid);
            $record = (object)[
                "confidenceid" => $confidence->id,
                "userid" => $storeduserid,
                "participantkey" => $participantkey,
                "level" => $level,
                "timecreated" => time(),
                "timemodified" => time(),
            ];

            return (int)$DB->insert_record("confidence_response", $record);
        } finally {
            $lock->release();
        }
    }

    /**
     * Method validate_presence.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param string $ipaddress Parameter ipaddress.
     * @param ?float $latitude Parameter latitude.
     * @param ?float $longitude Parameter longitude.
     * @return void Return value.
     */
    private static function validate_presence(stdClass $confidence, string $ipaddress, ?float $latitude,
            ?float $longitude): void {
        global $DB;

        $mode = (int)$confidence->presencevalidation;
        if ($mode === self::PRESENCE_NONE) {
            return;
        }

        $reference = $DB->get_record("confidence_reference", ["confidenceid" => $confidence->id]);
        if (!$reference) {
            throw new moodle_exception("referencenotset", "mod_confidence");
        }

        if ($mode === self::PRESENCE_IP) {
            if ($reference->ipaddress === "" || !hash_equals((string)$reference->ipaddress, $ipaddress)) {
                throw new moodle_exception("ipmismatch", "mod_confidence");
            }
            return;
        }

        if ($mode === self::PRESENCE_LOCATION) {
            if ($latitude === null || $longitude === null) {
                throw new moodle_exception("locationrequired", "mod_confidence");
            }
            if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                throw new moodle_exception("invalidlocation", "mod_confidence");
            }
            if ($reference->latitude === null || $reference->longitude === null) {
                throw new moodle_exception("referencenotset", "mod_confidence");
            }

            $distance = self::distance_meters(
                (float)$reference->latitude,
                (float)$reference->longitude,
                $latitude,
                $longitude
            );
            if ($distance > (float)$confidence->locationradius) {
                throw new moodle_exception("locationoutside", "mod_confidence", "", (object)[
                    "distance" => (int)round($distance),
                    "radius" => (int)$confidence->locationradius,
                ]);
            }
        }
    }

    /**
     * Method distance_meters.
     *
     * @param float $lat1 Parameter lat1.
     * @param float $lon1 Parameter lon1.
     * @param float $lat2 Parameter lat2.
     * @param float $lon2 Parameter lon2.
     * @return float Return value.
     */
    private static function distance_meters(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $earthradius = 6371000.0;
        $latdelta = deg2rad($lat2 - $lat1);
        $londelta = deg2rad($lon2 - $lon1);
        $a = sin($latdelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($londelta / 2) ** 2;
        return $earthradius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Method register_reference.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param stdClass $cm Parameter cm.
     * @param int $userid Parameter userid.
     * @param ?float $latitude Parameter latitude.
     * @param ?float $longitude Parameter longitude.
     * @param ?float $accuracy Parameter accuracy.
     * @return stdClass Return value.
     */
    public static function register_reference(stdClass $confidence, stdClass $cm, int $userid,
            ?float $latitude, ?float $longitude, ?float $accuracy): stdClass {
        global $DB;

        $context = context_module::instance($cm->id);
        require_capability("mod/confidence:managereference", $context, $userid);

        if ((int)$confidence->presencevalidation === self::PRESENCE_LOCATION) {
            if ($latitude === null || $longitude === null) {
                throw new moodle_exception("locationrequired", "mod_confidence");
            }
            if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                throw new moodle_exception("invalidlocation", "mod_confidence");
            }
            if ($accuracy !== null && $accuracy < 0) {
                $accuracy = 0.0;
            }
        } else {
            $latitude = null;
            $longitude = null;
            $accuracy = null;
        }

        $now = time();
        $record = (object)[
            "confidenceid" => $confidence->id,
            "userid" => $userid,
            "ipaddress" => (int)$confidence->presencevalidation === self::PRESENCE_IP ? (string)getremoteaddr() : "",
            "latitude" => $latitude,
            "longitude" => $longitude,
            "accuracy" => $accuracy,
            "timemodified" => $now,
        ];

        $existing = $DB->get_record("confidence_reference", ["confidenceid" => $confidence->id]);
        if ($existing) {
            $record->id = $existing->id;
            $record->timecreated = $existing->timecreated;
            $DB->update_record("confidence_reference", $record);
        } else {
            $record->timecreated = $now;
            $record->id = $DB->insert_record("confidence_reference", $record);
        }

        return $record;
    }

    /**
     * Method get_reference.
     *
     * @param int $confidenceid Parameter confidenceid.
     * @return ?stdClass Return value.
     */
    public static function get_reference(int $confidenceid): ?stdClass {
        global $DB;
        return $DB->get_record("confidence_reference", ["confidenceid" => $confidenceid]) ?: null;
    }

    /**
     * Method report_data.
     *
     * @param stdClass $confidence Parameter confidence.
     * @return array Return value.
     */
    public static function report_data(stdClass $confidence): array {
        global $DB;

        $responses = $DB->get_records("confidence_response", ["confidenceid" => $confidence->id], "timecreated ASC, id ASC");
        $participants = [];
        $userids = [];

        foreach ($responses as $response) {
            $key = !empty($confidence->anonymous)
                ? "a:" . $response->participantkey
                : "u:" . $response->userid;

            if (!isset($participants[$key])) {
                $participants[$key] = [
                    "userid" => (int)$response->userid,
                    "first" => (int)$response->level,
                    "latest" => (int)$response->level,
                    "submissions" => 0,
                    "timemodified" => (int)$response->timemodified,
                ];
            }
            $participants[$key]["latest"] = (int)$response->level;
            $participants[$key]["submissions"]++;
            $participants[$key]["timemodified"] = (int)$response->timemodified;

            if (empty($confidence->anonymous) && !empty($response->userid)) {
                $userids[(int)$response->userid] = (int)$response->userid;
            }
        }

        $users = [];
        if ($userids) {
            $users = $DB->get_records_list("user", "id", array_values($userids), "",
                "id,firstname,lastname,firstnamephonetic,lastnamephonetic,middlename,alternatename");
        }

        $firstcounts = [0, 0, 0, 0];
        $latestcounts = [0, 0, 0, 0];
        $improved = 0;
        $unchanged = 0;
        $declined = 0;
        $rows = [];
        $anonymousnumber = 0;

        foreach ($participants as $participant) {
            $firstcounts[$participant["first"] - 1]++;
            $latestcounts[$participant["latest"] - 1]++;

            if ($participant["latest"] > $participant["first"]) {
                $improved++;
            } else if ($participant["latest"] < $participant["first"]) {
                $declined++;
            } else {
                $unchanged++;
            }

            if (!empty($confidence->anonymous)) {
                $anonymousnumber++;
                $name = get_string("anonymousparticipant", "mod_confidence", $anonymousnumber);
            } else {
                $user = $users[$participant["userid"]] ?? null;
                $name = $user ? fullname($user) : get_string("unknownuser", "mod_confidence");
            }

            $rows[] = [
                "name" => $name,
                "first" => $participant["first"],
                "latest" => $participant["latest"],
                "submissions" => $participant["submissions"],
                "timemodified" => $participant["timemodified"],
            ];
        }

        return [
            "firstcounts" => $firstcounts,
            "latestcounts" => $latestcounts,
            "participants" => count($participants),
            "submissions" => count($responses),
            "improved" => $improved,
            "unchanged" => $unchanged,
            "declined" => $declined,
            "rows" => $rows,
        ];
    }

    /**
     * Method can_student_see_results.
     *
     * @param stdClass $confidence Parameter confidence.
     * @param bool $hasresponse Parameter hasresponse.
     * @param ?int $time Parameter time.
     * @return bool Return value.
     */
    public static function can_student_see_results(stdClass $confidence, bool $hasresponse, ?int $time = null): bool {
        $time = $time ?? time();
        if ((int)$confidence->showresults === self::SHOWRESULTS_AFTER_RESPONSE) {
            return $hasresponse;
        }
        if ((int)$confidence->showresults === self::SHOWRESULTS_AFTER_CLOSE) {
            return !empty($confidence->timeclose) && $time > (int)$confidence->timeclose;
        }
        return false;
    }
}
