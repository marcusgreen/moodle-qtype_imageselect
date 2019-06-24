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
 * Defines the editing form for the imageselect question type.
 *
 * @package    qtype
 * @subpackage imageselect
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * imageselect question editing form definition.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
        //Add fields specific to this question type
        //remove any that come with the parent class you don't want
        $mform->removeelement('questiontext');
        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), ['rows' => 5],
        $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);
        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_gapfill');
        $item = $this->selectable_image($mform);
        $mform->removeelement('defaultmark');
        $mform->removeelement('generalfeedback');
        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question')
        , array('rows' => 10), $this->editoroptions);
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');

        // To add combined feedback (correct, partial and incorrect).
        $this->add_combined_feedback_fields(true);
        // Adds hinting features.
        $this->add_interactive_settings(true, true);
    }

    protected function selectable_image($mform) {
        $selectableimage = [];

        $selectableimage[] = $mform->createElement('filepicker', 'dragitem', '', null,
                                    self::file_picker_options());
        $selectableimage[] = $mform->createElement('text', 'imagelabel',
                                                get_string('imagelabel', 'qtype_imageselect'),
                                                ['size' => 30, 'class' => 'tweakcss draglabel']);
        $mform->setType('imagelabel', PARAM_RAW); // These are validated manually.
// Title of group should probably be blank. Put in a header instead.
        $mform->addGroup($selectableimage,'selectableimage','Title of group', false);
        return $selectableimage;
    }
      /**
     * Options shared by all file pickers in the form.
     *
     * @return array Array of filepicker options.
     */
    public static function file_picker_options() {
        $filepickeroptions = array();
        $filepickeroptions['accepted_types'] = array('web_image');
        $filepickeroptions['maxbytes'] = 0;
        $filepickeroptions['maxfiles'] = 1;
        $filepickeroptions['subdirs'] = 0;
        return $filepickeroptions;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function qtype() {
        return 'imageselect';
    }
}
