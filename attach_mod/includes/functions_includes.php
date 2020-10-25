<?php

/***************************************************************************
 * functions_includes.php
 * -------------------
 * begin : Sunday, Mar 31, 2002
 * copyright : (C) 2002 Meik Sievertsen
 * email : acyd.burn@gmx.de
 *
 * $Id: functions_includes.php,v 1.30 2005/06/19 16:39:41 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 **************************************************************************
 * @param $query
 * @return false|resource
 */
//
// These are functions called directly from phpBB2 Files
//
//
// This function is used to count the queries executed by the Attachment Mod
//
function attach_sql_query($query)
{
    global $db, $dbms, $attach_num_queries;

    $attach_num_queries++;

    if (defined('ATTACH_QUERY_DEBUG')) {
        global $attach_sql_report, $attach_sql_time, $starttime;

        $curtime = explode(' ', microtime());

        $curtime = $curtime[0] + $curtime[1] - $starttime;
    }

    $attach_result = $db->sql_query($query);

    if (defined('ATTACH_QUERY_DEBUG')) {
        $endtime = explode(' ', microtime());

        $endtime = $endtime[0] + $endtime[1] - $starttime;

        $attach_sql_report .= "<pre>Query:\t" . preg_replace('/[\s]*[\n\r\t]+[\n\r\s\t]*/', "\n\t", $query) . "\n\n";

        $affected_rows = $db->sql_affectedrows($result);

        if ($attach_result) {
            $attach_sql_report .= "Time before: $curtime\nTime after: $endtime\nElapsed time: <b>" . ($endtime - $curtime) . "</b>\n</pre>";
        } else {
            $error = $db->sql_error();

            $attach_sql_report .= '<b>FAILED</b> - MySQL Error ' . $error['code'] . ': ' . $error['message'] . '<br><br><pre>';
        }

        $attach_sql_time += $endtime - $curtime;

        if (('mysql' == $dbms) || ('mysql4' == $dbms)) {
            if (0 === strpos($query, "SELECT")) {
                $html_table = false;

                if ($result = $GLOBALS['xoopsDB']->queryF("EXPLAIN $query", $db->db_connect_id)) {
                    $i = 0;

                    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                        $extra = array_pop($row);

                        if (!$html_table && count($row)) {
                            $html_table = true;

                            $attach_sql_report .= "<table width=100% border=1 cellpadding=2 cellspacing=1>\n";

                            $attach_sql_report .= "<tr>\n<td><b>" . implode("</b></td>\n<td><b>", array_keys($row));

                            $attach_sql_report .= "</b></td>\n<td><b>affected_rows";

                            $attach_sql_report .= "</b></td>\n<td><b>Extra" . "</b></td>\n</tr>\n";
                        }

                        $attach_sql_report .= "<tr>\n<td>" . implode("&nbsp;</td>\n<td>", array_values($row));

                        $attach_sql_report .= "&nbsp;</td>\n<td>" . ((0 == $i) ? $affected_rows : '');

                        $attach_sql_report .= "&nbsp;</td>\n<td>" . $extra . "&nbsp;</td>\n</tr>\n";

                        $i++;
                    }
                }

                if ($html_table) {
                    $attach_sql_report .= '</table><br>';
                }
            }

            $attach_sql_report .= "<HR>\n";
        }
    }

    return ($attach_result);
}

//
// Include the FAQ-File (faq.php)
//
function attach_faq_include($lang_file)
{
    global $phpbb_root_path, $board_config, $phpEx, $faq, $attach_config;

    if ((int)$attach_config['disable_mod']) {
        return;
    }

    if ('lang_faq' == $lang_file) {
        if (!@file_exists(@amod_realpath($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_faq_attach.' . $phpEx))) {
            include $phpbb_root_path . 'language/lang_english/lang_faq_attach.' . $phpEx;
        } else {
            include $phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_faq_attach.' . $phpEx;
        }
    }
}

//
// Setup Basic Authentication (auth.php)
//
function attach_setup_basic_auth($type, &$auth_fields, &$a_sql)
{
    switch ($type) {
        case AUTH_ALL:
            $a_sql .= ', a.auth_attachments, a.auth_download';
            $auth_fields[] = 'auth_attachments';
            $auth_fields[] = 'auth_download';
            break;
        case AUTH_ATTACH:
            $a_sql = 'a.auth_attachments';
            $auth_fields = ['auth_attachments'];
            break;
        case AUTH_DOWNLOAD:
            $a_sql = 'a.auth_download';
            $auth_fields = ['auth_download'];
            break;
        default:
            break;
    }
}

//
// Setup Forum Authentication (admin_forumauth.php)
//
function attach_setup_forum_auth(&$simple_auth_ary, &$forum_auth_fields, &$field_names)
{
    global $lang;

    // Add Attachment Auth

    // Post Attachments

    $simple_auth_ary[0][] = AUTH_MOD;

    $simple_auth_ary[1][] = AUTH_MOD;

    $simple_auth_ary[2][] = AUTH_MOD;

    $simple_auth_ary[3][] = AUTH_MOD;

    $simple_auth_ary[4][] = AUTH_MOD;

    $simple_auth_ary[5][] = AUTH_MOD;

    $simple_auth_ary[6][] = AUTH_MOD;

    // Download Attachments

    $simple_auth_ary[0][] = AUTH_ALL;

    $simple_auth_ary[1][] = AUTH_ALL;

    $simple_auth_ary[2][] = AUTH_REG;

    $simple_auth_ary[3][] = AUTH_ACL;

    $simple_auth_ary[4][] = AUTH_ACL;

    $simple_auth_ary[5][] = AUTH_MOD;

    $simple_auth_ary[6][] = AUTH_MOD;

    $forum_auth_fields[] = 'auth_attachments';

    $field_names['auth_attachments'] = $lang['Auth_attach'];

    $forum_auth_fields[] = 'auth_download';

    $field_names['auth_download'] = $lang['Auth_download'];
}

//
// Setup Usergroup Authentication (admin_ug_auth.php)
//
function attach_setup_usergroup_auth(&$forum_auth_fields, &$auth_field_match, &$field_names)
{
    global $lang;

    // Post Attachments

    $forum_auth_fields[] = 'auth_attachments';

    $auth_field_match['auth_attachments'] = AUTH_ATTACH;

    $field_names['auth_attachments'] = $lang['Auth_attach'];

    // Download Attachments

    $forum_auth_fields[] = 'auth_download';

    $auth_field_match['auth_download'] = AUTH_DOWNLOAD;

    $field_names['auth_download'] = $lang['Auth_download'];
}

//
// Setup Viewtopic Authentication for f_access
//
function attach_setup_viewtopic_auth(&$order_sql, &$sql)
{
    $order_sql = str_replace('f.auth_attachments', 'f.auth_attachments, f.auth_download, t.topic_attachment', $order_sql);

    $sql = str_replace('f.auth_attachments', 'f.auth_attachments, f.auth_download, t.topic_attachment', $sql);
}

//
// Setup s_auth_can in viewforum and viewtopic
//
function attach_build_auth_levels($is_auth, &$s_auth_can)
{
    global $lang, $attach_config, $phpEx, $forum_id;

    if ((int)$attach_config['disable_mod']) {
        return;
    }

    // If you want to have the rules window link within the forum view too, comment out the two lines, and comment the third line

    // $rules_link = '(<a href="attach_mod/attach_rules.' . $phpEx . '?f=' . $forum_id . '" target="_blank">Rules</a>)';

    // $s_auth_can .= ( ( $is_auth['auth_attachments'] ) ? $rules_link . ' ' . $lang['Rules_attach_can'] : $lang['Rules_attach_cannot'] ) . '<br>';

    $s_auth_can .= (($is_auth['auth_attachments'] && $is_auth['auth_post']) ? $lang['Rules_attach_can'] : $lang['Rules_attach_cannot']) . '<br>';

    $s_auth_can .= (($is_auth['auth_download']) ? $lang['Rules_download_can'] : $lang['Rules_download_cannot']) . '<br>';
}

//
// Called from admin_users.php and admin_groups.php in order to process Quota Settings
//
function attachment_quota_settings($admin_mode, $submit, $mode)
{
    global $template, $db, $_POST, $_GET, $lang, $group_id, $lang, $phpbb_root_path, $phpEx, $attach_config;

    @require_once $phpbb_root_path . 'attach_mod/includes/constants.' . $phpEx;

    if (!(int)$attach_config['allow_ftp_upload']) {
        if (('/' == $attach_config['upload_dir'][0]) || (('/' != $attach_config['upload_dir'][0]) && (':' == $attach_config['upload_dir'][1]))) {
            $upload_dir = $attach_config['upload_dir'];
        } else {
            $upload_dir = '../' . $attach_config['upload_dir'];
        }
    } else {
        $upload_dir = $attach_config['download_path'];
    }

    include $phpbb_root_path . 'attach_mod/includes/functions_selects.' . $phpEx;

    include $phpbb_root_path . 'attach_mod/includes/functions_admin.' . $phpEx;

    if ('user' == $admin_mode) {
        $submit = (isset($_POST['submit'])) ? true : false;

        if (!$submit && 'save' != $mode) {
            if (isset($_GET[POST_USERS_URL]) || isset($_POST[POST_USERS_URL])) {
                $user_id = (isset($_POST[POST_USERS_URL])) ? (int)$_POST[POST_USERS_URL] : (int)$_GET[POST_USERS_URL];

                $this_userdata['user_id'] = $user_id;

                if (empty($user_id)) {
                    message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
                }
            } else {
                $u_name = (isset($_POST['username'])) ? htmlspecialchars(trim($_POST['username']), ENT_QUOTES | ENT_HTML5) : htmlspecialchars(trim($_GET['username']), ENT_QUOTES | ENT_HTML5);

                $this_userdata = get_userdata($u_name);

                if (!$this_userdata) {
                    message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
                }
            }

            $user_id = (int)$this_userdata['user_id'];
        } else {
            $user_id = (isset($_POST['id'])) ? (int)$_POST['id'] : (int)$_GET['id'];

            if (empty($user_id)) {
                message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
            }
        }
    }

    if ('user' == $admin_mode && !$submit && 'save' != $mode) {
        // Show the contents

        $sql = 'SELECT quota_limit_id, quota_type FROM ' . QUOTA_TABLE . ' WHERE user_id = ' . $user_id;

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Unable to get Quota Settings', '', __LINE__, __FILE__, $sql);
        }

        $pm_quota = -1;

        $upload_quota = -1;

        while (false !== ($row = $db->sql_fetchrow($result))) {
            if (QUOTA_UPLOAD_LIMIT == $row['quota_type']) {
                $upload_quota = $row['quota_limit_id'];
            } elseif (QUOTA_PM_LIMIT == $row['quota_type']) {
                $pm_quota = $row['quota_limit_id'];
            }
        }

        $template->assign_vars(
            [
                'S_SELECT_UPLOAD_QUOTA' => quota_limit_select('user_upload_quota', $upload_quota),
'S_SELECT_PM_QUOTA' => quota_limit_select('user_pm_quota', $pm_quota),
'L_UPLOAD_QUOTA' => $lang['Upload_quota'],
'L_PM_QUOTA' => $lang['Pm_quota'],
            ]
        );
    }

    if ('user' == $admin_mode && $submit && $_POST['deleteuser']) {
        process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, -1);

        process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, -1);
    } elseif ('user' == $admin_mode && $submit && 'save' == $mode) {
        // Get the contents

        $upload_quota = (int)$_POST['user_upload_quota'];

        $pm_quota = (int)$_POST['user_pm_quota'];

        if ($upload_quota <= 0) {
            process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, -1);
        } else {
            process_quota_settings($admin_mode, $user_id, QUOTA_UPLOAD_LIMIT, $upload_quota);
        }

        if ($pm_quota <= 0) {
            process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, -1);
        } else {
            process_quota_settings($admin_mode, $user_id, QUOTA_PM_LIMIT, $pm_quota);
        }
    }

    if ('group' == $admin_mode && 'newgroup' == $mode) {
        return;
    } elseif ('group' == $admin_mode) {
        // Get group id again, we do not trust phpBB here, Mods may be installed ;)

        if (isset($_POST[POST_GROUPS_URL]) || isset($_GET[POST_GROUPS_URL])) {
            $group_id = (isset($_POST[POST_GROUPS_URL])) ? (int)$_POST[POST_GROUPS_URL] : (int)$_GET[POST_GROUPS_URL];
        } else {
            // This should not occur :(

            $group_id = '';
        }
    }

    if ('group' == $admin_mode && !$submit && isset($_POST['edit'])) {
        // Show the contents

        $sql = 'SELECT quota_limit_id, quota_type FROM ' . QUOTA_TABLE . ' WHERE group_id = ' . $group_id;

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Unable to get Quota Settings', '', __LINE__, __FILE__, $sql);
        }

        $pm_quota = -1;

        $upload_quota = -1;

        while (false !== ($row = $db->sql_fetchrow($result))) {
            if (QUOTA_UPLOAD_LIMIT == $row['quota_type']) {
                $upload_quota = $row['quota_limit_id'];
            } elseif (QUOTA_PM_LIMIT == $row['quota_type']) {
                $pm_quota = $row['quota_limit_id'];
            }
        }

        $template->assign_vars(
            [
                'S_SELECT_UPLOAD_QUOTA' => quota_limit_select('group_upload_quota', $upload_quota),
'S_SELECT_PM_QUOTA' => quota_limit_select('group_pm_quota', $pm_quota),
'L_UPLOAD_QUOTA' => $lang['Upload_quota'],
'L_PM_QUOTA' => $lang['Pm_quota'],
            ]
        );
    }

    if ('group' == $admin_mode && $submit && isset($_POST['group_delete'])) {
        process_quota_settings($admin_mode, $group_id, QUOTA_UPLOAD_LIMIT, -1);

        process_quota_settings($admin_mode, $group_id, QUOTA_PM_LIMIT, -1);
    } elseif ('group' == $admin_mode && $submit) {
        // Get the contents

        $upload_quota = (int)$_POST['group_upload_quota'];

        $pm_quota = (int)$_POST['group_pm_quota'];

        if ($upload_quota <= 0) {
            process_quota_settings($admin_mode, $group_id, QUOTA_UPLOAD_LIMIT, -1);
        } else {
            process_quota_settings($admin_mode, $group_id, QUOTA_UPLOAD_LIMIT, $upload_quota);
        }

        if ($pm_quota <= 0) {
            process_quota_settings($admin_mode, $group_id, QUOTA_PM_LIMIT, -1);
        } else {
            process_quota_settings($admin_mode, $group_id, QUOTA_PM_LIMIT, $pm_quota);
        }
    }
}

//
// Called from usercp_viewprofile, displays the User Upload Quota Box, Upload Stats and a Link to the User Attachment Control Panel
// Groups are able to be grabbed, but it's not used within the Attachment Mod. ;)
//
function display_upload_attach_box_limits($user_id, $group_id = -1)
{
    global $attach_config, $board_config, $phpbb_root_path, $lang, $db, $template, $phpEx, $userdata, $profiledata;

    if ((ADMIN != $userdata['user_level']) && ($userdata['uid'] != $user_id)) {
        return;
    }

    // Return if the user is not within the to be listed Group

    if (-1 != $group_id) {
        if (!user_in_group($user_id, $group_id)) {
            return;
        }
    }

    $attachments = new attach_posting();

    $attachments->PAGE = PAGE_INDEX;

    // Get the assigned Quota Limit. For Groups, we are directly getting the value, because this Quota can change from user to user.

    if (-1 != $group_id) {
        $sql = 'SELECT l.quota_limit FROM ' . QUOTA_TABLE . ' q, ' . QUOTA_LIMITS_TABLE . ' l
WHERE (q.group_id = ' . $group_id . ') AND (q.quota_type = ' . QUOTA_UPLOAD_LIMIT . ') 
AND (q.quota_limit_id = l.quota_limit_id) LIMIT 1';

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Could not get Group Quota', '', __LINE__, __FILE__, $sql);
        }

        if ($db->sql_numrows($result) > 0) {
            $row = $db->sql_fetchrow($result);

            $attach_config['upload_filesize_limit'] = (int)$row['quota_limit'];
        } else {
            // Set Default Quota Limit

            $quota_id = (int)$attach_config['default_upload_quota'];

            if (0 == $quota_id) {
                $attach_config['upload_filesize_limit'] = (int)$attach_config['attachment_quota'];
            } else {
                $sql = 'SELECT quota_limit FROM ' . QUOTA_LIMITS_TABLE . '
WHERE quota_limit_id = ' . $quota_id . ' LIMIT 1';

                if (!($result = attach_sql_query($sql))) {
                    message_die(GENERAL_ERROR, 'Could not get Quota Limit', '', __LINE__, __FILE__, $sql);
                }

                if ($db->sql_numrows($result) > 0) {
                    $row = $db->sql_fetchrow($result);

                    $attach_config['upload_filesize_limit'] = (int)$row['quota_limit'];
                } else {
                    $attach_config['upload_filesize_limit'] = (int)$attach_config['attachment_quota'];
                }
            }
        }
    } else {
        if (is_array($profiledata)) {
            $attachments->get_quota_limits($profiledata, $user_id);
        } else {
            $attachments->get_quota_limits($userdata, $user_id);
        }
    }

    if (0 == (int)$attach_config['upload_filesize_limit']) {
        $upload_filesize_limit = (int)$attach_config['attachment_quota'];
    } else {
        $upload_filesize_limit = (int)$attach_config['upload_filesize_limit'];
    }

    if (0 == $upload_filesize_limit) {
        $user_quota = $lang['Unlimited'];
    } else {
        $size_lang = ($upload_filesize_limit >= 1048576) ? $lang['MB'] : (($upload_filesize_limit >= 1024) ? $lang['KB'] : $lang['Bytes']);

        if ($upload_filesize_limit >= 1048576) {
            $user_quota = (round($upload_filesize_limit / 1048576 * 100) / 100) . ' ' . $size_lang;
        } elseif ($upload_filesize_limit >= 1024) {
            $user_quota = (round($upload_filesize_limit / 1024 * 100) / 100) . ' ' . $size_lang;
        } else {
            $user_quota = ($upload_filesize_limit) . ' ' . $size_lang;
        }
    }

    // Get all attach_id's the specific user posted, but only uploads to the board and not Private Messages

    $sql = 'SELECT attach_id 
FROM ' . ATTACHMENTS_TABLE . '
WHERE (user_id_1 = ' . $user_id . ') AND (privmsgs_id = 0)
GROUP BY attach_id';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
    }

    $attach_ids = $db->sql_fetchrowset($result);

    $num_attach_ids = $db->sql_numrows($result);

    $attach_id = [];

    for ($j = 0; $j < $num_attach_ids; $j++) {
        $attach_id[] = (int)$attach_ids[$j]['attach_id'];
    }

    $upload_filesize = (count($attach_id) > 0) ? get_total_attach_filesize(implode(',', $attach_id)) : 0;

    $size_lang = ($upload_filesize >= 1048576) ? $lang['MB'] : (($upload_filesize >= 1024) ? $lang['KB'] : $lang['Bytes']);

    if ($upload_filesize >= 1048576) {
        $user_uploaded = (round($upload_filesize / 1048576 * 100) / 100) . ' ' . $size_lang;
    } elseif ($upload_filesize >= 1024) {
        $user_uploaded = (round($upload_filesize / 1024 * 100) / 100) . ' ' . $size_lang;
    } else {
        $user_uploaded = ($upload_filesize) . ' ' . $size_lang;
    }

    $upload_limit_pct = ($upload_filesize_limit > 0) ? round(($upload_filesize / $upload_filesize_limit) * 100) : 0;

    $upload_limit_img_length = ($upload_filesize_limit > 0) ? round(($upload_filesize / $upload_filesize_limit) * $board_config['privmsg_graphic_length']) : 0;

    if ($upload_limit_pct > 100) {
        $upload_limit_img_length = $board_config['privmsg_graphic_length'];
    }

    $upload_limit_remain = ($upload_filesize_limit > 0) ? $upload_filesize_limit - $upload_filesize : 100;

    $l_box_size_status = sprintf($lang['Upload_percent_profile'], $upload_limit_pct);

    $template->assign_block_vars('switch_upload_limits', []);

    $template->assign_vars(
        [
            'L_UACP' => $lang['UACP'],
'L_UPLOAD_QUOTA' => $lang['Upload_quota'],
'U_UACP' => append_sid($phpbb_root_path . 'uacp.' . $phpEx . '?u=' . $user_id . '&amp;sid=' . $userdata['sess_id']),
'UPLOADED' => sprintf($lang['User_uploaded_profile'], $user_uploaded),
'QUOTA' => sprintf($lang['User_quota_profile'], $user_quota),
'UPLOAD_LIMIT_IMG_WIDTH' => $upload_limit_img_length,
'UPLOAD_LIMIT_PERCENT' => $upload_limit_pct,
'PERCENT_FULL' => $l_box_size_status,
        ]
    );
}

//
// Function responsible for viewonline (within viewonline.php and the admin index page)
//
// added directly after the switch statement
// viewonline.php:
// perform_attach_pageregister($row['session_page']);
// admin/index.php:
// perform_attach_pageregister($onlinerow_reg[$i]['user_session_page'], TRUE);
// perform_attach_pageregister($onlinerow_guest[$i]['session_page'], TRUE);
//
function perform_attach_pageregister($session_page, $in_admin = false)
{
    global $location, $location_url, $lang;

    switch ($session_page) {
        case (PAGE_UACP):
            $location = $lang['User_acp_title'];
            $location_url = ($in_admin) ? "index.$phpEx?pane=right" : "index.$phpEx";
            break;
        case (PAGE_RULES):
            $location = $lang['Rules_page'];
            $location_url = ($in_admin) ? "index.$phpEx?pane=right" : "index.$phpEx";
            break;
    }
}
