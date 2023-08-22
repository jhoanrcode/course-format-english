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
 * Plugin upgrade manager.
 * @package    formato_uniminuto_english
 * @copyright  2023 onwards Jhoan Avila 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_format_uniminuto_english_upgrade($oldversion = 0){
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2023070602) { 
        // Create the section break table.
        $table = new xmldb_table('hvp_english_category');

        // Add table fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, true);
        $table->add_field('number_slide', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('hvp_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('library', XMLDB_TYPE_CHAR, '255', null, null, null, '');
        $table->add_field('category', XMLDB_TYPE_CHAR, '50', null, null, null, '');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, 0);

        // Add keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, [ 'id' ]);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2023070602, 'format', 'uniminuto_english');
    }

    return true;
}



