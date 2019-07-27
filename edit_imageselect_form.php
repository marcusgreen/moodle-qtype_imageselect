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
    /**
     * The number of drop zones that get added at a time.
     */
    const ADD_NUM_ITEMS = 1;

    /**
     * The default starting number of drop zones.
     */
    const START_NUM_ITEMS = 4;

    protected function definition_inner($mform) {
        //Add fields specific to this question type
        //remove any that come with the parent class you don't want
        $mform->removeelement('questiontext');
        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), ['rows' => 5],
        $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);
        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_gapfill');
        //based on qtype_ddtoimage_edit_form_base
        list($itemrepeatsatstart, $imagerepeats) = $this->get_image_item_repeats();
        $this->definition_selectable_images($mform, $itemrepeatsatstart);

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

    protected function definition_selectable_images($mform, $itemrepeatsatstart) {
        $this->repeat_elements($this->selectable_image($mform), $itemrepeatsatstart,
                $this->selectable_image_repeated_options(),
                'noitems', 'additems', self::ADD_NUM_ITEMS,
                get_string('addmoreimages', 'qtype_imageselect'), true);
    }

    public function data_preprocessing($question) {
        $imageids = []; // Drag no -> dragid.
        if (!empty($question->options)) {
            $question->images = [];
            // foreach ($question->options->images as $image) {
            //     $imageindex = $image->no - 1;
            //     $question->imagelabel[$imageindex] = $image->label;
            //     $imageids[$imageindex] = $image->id;

            // }

    }
}

    protected function selectable_image_repeated_options() {
        $repeatedoptions = [];
        $repeatedoptions['imagegroup']['default'] = '1';
        return $repeatedoptions;
    }

    protected function selectable_image($mform) {
        //see draggable_item l 138
    // https://github.com/moodle/moodle/blob/8d9614b3416634d3ca9168ea86a624e75729e34d/question/type/ddimageortext/edit_ddimageortext_form.php#L138
        $selectableimageitem = [];

        // $selectableimageitem[] = $mform->createElement('group', 'images',
        // get_string('selectableitemheader', 'qtype_imageselect', '{no}'));

        $selectableimageitem[] = $mform->createElement('filepicker', 'imageitem', '', null,
                                    self::file_picker_options());
        $selectableimageitem[] = $mform->createElement('text', 'imagelabel',
                                                get_string('imagelabel', 'qtype_imageselect'),
                                                ['size' => 30, 'class' => 'tweakcss draglabel']);
        $mform->setType('imagelabel', PARAM_RAW); // These are validated manually.
        return $selectableimageitem;
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

        /**
     * Returns an array of starting number of repeats, and the total number of repeats.
     *
     * @return array
     */
    protected function get_image_item_repeats() {
        $countimages = 0;
        if (isset($this->question->id)) {
            // foreach ($this->question->options->drags as $drag) {
            //     $countimages = max($countimages, $drag->no);
            // }
        }
        if (!$countimages) {
            $countimages = self::START_NUM_ITEMS;
        }
        $itemrepeatsatstart = $countimages;
        $imagerepeats = optional_param('noitems', $itemrepeatsatstart, PARAM_INT);
        $addfields = optional_param('additems', false, PARAM_BOOL);
        if ($addfields) {
            $imagerepeats += self::ADD_NUM_ITEMS;
        }
        return array($itemrepeatsatstart, $imagerepeats);
    }


    public function qtype() {
        return 'imageselect';
    }
}
