/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   qtype_imageselect
 * @copyright 2021 Marcus Green
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
  var clickables = document.querySelectorAll(".selectableimage");
  clickables.forEach(item => {
    item.addEventListener('click', event => {
      var el = document.getElementById(event.currentTarget.id);
      var id = event.currentTarget.id;
      var number = id.split('-')[1];
      debugger;
      var cbx = document.getElementById('imagecheck_'+number);
      if (el.style.borderStyle == 'dotted') {
        el.style.borderStyle = '';
        cbx.checked = false;
      } else {
        el.style.borderStyle = 'dotted';
        cbx.checked = true;
      }

    });
  });


};
