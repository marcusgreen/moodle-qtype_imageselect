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
 * Contains the helper class for the select missing words question type tests.
 *
 * @package    qtype
 * @copyright  Year Yourname
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
/**
 * utilities used by the other test classes
 *
 * @package    qtype_imageselect
 * @copyright  2021 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect_helper extends question_test_helper {

    /**
     *  must be implemented or class made abstract
     *
     * @return string
     */
    public function get_test_questions() {
        return [];
    }


    /**
     * Slight improvements over original make_question class
     *
     * @param string $questiontext
     * @param array $poptions
     * @return qtype_gapfill
     */
    public static function make_question($questiontext = "", array $poptions =[]) {

        $options = [

        ];

        $type = 'imageselect';
        question_bank::load_question_definition_classes($type);
        $question = new qtype_imageselect_question();
        $question->questiontext = $questiontext;
        test_question_maker::initialise_a_question($question);

        $question->qtype = question_bank::get_qtype($type);
        return $question;

    }

}
