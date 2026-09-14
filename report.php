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
 * report.php
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
require_capability("mod/confidence:viewreport", $context);

$PAGE->set_url(new moodle_url("/mod/confidence/report.php", ["id" => $cm->id]));
$PAGE->set_title(get_string("reporttitle", "mod_confidence", format_string($confidence->name)));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->strings_for_js([
    "legendbefore",
    "legendafter",
], "mod_confidence");
$PAGE->requires->js_call_amd("mod_confidence/report", "init", [$cm->id, array_values(\mod_confidence\manager::get_levels())]);

$data = \mod_confidence\manager::report_data($confidence);
$templatecontext = [
    "cmid" => $cm->id,
    "name" => format_string($confidence->name),
    "anonymous" => !empty($confidence->anonymous),
    "participants" => $data["participants"],
    "submissions" => $data["submissions"],
    "improved" => $data["improved"],
    "unchanged" => $data["unchanged"],
    "declined" => $data["declined"],
    "backurl" => (new moodle_url("/mod/confidence/view.php", ["id" => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_confidence/report", $templatecontext);
echo $OUTPUT->footer();
