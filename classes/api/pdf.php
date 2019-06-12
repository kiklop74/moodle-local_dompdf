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
 * Autoload library
 *
 * @package   local_dompdf
 * @copyright 2019 onwards Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dompdf\api;

use Dompdf\Dompdf;
use invalid_dataroot_permissions;
use coding_exception;
use file_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class pdf
 * @package   local_dompdf
 * @copyright 2019 onwards Darko Miletic
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class pdf {

    /**
     * Load the library
     * @return void
     */
    public static function autoload() {
        require_once(__DIR__.'/../../vendor/autoload.php');
    }

    /**
     * Create new instance
     * @param array|null $options
     * @return Dompdf
     * @throws invalid_dataroot_permissions
     * @throws coding_exception
     */
    public static function createnew(array $options = null) {
        global $CFG;
        self::autoload();
        $cachedir = make_localcache_directory('dompdf');
        $default = [
            'temp_dir' => $cachedir,
            'font_cache' => $cachedir,
            'log_output_file' => sprintf('%s/log.html', $cachedir),
            'default_paper_size' => 'A4',
            'default_paper_orientation' => 'portrait',
            'is_html5_parser_enabled' => true,
            'is_php_enabled' => false
        ];
        if ($CFG->debugdeveloper === DEBUG_DEVELOPER) {
            $default['debug_png'] = true;
            $default['debug_keep_temp'] = true;
            $default['debug_css'] = true;
            $default['debug_layout'] = true;
        }
        $opts = $default;
        if (is_array($options)) {
            $opts = array_merge($default, $options);
        }
        return new \Dompdf\Dompdf($opts);
    }

    /**
     * Converts images in the form acceptable to the library
     * @param string $html
     * @param int $itemid
     * @param string $filearea
     * @param int $contextid
     * @param string $component
     * @return string
     * @throws file_exception
     * @throws coding_exception
     */
    public static function file_rewrite_image_urls($html, $itemid, $filearea, $contextid, $component) {
        return xmlutil::recode_images($html, $itemid, $filearea, $contextid, $component);
    }

}