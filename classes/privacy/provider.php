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
 * provider.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_confidence\privacy;

use context;
use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Class provider.
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Method get_metadata.
     *
     * @param collection $collection Parameter collection.
     * @return collection Return value.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table("confidence_response", [
            "userid" => "privacy:metadata:confidence_response:userid",
            "participantkey" => "privacy:metadata:confidence_response:participantkey",
            "level" => "privacy:metadata:confidence_response:level",
            "timecreated" => "privacy:metadata:confidence_response:timecreated",
            "timemodified" => "privacy:metadata:confidence_response:timemodified",
        ], "privacy:metadata:confidence_response");

        $collection->add_database_table("confidence_reference", [
            "userid" => "privacy:metadata:confidence_reference:userid",
            "ipaddress" => "privacy:metadata:confidence_reference:ipaddress",
            "latitude" => "privacy:metadata:confidence_reference:latitude",
            "longitude" => "privacy:metadata:confidence_reference:longitude",
            "accuracy" => "privacy:metadata:confidence_reference:accuracy",
            "timecreated" => "privacy:metadata:confidence_reference:timecreated",
            "timemodified" => "privacy:metadata:confidence_reference:timemodified",
        ], "privacy:metadata:confidence_reference");

        return $collection;
    }

    /**
     * Method get_contexts_for_userid.
     *
     * @param int $userid Parameter userid.
     * @return contextlist Return value.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {confidence} c ON c.id = cm.instance
             LEFT JOIN {confidence_response} r ON r.confidenceid = c.id AND r.userid = :responseuserid
             LEFT JOIN {confidence_reference} ref ON ref.confidenceid = c.id AND ref.userid = :referenceuserid
                 WHERE r.id IS NOT NULL OR ref.id IS NOT NULL";
        $contextlist->add_from_sql($sql, [
            "contextlevel" => CONTEXT_MODULE,
            "modname" => "confidence",
            "responseuserid" => $userid,
            "referenceuserid" => $userid,
        ]);

        $anonymouscmids = [];
        $sql = "SELECT c.id, c.anonymoussalt, cm.id AS cmid
                  FROM {confidence} c
                  JOIN {modules} m ON m.name = :modname
                  JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = c.id
                 WHERE c.anonymous = 1";
        foreach ($DB->get_records_sql($sql, ["modname" => "confidence"]) as $instance) {
            $participantkey = hash_hmac("sha256", (string)$userid, $instance->anonymoussalt);
            if ($DB->record_exists("confidence_response", [
                    "confidenceid" => $instance->id,
                    "participantkey" => $participantkey,
                ])) {
                $anonymouscmids[] = (int)$instance->cmid;
            }
        }

        if ($anonymouscmids) {
            [$insql, $params] = $DB->get_in_or_equal($anonymouscmids, SQL_PARAMS_NAMED, "cmid");
            $params["contextlevel"] = CONTEXT_MODULE;
            $contextlist->add_from_sql(
                "SELECT ctx.id FROM {context} ctx WHERE ctx.contextlevel = :contextlevel AND ctx.instanceid {$insql}",
                $params
            );
        }

        return $contextlist;
    }

    /**
     * Method export_user_data.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id("confidence", $context->instanceid);
            if (!$cm) {
                continue;
            }
            $confidence = $DB->get_record("confidence", ["id" => $cm->instance]);
            if (!$confidence) {
                continue;
            }

            $conditions = ["confidenceid" => $confidence->id];
            if (!empty($confidence->anonymous)) {
                $conditions["participantkey"] = hash_hmac("sha256", (string)$userid, $confidence->anonymoussalt);
            } else {
                $conditions["userid"] = $userid;
            }

            $responses = $DB->get_records("confidence_response", $conditions, "timecreated ASC");
            $export = [];
            foreach ($responses as $response) {
                $export[] = [
                    "level" => $response->level,
                    "timecreated" => transform::datetime($response->timecreated),
                ];
            }
            $userdata = [];
            if ($export) {
                $userdata["responses"] = $export;
            }

            $reference = $DB->get_record("confidence_reference", [
                "confidenceid" => $confidence->id,
                "userid" => $userid,
            ]);
            if ($reference) {
                $userdata["teacherreference"] = [
                    "ipaddress" => $reference->ipaddress,
                    "latitude" => $reference->latitude,
                    "longitude" => $reference->longitude,
                    "accuracy" => $reference->accuracy,
                    "timecreated" => transform::datetime($reference->timecreated),
                    "timemodified" => transform::datetime($reference->timemodified),
                ];
            }

            if ($userdata) {
                writer::with_context($context)->export_data([get_string("pluginname", "mod_confidence")], (object)$userdata);
            }
        }
    }

    /**
     * Method delete_data_for_all_users_in_context.
     *
     * @param context $context Parameter context.
     * @return void Return value.
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if (!$context instanceof context_module) {
            return;
        }
        $cm = get_coursemodule_from_id("confidence", $context->instanceid);
        if (!$cm) {
            return;
        }
        $DB->delete_records("confidence_response", ["confidenceid" => $cm->instance]);
        $DB->delete_records("confidence_reference", ["confidenceid" => $cm->instance]);
    }

    /**
     * Method delete_data_for_user.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id("confidence", $context->instanceid);
            if (!$cm) {
                continue;
            }
            $confidence = $DB->get_record("confidence", ["id" => $cm->instance]);
            if (!$confidence) {
                continue;
            }

            if (!empty($confidence->anonymous)) {
                $participantkey = hash_hmac("sha256", (string)$userid, $confidence->anonymoussalt);
                $DB->delete_records("confidence_response", [
                    "confidenceid" => $confidence->id,
                    "participantkey" => $participantkey,
                ]);
            } else {
                $DB->delete_records("confidence_response", [
                    "confidenceid" => $confidence->id,
                    "userid" => $userid,
                ]);
            }
            $DB->delete_records("confidence_reference", ["confidenceid" => $confidence->id, "userid" => $userid]);
        }
    }
}
