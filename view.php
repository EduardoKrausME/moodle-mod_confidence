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
 * view.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$cm = get_coursemodule_from_id("confidence", $id, 0, false, MUST_EXIST);
$course = $DB->get_record("course", ["id" => $cm->course], "*", MUST_EXIST);
$confidence = $DB->get_record("confidence", ["id" => $cm->instance], "*", MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/confidence:view", $context);

require_once($CFG->libdir . "/completionlib.php");
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url(new moodle_url("/mod/confidence/view.php", ["id" => $cm->id]));
$PAGE->set_title(format_string($confidence->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->strings_for_js([
    "locationunsupported",
    "gettinglocation",
    "locationdenied",
    "savingreference",
], "mod_confidence");

$manager = \mod_confidence\manager::class;
$levels = $manager::get_levels();
$currentresponse = $manager::get_latest_user_response($confidence, $USER->id);
$canrespond = has_capability("mod/confidence:respond", $context) && $manager::is_open($confidence);
$ismanager = has_capability("mod/confidence:viewreport", $context);
$reference = $manager::get_reference($confidence->id);

$availabilitymessage = "";
if (!empty($confidence->timeopen) && time() < (int)$confidence->timeopen) {
    $availabilitymessage = get_string("opensat", "mod_confidence", userdate($confidence->timeopen));
} else if (!empty($confidence->timeclose) && time() > (int)$confidence->timeclose) {
    $availabilitymessage = get_string("closedat", "mod_confidence", userdate($confidence->timeclose));
}

$options = [];
foreach ($levels as $value => $label) {
    $options[] = [
        "value" => $value,
        "label" => $label,
        "checked" => $currentresponse && (int)$currentresponse->level === $value,
    ];
}

$studentresults = [];
$showstudentresults = $manager::can_student_see_results($confidence, (bool)$currentresponse);
if ($showstudentresults) {
    $data = $manager::report_data($confidence);
    $total = max(1, array_sum($data["latestcounts"]));
    foreach ($levels as $index => $label) {
        $count = $data["latestcounts"][$index - 1];
        $studentresults[] = [
            "label" => $label,
            "count" => $count,
            "percent" => (int)round(($count / $total) * 100),
        ];
    }
}

$referenceready = false;
if ((int)$confidence->presencevalidation === $manager::PRESENCE_IP) {
    $referenceready = $reference && (string)$reference->ipaddress !== "";
} else if ((int)$confidence->presencevalidation === $manager::PRESENCE_LOCATION) {
    $referenceready = $reference && $reference->latitude !== null && $reference->longitude !== null;
}

$presence = [
    "enabled" => (int)$confidence->presencevalidation !== $manager::PRESENCE_NONE,
    "ip" => (int)$confidence->presencevalidation === $manager::PRESENCE_IP,
    "location" => (int)$confidence->presencevalidation === $manager::PRESENCE_LOCATION,
    "radius" => (int)$confidence->locationradius,
    "referenceready" => $referenceready,
];

$templatecontext = [
    "cmid" => $cm->id,
    "name" => format_string($confidence->name),
    "intro" => format_module_intro("confidence", $confidence, $cm->id),
    "question" => format_text($confidence->question, $confidence->questionformat, ["context" => $context]),
    "canrespond" => $canrespond,
    "allowchange" => !empty($confidence->allowchange),
    "hasresponse" => (bool)$currentresponse,
    "currentlabel" => $currentresponse ? $levels[(int)$currentresponse->level] : "",
    "options" => $options,
    "availabilitymessage" => $availabilitymessage,
    "requireslocation" => (int)$confidence->presencevalidation === $manager::PRESENCE_LOCATION,
    "showstudentresults" => $showstudentresults,
    "studentresults" => $studentresults,
    "ismanager" => $ismanager,
    "presence" => $presence,
    "referenceip" => $reference ? s($reference->ipaddress) : "",
    "referencelocation" => $reference && $reference->latitude !== null
        ? format_float((float)$reference->latitude, 6) . ", " . format_float((float)$reference->longitude, 6)
        : "",
    "reporturl" => (new moodle_url("/mod/confidence/report.php", ["id" => $cm->id]))->out(false),
    "sesskey" => sesskey(),
];

$PAGE->requires->js_call_amd("mod_confidence/response", "init");
if ($ismanager && (int)$confidence->presencevalidation !== $manager::PRESENCE_NONE) {
    $PAGE->requires->js_call_amd("mod_confidence/reference", "init", [$cm->id, (int)$confidence->presencevalidation]);
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_confidence/view", $templatecontext);
echo $OUTPUT->footer();
