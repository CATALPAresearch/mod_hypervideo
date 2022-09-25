<?php
    
    defined('MOODLE_INTERNAL') || die();
    
    $settings->add(
        new admin_setting_configtext(
            'hypervideo/someconfig', 
            get_string('confentry', 'hypervideo'),
            get_string('confdescription', 'hypervideo'), 
            get_string('confvalue', 'hypervideo'), 
            PARAM_RAW, 
            40
        )
    );