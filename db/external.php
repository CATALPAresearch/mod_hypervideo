<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . "/externallib.php");

class mod_hypervideo_external extends external_api {

    public static function getInfo_parameters(){
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'id of cwr', VALUE_REQUIRED)
            )
        ); 
    }

    public static function getInfo($cmid){        
        try{
            global $DB, $USER, $PAGE;                  
            $cm = get_coursemodule_from_id('hypervideo', $cmid, 0, false, MUST_EXIST);           
            $data = $DB->get_record('hypervideo', array('id' => $cm->instance), '*', MUST_EXIST);
            $course = $data->course;
            $permission = new mod_hypervideo\permission\course($USER->id, $course);
            if(!$permission->isAnyKindOfModerator()){                
                throw new Exception('No permission');
            }   
            try{               
                $PAGE->set_context(context_system::instance());    
                $data->intro = format_module_intro('hypervideo', $data, $cm->id);     
            } catch(Exception $e){}    
            return array(
                'success' => true,
                'data' => json_encode($data)
            );
        } catch(Exception $ex){
            return array(
                'success' => false,
                'data' => json_encode($ex->getMessage())
            ); 
        }        
    }

    public static function getInfo_returns(){
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Success Variable'),
                'data' => new external_value(PARAM_RAW, 'The output')
            )
        );
    }

    public static function getInfo_is_allowed_from_ajax(){
        return true;
    }   




    public static function log_parameters() {
        return new external_function_parameters(
            array(
                'data' =>
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
                            'hypervideoid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
                            'action' => new external_value(PARAM_TEXT, '..action', VALUE_OPTIONAL),
                            'utc' => new external_value(PARAM_INT, '...utc time', VALUE_OPTIONAL),
                            'entry' => new external_value(PARAM_RAW, 'log data', VALUE_OPTIONAL)
                        )
                    )
            )
        );
    }
    public static function log($data) {
        global $DB, $USER; 
        $r = new stdClass();
        $r->component = 'mod_hypervideo';
        $r->eventname = '\mod_hypervideo\event\course_module_' . $data['action'];
        $r->action = $data['action'];
        $r->target = 'course_module';
        $r->objecttable = 'hypervideo';
        $r->objectid = 0;
        $r->crud = 'r';
        $r->edulevel = 2;
        $r->contextid = 120;
        $r->contextlevel = 70;
        $r->contextinstanceid = 86;
        $r->userid = $USER->id;
        $r->courseid = (int) $data['courseid'];
        $r->anonymous = 0;
        $r->other = $data['entry']; // unserialize(json_encode(  // unserialize(json_encode(json_decode($data['entry'])))
        $r->timecreated = $data['utc'];
        $r->origin = 'web';
        $r->ip = $_SERVER['REMOTE_ADDR'];

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->insert_record("logstore_standard_log", (array) $r);
            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            error_log("writing video log to logstore failed");
        }
        
        $d = json_decode($data['entry']);
        $res = -1;
        $DB->set_debug(true);
        
        try {
            $transaction2 = $DB->start_delegated_transaction();
            $res = $DB->insert_record('hypervideo_log', [
                'hypervideo'  => 2,//(int) $data['hypervideoid'],
                'userid' => 2,//(int) $USER->id,
                'course' => 2,//(int) $data['courseid'],
                'url' => 'x',//(String) $d->location->url,
                'context' => 'x',//(String) $d->value->context,
                'position' => 'x',//(String) round($d->value->currenttime, 3),
                'actions' => 'x',//(String) $d->action,
                'value'  => 'x',//(String) $d->value->values,
                'timemodified' => 16650,//$d->utc,
                'duration'  => 2,//(int) round($d->value->duration, 3)
            ]);
            $transaction2->allow_commit();
        } catch (Exception $e) {
            $res = $e;
            $transaction2->rollback($e);
            //error_log("writing video log to hypervideo log table failed");
        }
        
        $DB->set_debug(false);
        return array(
            'success' => true,
            'response' => json_encode($res)
        );
    }
    public static function log_returns() {
        //return null;
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, ''),
                'response' => new external_value(PARAM_RAW, '')
            )
        );
    }




    public static function videoprogress_parameters() {
        return new external_function_parameters(
            array(
                'data' =>
                    new external_single_structure(
                        array(
                            'course' => new external_value(PARAM_INT, 'course id', VALUE_OPTIONAL),
                            'hypervideo' => new external_value(PARAM_INT, 'video id', VALUE_OPTIONAL)
                    )
                )
            )
        );
    }
    public static function videoprogress($data)
    {
        global $DB, $USER;
        $videoprogress = $DB->get_record_sql("SELECT count(actions) as videoprogress
            FROM {hypervideo_log}
            WHERE 
            actions = 'playback' AND
            course = :course AND
            userid = :userid AND
            hypervideo = :hypervideo
            LIMIT 1
            ;", [
            'course' => (int)$data['course'],
            'userid' => (int)$USER->id,
            'hypervideo' => (int)$data['hypervideo']
        ]);

        $survey = $DB->get_record_sql("SELECT *
            FROM {logstore_standard_log}
            WHERE 
            component = 'mod_hypervideo' AND
            action = 'survey' AND
            courseid = :course AND
            userid = :userid AND
            contextinstanceid = :hypervideo
            LIMIT 1
            ;", [
            'course' => (int)$data['course'],
            'userid' => (int)$USER->id,
            'hypervideo' => (int)$data['hypervideo']
        ]);
        
        return array(
            'success' => true,
            'data' => json_encode([ 
                'videoprogress' => $videoprogress->videoprogress,
                'survey' => $survey
            ])
        );
    }
    public static function videoprogress_returns()
    {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, ''),
                'data' => new external_value(PARAM_RAW, '')
            )
        );
    }
    public static function videoprogress_is_allowed_from_ajax()
    {
        return true;
    }



   


    public static function survey_parameters() {
        return new external_function_parameters(
            array(
                'data' =>
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
                            'hypervideoid' => new external_value(PARAM_INT, 'id of course', VALUE_OPTIONAL),
                            'url' => new external_value(PARAM_TEXT, '..action', VALUE_OPTIONAL),
                            'utc' => new external_value(PARAM_INT, '...utc time', VALUE_OPTIONAL),
                            'q1' => new external_value(PARAM_RAW, 'question response', VALUE_OPTIONAL),
                            'q2' => new external_value(PARAM_RAW, 'question response', VALUE_OPTIONAL),
                            'q3' => new external_value(PARAM_RAW, 'question response', VALUE_OPTIONAL)
                        )
                    )
            )
        );
    }
    public static function survey($data) {
        global $DB, $USER;

        $responses = array(
            'url'=> $data['url'], 
            'q1'=> $data['q1'],
            'q2'=> $data['q2'],
            'q3'=> $data['q3']
        );

        $r = new stdClass();
        $r->component = 'mod_hypervideo';
        $r->eventname = '\mod_hypervideo\event\video_progress_survey';
        $r->action = 'survey';
        $r->target = 'course_module';
        $r->objecttable = 'hypervideo';
        $r->objectid = 0;
        $r->crud = 'r';
        $r->edulevel = 2;
        $r->contextid = 120;
        $r->contextlevel = 70;
        $r->contextinstanceid = (int) $data['hypervideoid'];
        $r->userid = $USER->id;
        $r->courseid = (int) $data['courseid'];
        $r->anonymous = 0;
        $r->other = json_encode($responses); // unserialize(json_encode(  // unserialize(json_encode(json_decode($data['entry'])))
        $r->timecreated = $data['utc'];
        $r->origin = 'web';
        $r->ip = $_SERVER['REMOTE_ADDR'];

        try {
            $transaction = $DB->start_delegated_transaction();
            $res = $DB->insert_record("logstore_standard_log", (array) $r);
            $transaction->allow_commit();
        } catch (Exception $e) {
            $transaction->rollback($e);
            error_log("writing video log to logstore failed");
        }
        
        return array(
            'success' => true,
            'data' => json_encode($res)
        );
    }    
    public static function survey_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, ''),
                'data' => new external_value(PARAM_RAW, 'Server respons to the incomming log'))
        );
    }
   
}
