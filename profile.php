<?php

/***************************************************************************
 * profile.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: profile.php,v 1.193.2.3 2005/03/02 23:16:17 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
define('IN_PHPBB', true);
$phpbb_root_path = './';
include $phpbb_root_path . 'extension.inc';
include $phpbb_root_path . 'common.' . $phpEx;
//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_PROFILE);
init_userprefs($userdata);
//
// End session management
//
// session id check
if (!empty($_POST['sid']) || !empty($_GET['sid'])) {
    $sid = (!empty($_POST['sid'])) ? $_POST['sid'] : $_GET['sid'];
} else {
    $sid = '';
}
//
// Set default email variables
//
$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name = ('' != $script_name) ? $script_name . '/profile.' . $phpEx : 'profile.' . $phpEx;
$server_name = trim($board_config['server_name']);
$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://';
$server_port = (80 != $board_config['server_port']) ? ':' . trim($board_config['server_port']) . '/' : '/';
$server_url = $server_protocol . $server_name . $server_port . $script_name;
// -----------------------
// Page specific functions
//
function gen_rand_string($hash)
{
    $chars = [
        'a',
        'A',
        'b',
        'B',
        'c',
        'C',
        'd',
        'D',
        'e',
        'E',
        'f',
        'F',
        'g',
        'G',
        'h',
        'H',
        'i',
        'I',
        'j',
        'J',
        'k',
        'K',
        'l',
        'L',
        'm',
        'M',
        'n',
        'N',
        'o',
        'O',
        'p',
        'P',
        'q',
        'Q',
        'r',
        'R',
        's',
        'S',
        't',
        'T',
        'u',
        'U',
        'v',
        'V',
        'w',
        'W',
        'x',
        'X',
        'y',
        'Y',
        'z',
        'Z',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '0',
    ];

    $max_chars = count($chars) - 1;

    mt_srand((float)microtime() * 1000000);

    $rand_str = '';

    for ($i = 0; $i < 8; $i++) {
        $rand_str = (0 == $i) ? $chars[random_int(0, $max_chars)] : $rand_str . $chars[random_int(0, $max_chars)];
    }

    return ($hash) ? md5($rand_str) : $rand_str;
}

//
// End page specific functions
// ---------------------------
//
// Start of program proper
//
if (isset($_GET['mode']) || isset($_POST['mode'])) {
    $mode = $_GET['mode'] ?? $_POST['mode'];

    if ('viewprofile' == $mode) {
        include $phpbb_root_path . 'includes/usercp_viewprofile.' . $phpEx;

        exit;
    } elseif ('editprofile' == $mode || 'register' == $mode) {
        if (!$userdata['session_logged_in'] && 'editprofile' == $mode) {
            redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=editprofile", true));
        }

        include $phpbb_root_path . 'includes/usercp_register.' . $phpEx;

        exit;
    } elseif ('sendpassword' == $mode) {
        include $phpbb_root_path . 'includes/usercp_sendpasswd.' . $phpEx;

        exit;
    } elseif ('activate' == $mode) {
        include $phpbb_root_path . 'includes/usercp_activate.' . $phpEx;

        exit;
    } elseif ('email' == $mode) {
        include $phpbb_root_path . 'includes/usercp_email.' . $phpEx;

        exit;
    }
}
redirect(append_sid("index.$phpEx", true));
