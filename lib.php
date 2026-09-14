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
 * lib.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return supported Moodle features.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function confidence_supports($feature) {
    if (defined("FEATURE_MOD_PURPOSE") && $feature === FEATURE_MOD_PURPOSE) {
        return MOD_PURPOSE_COMMUNICATION;
    }

    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_OTHER;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Add an activity instance.
 *
 * @param stdClass $data Form data.
 * @param moodleform|null $mform Form object.
 * @return int
 */
function confidence_add_instance($data, $mform = null) {
    return \mod_confidence\manager::add_instance($data);
}

/**
 * Update an activity instance.
 *
 * @param stdClass $data Form data.
 * @param moodleform|null $mform Form object.
 * @return bool
 */
function confidence_update_instance($data, $mform = null) {
    return \mod_confidence\manager::update_instance($data);
}

/**
 * Delete an activity instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function confidence_delete_instance($id) {
    return \mod_confidence\manager::delete_instance($id);
}


/**
 * Serve files from the activity intro area.
 *
 * @param stdClass $course Course record.
 * @param stdClass $cm Course module record.
 * @param context $context Module context.
 * @param string $filearea File area.
 * @param array $args File path arguments.
 * @param bool $forcedownload Force download.
 * @param array $options Additional options.
 * @return bool
 */
function confidence_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_MODULE || $filearea !== "intro") {
        return false;
    }

    require_login($course, true, $cm);
    require_capability("mod/confidence:view", $context);

    $fs = get_file_storage();
    $relativepath = implode("/", $args);
    $fullpath = "/{$context->id}/mod_confidence/intro/0/{$relativepath}";
    $file = $fs->get_file_by_hash(sha1($fullpath));
    if (!$file || $file->is_directory()) {
        return false;
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}
