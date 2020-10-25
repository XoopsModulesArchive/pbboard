<?php

/***************************************************************************
 * sessions.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: sessions.php,v 1.58.2.10 2005/04/05 12:04:33 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 **************************************************************************
 * @param $user_ip
 * @param $thispage_id
 * @return false|mixed
 */
//
// Checks for a given user session, tidies session table and updates user
// sessions at each page refresh
//
function session_pagestart($user_ip, $thispage_id)
{
    global $db, $lang, $board_config, $sid_bb, $uid_bb, $issess;

    global $HTTP_COOKIE_VARS, $_GET, $SID;

    $cookiename = $board_config['cookie_name'];

    $cookiepath = $board_config['cookie_path'];

    $cookiedomain = $board_config['cookie_domain'];

    $cookiesecure = $board_config['cookie_secure'];

    $current_time = time();

    unset($userdata);

    if ($sid_bb) {
        $session_id = $sid_bb;

        $sessionmethod = SESSION_METHOD_GET;
    }

    // Does a session exist?

    $sql = 'SELECT u.*, s.*
FROM ' . SESSIONS_TABLE . ' s, ' . USERS_TABLE . " u
WHERE s.sess_id = '" . $session_id . "'
AND u.uid = s.session_user_id";

    if (!($result = $db->sql_query($sql))) {
        message_die(CRITICAL_ERROR, 'Error doing DB query userdata row fetch', '', __LINE__, __FILE__, $sql);
    }

    $userdata = $db->sql_fetchrow($result);

    // Did the session exist in the DB?

    if (isset($userdata['uid'])) {
        // Do not check IP assuming equivalence, if IPv4 we'll check only first 24

        // bits ... I've been told (by vHiker) this should alleviate problems with

        // load balanced et al proxies while retaining some reliance on IP security.

        $ip_check_s = mb_substr($userdata['sess_ip'], 0, 6);

        $ip_check_u = mb_substr($user_ip, 0, 6);

        if ($ip_check_s == $ip_check_u) {
            $SID = defined('IN_ADMIN') ? 'sid=' . $session_id : '';

            // Only update session DB a minute or so after last update

            if ($current_time - $userdata['sess_updated'] > 60) {
                $sql = 'UPDATE ' . SESSIONS_TABLE . "
SET sess_updated = $current_time, session_page = $thispage_id
WHERE sess_id = '" . $userdata['sess_id'] . "'";

                if (!$db->sql_query($sql)) {
                    message_die(CRITICAL_ERROR, 'Error updating sessions table', '', __LINE__, __FILE__, $sql);
                }

                if (ANONYMOUS != $userdata['uid']) {
                    $sql = 'UPDATE ' . USERS_TABLE . "
SET user_session_time = $current_time, user_session_page = $thispage_id
WHERE uid = " . $userdata['uid'];

                    if (!$db->sql_query($sql)) {
                        message_die(CRITICAL_ERROR, 'Error updating sessions table', '', __LINE__, __FILE__, $sql);
                    }
                }
            }

            return $userdata;
        }
    }

    return $userdata;
}

//
// Append $SID to a url. Borrowed from phplib and modified. This is an
// extra routine utilised by the session code above and acts as a wrapper
// around every single URL and form action. If you replace the session
// code you must include this routine, even if it's empty.
//
function append_sid($url, $non_html_amp = false)
{
    global $SID;

    if (!empty($SID) && !preg_match('#sid=#', $url)) {
        $url .= ((false !== mb_strpos($url, '?')) ? (($non_html_amp) ? '&' : '&amp;') : '?') . $SID;
    }

    return $url;
}
