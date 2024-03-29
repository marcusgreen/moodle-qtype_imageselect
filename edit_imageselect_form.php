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
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
MoodleQuickForm::registerElementType('singleimage', __DIR__."/singleimage.php", 'MoodleQuickForm_singleimage');

/**
 * imageselect question editing form definition.
 *
 * @copyright  Marcus Green 2021
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
    const START_NUM_ITEMS = 2;

    public function data_preprocessing($question) {
        $imageids = []; // Drag no -> dragid.
        // Initialise file picker for images.

        if (!empty($question->options)) {
            $question->images = [];
            foreach ($question->options->images as $image) {
                $imageindex = $image->no - 1;
                $question->images[$imageindex] = [];
                $imageids[$imageindex] = $image->id;
            }
            // Initialise singleimage element for images.
            list(, $imagerepeats) = $this->get_image_item_repeats();
            $draftitemids = optional_param_array('imageitem', [], PARAM_INT);
            for ($imageindex = 0; $imageindex < $imagerepeats; ++$imageindex) {
                $draftitemid = $draftitemids[$imageindex] ?? 0;
                $itemid = $imageids[$imageindex] ?? null;

                file_prepare_draft_area($draftitemid, $this->context->id, 'qtype_imageselect',
                                        'selectableimage', $itemid, self::file_picker_options());

                $question->imageitem[$imageindex] = $draftitemid;
            }
            foreach ($question->options->images as $image) {
                // this should populate the file pickers with existing files
                $imageindex = $image->no - 1;
                if (!isset($question->imageitem[$imageindex])) {
                    /** used if there will be a lable/text entry */
                    $fileexists = false;
                } else {
                    $fileexists = self::file_uploaded($question->imageitem[$imageindex]);
                }
                $question->imagelabel[$imageindex] = $image->label;
                $question->fraction[$imageindex] = $image->fraction;

            }
        }
        /* populates the hints and adds clearincorrect and and shownumcorrect (true,true) */
        $question = $this->data_preprocessing_hints($question, true, true);
        return $question;
    }


    /**
     * Checks to see if a file has been uploaded.
     *
     * @param string $draftitemid The draft id
     *
     * @return bool true if files exist, false if not
     */
    public static function file_uploaded($draftitemid) {
        $draftareafiles = file_get_drafarea_files($draftitemid);
        do {
            $draftareafile = array_shift($draftareafiles->list);
        } while (null !== $draftareafile && '.' == $draftareafile->filename);
        if (null === $draftareafile) {
            return false;
        }

        return true;
    }

    /**
     * Options shared by all file pickers in the form.
     *
     * @return array array of filepicker options
     */
    public static function file_picker_options() {
        $filepickeroptions = [];
        $filepickeroptions['accepted_types'] = ['web_image'];
        $filepickeroptions['maxbytes'] = 0;
        $filepickeroptions['maxfiles'] = 1;
        $filepickeroptions['subdirs'] = 0;

        return $filepickeroptions;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $correctcount = 0;
        for ($imageindex = 0; $imageindex < $data['noitems']; ++$imageindex) {
             $fraction = (float) $data['fraction'][$imageindex];
            if ($fraction > 0) {
                $correctcount ++;
            }

        }
        if ($correctcount == 0) {
            $errors['questiontext'] = get_string('markonecorrect', 'qtype_imageselect');
        }
        if ($errors) {
            return $errors;
        } else {
            return true;
        }
    }

    public function qtype() {
        return 'imageselect';
    }

    protected function definition_inner($mform) {
        global $PAGE;
        $PAGE->requires->css('/question/type/imageselect/amd/src/cropper.css');

        $mform->addElement('html', '<a href=./type/imageselect/test.html>Test file</a>');
        $mform->removeelement('questiontext');
        $mform->addElement('editor', 'questiontext', get_string('questiontext', 'question'), ['rows' => 5],
        $this->editoroptions);
        $mform->setType('questiontext', PARAM_RAW);
        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_imageselect');

        list($itemrepeatsatstart, ) = $this->get_image_item_repeats();
        $this->definition_selectable_images($mform, $itemrepeatsatstart);

        $mform->removeelement('defaultmark');
        $mform->removeelement('generalfeedback');
        $mform->addElement('editor', 'generalfeedback', get_string('generalfeedback', 'question'), ['rows' => 10], $this->editoroptions);
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'question');
        $this->add_penalty($mform);

        // To add combined feedback (correct, partial and incorrect).
        $this->add_combined_feedback_fields(true);
        // Adds hinting features.
        $this->add_interactive_settings(true, true);

    }

    /**
     * Add penalty for incorrectly selected text items. This is a fraction that is
     * multiplied by the number of correct responses. So if you select 2 correct
     * and 2 incorrect the and the penalty is .5 the calculation is 2*.5 =1 (of the incorrect)
     * then deduct 1 from the correct count of 2 giving  final result of 1
     * @param object $mform
     */
    protected function add_penalty($mform) {
        $config = get_config('qtype_imageselect');
        $penalties = array(
            1.0000000,
            0.5000000,
            0.3333333,
            0.2500000,
            0.2000000,
            0.1000000,
            0.0000000
        );
        if (!empty($this->question->imagepenalty) && !in_array($this->question->imagepenalty, $penalties)) {
            $penalties[] = $this->question->imagepenalty;
            sort($penalties);
        }

        $penaltyoptions = array();
        foreach ($penalties as $imagepenalty) {
            $penaltyoptions["{$imagepenalty}"] = (100 * $imagepenalty) . '%';
        }

        $mform->addElement('select', 'imagepenalty', get_string('imagepenalty', 'qtype_imageselect'), $penaltyoptions);
        $mform->addHelpButton('imagepenalty', 'imagepenalty', 'qtype_imageselect');
        $mform->setDefault('imagepenalty', $config->imagepenalty);
    }
    protected function definition_selectable_images($mform, $itemrepeatsatstart) {
        $this->repeat_elements($this->selectable_image($mform), $itemrepeatsatstart,
                $this->selectable_image_repeated_options(),
                'noitems', 'additems', self::ADD_NUM_ITEMS,
                get_string('addmoreimages', 'qtype_imageselect'), true);
    }

    protected function selectable_image_repeated_options() {
        $repeatedoptions = [];
        $repeatedoptions['imagegroup']['default'] = '1';

        return $repeatedoptions;
    }

    protected function selectable_image($mform) {
        global $USER;
        $selectableimageitem = [];
        $context = context_user::instance($USER->id, IGNORE_MISSING);

         $singleimageoptions = [
            'maxbytes' => 100,
            'component' => 'qtype_imageselect',
            'filearea' => 'selectableimage',
            'currentimage' => '',
            'contextid' => $context->id,
            'size' => 1000
         ];

         $selectableimageitem[] = $mform->createElement('group', 'images',
         get_string('selectableitemheader', 'qtype_imageselect', '{no}'));

         $selectableimageitem[] = $mform->createElement('singleimage', 'imageitem', "", null, $singleimageoptions);

         $selectableimageitem[] = $mform->createElement('text', 'imagelabel', get_string('imagelabel', 'qtype_imageselect'),
         ['size' => 30, 'class' => 'tweakcss draglabel']);
         $mform->setType('imagelabel', PARAM_RAW); // These are validated manually.

        $selectableimageitem[] = $mform->createElement('advcheckbox', 'fraction', ' ',
        get_string('iscorrect', 'qtype_imageselect'));

        // $selectableimageitem[] = $mform->createElement('text', 'fraction', 'Fraction ');
        // get_string('iscorrect', 'qtype_imageselect'));
        $mform->setType('fraction', PARAM_RAW);

        return $selectableimageitem;
    }

    /**
     * Returns an array of starting number of repeats, and the total number of repeats.
     *
     * @return array
     */
    protected function get_image_item_repeats() {
        $countimages = 0;

        if (isset($this->question->id)) {
            foreach ($this->question->options->images as $image) {
                $countimages = max($countimages, $image->no);
            }
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

        return [$itemrepeatsatstart, $imagerepeats];
    }

}
