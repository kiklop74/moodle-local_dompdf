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
        self::autoload();
        $cachedir = make_localcache_directory('dompdf');
        $default = [
            'temp_dir' => $cachedir,
            'font_cache' => $cachedir,
            'default_paper_size' => 'A4',
            'default_paper_orientation' => 'portrait',
            'is_php_enabled' => false
        ];
        $opts = $default;
        if (is_array($options)) {
            $opts = array_merge($default, $options);
        }
        return new \Dompdf\Dompdf($opts);
    }

}