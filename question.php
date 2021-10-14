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
 * imageselect question definition class.
 *
 * @package    qtype_imageselect
 * @copyright  2021 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This holds the definition of a particular question of this type.
 * If you load three questions from the question bank, then you will get three instances of
 * that class. This class is not just the question definition, it can also track the current 7
 * state of a question as a student attempts it through a question_attempt instance.
 */

/**
 * Represents a imageselect question.
 *
 * @package    qtype_imageselect
 * @copyright  2021 Marcus Green

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect_question extends question_graded_automatically_with_countback {

    /**
     * @var array place number => group number of the places in the question
     * text where choices can be put. Places are numbered from 1.
     */
    public $images = [];
    /**
     * get expected data types
     *
     * @return array
     */
    public function get_expected_data() {
        $data = [];
        foreach (array_keys($this->images) as $key) {
            $data['p' . $key] = PARAM_RAW_TRIMMED;
        }
        return $data;

    }
    /**
     * returns string of place key value prepended with img, i.e. img_0 or img_1 etc
     * @param int $place stem number
     * @return string the question-type variable name.
     */
    public function field($place) {
        return 'p' . $place;
    }

    public function start_attempt(question_attempt_step $step, $variant) {
        parent::start_attempt($step, $variant);

        // TODO
        /* there are 9 occurrances of this method defined in files called question.php a new install of Moodle
        so you are probably going to have to define it */
    }
    /**
     * At runtime, decide if a word has been clicked on to select
     *
     * @param number $place
     * @param array $response
     * @return boolean
     */
    public function is_image_selected($place, $response) {
        $responseplace = 'p' . $place;
        if (isset($response[$responseplace]) && (($response[$responseplace] == "on" ) || ($response[$responseplace] == "true" ) )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Value returned will be written to responsesummary field of
     * the question_attempts table
     *
     * @param array $response
     * @return string
     */
    public function summarise_response(array $response) {
        $summary = "";
        foreach ($response as $value) {
            $summary .= " " . $value . " ";
        }
        return $summary;
    }
    /**
     *
     * Have any images been selected?
     *
     * @param array $response
     * @return boolean
     */
    public function is_complete_response(array $response) {
        foreach ($response as $item) {
            if ($item == "on") {
                return true;
            }
        }
        return false;
    }


    public function get_validation_error(array $response) {
        // TODO.
        return '';
    }

    /**
     * if you are moving from viewing one question to another this will
     * discard the processing if the answer has not changed. If you don't
     * use this method it will constantantly generate new question steps and
     * the question will be repeatedly set to incomplete. This is a comparison of
     * the equality of two arrays.
     * Comment from base class:
     *
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */

    public function is_same_response(array $prevresponse, array $newresponse) {
        if ($prevresponse === $newresponse) {
            return true;
        } else {
            return false;
        }
    }

     /**
      * @return question_answer an answer that
      * contains the a response that would get full marks.
      * used in preview mode. If this doesn't return a
      * correct value the button labeled "Fill in correct response"
      * in the preview form will not work. This value gets written
      * into the rightanswer field of the question_attempts table
      * when a quiz containing this question starts.
      */
    public function get_correct_response() {
       // $correctplaces = $this->get_correct_places($this->questiontext, $this->delimitchars);

        $correctresponse['p1'] = 'on';
        $correctresponse['p2'] = 'on';
        return $correctresponse;
    }
    /**
     * Given a response, reset the parts that are wrong. Relevent in
     * interactive with multiple tries
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        foreach ($response as $key => $value) {
            /*clear the wrong response/s*/
        }
        return $response;
    }

    public function check_file_access($qa, $options, $component, $filearea,
            $args, $forcedownload) {
        if ('question' == $component && 'hint' == $filearea) {
            return $this->check_hint_file_access($qa, $options, $args);
        }else if ($filearea == 'selectableimage') {
            $validfilearea = true;
        } else {
            $validfilearea = false;
        }
        if ($component == 'qtype_imageselect' && $validfilearea) {

            return true;
            // else if ($filearea == 'dragimage') {
            // foreach ($question->choices as $group) {
            // foreach ($group as $drag) {
            // if ($drag->id == $itemid) {
            // return true;
            // }
            // }
            // }
            // return false;
        }
    }
    // lib/editor/atto/yui/build/moodle-editor_atto-editor/moodle-editor_atto-editor.j
    /**
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        // TODO.
        $fraction = 0;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

     /**
      * Work out a final grade for this attempt, taking into account all the
      * tries the student made. Used in interactive behaviour once all
      * hints have been used.     *
      * @param array $responses an array of arrays of the response for each try.
      * Each element of this array is a response array, as would be
      * passed to {@link grade_response()}. There may be between 1 and
      * $totaltries responses.
      * @param int $totaltries is the maximum number of tries allowed. Generally
      * not used in the implementation.
      * @return numeric the fraction that should be awarded for this
      * sequence of response.
      *
      */
    public function compute_final_grade($responses, $totaltries) {
        $var = 1;
        /*This method is typically where penalty is used.
        When questions are run using the 'Interactive with multiple
        tries or 'Adaptive mode' behaviour, so that the student will
        have several tries to get the question right, then this option
        controls how much they are penalised for each incorrect try.

        The penalty is a proportion of the total question grade, so if
        the question is worth three marks, and the penalty is 0.3333333,
        then the student will score 3 if they get the question right first
        time, 2 if they get it right second try, and 1 of they get it right
        on the third try.*/
        // TODO
        return 1;
    }
}
