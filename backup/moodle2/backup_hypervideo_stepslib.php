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
 * @package   mod_hypervideo
 * @category  backup
 * @copyright 2023 Regina Kasakowskij {regina.kasakowskij@fernuni-hagen.de}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that will be used by the backup_hypervideo_activity_task
 */

/**
 * Define the complete hypervideo structure for backup, with file and id annotations
 */
class backup_hypervideo_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $hypervideo = new backup_nested_element('hypervideo', array('id'), array(
            'course', 'name', 'url', 'intro', 'introformat',
            'timecreated', 'timemodified'));

        // Define sources
        $hypervideo->set_source_table('hypervideo', array('id' => backup::VAR_ACTIVITYID));

        
        $hypervideo_log = new backup_nested_element('hypervideo_log', array('id'), array(
            'hypervideo', 'userid', 'course', 'url', 'context',
            'position', 'actions', 'values', 'duration',
            'timemodified'));
        $hypervideo_log->set_source_table('hypervideo', array('id' => backup::VAR_ACTIVITYID));

        // Define file annotations
      //  $hypervideo->annotate_files('mod_hypervideo', 'intro', null, null);

        $hypervideo->add_child($hypervideo_log);
       
        return $this->prepare_activity_structure($hypervideo);
    }
}
