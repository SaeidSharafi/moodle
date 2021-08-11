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
 * course_renderer.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_egkuro
 * @copyright   2015 LMSACE Dev Team, lmsace.com
 * @author      LMSACE Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/renderer.php");

/**
 * This class has function for core course renderer
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_egkuro_core_course_renderer extends core_course_renderer
{

    /**
     * Outputs contents for frontpage as configured in $CFG->frontpage or $CFG->frontpageloggedin
     *
     * @return string
     */
    public function frontpage() {
        global $CFG, $SITE;

        $output = '';

        if (isloggedin() and !isguestuser() and isset($CFG->frontpageloggedin)) {
            $frontpagelayout = $CFG->frontpageloggedin;
        } else {
            $frontpagelayout = $CFG->frontpage;
        }

        foreach (explode(',', $frontpagelayout) as $v) {
            switch ($v) {
                // Display the main part of the front page.
                case FRONTPAGENEWS:
                    if ($SITE->newsitems) {
                        // Print forums only when needed.
                        require_once($CFG->dirroot .'/mod/forum/lib.php');
                        if (($newsforum = forum_get_course_forum($SITE->id, 'news')) &&
                            ($forumcontents = $this->frontpage_news($newsforum))) {
                            $newsforumcm = get_fast_modinfo($SITE)->instances['forum'][$newsforum->id];
                            $output .= $this->frontpage_part('skipsitenews', 'site-news-forum',
                                $newsforumcm->get_formatted_name(), $forumcontents);
                        }
                    }
                    break;

                case FRONTPAGEENROLLEDCOURSELIST:
                    $mycourseshtml = $this->frontpage_my_courses();
                    if (!empty($mycourseshtml)) {
                        $output .= $this->frontpage_part('skipmycourses', 'frontpage-course-list',
                            get_string('mycourses'), $mycourseshtml);
                    }else{
                        $admins = get_admins();
                        $isadmin = false;
                        global $USER;
                        foreach ($admins as $admin) {
                            if ($USER->id == $admin->id) {
                                $isadmin = true;
                                break;
                            }
                        }
                        if ($isadmin) {
                            $availablecourseshtml = $this->frontpage_available_courses();
                            $output .= $this->frontpage_part('skipavailablecourses', 'frontpage-available-course-list',
                                get_string('availablecourses'), $availablecourseshtml);
                        }
                    }
                    break;

                case FRONTPAGEALLCOURSELIST:
                    $availablecourseshtml = $this->frontpage_available_courses();
                    $output .= $this->frontpage_part('skipavailablecourses', 'frontpage-available-course-list',
                        get_string('availablecourses'), $availablecourseshtml);
                    break;

                case FRONTPAGECATEGORYNAMES:
                    $output .= $this->frontpage_part('skipcategories', 'frontpage-category-names',
                        get_string('categories'), $this->frontpage_categories_list());
                    break;

                case FRONTPAGECATEGORYCOMBO:
                    $output .= $this->frontpage_part('skipcourses', 'frontpage-category-combo',
                        get_string('courses'), $this->frontpage_combo_list());
                    break;

                case FRONTPAGECOURSESEARCH:
                    $output .= $this->box($this->course_search_form('', 'short'), 'mdl-align');
                    break;

            }
            $output .= '<br />';
        }

        return $output;
    }

    /**
     * Renderer function for the frontpage available courses.
     * @return string
     */
    public function frontpage_available_courses()
    {
        /* available courses */
        global $CFG, $OUTPUT;
        // require_once($CFG->libdir. '/coursecatlib.php');.

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(array(
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php'),
            'viewmoretext' => new lang_string('fulllistofcourses')
        ));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = core_course_category::get(0)->get_courses($chelper->get_courses_display_options());
        $totalcount = core_course_category::get(0)->get_courses_count($chelper->get_courses_display_options());
        if (true) {
            $coursemenucontent = $OUTPUT->context_header_settings_menu();
            if (true) {
                if ($coursemenucontent) {
                    echo '<div class="frontpage-setting"><div class="context-header-settings-menu">' .
                        $coursemenucontent .
                        '</div></div>';

                }
            }
            return $this->coursecat_courses($chelper, $courses, $totalcount);
        } else {
            $rcourseids = array_keys($courses);
            $newcourse = get_string('availablecourses');

            $header = '<div id="frontpage-course-list"><h2>' . $newcourse . '</h2><div class="courses frontpage-course-list-all">';
            $footer = '</div></div>';
            $content = '';
            if (count($rcourseids) > 0) {
                $content .= '<div class="row">';
                foreach ($rcourseids as $courseid) {

                    $rowcontent = '';

                    $course = get_course($courseid);

                    $no = get_config('theme_egkuro', 'patternselect');
                    $nimgp = (empty($no) || $no == "default") ? 'default/no-image' : 'cs0' . $no . '/no-image';
                    $noimgurl = $OUTPUT->image_url($nimgp, 'theme');
                    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

                    if ($course instanceof stdClass) {
                        // require_once($CFG->libdir. '/coursecatlib.php');.
                        $course = new core_course_list_element($course);
                    }

                    $imgurl = '';
                    $context = context_course::instance($course->id);

                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                            '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                            $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                        if (!$isimage) {
                            $imgurl = $noimgurl;
                        }
                    }

                    if (empty($imgurl)) {
                        $imgurl = $noimgurl;
                    }

                    $rowcontent .= '<div class="col-md-3 col-sm-6"><div class="fp-coursebox"><div class="fp-coursethumb"><a href="' . $courseurl . '"><img src="' . $imgurl . '" width="243" height="165" alt=""></a></div><div class="fp-courseinfo"><h5><a href="' . $courseurl . '">' . $course->get_formatted_name() . '</a></h5></div></div></div>';
                    $content .= $rowcontent;
                }
                $content .= '</div>';
            }

            $coursehtml = $header . $content . $footer;


            if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
                // Print link to create a new course, for the 1st available category.
                $coursehtml .= $this->add_new_course_button();
            }
            return $coursehtml;
        }

    }

    /**
     * Promoted courses.
     * @return string
     */
    public function promoted_courses()
    {
        global $CFG, $OUTPUT, $DB;

        $pcourseenable = theme_egkuro_get_setting('pcourseenable');
        if (!$pcourseenable) {
            return false;
        }

        $featuredcontent = '';
        /* Get Featured courses id from DB */
        $featuredids = theme_egkuro_get_setting('promotedcourses');
        $rcourseids = (!empty($featuredids)) ? explode(",", $featuredids) : array();
        if (empty($rcourseids)) {
            return false;
        }

        $hcourseids = theme_egkuro_hidden_courses_ids();

        if (!empty($hcourseids)) {
            foreach ($rcourseids as $key => $val) {
                if (in_array($val, $hcourseids)) {
                    unset($rcourseids[$key]);
                }
            }
        }

        foreach ($rcourseids as $key => $val) {
            $ccourse = $DB->get_record('course', array('id' => $val));
            if (empty($ccourse)) {
                unset($rcourseids[$key]);
                continue;
            }
        }

        if (empty($rcourseids)) {
            return false;
        }

        $fcourseids = array_chunk($rcourseids, 6);
        $totalfcourse = count($fcourseids);
        $promotedtitle = theme_egkuro_get_setting('promotedtitle', 'format_html');
        $promotedtitle = theme_egkuro_lang($promotedtitle);

        $featuredheader = '<div class="custom-courses-list" id="Promoted-Courses"><div class="container"><div class="titlebar with-felements"><h2>' . $promotedtitle . '</h2><div class="slidenav pagenav"><button class="nav-item nav-prev slick-prev"><i class="fa fa-chevron-right"></i><i class="fa fa-chevron-left"></i></button><button class="nav-item nav-next slick-next"><i class="fa fa-chevron-right"></i><i class="fa fa-chevron-left"></i></button><div class="clearfix"></div></div><div class="clearfix"></div></div><div class="promoted_courses" data-crow="' . $totalfcourse . '">';

        $featuredfooter = ' </div></div></div>';

        if (!empty($fcourseids)) {
            foreach ($fcourseids as $courseids) {
                $rowcontent = '<div><div class="row staw">';
                foreach ($courseids as $courseid) {
                    $course = get_course($courseid);
                    $no = get_config('theme_egkuro', 'patternselect');
                    $nimgp = (empty($no) || $no == "default") ? 'default/no-image' : 'cs0' . $no . '/no-image';

                    $noimgurl = $OUTPUT->image_url($nimgp, 'theme');

                    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

                    if ($course instanceof stdClass) {
                        // require_once($CFG->libdir. '/coursecatlib.php');.
                        $course = new core_course_list_element($course);
                    }

                    $imgurl = '';

                    $summary = theme_egkuro_strip_html_tags($course->summary);
                    $summary = theme_egkuro_course_trim_char($summary, 75);

                    $context = context_course::instance($course->id);
                    $nostudents = count_role_users(5, $context);

                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                            '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                            $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                        if (!$isimage) {
                            $imgurl = $noimgurl;
                        }
                    }
                    if (empty($imgurl)) {
                        $imgurl = $noimgurl;
                    }
                    $coursehtml = '<div class="col-md-2"><div class="course-box"><div class="thumb"><a href="' . $courseurl . '"><img src="' . $imgurl . '" width="135" height="135" alt=""></a></div><div class="info"><h5><a href="' . $courseurl . '">' . $course->get_formatted_name() . '</a></h5></div></div></div>';

                    $rowcontent .= $coursehtml;
                }
                $rowcontent .= '</div></div>';
                $featuredcontent .= $rowcontent;
            }
        }
        $featuredcourses = $featuredheader . $featuredcontent . $featuredfooter;
        return $featuredcourses;
    }


    /**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null)
    {

        global $CFG;

//        $theme = \theme_config::load('kuro');
//
//        if (!empty($theme->settings->courselistview)) {
//           // return parent::coursecat_courses($chelper, $courses, $totalcount);
//        }

        if ($totalcount === null) {
            $totalcount = count($courses);
        }

        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit.
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // Prepare content of paging bar if it is needed.
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // There are more results that can fit on one page.
            if ($paginationurl) {
                // The option paginationurl was specified, display pagingbar.
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                    $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                        get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // The option for 'View more' link was specified, display more link.
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new \lang_string('viewmore'));
                /**
                 *
                 * Hide all courses link for non admin users
                 *
                 */
                $admins = get_admins();
                $isadmin = false;
                global $USER;
                foreach ($admins as $admin) {
                    if ($USER->id == $admin->id) {
                        $isadmin = true;
                        break;
                    }
                }
                if ($isadmin) {
                    $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
                } else {
                    $morelink = "";
                }
                // $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                // array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // There are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode.
            $pagingbar = html_writer::tag(
                'div',
                html_writer::link(
                    $paginationurl->out(
                        false,
                        array('perpage' => $CFG->coursesperpage)
                    ),
                    get_string('showperpage', '', $CFG->coursesperpage)
                ),
                array('class' => 'paging paging-showperpage')
            );
        }

        // Display list of courses.
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 1;
        $content .= html_writer::start_tag('div', array('class' => 'row card-deck mt-2'));
        foreach ($courses as $course) {
            $content .= $this->coursecat_coursebox($chelper, $course);

//            if ($coursecount % 4 == 0) {
//                $content .= html_writer::end_tag('div');
//                $content .= html_writer::start_tag('div', array('class' => 'card-deck mt-2'));
//            }

            $coursecount++;
        }

        $content .= html_writer::end_tag('div');

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // End courses.
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     *
     * @throws \coding_exception
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '')
    {

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        $classes = trim('col-md-3 mb-2');
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }

        // End coursebox.
        $content = html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        //var_dump($course);
        $content .= $this->coursecat_coursebox_content($chelper, $course);

        $content .= html_writer::end_tag('div'); // End coursebox.

        return $content;
    }


    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course)
    {
        global $CFG, $OUTPUT;
        $content = '';
        $image_array = array();
        $summary = "";
        $contacts = array();
        $no = get_config('theme_egkuro', 'patternselect');
        $nimgp = (empty($no) || $no == "default") ? 'default/no-image' : 'cs0' . $no . '/no-image';
        $noimgurl = $OUTPUT->image_url($nimgp, 'theme');
//        var_dump($course);
        foreach ($course->get_course_overviewfiles() as $file) {

            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);

            $courseItem['image_url'] = $url;
            //echo $url;
            if (!$isimage) {
                $courseItem['image_url'] = $noimgurl;
            }

            array_push($image_array, $courseItem);
//            var_dump($image_array);

        }
        if (!$image_array) {
            $courseItem['image_url'] = $noimgurl;
            array_push($image_array, $courseItem);
        }
        // Display course summary.
        if ($course->has_summary()) {
            $summary = $chelper->get_course_formatted_summary($course);
        }
        // Display course contacts. See course_in_list::get_course_contacts().
        if ($course->has_course_contacts()) {
            $counter = 0;
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $contact['role'] = $coursecontact['rolename'];
                $contact['name'] = $coursecontact['username'];
                $contact['url'] = new moodle_url('/message/index.php', array('id' => $userid));
                array_push($contacts, $contact);
                if ($counter > 3){
                    break;
                }
                $counter++;
            }


        }
        $data = (object)[
            'name' => $course->fullname,
            'images' => $image_array,
            'summary' => $summary,
            'contacts' => $contacts,
            'contact_string' => get_string('contactteacher', 'theme_egkuro'),
            'url' => new moodle_url('/course/view.php', array('id' => $course->id))

        ];
        return $this->render_from_template('theme_egkuro/coursecat_coursebox_content', $data);
    }

}