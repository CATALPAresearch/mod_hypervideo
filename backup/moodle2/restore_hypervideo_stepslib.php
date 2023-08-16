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
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_hypervideo_activity_task
 */

/**
 * Structure step to restore one hypervideo activity
 */
class restore_hypervideo_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('hypervideo', '/activity/hypervideo');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_hypervideo($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the hypervideo record
        $newitemid = $DB->insert_record('hypervideo', $data);
        //$this->set_mapping('hypervideo', $oldid, $newitemid, true);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
    protected function process_hypervideo_log($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        // $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the hypervideo record
        $newitemid = $DB->insert_record('hypervideo_log', $data);
        $this->set_mapping('hypervideo_log', $oldid, $newitemid, true);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
    

    protected function after_execute() {
        // Add hypervideo related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_hypervideo', 'intro', null);
    }
}
