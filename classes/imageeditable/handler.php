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

namespace qtype_imageselect\imageeditable;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/filelib.php');

/**
 * Class core_user/imageeditable/handler for the image_editable output component.
 *
 * @package    core_user
 * @since      Moodle 4.0
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler {

    /**
     * Check permissions for storing the image.
     *
     * @param int $contextid The contextid
     */
    private static function check_permissions($contextid): void {
        global $USER;
        $context = \context::instance_by_id($contextid);

        $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);
        $systemcontext = \context_system::instance();

        if ($user->id == $USER->id) {
            require_capability('moodle/user:editownprofile', $systemcontext);
        } else {
            require_capability('moodle/user:editprofile', $context);
            if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
                throw new moodle_exception('useradmineditadmin');
            }
        }
    }

    /**
     * Process the image and return the new image URL.
     *
     * @param int $contextid The user context id.
     * @param string $filearea The filearea where this image should be stored.
     * @param string $filename The image file name.
     * @param string $binary The image binary data.
     */
    public static function store(int $contextid, string $filearea, string $filename, string $binary): void {
        global $CFG;

        self::check_permissions($contextid);
        $context = \context::instance_by_id($contextid);

        $draftitemid = file_get_unused_draft_itemid();

        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftitemid,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, $binary);

        $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);
        $user->imagefile = $draftitemid;
        $filemanageroptions = ['maxbytes' => $CFG->maxbytes,
            'subdirs'        => 0,
            'maxfiles'       => 1,
            'accepted_types' => 'optimised_image'];
        \core_user::update_picture($user, $filemanageroptions);
    }

    /**
     * Process the formdata from the singleimage form element.
     *
     * @param int $draftitemid The draftitemid for the image
     * @param array $singleimageoptions The configuration for the singleimage form field.
     */
    public static function process_formdata(int $draftitemid, array $singleimageoptions): void {
        global $USER;

        $contextid = $singleimageoptions['contextid'];
        $filearea = $singleimageoptions['filearea'];
        self::check_permissions($contextid);

        if ($draftitemid == -1) {
            self::delete($contextid, $filearea);
            return;
        }

        $personalcontext = \context_user::instance($USER->id);

        $fs = get_file_storage();

        foreach ($fs->get_area_files($personalcontext->id, 'user', 'draft', $draftitemid) as $draftfile) {
            if ($draftfile->is_valid_image()) {
                self::delete($contextid, $filearea);
                $newimage = array(
                    'contextid' => $contextid,
                    'component' => 'course',
                    'filearea' => $filearea,
                    'itemid' => 0,
                    'filepath' => '/',
                    'filename' => $draftfile->get_filename()
                );
                $fs->create_file_from_storedfile($newimage, $draftfile);
            }
            $draftfile->delete();
        }
    }

    /**
     * Delete the image
     *
     * @param int $contextid The user contextid.
     * @param string $filearea The filearea for this file.
     */
    public static function delete(int $contextid, string $filearea): void {
        global $DB;

        self::check_permissions($contextid);
        $context = \context::instance_by_id($contextid);

        $fs = get_file_storage();
        $fs->delete_area_files($contextid, 'user', $filearea);
        $DB->set_field('user', 'picture', 0, array('id' => $context->instanceid));
    }
}
