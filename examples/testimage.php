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
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/local/dompdf/examples/postform.php');

require_login();

$PAGE->set_url('/local/dompdf/examples/testimage.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Example');
$PAGE->set_heading('Example of exporting PDF with images');
$PAGE->navbar->add($PAGE->title, $PAGE->url);

/** @var core_renderer $OUTPUT */
$OUTPUT;

$form = new postform($PAGE->url);
if ($form->is_submitted()) {
    $data = $form->get_data();
    if ($data and $data->forumposts) {
        $postdata = $DB->get_record_sql(
            '
              SELECT fp.id, f.id AS forumid, fp.subject, fp.message, fp.messageformat
                FROM {forum_posts} fp
                JOIN {forum_discussions} fd ON fd.id = fp.discussion
                JOIN {forum} f ON f.id = fd.forum
               WHERE fp.id = :id
            ',
            ['id' => $data->forumposts]
        );

        $cm = get_coursemodule_from_instance('forum', $postdata->forumid);
        $context = context_module::instance($cm->id);
        $options = [
            'noclean' => true, 'para' => false, 'filter' => true,
            'context' => $context, 'overflowdiv' => true
        ];
        $processimages = \local_dompdf\api\pdf::file_rewrite_image_urls(
            $postdata->message, $postdata->id, 'post', $context->id, 'mod_forum'
        );
        $html = format_text($processimages, $postdata->messageformat, $options);
        $pdf = \local_dompdf\api\pdf::createnew();
        $pdf->loadHtml($html);
        $pdf->render();
        send_file(
            $pdf->output(), 'examplewithimage.pdf', null, 0, true, true, 'application/pdf'
        );
    }
}

echo $OUTPUT->header();

$form->display();

echo $OUTPUT->footer();