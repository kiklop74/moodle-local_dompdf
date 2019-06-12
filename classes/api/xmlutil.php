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
 * XML handling utilities
 *
 * @package   local_dompdf
 * @copyright 2019 onwards Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dompdf\api;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Class xmlutil
 * @package   local_dompdf
 * @copyright 2019 onwards Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class xmlutil {

    /** @var string UTF-8 */
    const UTF8 = 'UTF-8';
    /** @var string */
    const XML1 = '1.0';

    /**
     * Extracts inner HTML from parent node.
     * @param DOMNode $node
     * @return string
     */
    public static function innerhtml(DOMNode $node) {
        return implode(
            array_map(
                [$node->ownerDocument, 'saveHTML'],
                iterator_to_array($node->childNodes)
            )
        );
    }

    /**
     * This method decode's and loads HTML fragment.
     * Useful for searching and altering the HTML fragment.
     *
     * All this complication comes from the fact that libxml parser defaults to LATIN1 charset for HTML
     * unless it finds something else in meta tag. It also does not cooperate with HTML5.
     *
     * @param string $htmlfragment
     * @param string $decode
     * @return DOMDocument | null
     */
    public static function loadhtmlfragment($htmlfragment, $decode) {
        $result = null;
        if ($htmlfragment) {
            $doc = new DOMDocument(self::XML1, self::UTF8);
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = true;
            $doc->strictErrorChecking = false;
            $doc->recover = true;
            $doc->validateOnParse = false;
            $options = LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET;
            $decoded = $decode ? html_entity_decode($htmlfragment, ENT_QUOTES, self::UTF8) : $htmlfragment;
            $wraphtml = "<html lang='en'>
                        <head>
                          <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
                          <title>title</title>
                        </head>
                        <body>
                          $decoded
                        </body>
                        </html>";
            if (@$doc->loadHTML($wraphtml, $options)) {
                $result = $doc;
            }
        }
        return $result;
    }

    /**
     * Extracts the wrapped inner HTML
     *
     * This method should be used in conjunction with loadhtmlfragment only
     *
     * @param DOMDocument $doc
     * @return null|string
     */
    public static function extractfragment(DOMDocument $doc) {
        $result = null;
        $nodel = $doc->getElementsByTagName('body');
        if ($nodel->length) {
            $result = self::innerhtml($nodel->item(0));
        }
        return $result;
    }

    /**
     * Replaces relative image paths with base64 encoded data
     * @param string $html
     * @param int $itemid
     * @param string $filearea
     * @param int $contextid
     * @param string $component
     * @return string
     * @throws \coding_exception
     * @throws \file_exception
     */
    public static function recode_images($html, $itemid, $filearea, $contextid, $component) {
        $result = $html;
        $doc = self::loadhtmlfragment($html, false);
        if ($doc === null) {
            return $result;
        }
        $xpath = new DOMXPath($doc);
        $seed = '@@PLUGINFILE@@'; $empty = ''; $srcattribute = 'src';
        $items = $xpath->query(sprintf('//img[@%1$s and starts-with(@%1$s, "%2$s/")]', $srcattribute, $seed));
        if ($items->length > 0) {
            $fs = get_file_storage();
            /** @var DOMElement[] $items */
            $items;
            foreach ($items as $item) {
                $src = str_replace($seed, $empty, $item->getAttribute($srcattribute));
                $path = pathinfo($src);
                $imagefile = $fs->get_file(
                    $contextid, $component, $filearea, $itemid, $path['dirname'], $path['basename']
                );
                if (($imagefile !== false) and $imagefile->is_valid_image()) {
                    $imagecontent = sprintf(
                        'data:%s;charset=UTF-8;base64,%s',
                        $imagefile->get_mimetype(),
                        base64_encode($imagefile->get_content())
                    );
                    $item->setAttribute($srcattribute, $imagecontent);
                }
            }
            $result = self::extractfragment($xpath->document);
        }
        $xpath = null;

        return $result;
    }

}