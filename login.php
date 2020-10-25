<?php

/***************************************************************************
 * login.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: login.php,v 1.47.2.12 2005/05/06 20:18:42 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
//
// Allow people to reach login page if
// board is shut down
//
define('IN_LOGIN', true);
define('IN_PHPBB', true);
$phpbb_root_path = './';
include $phpbb_root_path . 'extension.inc';
include $phpbb_root_path . 'common.' . $phpEx;
//
// Set page ID for session management
//
$userdata = session_pagestart($user_ip, PAGE_LOGIN);
init_userprefs($userdata);
//
// End session management
//
if (isset($_POST['login']) || isset($_GET['login']) || isset($_POST['logout']) || isset($_GET['logout'])) {
    if ((isset($_POST['login']) || isset($_GET['login'])) && !$userdata['session_logged_in']) {
        $username = isset($_POST['username']) ? trim(htmlspecialchars($_POST['username'], ENT_QUOTES | ENT_HTML5)) : '';

        $username = mb_substr(str_replace("\'", "'", $username), 0, 25);

        $password = isset($_POST['password']) ? md5($_POST['password']) : '';

        $sql = 'SELECT uid, uname, pass, level, user_level
FROM ' . USERS_TABLE . "
WHERE uname = '" . str_replace("\'", "''", $username) . "'";

        if (!($result = $db->sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Error in obtaining userdata', '', __LINE__, __FILE__, $sql);
        }

        if ($row = $db->sql_fetchrow($result)) {
            if (ADMIN != $row['user_level'] && $board_config['board_disable']) {
                redirect(append_sid("index.$phpEx", true));
            } else {
                if ($password == $row['pass'] && $row['level']) {
                    $autologin = (isset($_POST['autologin'])) ? true : 0;

                    // XOOPS redirect login

                    @header('location: ' . XOOPS_URL . '/user.php?op=login&uname=' . $username . '&pass=' . $password . '&pbb=' . $HTTP_SERVER_VARS['HTTP_REFERER']);
                } else {
                    $redirect = (!empty($_POST['redirect'])) ? $_POST['redirect'] : '';

                    $redirect = str_replace('?', '&', $redirect);

                    $template->assign_vars(
                        [
                            'META' => "<meta http-equiv=\"refresh\" content=\"3;url=login.$phpEx?redirect=$redirect\">",
                        ]
                    );

                    $message = $lang['Error_login'] . '<br><br>' . sprintf($lang['Click_return_login'], "<a href=\"login.$phpEx?redirect=$redirect\">", '</a>') . '<br><br>' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

                    message_die(GENERAL_MESSAGE, $message);
                }
            }
        } else {
            $redirect = (!empty($_POST['redirect'])) ? $_POST['redirect'] : '';

            $redirect = str_replace('?', '&', $redirect);

            $template->assign_vars(
                [
                    'META' => "<meta http-equiv=\"refresh\" content=\"3;url=login.$phpEx?redirect=$redirect\">",
                ]
            );

            $message = $lang['Error_login'] . '<br><br>' . sprintf($lang['Click_return_login'], "<a href=\"login.$phpEx?redirect=$redirect\">", '</a>') . '<br><br>' . sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

            message_die(GENERAL_MESSAGE, $message);
        }
    } elseif ((isset($_GET['logout']) || isset($_POST['logout'])) && $userdata['session_logged_in']) {
        // XOOPS redirect logout

        @header('location: ' . XOOPS_URL . '/user.php?op=logout');
    } else {
        $url = (!empty($_POST['redirect'])) ? $_POST['redirect'] : "index.$phpEx";

        redirect(append_sid($url, true));
    }
} else {
    // Do a full login page dohickey if

    // user not already logged in

    if (!$userdata['session_logged_in']) {
        $page_title = $lang['Login'];

        include $phpbb_root_path . 'includes/page_header.' . $phpEx;

        $template->set_filenames(
            [
                'body' => 'login_body.tpl',
            ]
        );

        if (isset($_POST['redirect']) || isset($_GET['redirect'])) {
            $forward_to = $HTTP_SERVER_VARS['QUERY_STRING'];

            if (preg_match("/^redirect=([a-z0-9\.#\/\?&=\+\-_]+)/si", $forward_to, $forward_matches)) {
                $forward_to = (!empty($forward_matches[3])) ? $forward_matches[3] : $forward_matches[1];

                $forward_match = explode('&', $forward_to);

                if (count($forward_match) > 1) {
                    $forward_page = '';

                    for ($i = 1, $iMax = count($forward_match); $i < $iMax; $i++) {
                        if (!preg_match('sid=', $forward_match[$i])) {
                            if ('' != $forward_page) {
                                $forward_page .= '&';
                            }

                            $forward_page .= $forward_match[$i];
                        }
                    }

                    $forward_page = $forward_match[0] . '?' . $forward_page;
                } else {
                    $forward_page = $forward_match[0];
                }
            }
        } else {
            $forward_page = '';
        }

        $username = (ANONYMOUS != $userdata['uid']) ? $userdata['uname'] : '';

        $s_hidden_fields = '<input type="hidden" name="redirect" value="' . $forward_page . '">';

        make_jumpbox('viewforum.' . $phpEx, $forum_id);

        $template->assign_vars(
            [
                'USERNAME' => $username,
'L_ENTER_PASSWORD' => $lang['Enter_password'],
'L_SEND_PASSWORD' => $lang['Forgotten_password'],
'U_SEND_PASSWORD' => append_sid("profile.$phpEx?mode=sendpassword"),
'S_HIDDEN_FIELDS' => $s_hidden_fields,
            ]
        );

        $template->pparse('body');

        include $phpbb_root_path . 'includes/page_tail.' . $phpEx;
    } else {
        redirect(append_sid("index.$phpEx", true));
    }
}
