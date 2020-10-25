<?php

/***************************************************************************
 * usercp_activate.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: usercp_activate.php,v 1.6.2.7 2005/05/03 23:24:02 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
if (!defined('IN_PHPBB')) {
    die('Hacking attempt');

    exit;
}
$sql = 'SELECT level, uid, uname, email, user_newpasswd, user_lang, actkey
FROM ' . USERS_TABLE . '
WHERE uid = ' . (int)$_GET[POST_USERS_URL];
if (!($result = $db->sql_query($sql))) {
    message_die(GENERAL_ERROR, 'Could not obtain user information', '', __LINE__, __FILE__, $sql);
}
if ($row = $db->sql_fetchrow($result)) {
    if ($row['level'] && '' == trim($row['actkey'])) {
        $template->assign_vars(
            [
                'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.$phpEx") . '">',
            ]
        );

        message_die(GENERAL_MESSAGE, $lang['Already_activated']);
    } elseif ((trim($row['actkey']) == trim($_GET['act_key'])) && ('' != trim($row['actkey']))) {
        $sql_update_pass = ('' != $row['user_newpasswd']) ? ", user_password = '" . str_replace("\'", "''", $row['user_newpasswd']) . "', user_newpasswd = ''" : '';

        $sql = 'UPDATE ' . USERS_TABLE . "
SET level = 1, actkey = ''" . $sql_update_pass . '
WHERE uid = ' . $row['uid'];

        if (!($result = $db->sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql_update);
        }

        if (USER_ACTIVATION_ADMIN == (int)$board_config['require_activation'] && '' == $sql_update_pass) {
            include $phpbb_root_path . 'includes/emailer.' . $phpEx;

            $emailer = new emailer($board_config['smtp_delivery']);

            $emailer->from($board_config['board_email']);

            $emailer->replyto($board_config['board_email']);

            $emailer->use_template('admin_welcome_activated', $row['user_lang']);

            $emailer->email_address($row['email']);

            $emailer->set_subject($lang['Account_activated_subject']);

            $emailer->assign_vars(
                [
                    'SITENAME' => $board_config['sitename'],
'USERNAME' => $row['uname'],
'PASSWORD' => $password_confirm,
'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br>', "\n", "-- \n" . $board_config['board_email_sig']) : '',
                ]
            );

            $emailer->send();

            $emailer->reset();

            $template->assign_vars(
                [
                    'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.$phpEx") . '">',
                ]
            );

            message_die(GENERAL_MESSAGE, $lang['Account_active_admin']);
        } else {
            $template->assign_vars(
                [
                    'META' => '<meta http-equiv="refresh" content="10;url=' . append_sid("index.$phpEx") . '">',
                ]
            );

            $message = ('' == $sql_update_pass) ? $lang['Account_active'] : $lang['Password_activated'];

            message_die(GENERAL_MESSAGE, $message);
        }
    } else {
        message_die(GENERAL_MESSAGE, $lang['Wrong_activation']);
    }
} else {
    message_die(GENERAL_MESSAGE, $lang['No_such_user']);
}
