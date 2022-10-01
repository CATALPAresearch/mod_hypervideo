<?php

require('../../config.php');
require_once('lib.php');


$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$g  = optional_param('g', 0, PARAM_INT);  // hypervideo ID

if (!empty($id)) {
    if (! $cm = get_coursemodule_from_id('hypervideo', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $hypervideo = $DB->get_record("hypervideo", array("id"=>$cm->instance))) {
        print_error('invalidid', 'hypervideo');
    }

} else if (!empty($g)) {
    if (! $hypervideo = $DB->get_record("hypervideo", array("id"=>$g))) {
        print_error('invalidid', 'hypervideo');
    }
    if (! $course = $DB->get_record("course", array("id"=>$hypervideo->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("hypervideo", $hypervideo->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $id = $cm->id;
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
$PAGE->set_title($course->shortname.': '.$hypervideo->name);
$PAGE->set_heading($hypervideo->name);
$PAGE->set_url('/mod/hypervideo/view.php', array('id' => $cm->id));

echo $OUTPUT->header();

//echo ''.$hypervideo->id;
echo <<<'EOT'
<div id="app"></div>
EOT;
$PAGE->requires->js_call_amd('mod_hypervideo/app-lazy', 'init', [
    'coursemoduleid' => $cm->id,
    'contextid' => 0,//$cm->context->id,
    'courseid' => $course->id,
    'hypervideoid' => $hypervideo->id,
    'fullPluginName' => 'mod_hyperaudio',
    'url' => $hypervideo->url,
    'title' => 'Video: '. $hypervideo->name
]);
echo $OUTPUT->footer($course);

//$json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
//var_dump(json_decode($json));
//var_dump(unserialize($json));


