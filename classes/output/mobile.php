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
 * Mobile output class for qtype_imageselect
 *
 * @package    qtype_imageselect
 * @copyright  2018 YOUR NAME
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_imageselect\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Mobile output class for imageselect question type
 *
 * @package    qtype_imageselect
 * @copyright  20XX YOUR NAME
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mobile {

    /**
     * Returns the imageselect question type for the quiz the mobile app.
     *
     * @return void
     */
    public static function mobile_get_imageselect() {
        global $CFG;
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => file_get_contents($CFG->dirroot .'/question/type/imageselect/mobile/qtype-imageselect.html')
                    ]
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/question/type/imageselect/mobile/mobile.js')
        ];
    }
}
