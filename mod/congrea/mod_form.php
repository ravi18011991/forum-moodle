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
 * The main congrea configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_congrea
 * @copyright  2014 Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/congrea/locallib.php');

/**
 * Module instance settings form
 *
 * @copyright  2014 Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_congrea_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('congreaname', 'congrea'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'congreaname', 'congrea');
        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->version > 2014111008) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor(); // Moodle.2.8.9 or earlier.
        }
        // Adding the rest of congrea settings
        // Adding list of teachers.
        $teacheroptions = congrea_course_teacher_list();
        if (empty($teacheroptions)) {
            $teacheroptions = "";
        } else {
            $teacheroptions[0] = 'Select';
        }
        $mform->addElement('select', 'moderatorid', get_string('selectteacher', 'congrea'), $teacheroptions);
        $mform->addHelpButton('moderatorid', 'selectteacher', 'congrea');
        // Audio settings.
        $options = array(1 => get_string('enable', 'congrea'),
            0 => get_string('disable', 'congrea'));
        $mform->addElement('select', 'audio', get_string('audio', 'congrea'), $options);
        $mform->addHelpButton('audio', 'audio', 'congrea');
        // Video Settings.
        $options = array(1 => get_string('enable', 'congrea'),
            0 => get_string('disable', 'congrea'));
        $mform->addElement('select', 'video', get_string('video', 'congrea'), $options);
        $mform->addHelpButton('video', 'video', 'congrea');
        // Schedule fo session.
        $mform->addElement('header', 'general', get_string('sessionsschedule', 'congrea'));
        $mform->addElement('date_time_selector', 'opentime', get_string('opentime', 'congrea'));
        $mform->addRule('opentime', null, 'required', null, 'client');
        $mform->addElement('date_time_selector', 'closetime', get_string('closetime', 'congrea'));
        $mform->addRule('closetime', null, 'required', null, 'client');
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Validate this form.
     *
     * @param array $data submitted data
     * @param array $files not used
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Check open and close times are consistent.
        if ($data['opentime'] != 0 && $data['closetime'] != 0 &&
                $data['closetime'] < $data['opentime']) {
            $errors['closetime'] = get_string('closebeforeopen', 'congrea');
        }
        if ($data['opentime'] != 0 && $data['closetime'] == 0) {
            $errors['closetime'] = get_string('closenotset', 'congrea');
        }
        if ($data['opentime'] != 0 && $data['closetime'] != 0 &&
                $data['closetime'] == $data['opentime']) {
            $errors['closetime'] = get_string('closesameopen', 'congrea');
        }
        return $errors;
    }

}
