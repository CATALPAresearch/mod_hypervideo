<?php

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_hypervideo_getInfo' => array(
        'classname'   => 'mod_hypervideo_external',
        'methodname'  => 'getInfo',
        'classpath'   => 'mod/hypervideo/db/external.php',
        'description' => 'Get all users to display.',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true
    ),
    'mod_hypervideo_log' => array(
        'classname'   => 'mod_hypervideo_external',
        'methodname'  => 'log',
        'classpath'   => 'mod/hypervideo/db/external.php',
        'description' => 'Get all users to display.',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true
    ),
    'mod_hypervideo_videoprogress' => array(
        'classname'   => 'mod_hypervideo_external',
        'methodname'  => 'videoprogress',
        'classpath'   => 'mod/hypervideo/db/external.php',
        'description' => 'Get all users to display.',
        'type'        => 'read',
        'ajax'        => true,
        'loginrequired' => true
    ),
    'mod_hypervideo_survey' => array(
        'classname'   => 'mod_hypervideo_external',
        'methodname'  => 'survey',
        'classpath'   => 'mod/hypervideo/db/external.php',
        'description' => 'Get all users to display.',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => true
    )
);
