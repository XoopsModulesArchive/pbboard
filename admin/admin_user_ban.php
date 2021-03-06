<?php

/***************************************************************************
 * admin_user_ban.php
 * -------------------
 * begin : Tuesday, Jul 31, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: admin_user_ban.php,v 1.21.2.4 2005/03/31 06:56:30 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
define('IN_PHPBB', 1);
if (!empty($setmodules)) {
    $filename = basename(__FILE__);

    $module['Users']['Ban_Management'] = $filename;

    return;
}
//
// Load default header
//
$phpbb_root_path = './../';
require $phpbb_root_path . 'extension.inc';
require __DIR__ . '/pagestart.' . $phpEx;
//
// Start program
//
if (isset($_POST['submit'])) {
    $user_bansql = '';

    $email_bansql = '';

    $ip_bansql = '';

    $user_list = [];

    if (!empty($_POST['username'])) {
        $this_userdata = get_userdata($_POST['username'], true);

        if (!$this_userdata) {
            message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
        }

        $user_list[] = $this_userdata['uid'];
    }

    $ip_list = [];

    if (isset($_POST['ban_ip'])) {
        $ip_list_temp = explode(',', $_POST['ban_ip']);

        for ($i = 0, $iMax = count($ip_list_temp); $i < $iMax; $i++) {
            if (preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', trim($ip_list_temp[$i]), $ip_range_explode)) {
                // Don't ask about all this, just don't ask ... !

                $ip_1_counter = $ip_range_explode[1];

                $ip_1_end = $ip_range_explode[5];

                while ($ip_1_counter <= $ip_1_end) {
                    $ip_2_counter = ($ip_1_counter == $ip_range_explode[1]) ? $ip_range_explode[2] : 0;

                    $ip_2_end = ($ip_1_counter < $ip_1_end) ? 254 : $ip_range_explode[6];

                    if (0 == $ip_2_counter && 254 == $ip_2_end) {
                        $ip_2_counter = 255;

                        $ip_2_fragment = 255;

                        $ip_list[] = encode_ip("$ip_1_counter.255.255.255");
                    }

                    while ($ip_2_counter <= $ip_2_end) {
                        $ip_3_counter = ($ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1]) ? $ip_range_explode[3] : 0;

                        $ip_3_end = ($ip_2_counter < $ip_2_end || $ip_1_counter < $ip_1_end) ? 254 : $ip_range_explode[7];

                        if (0 == $ip_3_counter && 254 == $ip_3_end) {
                            $ip_3_counter = 255;

                            $ip_3_fragment = 255;

                            $ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.255.255");
                        }

                        while ($ip_3_counter <= $ip_3_end) {
                            $ip_4_counter = ($ip_3_counter == $ip_range_explode[3] && $ip_2_counter == $ip_range_explode[2] && $ip_1_counter == $ip_range_explode[1]) ? $ip_range_explode[4] : 0;

                            $ip_4_end = ($ip_3_counter < $ip_3_end || $ip_2_counter < $ip_2_end) ? 254 : $ip_range_explode[8];

                            if (0 == $ip_4_counter && 254 == $ip_4_end) {
                                $ip_4_counter = 255;

                                $ip_4_fragment = 255;

                                $ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.255");
                            }

                            while ($ip_4_counter <= $ip_4_end) {
                                $ip_list[] = encode_ip("$ip_1_counter.$ip_2_counter.$ip_3_counter.$ip_4_counter");

                                $ip_4_counter++;
                            }

                            $ip_3_counter++;
                        }

                        $ip_2_counter++;
                    }

                    $ip_1_counter++;
                }
            } elseif (preg_match('/^([\w\-_]\.?){2,}$/is', trim($ip_list_temp[$i]))) {
                $ip = gethostbynamel(trim($ip_list_temp[$i]));

                for ($j = 0, $jMax = count($ip); $j < $jMax; $j++) {
                    if (!empty($ip[$j])) {
                        $ip_list[] = encode_ip($ip[$j]);
                    }
                }
            } elseif (preg_match('/^([0-9]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$/', trim($ip_list_temp[$i]))) {
                $ip_list[] = encode_ip(str_replace('*', '255', trim($ip_list_temp[$i])));
            }
        }
    }

    $email_list = [];

    if (isset($_POST['ban_email'])) {
        $email_list_temp = explode(',', $_POST['ban_email']);

        for ($i = 0, $iMax = count($email_list_temp); $i < $iMax; $i++) {
            // This ereg match is based on one by php@unreelpro.com

            // contained in the annotated php manual at php.com (ereg

            // section)

            if (preg_match('#^(([a-z0-9&.-_+])|(\*))+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*?[a-z]+$#is', trim($email_list_temp[$i]))) {
                $email_list[] = trim($email_list_temp[$i]);
            }
        }
    }

    $sql = 'SELECT *
FROM ' . BANLIST_TABLE;

    if (!($result = $db->sql_query($sql))) {
        message_die(GENERAL_ERROR, "Couldn't obtain banlist information", '', __LINE__, __FILE__, $sql);
    }

    $current_banlist = $db->sql_fetchrowset($result);

    $db->sql_freeresult($result);

    $kill_session_sql = '';

    for ($i = 0, $iMax = count($user_list); $i < $iMax; $i++) {
        $in_banlist = false;

        for ($j = 0, $jMax = count($current_banlist); $j < $jMax; $j++) {
            if ($user_list[$i] == $current_banlist[$j]['ban_userid']) {
                $in_banlist = true;
            }
        }

        if (!$in_banlist) {
            $kill_session_sql .= (('' != $kill_session_sql) ? ' OR ' : '') . 'session_user_id = ' . $user_list[$i];

            $sql = 'INSERT INTO ' . BANLIST_TABLE . ' (ban_userid)
VALUES (' . $user_list[$i] . ')';

            if (!$db->sql_query($sql)) {
                message_die(GENERAL_ERROR, "Couldn't insert ban_userid info into database", '', __LINE__, __FILE__, $sql);
            }
        }
    }

    for ($i = 0, $iMax = count($ip_list); $i < $iMax; $i++) {
        $in_banlist = false;

        for ($j = 0, $jMax = count($current_banlist); $j < $jMax; $j++) {
            if ($ip_list[$i] == $current_banlist[$j]['ban_ip']) {
                $in_banlist = true;
            }
        }

        if (!$in_banlist) {
            if (preg_match('/(ff\.)|(\.ff)/is', chunk_preg_split($ip_list[$i], 2, '.'))) {
                $kill_ip_sql = "session_ip LIKE '" . str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_preg_split($ip_list[$i], 2, '.'))) . "'";
            } else {
                $kill_ip_sql = "session_ip = '" . $ip_list[$i] . "'";
            }

            $kill_session_sql .= (('' != $kill_session_sql) ? ' OR ' : '') . $kill_ip_sql;

            $sql = 'INSERT INTO ' . BANLIST_TABLE . " (ban_ip)
VALUES ('" . $ip_list[$i] . "')";

            if (!$db->sql_query($sql)) {
                message_die(GENERAL_ERROR, "Couldn't insert ban_ip info into database", '', __LINE__, __FILE__, $sql);
            }
        }
    }

    // Now we'll delete all entries from the session table with any of the banned

    // user or IP info just entered into the ban table ... this will force a session

    // initialisation resulting in an instant ban

    if ('' != $kill_session_sql) {
        $sql = 'DELETE FROM ' . SESSIONS_TABLE . "
WHERE $kill_session_sql";

        if (!$db->sql_query($sql)) {
            message_die(GENERAL_ERROR, "Couldn't delete banned sessions from database", '', __LINE__, __FILE__, $sql);
        }
    }

    for ($i = 0, $iMax = count($email_list); $i < $iMax; $i++) {
        $in_banlist = false;

        for ($j = 0, $jMax = count($current_banlist); $j < $jMax; $j++) {
            if ($email_list[$i] == $current_banlist[$j]['ban_email']) {
                $in_banlist = true;
            }
        }

        if (!$in_banlist) {
            $sql = 'INSERT INTO ' . BANLIST_TABLE . " (ban_email)
VALUES ('" . str_replace("\'", "''", $email_list[$i]) . "')";

            if (!$db->sql_query($sql)) {
                message_die(GENERAL_ERROR, "Couldn't insert ban_email info into database", '', __LINE__, __FILE__, $sql);
            }
        }
    }

    $where_sql = '';

    if (isset($_POST['unban_user'])) {
        $user_list = $_POST['unban_user'];

        for ($i = 0, $iMax = count($user_list); $i < $iMax; $i++) {
            if (-1 != $user_list[$i]) {
                $where_sql .= (('' != $where_sql) ? ', ' : '') . $user_list[$i];
            }
        }
    }

    if (isset($_POST['unban_ip'])) {
        $ip_list = $_POST['unban_ip'];

        for ($i = 0, $iMax = count($ip_list); $i < $iMax; $i++) {
            if (-1 != $ip_list[$i]) {
                $where_sql .= (('' != $where_sql) ? ', ' : '') . $ip_list[$i];
            }
        }
    }

    if (isset($_POST['unban_email'])) {
        $email_list = $_POST['unban_email'];

        for ($i = 0, $iMax = count($email_list); $i < $iMax; $i++) {
            if (-1 != $email_list[$i]) {
                $where_sql .= (('' != $where_sql) ? ', ' : '') . $email_list[$i];
            }
        }
    }

    if ('' != $where_sql) {
        $sql = 'DELETE FROM ' . BANLIST_TABLE . "
WHERE ban_id IN ($where_sql)";

        if (!$db->sql_query($sql)) {
            message_die(GENERAL_ERROR, "Couldn't delete ban info from database", '', __LINE__, __FILE__, $sql);
        }
    }

    $message = $lang['Ban_update_sucessful'] . '<br><br>' . sprintf($lang['Click_return_banadmin'], '<a href="' . append_sid("admin_user_ban.$phpEx") . '">', '</a>') . '<br><br>' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');

    message_die(GENERAL_MESSAGE, $message);
} else {
    $template->set_filenames(
        [
            'body' => 'admin/user_ban_body.tpl',
        ]
    );

    $template->assign_vars(
        [
            'L_BAN_TITLE' => $lang['Ban_control'],
'L_BAN_EXPLAIN' => $lang['Ban_explain'],
'L_BAN_EXPLAIN_WARN' => $lang['Ban_explain_warn'],
'L_IP_OR_HOSTNAME' => $lang['IP_hostname'],
'L_EMAIL_ADDRESS' => $lang['Email_address'],
'L_SUBMIT' => $lang['Submit'],
'L_RESET' => $lang['Reset'],
'S_BANLIST_ACTION' => append_sid("admin_user_ban.$phpEx"),
        ]
    );

    $template->assign_vars(
        [
            'L_BAN_USER' => $lang['Ban_username'],
'L_BAN_USER_EXPLAIN' => $lang['Ban_username_explain'],
'L_BAN_IP' => $lang['Ban_IP'],
'L_BAN_IP_EXPLAIN' => $lang['Ban_IP_explain'],
'L_BAN_EMAIL' => $lang['Ban_email'],
'L_BAN_EMAIL_EXPLAIN' => $lang['Ban_email_explain'],
        ]
    );

    $userban_count = 0;

    $ipban_count = 0;

    $emailban_count = 0;

    $sql = 'SELECT b.ban_id, u.uid, u.uname
FROM ' . BANLIST_TABLE . ' b, ' . USERS_TABLE . ' u
WHERE u.uid = b.ban_userid
AND b.ban_userid <> 0
AND u.uid <> ' . ANONYMOUS . '
ORDER BY u.uid ASC';

    if (!($result = $db->sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not select current user_id ban list', '', __LINE__, __FILE__, $sql);
    }

    $user_list = $db->sql_fetchrowset($result);

    $db->sql_freeresult($result);

    $select_userlist = '';

    for ($i = 0, $iMax = count($user_list); $i < $iMax; $i++) {
        $select_userlist .= '<option value="' . $user_list[$i]['ban_id'] . '">' . $user_list[$i]['uname'] . '</option>';

        $userban_count++;
    }

    if ('' == $select_userlist) {
        $select_userlist = '<option value="-1">' . $lang['No_banned_users'] . '</option>';
    }

    $select_userlist = '<select name="unban_user[]" multiple="multiple" size="5">' . $select_userlist . '</select>';

    $sql = 'SELECT ban_id, ban_ip, ban_email
FROM ' . BANLIST_TABLE;

    if (!($result = $db->sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not select current ip ban list', '', __LINE__, __FILE__, $sql);
    }

    $banlist = $db->sql_fetchrowset($result);

    $db->sql_freeresult($result);

    $select_iplist = '';

    $select_emaillist = '';

    for ($i = 0, $iMax = count($banlist); $i < $iMax; $i++) {
        $ban_id = $banlist[$i]['ban_id'];

        if (!empty($banlist[$i]['ban_ip'])) {
            $ban_ip = str_replace('255', '*', decode_ip($banlist[$i]['ban_ip']));

            $select_iplist .= '<option value="' . $ban_id . '">' . $ban_ip . '</option>';

            $ipban_count++;
        } elseif (!empty($banlist[$i]['ban_email'])) {
            $ban_email = $banlist[$i]['ban_email'];

            $select_emaillist .= '<option value="' . $ban_id . '">' . $ban_email . '</option>';

            $emailban_count++;
        }
    }

    if ('' == $select_iplist) {
        $select_iplist = '<option value="-1">' . $lang['No_banned_ip'] . '</option>';
    }

    if ('' == $select_emaillist) {
        $select_emaillist = '<option value="-1">' . $lang['No_banned_email'] . '</option>';
    }

    $select_iplist = '<select name="unban_ip[]" multiple="multiple" size="5">' . $select_iplist . '</select>';

    $select_emaillist = '<select name="unban_email[]" multiple="multiple" size="5">' . $select_emaillist . '</select>';

    $template->assign_vars(
        [
            'L_UNBAN_USER' => $lang['Unban_username'],
'L_UNBAN_USER_EXPLAIN' => $lang['Unban_username_explain'],
'L_UNBAN_IP' => $lang['Unban_IP'],
'L_UNBAN_IP_EXPLAIN' => $lang['Unban_IP_explain'],
'L_UNBAN_EMAIL' => $lang['Unban_email'],
'L_UNBAN_EMAIL_EXPLAIN' => $lang['Unban_email_explain'],
'L_USERNAME' => $lang['Username'],
'L_LOOK_UP' => $lang['Look_up_User'],
'L_FIND_USERNAME' => $lang['Find_username'],
'U_SEARCH_USER' => append_sid("./../search.$phpEx?mode=searchuser"),
'S_UNBAN_USERLIST_SELECT' => $select_userlist,
'S_UNBAN_IPLIST_SELECT' => $select_iplist,
'S_UNBAN_EMAILLIST_SELECT' => $select_emaillist,
'S_BAN_ACTION' => append_sid("admin_user_ban.$phpEx"),
        ]
    );
}
$template->pparse('body');
require __DIR__ . '/page_footer_admin.' . $phpEx;
