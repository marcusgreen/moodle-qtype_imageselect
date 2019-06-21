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
 * Question type class for the imageselect question type.
 *
 * @package    qtype
 * @subpackage imageselect
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /*https://docs.moodle.org/dev/Question_types#Question_type_and_question_definition_classes*/


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/imageselect/question.php');


/**
 * The imageselect question type.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect extends question_type {

      /* ties additional table fields to the database */
    public function extra_question_fields() {
       // return true;
       // return array('question_imageselect');
    }
    public function move_files($questionid, $oldcontextid, $newcontextid) {
   //     parent::move_files($questionid, $oldcontextid, $newcontextid);
    //    $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
    //    parent::delete_files($questionid, $contextid);
    //    $this->delete_files_in_hints($questionid, $contextid);
    }
     

    /**
     * @param stdClass $question
     * @param array $form
     * @return object
     */
    public function save_question($question, $form) {
        return parent::save_question($question, $form);
    }

    public function save_question_options($question) {
      //TODO
      /* save question specific data (to extra question fields) */
      global $DB;
      $options = $DB->get_record('question_imageselect', array('questionid' => $question->id));
      if(!$options){
        $options = new stdClass();
        $options->questionid = $question->id;
        $options->id = $DB->insert_record('question_imageselect', $options);
      }
      $options = $this->save_combined_feedback_helper($options, $question, $question->context, true);
      $DB->update_record('question_imageselect', $options);
        /* for fields apart from combined feedback ones */
        parent::save_question_options($question);
        $this->save_hints($question);
    }
    /**
     * Writes to the database, runs from question editing form
     *
     * @param stdClass $question
     * @param stdClass $options
     * @param context_course_object $context
     */

    public function update_imageselect($question) {
        global $DB;
        $options = $DB->get_record('question_imageselect', array('questionid' => $question->id));
        if (!$options) {
            $options = new stdClass();
            $options->question = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->id = $DB->insert_record('question_imageselect', $options);
        }

        $options = $this->save_combined_feedback_helper($options, $question, $question->contextid, true);
        $DB->update_record('question_imageselect', $options);
    }

 /* populates fields such as combined feedback */
   public function get_question_options($question) {
           parent::get_question_options($question);
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_question_answers($question, $questiondata);
        parent::initialise_combined_feedback($question, $questiondata);
    }
    
   public function initialise_question_answers(question_definition $question, $questiondata,$forceplaintextanswers = true){ 
     //TODO
    }
    
    public function import_from_xml($data, $question, qformat_xml $format, $extra = null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'question_imageselect') {
            return false;
        }
        $question = parent::import_from_xml($data, $question, $format, null);
        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        return $question;
    }
    public function export_to_xml($question, qformat_xml $format, $extra = null) {
        global $CFG;
        $pluginmanager = core_plugin_manager::instance();
        $gapfillinfo = $pluginmanager->get_plugin_info('question_imageselect');
        $output = parent::export_to_xml($question, $format);
        //TODO
        $output .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $output;
    }


    public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }

    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }
}
