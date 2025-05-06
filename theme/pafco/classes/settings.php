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
 * Theme helper to load a theme configuration.
 *
 * @package    theme_pafco
 * @copyright  2024 Pafco - http://pafcoerp.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot. '/lib/outputlib.php');

/**
 * Helper to load a theme configuration.
 *
 * @package    theme_pafco
 * @copyright  2024 Pafco - http://pafcoerp.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_pafco_settings {
    /**
     * @var \stdClass $theme The theme object.
     */
    protected $theme;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->theme = theme_config::load('pafco');
    }

    /**
     * Magic method to get theme settings
     *
     * @param string $name
     *
     * @return false|string|null
     */
    public function __get(string $name) {

        if (empty($this->theme->settings->$name)) {
            return false;
        }

        return $this->theme->settings->$name;
    }


    /**
     * Get frontpage settings
     *
     * @return array
     */
    public function frontpage() {
        return array_merge(
            $this->frontpage_numbers(),
            $this->blog_entries(),
            $this->faq()
        );
    }


    /**
     * Get config theme numbers
     *
     * @return array
     */
    public function frontpage_numbers() {
        global $DB;

        if ($templatecontext['numbersfrontpage'] = $this->numbersfrontpage) {
            $templatecontext['numberscontent'] = $this->numbersfrontpagecontent ? format_text($this->numbersfrontpagecontent) : '';
            $templatecontext['numbersusers'] = $DB->count_records_sql(
                'SELECT COUNT(DISTINCT userid)
                    FROM {role_assignments}
                    WHERE roleid = 5'
            );
            $templatecontext['numberscourses'] = $DB->count_records('course', ['visible' => 1]) - 1;
            $templatecontext['numbersteachers'] = $DB->count_records_sql(
                'SELECT COUNT(DISTINCT userid)
                    FROM {role_assignments}
                    WHERE roleid = 3'
            );
            $templatecontext['numbersactivties'] = $DB->count_records('course_modules', ['visible' => 1]) - 1;
        }

        return $templatecontext;
    }

    /**
     * Get config theme slideshow
     *
     * @return array
     */
    public function blog_entries() {
        global $DB;

        if (current_language() !== 'fa'){
            $templatecontext['no_news']=true;
            return $templatecontext;
        }

        $templatecontext['news'] = array_values($DB->get_records_sql('SELECT id,subject,summary,created FROM {post} ORDER BY created DESC',null,0,6));

        return $templatecontext;
    }


    /**
     * Get config theme slideshow
     *
     * @return array
     */
    public function faq() {
        $templatecontext['faqenabled'] = false;
        $theme = theme_config::load('pafco');

        if ($this->faqcount) {
            for ($i = 1; $i <= $this->faqcount; $i++) {
                $faqquestion = 'faqquestion' . $i;
                $faqanswer = 'faqanswer' . $i;

                if (!$this->$faqquestion || !$this->$faqanswer) {
                    continue;
                }

                $templatecontext['faq'][] = [
                    'id' => $i,
                    'question' => $this->$faqquestion,
                    'answer' => $this->$faqanswer
                ];
            }

            if ($templatecontext['faq'] && count($templatecontext['faq'])) {
                $templatecontext['faqenabled'] = true;
            }
        }

        return $templatecontext;
    }
}
