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
 * @copyright  Marcus Green 2021

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
// require_once($CFG->dirroot . '/question/type/ddimageortext/rendererbase.php');

/*
<li class="h5p-multi-media-choice-list-item"
    role="checkbox" aria-checked="true" aria-disabled="false"
    aria-label="something" title="or other"
    tabindex="0"
    style="position: absolute; left: 313.5px; top: 0px; width: 293.5px;">
<div class="h5p-multi-media-choice-option h5p-multi-media-choice-enabled h5p-multi-media-choice-selected">
    <div class="h5p-multi-media-choice-media-wrapper">
    <img src="http://localhost/wsel/pluginfile.php/32/mod_hvp/content/1/images/file-6154ab323b0f3.png"
    class="h5p-multi-media-choice-media">
</div>

</div>
</li>

*/

/**
 * Generates the output for drag-and-drop markers questions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_imageselect_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa,
        question_display_options $options) {

        $response = $qa->get_last_qt_data();

        $this->page->requires->js_call_amd('qtype_imageselect/image_select', 'init');

        $question = $qa->get_question();

        $output = '';
        $output .= $question->format_questiontext($qa);
        foreach ($question->images as $image) {
            $isselected = $question->is_image_selected($image->no, $response);
            $output .= $this->embedded_element($qa, $image, $options, $isselected);

        }
        $output = html_writer::tag('div', $output, ['class' => 'qtext qtype_imageselect']);
        // $output .= '<script src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>';
        // $output .= '<script>
        //                 // debugger;
        //                 // console.log("hello");
        //                 // const observer = lozad();
        //                 // observer.observe();
        //              </script>';
        return $output;
    }
    public function embedded_element(question_attempt $qa, $image,  question_display_options $options, $isselected) {
        $image->item = $this->get_input_id($qa, $image->no);
        $image->classes[] = "selectableimage";
        $class = $isselected ? ' selected' : '';

        $imageitem = '<div role="checkbox" name="selectableimage_p'.$image->no.'">';
        $fileurl = self::get_url_for_image($qa, 'selectableimage', $image->id);
       // $dimensions = ' width="100px" height="100px"';
        $dimensions = "";
        $title = $isselected ? 'Selected' : '';

        $imageitem .= '<img  title = '.$title .' class="selectableimage'. $class .'"  name="'.$image->item.'" id="selectableimage-'.$image->item.'" src="' . $fileurl .'" '.$dimensions. ' loading="lazy" >';

        $properties = [
            'type' => 'checkbox',
            'name' => $image->item,
            'id' => 'imagecheck_p'.$image->no,
            'class' => 'selcheck hidden '. $isselected ? 'selected hidden' : '',
            'role' => 'checkbox'
        ];


        if ($isselected) {
             $properties['checked'] = 'true';
        }

        $checkbox = html_writer::empty_tag('input', $properties);

        $properties = [
            'type' => 'hidden',
            'name' => $image->item,
            'role' => 'checkbox',
            'value' => 'off'
        ];
        $hidden = html_writer::empty_tag('input', $properties);
        $imageitem .= $hidden;

        $imageitem .= $checkbox;
        $imageitem .= '</div>';
        return $imageitem;
    }

    /**
     * Creates the name of the field/checkbox
     * that identifies the selectable item
     *
     * @param question_attempt $qa
     * @param int $place
     * @return string
     */
    protected function get_input_id(question_attempt $qa, $place) {
        /* prefix is the number of this question attempt */
        $qprefix = $qa->get_qt_field_name('');
        $inputname = $qprefix . 'p' . ($place);
        return $inputname;
    }
    public function specific_feedback(question_attempt $qa) {
        // TODO.
        return '';
    }

    public function correct_response(question_attempt $qa) {
        // TODO.
        return '';
    }
    /**
     * Returns the URL for an image
     *
     * @param object $qa Question attempt object
     * @param string $filearea File area descriptor
     * @param int $itemid Item id to get
     * @return string Output url, or null if not found
     */
    protected static function get_url_for_image(question_attempt $qa, $filearea, $itemid = 0) {
        $question = $qa->get_question();
        $qubaid = $qa->get_usage_id();
        $slot = $qa->get_slot();
        $fs = get_file_storage();
        if ($filearea == 'bgimage') {
            $itemid = $question->id;
        }
        $componentname = $question->qtype->plugin_name();
        $draftfiles = $fs->get_area_files($question->contextid, $componentname,
                                                                        $filearea, $itemid, 'id');
        if ($draftfiles) {
            foreach ($draftfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $url = moodle_url::make_pluginfile_url($question->contextid, $componentname,
                                            $filearea, "$qubaid/$slot/{$itemid}", '/',
                                            $file->get_filename());
                return $url->out();
            }
        }
        return null;
    }

}