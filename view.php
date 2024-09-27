<?php

require('../../config.php');
require_once('lib.php');



$course_id = optional_param('id', 0, PARAM_INT); // Course Module ID
$hypervideo_id  = optional_param('g', 0, PARAM_INT);  // hypervideo ID

if (!empty($course_id)) {
    if (! $cm = get_coursemodule_from_id('hypervideo', $course_id)) {
        print_error('invalidcoursemodule', 'hypervideo');
    }
    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf', 'hypervideo');
    }
    if (! $hypervideo = $DB->get_record("hypervideo", array("id" => $cm->instance))) {
        print_error('invalidid', 'hypervideo');
    }
} else if (!empty($hypervideo_id)) {
    if (! $hypervideo = $DB->get_record("hypervideo", array("id" => $hypervideo_id))) {
        print_error('invalidid', 'hypervideo');
    }
    if (! $course = $DB->get_record("course", array("id" => $hypervideo->course))) {
        print_error('invalidcourseid', 'hypervideo');
    }
    if (!$cm = get_coursemodule_from_instance("hypervideo", $hypervideo->id, $course->id)) {
        print_error('invalidcoursemodule', 'hypervideo');
    }
    $course_id = $cm->id;
} else {
    print_error('invalidid', 'hypervideo');
}

require_login($course, true, $cm);

if ($cm) {
    $PAGE->set_cm($cm, $course);
} else {
    $PAGE->set_course($course);
}
$PAGE->set_pagelayout('base');
$PAGE->set_title($course->shortname . ': ' . $hypervideo->name);
$PAGE->set_heading($hypervideo->name);
$PAGE->set_url('/mod/hypervideo/view.php', array('id' => $cm->id));

echo $OUTPUT->header();
echo html_writer::div('', 'app', ['id' => 'app']);

$PAGE->requires->js_call_amd('mod_hypervideo/app-lazy', 'init', [
    'coursemoduleid' => $cm->id,
    'contextid' => $cm->context->id,
    'courseid' => $course->id,
    'hypervideoid' => $hypervideo->id,
    'fullPluginName' => 'mod_hypervideo',
    'url' => $hypervideo->url,
    'title' => 'Video: ' . $hypervideo->name
]);

echo $OUTPUT->footer($course);

//$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
//var_dump(json_decode($json));
//var_dump(unserialize($json));
