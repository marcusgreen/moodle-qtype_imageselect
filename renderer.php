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
 * imageselect question renderer class.
 *
 * @package    qtype
 * @subpackage imageselect
 * @copyright  Marcus Green 2019

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for imageselect questions.
 *
 * @copyright  Marcus Green 2019
*
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/rendererbase.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/rendererbase.php');


/**
 * Generates the output for drag-and-drop markers questions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect_renderer extends qtype_ddtoimage_renderer_base {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

                global $PAGE;

		$output = '';
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        $questiontext = $question->format_questiontext($qa);
        $i=0;
        $context=context_system::instance();
        $fs = get_file_storage();
      //  $filerecord = $fs->get_area_files($context->id,'qtype_imageselect','selectableimage',0, false, null, false);
        $context=context_system::instance();
		$fileurl = '';
        foreach ($question->images as $image ) {
            $fileurl = self::get_url_for_image($qa, 'selectableimage', $image->id);
			$output .= '<img src=' . $fileurl . '>';
        }

    
       
      //  $output .= html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        /* Some code to restore the state of the question as you move back and forth
        from one question to another in a quiz and some code to disable the input fields
        once a quesiton is submitted/marked */

        /* if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }*/
        return $output;
    }

    public function specific_feedback(question_attempt $qa) {
        // TODO.
        return '';
    }

    public function correct_response(question_attempt $qa) {
        // TODO.
        return '';
    }
}
