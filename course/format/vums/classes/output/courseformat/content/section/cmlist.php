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
 * Contains the default activity list from a section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_vums\output\courseformat\content\section;

use core_courseformat\base as course_format;
use core_courseformat\output\local\content\section\cmlist as cmlist_base;
use core_courseformat\output\local\content\section\optional;
use core_courseformat\output\local\content\section\renderer_base;

use section_info;

class cmlist extends cmlist_base
{

    /**
     * Constructor.
     *
     * @param  course_format  $format  the course format
     * @param  section_info  $section  the section info
     * @param  array  $displayoptions  optional extra display options
     */
    public function __construct(course_format $format, section_info $section, array $displayoptions = [])
    {
        parent::__construct($format, $section, $displayoptions);

        // Get the necessary classes.
        $this->itemclass = $format->get_output_classname('content\\section\\cmitem');
    }

    public function get_template_name(\renderer_base $renderer): string
    {
        return 'format_vums/local/content/section/cmlist';
    }
}
