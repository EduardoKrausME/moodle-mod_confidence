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
 * report.js
 *
 * @package   mod_confidence
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/ajax", "core/notification"], function($, Ajax, Notification) {
    var namespace = "http://www.w3.org/2000/svg";
    var chartType = "pie";
    var latestData = null;
    var labels = [];

    var createSvg = function(width, height) {
        var svg = document.createElementNS(namespace, "svg");
        svg.setAttribute("viewBox", "0 0 " + width + " " + height);
        svg.setAttribute("width", "100%");
        svg.setAttribute("height", height);
        return svg;
    };

    var appendText = function(svg, x, y, text, className, anchor) {
        var node = document.createElementNS(namespace, "text");
        node.setAttribute("x", x);
        node.setAttribute("y", y);
        node.setAttribute("class", className || "");
        if (anchor) {
            node.setAttribute("text-anchor", anchor);
        }
        node.textContent = text;
        svg.appendChild(node);
    };

    var appendRect = function(svg, x, y, width, height, className) {
        var node = document.createElementNS(namespace, "rect");
        node.setAttribute("x", x);
        node.setAttribute("y", y);
        node.setAttribute("width", Math.max(0, width));
        node.setAttribute("height", Math.max(0, height));
        node.setAttribute("class", className);
        svg.appendChild(node);
    };

    var renderPie = function(container, data) {
        var svg = createSvg(640, 320);
        var total = data.latestcounts.reduce(function(sum, value) { return sum + value; }, 0);
        var cx = 175;
        var cy = 160;
        var radius = 115;
        var start = -Math.PI / 2;

        data.latestcounts.forEach(function(value, index) {
            var fraction = total > 0 ? value / total : 0;
            var end = start + fraction * Math.PI * 2;
            if (fraction > 0) {
                var x1 = cx + radius * Math.cos(start);
                var y1 = cy + radius * Math.sin(start);
                var x2 = cx + radius * Math.cos(end);
                var y2 = cy + radius * Math.sin(end);
                var large = fraction > 0.5 ? 1 : 0;
                var path = document.createElementNS(namespace, "path");
                path.setAttribute("d", "M " + cx + " " + cy + " L " + x1 + " " + y1 +
                    " A " + radius + " " + radius + " 0 " + large + " 1 " + x2 + " " + y2 + " Z");
                path.setAttribute("class", "confidence-chart-level-" + (index + 1));
                svg.appendChild(path);
            }
            start = end;
        });

        labels.forEach(function(label, index) {
            appendRect(svg, 360, 70 + index * 48, 18, 18, "confidence-chart-level-" + (index + 1));
            appendText(svg, 390, 84 + index * 48, label + ": " + data.latestcounts[index], "confidence-chart-label");
        });
        container.append(svg);
    };

    var renderBars = function(container, data) {
        var svg = createSvg(700, 340);
        var maxValue = Math.max.apply(null, data.firstcounts.concat(data.latestcounts).concat([1]));
        var baseline = 275;
        var maxHeight = 210;
        var groupWidth = 145;

        labels.forEach(function(label, index) {
            var x = 70 + index * groupWidth;
            var beforeHeight = data.firstcounts[index] / maxValue * maxHeight;
            var afterHeight = data.latestcounts[index] / maxValue * maxHeight;
            appendRect(svg, x, baseline - beforeHeight, 42, beforeHeight, "confidence-chart-before");
            appendRect(svg, x + 48, baseline - afterHeight, 42, afterHeight, "confidence-chart-level-" + (index + 1));
            appendText(svg, x + 45, 300, label, "confidence-chart-axis", "middle");
            appendText(svg, x + 21, baseline - beforeHeight - 8, data.firstcounts[index], "confidence-chart-value", "middle");
            appendText(svg, x + 69, baseline - afterHeight - 8, data.latestcounts[index], "confidence-chart-value", "middle");
        });
        appendText(svg, 70, 28, M.util.get_string("legendbefore", "mod_confidence"), "confidence-chart-legend");
        appendRect(svg, 48, 16, 14, 14, "confidence-chart-before");
        appendText(svg, 190, 28, M.util.get_string("legendafter", "mod_confidence"), "confidence-chart-legend");
        appendRect(svg, 168, 16, 14, 14, "confidence-chart-after");
        container.append(svg);
    };

    var renderArea = function(container, data) {
        var svg = createSvg(700, 340);
        var maxValue = Math.max.apply(null, data.firstcounts.concat(data.latestcounts).concat([1]));
        var top = 40;
        var bottom = 275;
        var beforeX = 180;
        var afterX = 520;

        labels.forEach(function(label, index) {
            var y1 = bottom - (data.firstcounts[index] / maxValue) * (bottom - top);
            var y2 = bottom - (data.latestcounts[index] / maxValue) * (bottom - top);
            var area = document.createElementNS(namespace, "path");
            area.setAttribute("d", "M " + beforeX + " " + bottom + " L " + beforeX + " " + y1 +
                " L " + afterX + " " + y2 + " L " + afterX + " " + bottom + " Z");
            area.setAttribute("class", "confidence-chart-area confidence-chart-level-" + (index + 1));
            svg.appendChild(area);
            appendText(svg, beforeX - 18, y1 + 4, data.firstcounts[index], "confidence-chart-value", "end");
            appendText(svg, afterX + 18, y2 + 4, data.latestcounts[index], "confidence-chart-value");
        });
        appendText(svg, beforeX, 310, M.util.get_string("legendbefore", "mod_confidence"), "confidence-chart-axis", "middle");
        appendText(svg, afterX, 310, M.util.get_string("legendafter", "mod_confidence"), "confidence-chart-axis", "middle");
        container.append(svg);
    };

    var renderChart = function(data) {
        var container = $("[data-confidence-chart]").empty();
        if (chartType === "bar") {
            renderBars(container, data);
        } else if (chartType === "area") {
            renderArea(container, data);
        } else {
            renderPie(container, data);
        }
    };

    var updateRows = function(rows) {
        var tbody = $("[data-confidence-report-rows]").empty();
        rows.forEach(function(row) {
            var tr = $("<tr>");
            $("<td>").text(row.name).appendTo(tr);
            $("<td>").text(labels[row.first - 1] || row.first).appendTo(tr);
            $("<td>").text(labels[row.latest - 1] || row.latest).appendTo(tr);
            $("<td>").text(row.submissions).appendTo(tr);
            $("<td>").text(new Date(row.timemodified * 1000).toLocaleString()).appendTo(tr);
            tbody.append(tr);
        });
    };

    var updateMetrics = function(data) {
        ["participants", "submissions", "improved", "unchanged", "declined"].forEach(function(key) {
            $("[data-metric='" + key + "']").text(data[key]);
        });
    };

    var load = function(cmid) {
        Ajax.call([{
            methodname: "mod_confidence_get_report_data",
            args: {cmid: cmid}
        }])[0].done(function(data) {
            latestData = data;
            updateMetrics(data);
            updateRows(data.rows);
            renderChart(data);
        }).fail(Notification.exception);
    };

    return {
        init: function(cmid, levelLabels) {
            labels = levelLabels;
            $(document).on("click", "[data-chart-type]", function() {
                chartType = $(this).data("chart-type");
                $("[data-chart-type]").removeClass("active");
                $(this).addClass("active");
                if (latestData) {
                    renderChart(latestData);
                }
            });
            load(cmid);
            window.setInterval(function() {
                load(cmid);
            }, 3000);
        }
    };
});
