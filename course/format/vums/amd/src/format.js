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
 * Enhancements to all components for easy course accessibility.
 *
 * @module     format/vums
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    /**
     * Init method
     *
     * @param {Object} availableFormats Available formates
     */
    function init(availableFormats) {
        $(document).ready(function() {
            var sectionLayoutVal;
            var sectionBackgroundVal;
            var layoutValue = $("#id_vumscourseformat").val();
            window.localStorage.setItem('coursedisplay', $("#id_coursedisplay").val());
            // Hide and show the course settings on course format selection.
            $("#id_vumscourseformat").change(function() {
                var layoutValue = $("#id_vumscourseformat").val();
                // CARD.
                if (layoutValue == availableFormats.VUMS_CARD_FORMAT.format) {
                    $("#id_coursedisplay option[value='0']").hide();
                    $('#id_coursedisplay').val(1).trigger('change');
                    $("#id_vumsteacherdisplay").parent().parent().hide();
                    $("#id_vumsdefaultsectionview").parent().parent().hide();
                    $("#id_vumsenablecardbackgroundimg").parent().parent().show();
                    sectionBackgroundVal = $("#id_vumsenablecardbackgroundimg").val();
                    if (sectionBackgroundVal == 0) {
                        $("#id_vumsdefaultsectiontheme").parent().parent().hide();
                    } else {
                        $("#id_vumsdefaultsectiontheme").parent().parent().show();
                    }
                    // LIST.
                } else {
                    $("#id_coursedisplay option[value='0']").show();
                    var oldcoursedisplay = window.localStorage.getItem('coursedisplay');
                    $('#id_coursedisplay').val(oldcoursedisplay).trigger('change');
                    $("#id_vumsteacherdisplay").parent().parent().show();
                    $("#id_vumsenablecardbackgroundimg").parent().parent().hide();
                    $("#id_vumsdefaultsectiontheme").parent().parent().hide();
                }
                sectionLayoutVal = $("#id_coursedisplay").val();
                if (sectionLayoutVal == 1) {
                    $("#id_vumsdefaultsectionview").parent().parent().hide();
                } else {
                    $("#id_vumsdefaultsectionview").parent().parent().show();
                }
            }).trigger('change');

            // CARD.
            if (layoutValue == availableFormats.VUMS_CARD_FORMAT.format) {
                $("#id_coursedisplay option[value='0']").hide();
                $("#id_vumsteacherdisplay").parent().parent().hide();
                $("#id_vumsdefaultsectionview").parent().parent().hide();
                sectionBackgroundVal = $("#id_vumsenablecardbackgroundimg").val();
                if (sectionBackgroundVal == 0) {
                    $("#id_vumsdefaultsectiontheme").parent().parent().hide();
                } else {
                    $("#id_vumsdefaultsectiontheme").parent().parent().show();
                }
                // LIST.
            } else {
                $("#id_vumsteacherdisplay").parent().parent().show();
                sectionLayoutVal = $("#id_coursedisplay").val();
                if (sectionLayoutVal == 1) {
                    $("#id_vumsdefaultsectionview").parent().parent().hide();
                }
                $("#id_vumsenablecardbackgroundimg").parent().parent().hide();
                $("#id_vumsdefaultsectiontheme").parent().parent().hide();
            }
            $("#id_coursedisplay").change(function() {
                sectionLayoutVal = $("#id_coursedisplay").val();
                if (sectionLayoutVal == 1) {
                    $("#id_vumsdefaultsectionview").parent().parent().hide();
                } else {
                    $("#id_vumsdefaultsectionview").parent().parent().show();
                }
            });

            $("#id_vumsenablecardbackgroundimg").change(function() {
                sectionBackgroundVal = $("#id_vumsenablecardbackgroundimg").val();
                if (sectionBackgroundVal == 0) {
                    $("#id_vumsdefaultsectiontheme").parent().parent().hide();
                } else {
                    $("#id_vumsdefaultsectiontheme").parent().parent().show();
                }
            });

            $("#course-introduction-button").on('click', () => {
                //hide introdution button for blocks
                $("#course-content-button2").addClass('d-none');
                $("#course-blocks").addClass('d-none');
                $("#course-blocks-button").removeClass('d-none');

                //show course cntent button
                $("#course-content-button").removeClass('d-none');
                //hide activites
                $("#list-container").addClass('d-none');
                $("#list-editing-container").addClass('d-none');
                //hide introdution button
                $("#course-introduction-button").addClass('d-none');
                //show introductions
                $("#course-introduction").removeClass('d-none');

            });
            $("#course-blocks-button").on('click', () => {
                //hide course content button for blocks
                $("#course-content-button").addClass('d-none');
                $("#course-introduction").addClass('d-none');
                $("#course-introduction-button").removeClass('d-none');

                //show course content button
                $("#course-content-button2").removeClass('d-none');
                //hide activites
                $("#list-container").addClass('d-none');
                $("#list-editing-container").addClass('d-none');

                //hide course block button
                $("#course-blocks-button").addClass('d-none');

                //show blocks
                $("#course-blocks").removeClass('d-none');
            });
            $("#course-content-button").on('click', () => {
                $("#list-container").removeClass('d-none');
                $("#list-editing-container").removeClass('d-none');
                $("#course-introduction-button").removeClass('d-none');
                $("#course-introduction").addClass('d-none');
                $("#course-content-button").addClass('d-none');
            });
            $("#course-content-button2").on('click', () => {
                $("#list-container").removeClass('d-none');
                $("#list-editing-container").removeClass('d-none');
                $("#course-blocks-button").removeClass('d-none');
                $("#course-blocks").addClass('d-none');
                $("#course-content-button2").addClass('d-none');
            });
        });
    }

    // Must return the init function.
    return {
        init: init
    };
});
