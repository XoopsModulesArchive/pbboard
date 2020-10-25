<?php

/***************************************************************************
 * admin_forumauth.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: admin_forumauth.php,v 1.23.2.4 2002/05/21 16:52:08 psotfx Exp $
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

    $module['Forums']['Permissions'] = $filename;

    return;
}
//
// Load default header
//
$no_page_header = true;
$phpbb_root_path = './../';
require $phpbb_root_path . 'extension.inc';
require __DIR__ . '/pagestart.' . $phpEx;
//
// Start program - define vars
//
// View Read Post Reply Edit Delete Sticky Announce Vote Poll
$simple_auth_ary = [
    0 => [AUTH_ALL, AUTH_ALL, AUTH_ALL, AUTH_ALL, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG],
1 => [AUTH_ALL, AUTH_ALL, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG],
2 => [AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_REG, AUTH_MOD, AUTH_MOD, AUTH_REG, AUTH_REG],
3 => [AUTH_ALL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_ACL, AUTH_ACL],
4 => [AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_ACL, AUTH_MOD, AUTH_ACL, AUTH_ACL],
5 => [AUTH_ALL, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD],
6 => [AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD, AUTH_MOD],
];
$simple_auth_types = [$lang['Public'], $lang['Registered'], $lang['Registered'] . ' [' . $lang['Hidden'] . ']', $lang['Private'], $lang['Private'] . ' [' . $lang['Hidden'] . ']', $lang['Moderators'], $lang['Moderators'] . ' [' . $lang['Hidden'] . ']'];
$forum_auth_fields = ['auth_view', 'auth_read', 'auth_post', 'auth_reply', 'auth_edit', 'auth_delete', 'auth_sticky', 'auth_announce', 'auth_vote', 'auth_pollcreate'];
$field_names = [
    'auth_view' => $lang['View'],
'auth_read' => $lang['Read'],
'auth_post' => $lang['Post'],
'auth_reply' => $lang['Reply'],
'auth_edit' => $lang['Edit'],
'auth_delete' => $lang['Delete'],
'auth_sticky' => $lang['Sticky'],
'auth_announce' => $lang['Announce'],
'auth_vote' => $lang['Vote'],
'auth_pollcreate' => $lang['Pollcreate'],
];
$forum_auth_levels = ['ALL', 'REG', 'PRIVATE', 'MOD', 'ADMIN'];
$forum_auth_const = [AUTH_ALL, AUTH_REG, AUTH_ACL, AUTH_MOD, AUTH_ADMIN];
attach_setup_forum_auth($simple_auth_ary, $forum_auth_fields, $field_names);
if (isset($_GET[POST_FORUM_URL]) || isset($_POST[POST_FORUM_URL])) {
    $forum_id = (isset($_POST[POST_FORUM_URL])) ? (int)$_POST[POST_FORUM_URL] : (int)$_GET[POST_FORUM_URL];

    $forum_sql = "AND forum_id = $forum_id";
} else {
    unset($forum_id);

    $forum_sql = '';
}
if (isset($_GET['adv'])) {
    $adv = (int)$_GET['adv'];
} else {
    unset($adv);
}
//
// Start program proper
//
if (isset($_POST['submit'])) {
    $sql = '';

    if (!empty($forum_id)) {
        if (isset($_POST['simpleauth'])) {
            $simple_ary = $simple_auth_ary[$_POST['simpleauth']];

            for ($i = 0, $iMax = count($simple_ary); $i < $iMax; $i++) {
                $sql .= (('' != $sql) ? ', ' : '') . $forum_auth_fields[$i] . ' = ' . $simple_ary[$i];
            }

            $sql = 'UPDATE ' . FORUMS_TABLE . " SET $sql WHERE forum_id = $forum_id";
        } else {
            for ($i = 0, $iMax = count($forum_auth_fields); $i < $iMax; $i++) {
                $value = $_POST[$forum_auth_fields[$i]];

                if ('auth_vote' == $forum_auth_fields[$i]) {
                    if (AUTH_ALL == $_POST['auth_vote']) {
                        $value = AUTH_REG;
                    }
                }

                $sql .= (('' != $sql) ? ', ' : '') . $forum_auth_fields[$i] . ' = ' . $value;
            }

            $sql = 'UPDATE ' . FORUMS_TABLE . " SET $sql WHERE forum_id = $forum_id";
        }

        if ('' != $sql) {
            if (!$db->sql_query($sql)) {
                message_die(GENERAL_ERROR, 'Could not update auth table', '', __LINE__, __FILE__, $sql);
            }
        }

        $forum_sql = '';

        $adv = 0;
    }

    $template->assign_vars(
        [
            'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid("admin_forumauth.$phpEx?" . POST_FORUM_URL . "=$forum_id") . '">',
        ]
    );

    $message = $lang['Forum_auth_updated'] . '<br><br>' . sprintf($lang['Click_return_forumauth'], '<a href="' . append_sid("admin_forumauth.$phpEx") . '">', '</a>');

    message_die(GENERAL_MESSAGE, $message);
} // End of submit
//
// Get required information, either all forums if
// no id was specified or just the requsted if it
// was
//
$sql = 'SELECT f.*
FROM ' . FORUMS_TABLE . ' f, ' . CATEGORIES_TABLE . " c
WHERE c.cat_id = f.cat_id
$forum_sql
ORDER BY c.cat_order ASC, f.forum_order ASC";
if (!($result = $db->sql_query($sql))) {
    message_die(GENERAL_ERROR, "Couldn't obtain forum list", '', __LINE__, __FILE__, $sql);
}
$forum_rows = $db->sql_fetchrowset($result);
$db->sql_freeresult($result);
if (empty($forum_id)) {
    // Output the selection table if no forum id was

    // specified

    $template->set_filenames(
        [
            'body' => 'admin/auth_select_body.tpl',
        ]
    );

    $select_list = '<select name="' . POST_FORUM_URL . '">';

    for ($i = 0, $iMax = count($forum_rows); $i < $iMax; $i++) {
        $select_list .= '<option value="' . $forum_rows[$i]['forum_id'] . '">' . $forum_rows[$i]['forum_name'] . '</option>';
    }

    $select_list .= '</select>';

    $template->assign_vars(
        [
            'L_AUTH_TITLE' => $lang['Auth_Control_Forum'],
'L_AUTH_EXPLAIN' => $lang['Forum_auth_explain'],
'L_AUTH_SELECT' => $lang['Select_a_Forum'],
'L_LOOK_UP' => $lang['Look_up_Forum'],
'S_AUTH_ACTION' => append_sid("admin_forumauth.$phpEx"),
'S_AUTH_SELECT' => $select_list,
        ]
    );
} else {
    // Output the authorisation details if an id was

    // specified

    $template->set_filenames(
        [
            'body' => 'admin/auth_forum_body.tpl',
        ]
    );

    $forum_name = $forum_rows[0]['forum_name'];

    @reset($simple_auth_ary);

    while (list($key, $auth_levels) = each($simple_auth_ary)) {
        $matched = 1;

        for ($k = 0, $kMax = count($auth_levels); $k < $kMax; $k++) {
            $matched_type = $key;

            if ($forum_rows[0][$forum_auth_fields[$k]] != $auth_levels[$k]) {
                $matched = 0;
            }
        }

        if ($matched) {
            break;
        }
    }

    // If we didn't get a match above then we

    // automatically switch into 'advanced' mode

    if (!isset($adv) && !$matched) {
        $adv = 1;
    }

    0 == $s_column_span;

    if (empty($adv)) {
        $simple_auth = '<select name="simpleauth">';

        for ($j = 0, $jMax = count($simple_auth_types); $j < $jMax; $j++) {
            $selected = ($matched_type == $j) ? ' selected="selected"' : '';

            $simple_auth .= '<option value="' . $j . '"' . $selected . '>' . $simple_auth_types[$j] . '</option>';
        }

        $simple_auth .= '</select>';

        $template->assign_block_vars(
            'forum_auth_titles',
            [
                'CELL_TITLE' => $lang['Simple_mode'],
            ]
        );

        $template->assign_block_vars(
            'forum_auth_data',
            [
                'S_AUTH_LEVELS_SELECT' => $simple_auth,
            ]
        );

        $s_column_span++;
    } else {
        // Output values of individual

        // fields

        for ($j = 0, $jMax = count($forum_auth_fields); $j < $jMax; $j++) {
            $custom_auth[$j] = '&nbsp;<select name="' . $forum_auth_fields[$j] . '">';

            for ($k = 0, $kMax = count($forum_auth_levels); $k < $kMax; $k++) {
                $selected = ($forum_rows[0][$forum_auth_fields[$j]] == $forum_auth_const[$k]) ? ' selected="selected"' : '';

                $custom_auth[$j] .= '<option value="' . $forum_auth_const[$k] . '"' . $selected . '>' . $lang['Forum_' . $forum_auth_levels[$k]] . '</option>';
            }

            $custom_auth[$j] .= '</select>&nbsp;';

            $cell_title = $field_names[$forum_auth_fields[$j]];

            $template->assign_block_vars(
                'forum_auth_titles',
                [
                    'CELL_TITLE' => $cell_title,
                ]
            );

            $template->assign_block_vars(
                'forum_auth_data',
                [
                    'S_AUTH_LEVELS_SELECT' => $custom_auth[$j],
                ]
            );

            $s_column_span++;
        }
    }

    $adv_mode = (empty($adv)) ? '1' : '0';

    $switch_mode = append_sid("admin_forumauth.$phpEx?" . POST_FORUM_URL . '=' . $forum_id . '&adv=' . $adv_mode);

    $switch_mode_text = (empty($adv)) ? $lang['Advanced_mode'] : $lang['Simple_mode'];

    $u_switch_mode = '<a href="' . $switch_mode . '">' . $switch_mode_text . '</a>';

    $s_hidden_fields = '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '">';

    $template->assign_vars(
        [
            'FORUM_NAME' => $forum_name,
'L_FORUM' => $lang['Forum'],
'L_AUTH_TITLE' => $lang['Auth_Control_Forum'],
'L_AUTH_EXPLAIN' => $lang['Forum_auth_explain'],
'L_SUBMIT' => $lang['Submit'],
'L_RESET' => $lang['Reset'],
'U_SWITCH_MODE' => $u_switch_mode,
'S_FORUMAUTH_ACTION' => append_sid("admin_forumauth.$phpEx"),
'S_COLUMN_SPAN' => $s_column_span,
'S_HIDDEN_FIELDS' => $s_hidden_fields,
        ]
    );
}
require __DIR__ . '/page_header_admin.' . $phpEx;
$template->pparse('body');
require __DIR__ . '/page_footer_admin.' . $phpEx;
