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

require(__DIR__ . '/../../../config.php');
global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/filelib.php');

$download = optional_param('download', false, PARAM_BOOL);

require_login();

$PAGE->set_url('/local/dompdf/examples/test.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Example');
$PAGE->set_heading('Example');
$PAGE->navbar->add($PAGE->title, $PAGE->url);

/** @var core_renderer $OUTPUT */
$OUTPUT;

if ($download) {
    $pdf = \local_dompdf\api\pdf::createnew();
    $pdf->loadHtml('<h1>We are the champions!</h1>');
    $pdf->render();
    send_file(
        $pdf->output(), 'example.pdf', null, 0, true, true, 'application/pdf'
    );
}

echo $OUTPUT->header();

echo $OUTPUT->single_button($PAGE->url->out(true, ['download' => true]), get_string('download'));

echo $OUTPUT->footer();