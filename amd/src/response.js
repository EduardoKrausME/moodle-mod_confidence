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
 * response.js
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery"], function($) {
    var locateAndSubmit = function(form) {
        var status = $(form).find(".confidence-location-status");
        if (!navigator.geolocation) {
            status.text(M.util.get_string("locationunsupported", "mod_confidence"));
            return;
        }

        status.text(M.util.get_string("gettinglocation", "mod_confidence"));
        navigator.geolocation.getCurrentPosition(function(position) {
            $(form).find("input[name='latitude']").val(position.coords.latitude);
            $(form).find("input[name='longitude']").val(position.coords.longitude);
            $(form).find("input[name='accuracy']").val(position.coords.accuracy || 0);
            form.dataset.locationReady = "1";
            form.submit();
        }, function() {
            status.text(M.util.get_string("locationdenied", "mod_confidence"));
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    };

    return {
        init: function() {
            M.util.js_pending("mod_confidence_response");
            $(document).on("submit", ".confidence-response-form", function(event) {
                if ($(this).data("requires-location") !== 1 || this.dataset.locationReady === "1") {
                    return;
                }
                event.preventDefault();
                locateAndSubmit(this);
            });
            M.util.js_complete("mod_confidence_response");
        }
    };
});
