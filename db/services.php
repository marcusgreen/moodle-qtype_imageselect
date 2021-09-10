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
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_editimage
 * @copyright   2019 Your Name <you@example.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
    $services = [
      'image_update' => [
          'functions' => ['qtype_imageselect_imageeditable_update_image'],
          'requiredcapability' => '',
          'restrictedusers' => 0,                                                                               // into the administration
          'enabled' => 1,
          'shortname' => '',
          'downloadfiles' => 0,
          'uploadfiles'  => 0
      ],
    ];
    $functions = [
      'qtype_imageselect_imageeditable_update_image' => [         //web service function name
          'classname'   => 'imageeditable',  //class containing the external function OR namespaced class in classes/external/XXXX.php
          'methodname'  => 'update_image',          //external function name
          'description' => 'Update image.',    //human readable description of the web service function
          'type'        => 'write',                  //database rights of the web service function (read, write)
          'ajax' => true,        // is the service available to 'internal' ajax calls.
          'capabilities' => '', // comma separated list of capabilities used by the function.
      ],
    ];