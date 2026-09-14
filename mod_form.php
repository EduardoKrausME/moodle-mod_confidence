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
 * mod_form.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->dirroot}/course/moodleform_mod.php");

/**
 * Class mod_confidence_mod_form.
 */
class mod_confidence_mod_form extends moodleform_mod {
    /**
     * Method definition.
     *
     * @return mixed Return value.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement("header", "general", get_string("general", "form"));
        $mform->addElement("text", "name", get_string("confidencename", "mod_confidence"), ["size" => 64]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");

        $this->standard_intro_elements();

        $mform->addElement("editor", "question_editor", get_string("question", "mod_confidence"), null, [
            "maxfiles" => 0,
            "maxbytes" => 0,
        ]);
        $mform->addRule("question_editor", null, "required", null, "client");

        $mform->addElement("header", "responsesettings", get_string("responsesettings", "mod_confidence"));
        $mform->addElement("selectyesno", "anonymous", get_string("anonymous", "mod_confidence"));
        $mform->addHelpButton("anonymous", "anonymous", "mod_confidence");
        $mform->setDefault("anonymous", 0);

        $mform->addElement("selectyesno", "allowchange", get_string("allowchange", "mod_confidence"));
        $mform->addHelpButton("allowchange", "allowchange", "mod_confidence");
        $mform->setDefault("allowchange", 1);

        $showoptions = [
            \mod_confidence\manager::SHOWRESULTS_NEVER => get_string("showresultsnever", "mod_confidence"),
            \mod_confidence\manager::SHOWRESULTS_AFTER_RESPONSE => get_string("showresultsafterresponse", "mod_confidence"),
            \mod_confidence\manager::SHOWRESULTS_AFTER_CLOSE => get_string("showresultsafterclose", "mod_confidence"),
        ];
        $mform->addElement("select", "showresults", get_string("showresults", "mod_confidence"), $showoptions);
        $mform->setDefault("showresults", \mod_confidence\manager::SHOWRESULTS_AFTER_RESPONSE);

        $mform->addElement("header", "availability", get_string("availability"));
        $mform->addElement("date_time_selector", "timeopen", get_string("timeopen", "mod_confidence"), ["optional" => true]);
        $mform->addElement("date_time_selector", "timeclose", get_string("timeclose", "mod_confidence"), ["optional" => true]);

        $mform->addElement("header", "presence", get_string("presencevalidation", "mod_confidence"));
        $presenceoptions = [
            \mod_confidence\manager::PRESENCE_NONE => get_string("presencenone", "mod_confidence"),
            \mod_confidence\manager::PRESENCE_IP => get_string("presenceip", "mod_confidence"),
            \mod_confidence\manager::PRESENCE_LOCATION => get_string("presencelocation", "mod_confidence"),
        ];
        $mform->addElement("select", "presencevalidation", get_string("presencevalidation", "mod_confidence"), $presenceoptions);
        $mform->addHelpButton("presencevalidation", "presencevalidation", "mod_confidence");
        $mform->setDefault("presencevalidation", \mod_confidence\manager::PRESENCE_NONE);

        $mform->addElement("text", "locationradius", get_string("locationradius", "mod_confidence"), ["size" => 8]);
        $mform->setType("locationradius", PARAM_INT);
        $mform->setDefault("locationradius", 100);
        $mform->addHelpButton("locationradius", "locationradius", "mod_confidence");
        $mform->hideIf("locationradius", "presencevalidation", "neq", \mod_confidence\manager::PRESENCE_LOCATION);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Method data_preprocessing.
     *
     * @param mixed $defaultvalues Parameter defaultvalues.
     * @return mixed Return value.
     */
    public function data_preprocessing(&$defaultvalues) {
        parent::data_preprocessing($defaultvalues);

        if (isset($defaultvalues["question"])) {
            $defaultvalues["question_editor"] = [
                "text" => $defaultvalues["question"],
                "format" => $defaultvalues["questionformat"] ?? FORMAT_HTML,
            ];
        }
    }

    /**
     * Method validation.
     *
     * @param mixed $data Parameter data.
     * @param mixed $files Parameter files.
     * @return mixed Return value.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data["timeopen"]) && !empty($data["timeclose"]) && $data["timeclose"] <= $data["timeopen"]) {
            $errors["timeclose"] = get_string("errortimeclose", "mod_confidence");
        }

        if ((int)$data["showresults"] === \mod_confidence\manager::SHOWRESULTS_AFTER_CLOSE && empty($data["timeclose"])) {
            $errors["timeclose"] = get_string("errorcloseforresults", "mod_confidence");
        }

        if ((int)$data["presencevalidation"] === \mod_confidence\manager::PRESENCE_LOCATION) {
            $radius = (int)($data["locationradius"] ?? 0);
            if ($radius < 1 || $radius > 5000) {
                $errors["locationradius"] = get_string("errorlocationradius", "mod_confidence");
            }
        }

        return $errors;
    }
}
