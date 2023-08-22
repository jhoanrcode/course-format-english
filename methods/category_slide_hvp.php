<?php
/**
 * Formato de Cursos
 *
 * @package    formato_uniminuto_english
 * @copyright  2023 onwards Jhoan Avila 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Clase Categoria de Diapositivas en actividades H5P **/
require_once ('../../../../config.php'); //require_once (dirname(__FILE__,5)."\config.php");
class category_slide_hvp {

    public static function run() {

        $obj = new self();
        $result = $obj->update_category_slide_hvp($_POST);
        echo json_encode($result);
    }

    /**
      * Actualizacion de categoria en slide de H5P.
      * @param    array    $data   Datos de origen Ajax.
     */
    public function update_category_slide_hvp($data) {
        global $DB;
        $data_category_slide_hvp = array();

        $sql_data_category_slide_hvp = "SELECT * FROM {hvp_english_category} WHERE id = :id_category_slide AND hvp_id = :id_hvp";
        $data_category_slide_hvp = $DB->get_record_sql($sql_data_category_slide_hvp, array('id_category_slide'=>$data["id_slide_category"], 'id_hvp'=>$data["id_hvp"]));

        //Si el registro existe se actualiza
        if ( !empty($data_category_slide_hvp) ) { 
            $record = new stdClass();
            $record->id = $data["id_slide_category"];
            $record->category = $data["category"];
            $DB->update_record('hvp_english_category', $record);
            return array("state"=>true);
        } else {
            return array("state"=>false);
        }
    }

} category_slide_hvp::run();