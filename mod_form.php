<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_hypervideo_mod_form extends moodleform_mod {
    /**
     * Defines forms elements.
     */
    public function definition() {
        global $CFG, $PAGE;
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', 'Title', array(
                'size' => '64'
        ));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Adding a text field "url" for the video URL
        
        $mform->addElement('text', 'url', 'URL', array(
            //'size' => '2048'
        ));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('url', PARAM_TEXT);
        } else {
            $mform->setType('url', PARAM_CLEANHTML);
        }
        //$mform->addRule('url', null, 'required', null, 'client');
        //$mform->addRule('url', get_string('maximumchars', '', 2048), 'maxlength', 2048, 'client');

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard action buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Checks that accesstimestart is before accesstimestop
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
