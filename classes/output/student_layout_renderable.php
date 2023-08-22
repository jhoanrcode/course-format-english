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

namespace format_uniminuto_english\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;
use moodle_url;

require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->dirroot.'/course/format/uniminuto_english/classes/course_format_data_common_uniminuto_english.php');
require_once($CFG->dirroot.'/course/format/uniminuto_english/lib.php');


class student_layout_renderable implements renderable, templatable {

    private $course;
    private $courseformat;
    protected $courserenderer;
    private $userole;
    private $courseformatdatacommonuniminuto;

    /**
     * Contructor
     * @param object          $course   Course object
     * @param course_renderer $renderer Course renderer
     */
    public function __construct($course, $renderer, $user) {
        $this->courseformat = course_get_format($course);
        $this->course = $this->courseformat->get_course();
        $this->courserenderer = $renderer;
        $this->userole = $user;
        $this->courseformatdatacommonuniminuto = \format_uniminuto_english\course_format_data_common_uniminuto_english::getinstance();
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * question mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE, $DB, $OUTPUT, $CFG;

        $export = new \stdClass();
        $renderer = $PAGE->get_renderer('format_uniminuto_english');

        // Get necessary default values required to display the UI.
        $editing = $PAGE->user_is_editing();
        $export->editing = $editing;
        $export->courseformat = get_config('format_uniminuto_english', 'defaultcourseformat');
        $export->theme = $PAGE->theme->name;

        $this->get_data_format_context($export, $renderer, $editing);

        return  $export;
    }

    /**
     * Get list layout context
     * @param  object      $export  Object in which context will be stored
     * @param  format_remuiformat $renderer format renderer
     * @param  bool        $editing  Editing mode
     */
    private function get_data_format_context(&$export, $renderer, $editing) {
        global $DB, $OUTPUT, $USER, $PAGE;

        //Datos globales
        $imagen_url = new moodle_url("/course/format/uniminuto_english/pix");

        //Datos exportados al template
        $export->user_id = $USER->id;
        $export->courseid = $this->course->id;
        $export->course_name = $this->course->fullname;
        $export->image_url = $imagen_url;
        $export->url_logo = $OUTPUT->get_logo_url();
        $user_role = $this->userole;

        //Datos Seccion Modulos
        $export->participants_url = new moodle_url('/user/index.php', array('id' => $this->course->id));
        $export->grades_url = new moodle_url('/grade/report/user/index.php', array('id' => $this->course->id));
        
        //Datos Seccion Modulos
        $export->info_sections_module = $this->get_data_seccion(1, 4, $user_role); //Secciones de Modulos
        $export->info_menu_recourse = $this->get_data_seccion(5, 10, $user_role); //Secciones de Recursos

        //print_r($export->info_sections_module[0]->data_activity);
        //print_r($export->info_menu_recourse[0]);
    }


    /**
     * Get data section
     */
    private function get_data_seccion($sectionindex, $section_end, $user_role) {

        $sections = array();
        for ($sectionindex; $sectionindex <= $section_end; $sectionindex++) {
            $section = $this->courseformatdatacommonuniminuto->info_all_seccion($this->course, $sectionindex, $section_end, $user_role);
            if ( !($section->hiddensection) ) { $sections[] = $section; } //Si la seccion esta oculta la ignoramos
        }
        return $sections;

    }



}
