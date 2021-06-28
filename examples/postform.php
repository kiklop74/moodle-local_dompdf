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
 * Example usage
 *
 * @package    local_dompdf
 * @copyright  2019 onwards Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

/**
 * Class postform
 * @package    local_dompdf
 * @copyright  2019 onwards Darko Miletic
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class postform extends moodleform {

    /**
     * Define the form
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function definition() {
        global $DB;

        $mform = $this->_form;

        $posts = array_merge(
            [0 => get_string('none')] + $DB->get_records_menu('forum_posts', null, '', 'id, subject')
        );

        /** @var MoodleQuickForm_select $forumposts */
        $forumposts = $mform->addElement('select', 'forumposts', 'Forum posts', $posts);
        $mform->setType($forumposts->getName(), PARAM_INT);

        $this->add_action_buttons(false, get_string('download'));
    }

}