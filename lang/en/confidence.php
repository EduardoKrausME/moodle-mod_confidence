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
 * confidence.php
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['activityclosed'] = 'This activity is not currently open for responses.';
$string['allowchange'] = 'Allow students to change their response';
$string['allowchange_help'] = 'When enabled, every new response is kept in the history. The report compares each participant\'s first response with their latest response.';
$string['anonymous'] = 'Anonymous responses';
$string['anonymous_help'] = 'When enabled, reports do not identify participants. The activity uses a pseudonymous participant key so first and latest responses can still be compared.';
$string['anonymousparticipant'] = 'Anonymous participant ';
$string['anonymousreport'] = 'Anonymous responses';
$string['autoreload'] = 'The report refreshes automatically every 3 seconds.';
$string['backtoactivity'] = 'Back to activity';
$string['cannotlock'] = 'Your response could not be locked for saving. Please try again.';
$string['changeresponse'] = 'Update your confidence';
$string['chartarea'] = 'Area';
$string['chartbar'] = 'Bars';
$string['chartpie'] = 'Pie';
$string['charttype'] = 'Chart type';
$string['closedat'] = 'Responses closed at .';
$string['confidence:addinstance'] = 'Add a Confidence level activity';
$string['confidence:managereference'] = 'Register the room presence reference';
$string['confidence:respond'] = 'Submit confidence responses';
$string['confidence:view'] = 'View Confidence level activities';
$string['confidence:viewreport'] = 'View confidence reports';
$string['confidencename'] = 'Activity name';
$string['declined'] = 'Decreased';
$string['distribution'] = 'Before and after distribution';
$string['erroranonymouslocked'] = 'Anonymous mode cannot be changed after responses have been submitted.';
$string['errorcloseforresults'] = 'Set a close time when results are configured to appear after the activity closes.';
$string['errorlocationradius'] = 'The location radius must be between 1 and 5000 meters.';
$string['errortimeclose'] = 'The close time must be after the open time.';
$string['firstresponse'] = 'First response';
$string['gettinglocation'] = 'Getting your location…';
$string['groupresults'] = 'Current group results';
$string['improved'] = 'Increased';
$string['invalidlevel'] = 'Invalid confidence level.';
$string['invalidlocation'] = 'The supplied location is invalid.';
$string['ipmismatch'] = 'Your current IP does not match the teacher\'s room reference.';
$string['lastupdate'] = 'Last update';
$string['latestresponse'] = 'Latest response';
$string['legendafter'] = 'After';
$string['legendbefore'] = 'Before';
$string['level1'] = 'I don\'t know';
$string['level2'] = 'I have little confidence';
$string['level3'] = 'I think I know';
$string['level4'] = 'I master it';
$string['livereport'] = 'Live report';
$string['locationdenied'] = 'Location permission was denied or the location could not be obtained.';
$string['locationoutside'] = 'You are approximately  meters from the room reference. The allowed radius is  meters.';
$string['locationradius'] = 'Allowed radius (meters)';
$string['locationradius_help'] = 'Maximum distance between the student\'s browser location and the room reference registered by the teacher.';
$string['locationrequired'] = 'Browser location is required to submit this response.';
$string['locationunsupported'] = 'This browser does not provide geolocation.';
$string['modulename'] = 'Confidence level';
$string['modulename_help'] = 'Confidence level is a metacognitive self-assessment activity. Students choose one of four levels: I don\'t know, I have little confidence, I think I know, or I master it. Responses can be repeated so teachers can compare before and after a lesson. The activity can be anonymous and can optionally restrict responses by the teacher\'s room IP or geolocation. It does not grade students and does not use AI.';
$string['modulename_summary'] = 'Collect a quick self-assessment of how confident students feel about a concept and compare the first response with the latest response.';
$string['modulename_tip'] = 'Use the same activity immediately before and after a class when you want students to reflect on how their perceived understanding changed.';
$string['modulenameplural'] = 'Confidence levels';
$string['noinstances'] = 'There are no Confidence level activities in this course.';
$string['opensat'] = 'Responses open at .';
$string['participant'] = 'Participant';
$string['participants'] = 'Participants';
$string['pluginadministration'] = 'Confidence level administration';
$string['pluginname'] = 'Confidence level';
$string['presenceip'] = 'Same IP as the teacher';
$string['presenceipnotice'] = 'This activity requires the same public IP as the teacher\'s room reference.';
$string['presencelocation'] = 'Location near the teacher reference';
$string['presencelocationnotice'] = 'This activity requires browser location within  meters of the teacher\'s room reference.';
$string['presencenone'] = 'No presence validation';
$string['presencevalidation'] = 'Presence validation';
$string['presencevalidation_help'] = 'Optionally require students to be on the same public IP as the teacher reference or physically close to a location registered by the teacher.';
$string['privacy:metadata:confidence_reference'] = 'Stores the teacher\'s room reference for presence validation.';
$string['privacy:metadata:confidence_reference:accuracy'] = 'The browser-reported accuracy of the teacher reference location.';
$string['privacy:metadata:confidence_reference:ipaddress'] = 'The teacher reference IP address.';
$string['privacy:metadata:confidence_reference:latitude'] = 'The teacher reference latitude.';
$string['privacy:metadata:confidence_reference:longitude'] = 'The teacher reference longitude.';
$string['privacy:metadata:confidence_reference:timecreated'] = 'The time the teacher reference was created.';
$string['privacy:metadata:confidence_reference:timemodified'] = 'The time the teacher reference was last updated.';
$string['privacy:metadata:confidence_reference:userid'] = 'The teacher who registered the reference.';
$string['privacy:metadata:confidence_response'] = 'Stores confidence response history.';
$string['privacy:metadata:confidence_response:level'] = 'The selected confidence level.';
$string['privacy:metadata:confidence_response:participantkey'] = 'A pseudonymous participant key used for anonymous activities.';
$string['privacy:metadata:confidence_response:timecreated'] = 'The time the response was created.';
$string['privacy:metadata:confidence_response:timemodified'] = 'The time the response record was last modified.';
$string['privacy:metadata:confidence_response:userid'] = 'The user ID for non-anonymous activities.';
$string['question'] = 'Question or content';
$string['referenceip'] = 'Reference IP';
$string['referencelocation'] = 'Reference location';
$string['referencemissingteacher'] = 'Register the room reference before students respond.';
$string['referencenotset'] = 'The teacher has not registered the room reference yet.';
$string['referencesaved'] = 'Room reference saved.';
$string['registerreference'] = 'Register room reference';
$string['reporttitle'] = 'Confidence report: ';
$string['responsealreadyexists'] = 'You have already responded and this activity does not allow changes.';
$string['responsesaved'] = 'Your confidence response was saved.';
$string['responsesettings'] = 'Response settings';
$string['savenewresponse'] = 'Save new response';
$string['savingreference'] = 'Saving room reference…';
$string['showresults'] = 'Students can see group results';
$string['showresultsafterclose'] = 'After the activity closes';
$string['showresultsafterresponse'] = 'After submitting a response';
$string['showresultsnever'] = 'Never';
$string['submissions'] = 'Responses';
$string['submitresponse'] = 'Submit response';
$string['teacherpanel'] = 'Teacher controls';
$string['timeclose'] = 'Close at';
$string['timeopen'] = 'Open from';
$string['unchanged'] = 'Unchanged';
$string['unknownuser'] = 'Unknown user';
$string['viewreport'] = 'Open live report';
$string['yourconfidence'] = 'How confident are you right now?';
$string['yourcurrentresponse'] = 'Your current response:';
