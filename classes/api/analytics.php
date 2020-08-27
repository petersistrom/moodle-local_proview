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
 * Proview
 *
 * This module provides support for remote proctoring quizzes and assessments using Proview
 *
 * @package    local_proview
 * @copyright  Talview, 2020
 * @author     Talview Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_proview\api;

defined('MOODLE_INTERNAL') || die();

use core\session\manager;

/**
 * Abstract local analytics class.
 * @copyright  Talview, 2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class analytics {
    /**
     * Encode a substring if required.
     *
     * @param string  $input  The string that might be encoded.
     * @param boolean $encode Whether to encode the URL.
     * @return string
     */
    private static function might_encode($input, $encode) {
        if (!$encode) {
            return str_replace("'", "\'", $input);
        }

        return urlencode($input);
    }

    /**
     * Get the Tracking URL for the request.
     *
     * @param bool|int $urlencode    Whether to encode URLs.
     * @param bool|int $leadingslash Whether to add a leading slash to the URL.
     * @return string A URL to use for tracking.
     */
    public static function trackurl($urlencode = false, $leadingslash = false) {
        global $DB, $PAGE;
        $pageinfo = get_context_info_array($PAGE->context->id);
        $trackurl = "";

        if ($leadingslash) {
            $trackurl .= "/";
        }

        return $trackurl;
    }

    /**
     * Whether to track this request.
     *
     * @return boolean
     *   The outcome of our deliberations.
     */
    public static function should_track() {
        if (!is_siteadmin()) {
            return true;
        }

        $trackadmin = get_config('local_proview', 'trackadmin');
        return ($trackadmin == 1);
    }

    /**
     * Get the user full name to record in tracking, taking account of masquerading if necessary.
     *
     * @return string
     *   The full name to log for the user.
     */
    public static function user_full_name() {
        global $USER;
        $user = $USER;
        $ismasquerading = manager::is_loggedinas();

        if ($ismasquerading) {
            $usereal = get_config('local_proview', 'masquerade_handling');
            if ($usereal) {
                $user = manager::get_realuser();
            }
        }

        $realname = fullname($user);
        return $realname;
    }
}
