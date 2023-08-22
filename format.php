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

global $CFG, $PAGE;

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

$context = context_course::instance($course->id);
//Adiciona la información del curso
$course = course_get_format($course)->get_course();

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

//Creamos automaticamente las secciones necesarias y asignamos nombres
if ( course_create_sections_if_missing($course, range(0, 10)) ) {
    $modinfo = get_fast_modinfo($course);
    $new_name_sections = array(
        get_string('module_welcome','format_uniminuto_english'),
        get_string('module_first','format_uniminuto_english'),
        get_string('module_second','format_uniminuto_english'),
        get_string('module_third','format_uniminuto_english'),
        get_string('help_ebook','format_uniminuto_english'),
        get_string('help_videoclasses','format_uniminuto_english'),
        get_string('help_games','format_uniminuto_english'),
        get_string('help_collaborative','format_uniminuto_english'),
        get_string('help_virtual','format_uniminuto_english'),
        get_string('help_examen','format_uniminuto_english')
    );
    for ($i=1; $i <= 10; $i++) { 
        $infosection = $modinfo->get_section_info($i);
        course_update_section($infosection->course, $infosection, array('name' => $new_name_sections[($i-1)]));
    }
}

$course->hiddensections = 1; // Las secciones ocultas son totalmente invisibles
$course->coursedisplay = COURSE_DISPLAY_MULTIPAGE; // Muestra las seccion tipo Lista
$section = optional_param('section', 0, PARAM_INT); // Almacena en que sección esta actualmente.

$renderer = $PAGE->get_renderer('format_uniminuto_english');
$baserenderer = $renderer->get_base_renderer();
$format = course_get_format($course);

//Determinamos el tipo de rol del usuario
$roles = get_user_roles($context, $USER->id, false); $rol_user = '';
foreach ($roles as $role) {
    if ( $role->roleid < 5 ) { //Admin
        //Si el Admin desea ser Estudiante desde cambio de rol
        if( property_exists($USER,'access') && array_key_exists($context->path, $USER->access['rsw']) && $USER->access['rsw'][$context->path] == 5 ){ $rol_user = 'student'; } 
        else { $rol_user = 'admin';  }
    }
    if ( $role->roleid == 5 && $role->shortname == 'student' ) { $rol_user = 'student'; }//Student
}

if ( !($USER->editing) ) {
    if ( $rol_user != '' ) {
        if ( $rol_user == 'admin') { //Vista Administracion
            $renderer->render_admin_layout( new \format_uniminuto_english\output\admin_layout_renderable($course, $baserenderer, $rol_user) );
            // Include course format js module.
            $PAGE->requires->js('/course/format/uniminuto_english/format.js');
            $PAGE->requires->js('/course/format/uniminuto_english/js/functionsTeacher.js');
        } else { //Vista Estudiante
            $renderer->render_student_layout( new \format_uniminuto_english\output\student_layout_renderable($course, $baserenderer, $rol_user) );
            // Include course format js module.
            $PAGE->requires->js('/course/format/uniminuto_english/format.js');
            $PAGE->requires->js('/course/format/uniminuto_english/js/functionsStudent.js');
        }
    }
} else{
    $outputclass = $format->get_output_classname('content');
    $widget = new $outputclass($format);
    echo $renderer->render_content($widget);
    // Include course format js module.
    $PAGE->requires->js('/course/format/uniminuto_english/format.js');
}

