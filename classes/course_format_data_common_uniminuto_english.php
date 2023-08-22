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

namespace format_uniminuto_english;
defined('MOODLE_INTERNAL') || die;
use context_module;
use moodle_url;
use cm_info;

/**
 *  Clase Datos en comun de kas modalidades en el formato de curso
 */
class course_format_data_common_uniminuto_english {

    protected static $instance;
    private $_plugin_config;

    private function __construct() {
        $this->plugin_config = "format_uniminuto_english";
    }


    /**
     * Singleton Implementation.  @return course_format_data_common_uniminuto_english Instance
     */
    public static function getinstance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    
    /**
     * Info Sections Data.
     * @param text      $info_course       Data Curso
     * @param int       $num_section       # Seccion
     * @param text      $last_num_section  # Ultima Seccion
     */
    public function info_all_seccion( $info_course, $num_section, $last_num_section, $user_role ) {

        $data_section = new \stdClass();
        $modinfo = get_fast_modinfo($info_course);
        $infosection = $modinfo->get_section_info($num_section); //Informacion basica de la seccion

        // Si la seccion para el usuario esta oculta no se renderiza 
        $data_section->hiddensection = $this->estado_seccion($infosection, $last_num_section);
        if ( $data_section->hiddensection ) { return $data_section; } 

        $data_section->num_section = ( $num_section < 5 ) ? ($infosection->section - 1) : ($infosection->section - 5);
        $data_section->name_section = get_section_name($info_course, $infosection->section);
        $data_section->icon_section = $this->icono_seccion($infosection->section);
        
        //Componentes activos segun rol
        if ( $user_role == "admin" ) {
            $data_section->active_carousel_section = ( $num_section == 1 || $num_section == 5 ) ? "active" : "" ;
            $data_section->id_accordion_h5p = ( $num_section >= 2 || $num_section <= 4 ) ? ("accordion".$num_section) : "" ;
            $data_section->data_activity = $this->info_actividades($info_course, $modinfo, $num_section, $info_course->id, $user_role);
            $data_section->has_activity = ( empty($data_section->data_activity) ) ? false : true ;
        } else {
            if ( $num_section >= 1 && $num_section <= 4 ) { //Secciones Unidades
                $data_section->active_nav_section = ""; $data_section->tab_welcome = false;
                $data_section->title_module = $this->titulo_a_caracter($data_section->name_section); // Titulo de secciones por letra
                //Seccion Welcome
                if ( $num_section == 1 ) { 
                    $data_section->active_nav_section = "active";
                    $data_section->tab_welcome = true;
                }
            } else{ //Secciones Recursos
                $data_section->active_carousel_recourse = ( $num_section == 5 ) ? "active" : "" ;
            }
            $data_section->data_activity = $this->info_actividades($info_course, $modinfo, $num_section, $info_course->id, $user_role);
        }

        return $data_section;
    }


    /**
      * Estado Visible de seccion data.
      * @param  object          $infosection        Data Seccion.
      * @param  int             $last_num_section   # Seccion.
     */
    public function estado_seccion($infosection, $last_num_section) {
        $hiddenbynumsections = $infosection->section > $last_num_section ? 1 : 0;
        $hiddenbysection = ($infosection->available && ($infosection->uservisible || $infosection->visible)) ? false : true ;
        $hiddensection = ($hiddenbynumsections || $hiddenbysection) ? true : false ;
        return $hiddensection;
    }


    /**
      * Icono de seccion.
      * @param  int             $num_section   # Seccion.
     */
    public function icono_seccion($num_section) {
        switch ($num_section) { 
            case 1: $img_icon_section = "welcome";  break;
            case 5: $img_icon_section = "e-book";  break;
            case 6: $img_icon_section = "video-class";  break;
            case 7: $img_icon_section = "game";  break;
            case 8: $img_icon_section = "collaborative";  break;
            case 9: $img_icon_section = "video-tutorial";  break;
            case 10: $img_icon_section = "exam";  break;
            default: $img_icon_section = "module";
        }
        return $img_icon_section;
    }


    /**
      * Estado Visible de actividad data.
      * @param  object          $mod        Data Actividad.
      * @param  int             $rol_user   Rol Usuario.
     */
    public function estado_actividad($mod, $rol_user) {

        /*echo $mod->name."<br>";
        echo "Borrado: ".$mod->deletioninprogress."<br>";
        echo "Visible: ".$mod->visible."<br>";
        echo "Visible en curso: ".$mod->is_visible_on_course_page()."<br>";
        echo "Visible usuario: ".$mod->uservisible."<br>";
        echo "Habilitado: ".$mod->available."<br><br>";*/
        $state_activity = false;
        if ( $mod->deletioninprogress == 0 ){
            if ($rol_user == "admin") { //Estado de actividad si es Rol Docente
                if ( ($mod->visible || $mod->is_visible_on_course_page()) && ( $mod->uservisible ) ) {
                    $state_activity = true;
                }
            } else { //Estado de actividad si es Rol Estudiante
                if ($mod->modname == "hvp" || $mod->modname == "h5pactivity") { // Si la actividad es un H5P
                    if ( ($mod->visible || $mod->is_visible_on_course_page()) ) {
                        $state_activity = true;
                    }
                } else {
                    if ( ($mod->visible || $mod->is_visible_on_course_page()) && ( $mod->uservisible && $mod->available) ) {
                        $state_activity = true;
                    }
                }
            }
        }
        return $state_activity;

    }


    /**
      * Informacion Basica Actividades por Seccion.
      * @param  object          $modinfo        Data Secciones.
      * @param  int             $num_section    # Seccion.
      * @param  int             $courseid       ID course.
      * @param  int             $userid         ID user.
     */
    public function info_actividades($info_course, $modinfo, $num_section, $courseid, $user_role) {

        global $DB;
        $info_activities = $info_activity =  array();
        $info_activities_welcome = $info_activity_welcome = $info_activities_units = $info_activity_unit = array();
        $info_url_frame_label = $slides = "";
        $completioninfo = new \completion_info($info_course);
        $modules_urls = get_all_instances_in_course('url', $info_course);

        foreach ($modinfo->sections[$num_section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];  
            //Si la actividad es visible
            if ( $this->estado_actividad($thismod, $user_role) ){

                // Segun el rol regresamos datos de actividades
                if ( ( $user_role == "admin" && ($num_section <= 1 || $num_section >= 5) ) || ( $user_role == "student" && $num_section >= 5 ) ) {
                    if ($thismod->modname == "label") {
                        //Ajustamos la ruta de imagenes para etiquetas
                        $modcontext = context_module::instance($thismod->id); //Context de actividad
                        $summary_label = ($DB->get_record( $thismod->modname, ['id' => $thismod->instance,'course'=>$courseid], 'intro'))->intro;
                        $search = "@@PLUGINFILE@@";
                        $replace = "../pluginfile.php/".$modcontext->id."/mod_label/intro";
                        $new_summary_label = str_replace($search, $replace, $summary_label);
    
                        // Guardar informacion de actividad
                        $info_activity = array(
                            "name_module" => $thismod->name, //Nombre de Actividad
                            "instance_module" => $thismod->instance, //Instancia de Actividad
                            "summary_label" => $new_summary_label, //Detalle Actividad Label
                            "type_activity_h5p" => false, //Si es actividad tipo H5P
                            "type_activity" => false, //Si es actividad o label
                        );
                    } 
                    else{ 
                        $moduleshtml = $this->course_section_cm_list_item_english($info_course, $completioninfo, $thismod, null); // Obtenemos opcion marcar finalizacion de actividad
                        list($type_link, $window_pageurl) = $this->tipo_url_actividad($thismod->modname, $thismod->url, $thismod->id, $thismod->name, $modules_urls); // Obtenemos la URL para abrir contenido de actividad

                        // Guardar informacion de actividad
                        $info_activity = array(
                            "name_module" => $thismod->name, //Nombre de Actividad
                            "instance_module" => $thismod->instance, //Instancia de Actividad
                            "pageurl" => $type_link, //URL de Actividad
                            "window_pageurl" => $window_pageurl, //Ventana donde se abre URL de Actividad
                            "type_module" => $thismod->modname, //Tipo de Actividad
                            "icon_module" => $thismod->get_icon_url()->out(false), //Icono de Actividad
                            "mark_completion" => $moduleshtml, //Boton Marcar Finalizacion de Actividad
                            "type_activity_h5p" => false, //Si es actividad tipo H5P
                            "type_activity" => true, //Si es actividad o label
                        );
                    }
                    $info_activities[] = $info_activity;
                } else if ( $user_role == "admin" && ($num_section >= 2 || $num_section <= 4) ){  // Rol Administrador - Modulos H5P
                    if ( ($thismod->modname == "hvp" || $thismod->modname == "h5pactivity")) {

                        $result_refresh = $this->refresh_diapositivas_h5p($thismod->instance, $courseid); //Actualizacion de Data H5P en BD
                        $moduleshtml = $this->course_section_cm_list_item_english($info_course, $completioninfo, $thismod, null); // Obtenemos opcion marcar finalizacion de actividad
                        list($type_link, $window_pageurl) = $this->tipo_url_actividad($thismod->modname, $thismod->url, $thismod->id, $thismod->name, $modules_urls); // Obtenemos la URL para abrir contenido de actividad
                        $slides = ($result_refresh != "Error") ? $this->cargue_diapositivas_categorias($thismod->instance, $courseid) : array() ; //Diapositivas de Modulo H5P con categorias

                        // Guardar informacion de actividad
                        $info_activity = array(
                            "name_module" => $thismod->name, //Nombre de Actividad
                            "id_module" => $thismod->id, //ID de Actividad
                            "pageurl" => $type_link, //URL de Actividad
                            "window_pageurl" => $window_pageurl, //Ventana donde se abre URL de Actividad
                            "type_module" => $thismod->modname, //Tipo de Actividad
                            "icon_module" => $thismod->get_icon_url()->out(false), //Icono de Actividad
                            "mark_completion" => $moduleshtml, //Boton Marcar Finalizacion de Actividad
                            "slides_categories" => $slides, //Lista diapositivas para categorizar
                            "type_activity_h5p" => true, //Si es actividad tipo H5P
                            "type_activity" => true, //Si es actividad o label
                        );

                        $info_activities[] = $info_activity;
                    }
                } else { // Rol Estudiante
                    //Seccion Welcome
                    if ( $num_section == 1 ) { 
                        if ($thismod->modname == "label") { //Obtenemos unicamente URL del video iFrame
                            //Obtenemos el codigo HTML del label
                            $modcontext = context_module::instance($thismod->id); //Context de actividad
                            $summary_label = ($DB->get_record( $thismod->modname, ['id' => $thismod->instance,'course'=>$courseid], 'intro'))->intro;
        
                            // Creamos un DOMDocument, y abrimos el HTML desde un string
                            $dochtml = new \DOMDocument(); $dochtml->loadHTML($summary_label);
                            // Obtenemos la URL del element iframe
                            $frame = $dochtml->getElementsByTagName('iframe');
                            // Guardar informacion de actividad
                            foreach($frame as $attr) { $info_url_frame_label = $attr->getAttribute('src'); }

                        } else { //Obtenemos los links en la senal de transito
                            if ( count($info_activities_welcome) < 4 ) { //Almacenamos las 4 primeras actividades
                                list($type_link, $window_pageurl) = $this->tipo_url_actividad($thismod->modname, $thismod->url, $thismod->id, $thismod->name, $modules_urls); // Obtenemos la URL para abrir contenido de actividad
                                // Guardar informacion de actividad
                                $info_activity_welcome = array(
                                    "name_module" => $thismod->name, //Nombre de Actividad
                                    "pageurl" => $type_link, //URL de Actividad
                                    "window_pageurl" => $window_pageurl, //Ventana donde se abre URL de Actividad
                                    "type_module" => $thismod->modname, //Tipo de Actividad
                                );
                                $info_activities_welcome[] = $info_activity_welcome;
                            }
                        }
                    } elseif ( $num_section >= 2 && $num_section <= 4 ){ //Seccion Unidades
                        if ( (count($info_activities_units) < 5) && ($thismod->modname == "hvp" || $thismod->modname == "h5pactivity")) { //Almacenamos las 5 primeras actividades H5P
                            //Determinamos si la actividad se encuentra habilitada
                            $class_state_available = ( $thismod->uservisible && $thismod->available) ? "unit" : "unit--inactive" ;                        
                            // Guardar informacion de actividad
                            $info_activity_unit = array(
                                "name_module" => $thismod->name, //Nombre de Actividad
                                "num_module" => ($num_section-1).".".(count($info_activities_units)+1), //Numero de Actividad
                                "type_module" => $thismod->modname, //Tipo de Actividad
                                "pageurl" => $thismod->url, //URL de la actividad H5P
                                "class_unit_state" => $class_state_available, //Estado habilitado o deshabilitado
                                "progress_data" => $this->progreso_categorias($thismod->instance, $courseid) //Data progreso por categorias de actividad                               
                            );
                            $info_activities_units[] = $info_activity_unit;
                        }
                    } else { //Seccion Recursos
                    }
                }
                                    
            }
        }

        if ( $user_role == "student" && $num_section <= 4 ) {
            $info_activities = array( 
                "url_frame" => $info_url_frame_label,
                "links_welcome" => $info_activities_welcome,
                "units" => $info_activities_units,
            );
        } 

        return $info_activities;
    }
    

    /**
      * Renderiza la casilla de Finalizacion de Actividad.
      * @param  object    $course          Info Curso.
      * @param  object    $completioninfo  Info finalizaciones del curso.
      * @param  cm_info   $mod             Data Actividad.
     */
    public function course_section_cm_list_item_english($course, $completioninfo, cm_info $mod, $displayoptions = array()) {
        global $PAGE;
        $html_output = '';

        $courserenderer = $PAGE->get_renderer('core', 'course');
        $html_output = $courserenderer->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
    
        return $html_output;
    }
    

    /**
      * Tipo de url para consultar contenido de actividad.
      * @param  object    $course          Info Curso.
      * @param  object    $completioninfo  Info finalizaciones del curso.
      * @param  cm_info   $mod             Data Actividad.
     */
    public function tipo_url_actividad($type_module, $url_module, $id_module, $name_module, $modules_urls ){

        $type_link = ""; $type_link_open = 'target="_self"';

        if ( $type_module === 'url' ){ //Configuracion URL para recursos tipo url
            //Recorremos todas los recursos tipo URL del curso
            foreach ($modules_urls as $url) {
                //Buscamos la informacion del recurso URL
                if ($url->coursemodule == $id_module && $url->visible == 1) { 
                    $type_link = $url->externalurl;
                    //Si la URL es tipo embedida se abre modal, de lo contrario es url directa
                    if ( !empty($url->externalurl) && $url->display == 6 ) { $type_link_open = 'onclick="'."window.open('".$url->externalurl."', '".$name_module."', 'width=620,height=450,scrollbars=yes,resizable=yes'); return false; ".'"';} 
                    else { $type_link_open = 'target="_blank"'; } 
                    break; 
                }
            }
        } else { //URL modal el resto de actividades
            $type_link = $url_module; $type_link_open = 'target="_blank"';
        }

        return array( $type_link, $type_link_open );
    }
    
    
    /**
     * Title text to char.
     * @param text      $title       Nombre del modulo
     */
    public function titulo_a_caracter( $title ) {

        $title_caracter = array(); $i = 1;
        //Separar caracteres de titulo
        $caracter = str_split($title, 1);  
        foreach ($caracter as $value) { 
            if ($value != " ") { $title_caracter[] = array("has_char_title" => true, "key_char_title" => $i, "char" => $value); $i++; } 
            else{ $title_caracter[] = array("has_char_title" => false); }
        }
        
        return $title_caracter;
    }
    
    
    /**
     * Refrescar Data de diapositivas por actividad H5P en BD.
      * @param  int     $id_course       ID course.
      * @param  int     $instance_hvp    ID H5P.
     */
    public function refresh_diapositivas_h5p( $instance_hvp, $id_course ) {
        global $DB; 
        $data_hvp = $data_hvp_english_category = $result_obj_slide_hvp = array();
        $result_action = "No change";

        //Consultamos en BD la actividad H5P
        $sql_data_hvp = "SELECT id, name, json_content, timecreated, timemodified FROM {hvp} WHERE course = :course AND id = :id_hvp";
        $data_hvp = $DB->get_record_sql($sql_data_hvp, array('course'=>$id_course, 'id_hvp'=>$instance_hvp));

        //Consultamos en BD hvp_english_category contiene data de los slide H5P
        $sql_data_hvp_english_category = "SELECT * FROM {hvp_english_category} WHERE course_id = :course AND hvp_id = :id_hvp ORDER BY id";
        $data_hvp_english_category = $DB->get_record_sql($sql_data_hvp_english_category, array('course'=>$id_course, 'id_hvp'=>$instance_hvp));

        if( !empty($data_hvp->timecreated) ){
            // Validamos si la Data slide H5P se debe crear o actualizar en hvp_english_category
            if ( empty($data_hvp_english_category->timecreated) || ($data_hvp->timemodified > $data_hvp_english_category->timecreated) ) { 
                //Extraemos Data slide H5P de Json_content y lo capturamos tipo object
                $result_obj_slide_hvp = $this->extraer_datos_json_hvp( $data_hvp->json_content, $id_course, $instance_hvp );
                if ( empty($data_hvp_english_category->timecreated) ) { // Si la Data slide del H5P NO existe en hvp_english_category
                    $action = "create";
                } else{ //Si Data slide de H5P fue modificado y deben ser Actualizado en hvp_english_category
                    $action = "update";
                } 
                $result_action = $this->update_diapositivas_categorias( $result_obj_slide_hvp, $instance_hvp, $id_course, $action );
            } 
        }

        return $result_action;
    }
    
    
    /**
     * Extraemos los datos del Json_Content en H5P.
      * @param  string    $json_content    Data H5P Json.
      * @param  int     $id_course       ID course.
      * @param  int     $instance_hvp    ID H5P.
    */
    public function extraer_datos_json_hvp( $json_content, $id_course, $instance_hvp ) {

        $data_slides_hvp_objs = array();
        $date = new \DateTime();
        $types_slide_hvp = array('H5P.MultiChoice', 'H5P.SingleChoiceSet', 'H5P.TrueFalse', 'H5P.Blanks', 'H5P.DragQuestion', 'H5P.MarkTheWords');
        $hvp_decode = json_decode( $json_content );
        
        //Recorremos el Json_Contet del H5P y buscamos la data de los slide del H5P
        foreach ($hvp_decode->presentation->slides as $index_slide_elements => $slide_elements) {
            //Determinamos si el slide es de actividades
            if(property_exists($slide_elements,'elements')){
                foreach ($slide_elements->elements as $value) {
                    $library_name = explode(" ", $value->action->library); //Extraemos la libreria de la actividad
                    //Determinamos que tipo de slide sera guardado
                    if( is_numeric( array_search($library_name[0],$types_slide_hvp) ) ){ 
                        //Armamos Object para pasar a BD
                        $data_slide_hvp_obj = new \stdClass();
                        $data_slide_hvp_obj->number_slide = ($index_slide_elements+1);
                        $data_slide_hvp_obj->course_id = $id_course;
                        $data_slide_hvp_obj->hvp_id = $instance_hvp;
                        $data_slide_hvp_obj->library = $library_name[0];
                        $data_slide_hvp_obj->category = "";
                        $data_slide_hvp_obj->timecreated = $date->format('U'); // Format 'U = Y-m-d H:i:s'
                        // Guardamos informacion
                        $data_slides_hvp_objs[] = $data_slide_hvp_obj; 
                    }
                }                
            }
        }

        return $data_slides_hvp_objs;
    }
    
    
    /**
      * Crea o Actualiza registro de diapositivas H5P en hvp_english_category.
      * @param  object  $obj_slide_hvp   Data H5P Object.
      * @param  int     $id_course       ID course.
      * @param  int     $instance_hvp    ID H5P.
      * @param  int     $action          String (Create o Update).
     */
    public function update_diapositivas_categorias( $obj_slide_hvp, $instance_hvp, $id_course, $action ) {
        global $DB; 
        $state = "No change";

        if ( $action == "create" && !empty($obj_slide_hvp) ) { //Creamos registros solo si Object trae datos
            //echo "Crear slides de HVP:".$instance_hvp." ";
            foreach ($obj_slide_hvp as $data_insert_slide) {
                $result_insert = $DB->insert_record('hvp_english_category', $data_insert_slide);
                $state = ($result_insert) ? "Success" : "Error";
            }
        } else { //Borramos los registros actuales
            //echo "Borrar slides de HVP:".$instance_hvp." ";
            $result_delete = $DB->delete_records('hvp_english_category', array('hvp_id'=>$instance_hvp, 'course_id' => $id_course));
            $state = ($result_delete) ? "Success" : "Error";
            //Si se borra exitosamente los registros, creamos registros solo si Object trae datos
            if ( !empty($obj_slide_hvp) && $result_delete == true ) { 
                //echo "Editar slides de HVP:".$instance_hvp." "; 
                foreach ($obj_slide_hvp as $data_insert_slide) {
                    $result_insert = $DB->insert_record('hvp_english_category', $data_insert_slide);
                    $state = ($result_insert) ? "Success" : "Error";
                }
            } 
        }
        
        return $state;
    }
    
    
    /**
     * Cargue de diapositivas por actividad H5P.
      * @param  int     $id_course       ID course.
      * @param  int     $instance_hvp    ID H5P.
     */
    public function cargue_diapositivas_categorias( $instance_hvp, $id_course ) {
        global $DB; 
        $data_hvp_adminformat_slides_category = $select_slides_hvp = $select_slide_hvp = array();

        //Consultamos en BD hvp_english_category contiene data de los slide H5P
        $sql_data_hvp_english_category = "SELECT * FROM {hvp_english_category} WHERE course_id = :course AND hvp_id = :id_hvp ORDER BY id";
        $data_hvp_adminformat_slides_category = $DB->get_records_sql($sql_data_hvp_english_category, array('course'=>$id_course, 'id_hvp'=>$instance_hvp));

        $options_category_hvp = array("Choose Category" => '',"Grammar" => 'Grammar',"Listening" => 'Listening',"Reading" => 'Reading',"Vocabulary" => 'Vocabulary',"Writing" => 'Writing');
        foreach ($data_hvp_adminformat_slides_category as $key => $value) {
            $html_options = '';
            foreach ($options_category_hvp as $key => $categories) { //Creamos options de lista desplegable
                $selected = ($categories == $value->category) ? 'selected' : '' ;
                $html_options.= '<option value="'.$categories.'" '.$selected.'>'.$key.'</option>';
            }
            $select_slide_hvp = array( //Armamos informacion para presentar Data slide en select admin
                "name_slide" => 'Slide #'.$value->number_slide, 
                "id_slide" => $value->id, 
                "id_hvp_module" => $value->hvp_id,
                "options" => $html_options,
            );
            $select_slides_hvp[] = $select_slide_hvp;
        }
        //echo "Select categories <br>";

        return  $select_slides_hvp;
    }
    
    
    /**
     * Calcular progreso de respuestas por categorias en actividades H5P.
      * @param  int     $id_course       ID course.
      * @param  int     $instance_hvp    ID H5P.
    */
    public function progreso_categorias($instance_hvp, $id_course) {
        global $USER, $DB; 
        $parent_results_hvp = array();
        $data_results_hvp = $data_hvp_category = array();
        $categories_progress = array( //Inicializamos avances por categorias
            array("name_category" => 'Grammar', "progress_category" => 0),
            array("name_category" => 'Listening', "progress_category" => 0),
            array("name_category" => 'Reading', "progress_category" => 0),
            array("name_category" => 'Vocabulary', "progress_category" => 0),
            array("name_category" => 'Writing', "progress_category" => 0)
        );

        //Se consulta el registro padre en BD de los resultados de actividad H5P
        $sql_parent_results_hvp = "SELECT id FROM {hvp_xapi_results} WHERE content_id = :id_hvp AND user_id = :user_id AND parent_id IS NULL";
        $parent_results_hvp = $DB->get_record_sql($sql_parent_results_hvp, array('id_hvp'=>$instance_hvp, 'user_id'=>$USER->id));

        if (!empty( $parent_results_hvp->id )) {
            //Se consulta los resultados de actividad H5P
            $sql_data_results_hvp = "SELECT id, content_id, user_id, parent_id, raw_score, max_score 
            FROM {hvp_xapi_results} WHERE content_id = :id_hvp AND user_id = :user_id AND parent_id = :parent_id";
            $data_results_hvp = $DB->get_records_sql($sql_data_results_hvp, array('id_hvp'=>$instance_hvp, 'user_id'=>$USER->id, 'parent_id'=>($parent_results_hvp->id) ));
        }

        if (!empty($data_results_hvp)) { //Si existen cargadas ya respuestas de la actividad H5P
            //Consultamos en BD hvp_english_category las categorias de los slide H5P
            $sql_data_hvp_category = "SELECT id, category FROM {hvp_english_category} WHERE course_id = :course AND hvp_id = :id_hvp ORDER BY id";
            $data_hvp_category = $DB->get_records_sql($sql_data_hvp_category, array('course'=>$id_course, 'id_hvp'=>$instance_hvp));
            //Inicializamos datos
            $data_results_hvp_value = array_values($data_results_hvp);
            $data_hvp_category_value = array_values($data_hvp_category);
            $categories_max_score = $categories_raw_score = array(0,0,0,0,0);

            if (!empty($data_hvp_category)){ //Si existen asignadas ya categorias para la actividad H5P
                //Si las dos tablas son iguales o si existen mas resultados que slides
                if( count($data_results_hvp_value) == count($data_hvp_category_value) || count($data_results_hvp_value) > count($data_hvp_category_value) ){
                    $max_count_data = count($data_hvp_category_value);
                } else { //Si existen mas slides que respuestas h5p
                    $max_count_data = count($data_results_hvp_value);
                }

                //Recorremos las dos tablas para categorizar resultados
                for ($i=0; $i < $max_count_data; $i++) { 
                    switch ($data_hvp_category_value[$i]->category) {
                        case 'Grammar':
                            $categories_raw_score[0] += $data_results_hvp_value[$i]->raw_score;
                            $categories_max_score[0] += $data_results_hvp_value[$i]->max_score;
                            break;
                        case 'Listening':
                            $categories_raw_score[1] += $data_results_hvp_value[$i]->raw_score;
                            $categories_max_score[1] += $data_results_hvp_value[$i]->max_score;
                            break;
                        case 'Reading':
                            $categories_raw_score[2] += $data_results_hvp_value[$i]->raw_score;
                            $categories_max_score[2] += $data_results_hvp_value[$i]->max_score;
                            break;
                        case 'Vocabulary':
                            $categories_raw_score[3] += $data_results_hvp_value[$i]->raw_score;
                            $categories_max_score[3] += $data_results_hvp_value[$i]->max_score;
                            break;
                        case 'Writing':
                            $categories_raw_score[4] += $data_results_hvp_value[$i]->raw_score;
                            $categories_max_score[4] += $data_results_hvp_value[$i]->max_score;
                            break;
                    }
                }
            }

            for ($i=0; $i < 5; $i++) { //Calculamos el progreso de avance
                $categories_progress[$i]["progress_category"] = ($categories_max_score[$i] != 0) ? round(($categories_raw_score[$i] / $categories_max_score[$i]) * 100) : 0;
            }
        }
        
        return $categories_progress;
    }

}