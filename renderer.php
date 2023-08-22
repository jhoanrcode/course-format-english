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
 * Formato de Cursos
 *
 * @package    formato_uniminuto_english
 * @copyright  2023 onwards Jhoan Avila 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/format/renderer.php');

use core_courseformat\output\section_renderer;

class format_uniminuto_english_renderer extends section_renderer {

    protected $courseformat;

    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course);
        /* Since format_trail_renderer::section_edit_controls() only displays the 'Set current section' control when editing
          mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
          other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    public function get_base_renderer() {
        return $this->courserenderer;
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'uniminuto_english'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_uniminuto_english');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate next/previous section links for naviation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    public function get_nav_links($course, $sections, $sectionno) {
       
    }


    /**
     * Renders user Admin.
     * @param \format_uniminuto_english\output\format_uniminuto_single_layout $section Object of the Section renderable.
     */
    public function render_admin_layout(
        \format_uniminuto_english\output\admin_layout_renderable $section) {
        $templatecontext = $section->export_for_template($this);
        if (isset($templatecontext->error)) {
            throw new \moodle_exception($templatecontext->error);
        } else {
            global $PAGE;
            $PAGE->requires->js('/lib/jquery/jquery-3.6.0.min.js');
            echo $this->render_from_template('format_uniminuto_english/admin_layout', $templatecontext);
        }
    }


    /**
     * Renders user Student.
     * @param \format_uniminuto_english\output\format_uniminuto_single_layout $section Object of the Section renderable.
     */
    public function render_student_layout(
        \format_uniminuto_english\output\student_layout_renderable $section) {
        $templatecontext = $section->export_for_template($this);
        if (isset($templatecontext->error)) {
            throw new \moodle_exception($templatecontext->error);
        } else {
            global $PAGE;
            echo '<link rel="stylesheet" href="format/uniminuto_english/css/student.css" type="text/css"> ';
            echo '<link rel="stylesheet" href="format/uniminuto_english/css/student-background.css" type="text/css"> ';
            echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> ';
            echo '<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet"> ';
            $PAGE->requires->js('/lib/jquery/jquery-3.6.0.min.js');
            echo $this->render_from_template('format_uniminuto_english/student_layout', $templatecontext);
        }
    }


    /**
     * Renders Course Mood Edit.
     * Format -> Core Moodle.
     * @param core_courseformat/local/content $data  Object.
     */
    public function render_content($widget) {
        $data = $widget->export_for_template($this);
        return $this->render_from_template('core_courseformat/local/content', $data);
    }
    

}
