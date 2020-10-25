<?php

/***************************************************************************
 * memberlist.php
 * -------------------
 * begin : Friday, May 11, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: memberlist.php,v 1.36.2.8 2005/06/09 13:06:19 psotfx Exp $
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
$userdata = session_pagestart($user_ip, PAGE_VIEWMEMBERS);
init_userprefs($userdata);
//
// End session management
//
$start = (isset($_GET['start'])) ? (int)$_GET['start'] : 0;
if (isset($_GET['mode']) || isset($_POST['mode'])) {
    $mode = (isset($_POST['mode'])) ? htmlspecialchars($_POST['mode'], ENT_QUOTES | ENT_HTML5) : htmlspecialchars($_GET['mode'], ENT_QUOTES | ENT_HTML5);
} else {
    $mode = 'joined';
}
if (isset($_POST['order'])) {
    $sort_order = ('ASC' == $_POST['order']) ? 'ASC' : 'DESC';
} elseif (isset($_GET['order'])) {
    $sort_order = ('ASC' == $_GET['order']) ? 'ASC' : 'DESC';
} else {
    $sort_order = 'ASC';
}
//
// Memberlist sorting
//
$mode_types_text = [$lang['Sort_Joined'], $lang['Sort_Username'], $lang['Sort_Location'], $lang['Sort_Posts'], $lang['Sort_Email'], $lang['Sort_Website'], $lang['Sort_Top_Ten']];
$mode_types = ['joindate', 'username', 'location', 'posts', 'email', 'website', 'topten'];
$select_sort_mode = '<select name="mode">';
for ($i = 0, $iMax = count($mode_types_text); $i < $iMax; $i++) {
    $selected = ($mode == $mode_types[$i]) ? ' selected="selected"' : '';

    $select_sort_mode .= '<option value="' . $mode_types[$i] . '"' . $selected . '>' . $mode_types_text[$i] . '</option>';
}
$select_sort_mode .= '</select>';
$select_sort_order = '<select name="order">';
if ('ASC' == $sort_order) {
    $select_sort_order .= '<option value="ASC" selected="selected">' . $lang['Sort_Ascending'] . '</option><option value="DESC">' . $lang['Sort_Descending'] . '</option>';
} else {
    $select_sort_order .= '<option value="ASC">' . $lang['Sort_Ascending'] . '</option><option value="DESC" selected="selected">' . $lang['Sort_Descending'] . '</option>';
}
$select_sort_order .= '</select>';
//
// Generate page
//
$page_title = $lang['Memberlist'];
include $phpbb_root_path . 'includes/page_header.' . $phpEx;
$template->set_filenames(
    [
        'body' => 'memberlist_body.tpl',
    ]
);
make_jumpbox('viewforum.' . $phpEx);
$template->assign_vars(
    [
        'L_SELECT_SORT_METHOD' => $lang['Select_sort_method'],
'L_EMAIL' => $lang['Email'],
'L_WEBSITE' => $lang['Website'],
'L_FROM' => $lang['Location'],
'L_ORDER' => $lang['Order'],
'L_SORT' => $lang['Sort'],
'L_SUBMIT' => $lang['Sort'],
'L_AIM' => $lang['AIM'],
'L_YIM' => $lang['YIM'],
'L_MSNM' => $lang['MSNM'],
'L_ICQ' => $lang['ICQ'],
'L_JOINED' => $lang['Joined'],
'L_POSTS' => $lang['Posts'],
'L_PM' => $lang['Private_Message'],
'S_MODE_SELECT' => $select_sort_mode,
'S_ORDER_SELECT' => $select_sort_order,
'S_MODE_ACTION' => append_sid("memberlist.$phpEx"),
    ]
);
switch ($mode) {
    case 'joined':
        $order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'username':
        $order_by = "uname $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'location':
        $order_by = "user_from $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'posts':
        $order_by = "posts $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'email':
        $order_by = "email $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'website':
        $order_by = "url $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
    case 'topten':
        $order_by = "posts $sort_order LIMIT 10";
        break;
    default:
        $order_by = "user_regdate $sort_order LIMIT $start, " . $board_config['topics_per_page'];
        break;
}
$sql = 'SELECT uname, uid, user_viewemail, posts, user_regdate, user_from, url, email, user_icq, user_aim, user_yim, user_msnm, user_avatar, user_avatar_type, user_allowavatar
FROM ' . USERS_TABLE . '
WHERE uid <> ' . ANONYMOUS . "
ORDER BY $order_by";
if (!($result = $db->sql_query($sql))) {
    message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
}
if ($row = $db->sql_fetchrow($result)) {
    $i = 0;

    do {
        $username = $row['uname'];

        $user_id = $row['uid'];

        $from = (!empty($row['user_from'])) ? $row['user_from'] : '&nbsp;';

        $joined = create_date($lang['DATE_FORMAT'], $row['user_regdate'], $board_config['board_timezone']);

        $posts = ($row['posts']) ?: 0;

        $poster_avatar = '';

        if ($row['user_avatar_type'] && ANONYMOUS != $user_id && $row['user_allowavatar']) {
            switch ($row['user_avatar_type']) {
                case USER_AVATAR_UPLOAD:
                    $poster_avatar = ($board_config['allow_avatar_upload']) ? '<img src="' . $board_config['avatar_path'] . '/' . $row['user_avatar'] . '" alt="" border="0">' : '';
                    break;
                case USER_AVATAR_REMOTE:
                    $poster_avatar = ($board_config['allow_avatar_remote']) ? '<img src="' . $row['user_avatar'] . '" alt="" border="0">' : '';
                    break;
                case USER_AVATAR_GALLERY:
                    $poster_avatar = ($board_config['allow_avatar_local']) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $row['user_avatar'] . '" alt="" border="0">' : '';
                    break;
            }
        }

        if (!empty($row['user_viewemail']) || ADMIN == $userdata['user_level']) {
            $email_uri = ($board_config['board_email_form']) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL . '=' . $user_id) : 'mailto:' . $row['email'];

            $email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0"></a>';

            $email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
        } else {
            $email_img = '&nbsp;';

            $email = '&nbsp;';
        }

        $temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id");

        $profile_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_profile'] . '" alt="' . $lang['Read_profile'] . '" title="' . $lang['Read_profile'] . '" border="0"></a>';

        $profile = '<a href="' . $temp_url . '">' . $lang['Read_profile'] . '</a>';

        $temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . "=$user_id");

        $pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0"></a>';

        $pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';

        $www_img = ($row['url']) ? '<a href="' . $row['url'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0"></a>' : '';

        $www = ($row['url']) ? '<a href="' . $row['url'] . '" target="_userwww">' . $lang['Visit_website'] . '</a>' : '';

        if (!empty($row['user_icq'])) {
            $icq_status_img = '<a href="http://wwp.icq.com/' . $row['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $row['user_icq'] . '&img=5" width="18" height="18" border="0"></a>';

            $icq_img = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0"></a>';

            $icq = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $row['user_icq'] . '">' . $lang['ICQ'] . '</a>';
        } else {
            $icq_status_img = '';

            $icq_img = '';

            $icq = '';
        }

        $aim_img = ($row['user_aim']) ? '<a href="aim:goim?screenname=' . $row['user_aim'] . '&amp;message=Hello+Are+you+there?"><img src="' . $images['icon_aim'] . '" alt="' . $lang['AIM'] . '" title="' . $lang['AIM'] . '" border="0"></a>' : '';

        $aim = ($row['user_aim']) ? '<a href="aim:goim?screenname=' . $row['user_aim'] . '&amp;message=Hello+Are+you+there?">' . $lang['AIM'] . '</a>' : '';

        $temp_url = append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id");

        $msn_img = ($row['user_msnm']) ? '<a href="' . $temp_url . '"><img src="' . $images['icon_msnm'] . '" alt="' . $lang['MSNM'] . '" title="' . $lang['MSNM'] . '" border="0"></a>' : '';

        $msn = ($row['user_msnm']) ? '<a href="' . $temp_url . '">' . $lang['MSNM'] . '</a>' : '';

        $yim_img = ($row['user_yim']) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $row['user_yim'] . '&amp;.src=pg"><img src="' . $images['icon_yim'] . '" alt="' . $lang['YIM'] . '" title="' . $lang['YIM'] . '" border="0"></a>' : '';

        $yim = ($row['user_yim']) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $row['user_yim'] . '&amp;.src=pg">' . $lang['YIM'] . '</a>' : '';

        $temp_url = append_sid("search.$phpEx?search_author=" . urlencode($username) . '&amp;showresults=posts');

        $search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . $lang['Search_user_posts'] . '" title="' . $lang['Search_user_posts'] . '" border="0"></a>';

        $search = '<a href="' . $temp_url . '">' . $lang['Search_user_posts'] . '</a>';

        $row_color = (!($i % 2)) ? $theme['td_color1'] : $theme['td_color2'];

        $row_class = (!($i % 2)) ? $theme['td_class1'] : $theme['td_class2'];

        $template->assign_block_vars(
            'memberrow',
            [
                'ROW_NUMBER' => $i + ($_GET['start'] + 1),
'ROW_COLOR' => '#' . $row_color,
'ROW_CLASS' => $row_class,
'USERNAME' => $username,
'FROM' => $from,
'JOINED' => $joined,
'POSTS' => $posts,
'AVATAR_IMG' => $poster_avatar,
'PROFILE_IMG' => $profile_img,
'PROFILE' => $profile,
'SEARCH_IMG' => $search_img,
'SEARCH' => $search,
'PM_IMG' => $pm_img,
'PM' => $pm,
'EMAIL_IMG' => $email_img,
'EMAIL' => $email,
'WWW_IMG' => $www_img,
'WWW' => $www,
'ICQ_STATUS_IMG' => $icq_status_img,
'ICQ_IMG' => $icq_img,
'ICQ' => $icq,
'AIM_IMG' => $aim_img,
'AIM' => $aim,
'MSN_IMG' => $msn_img,
'MSN' => $msn,
'YIM_IMG' => $yim_img,
'YIM' => $yim,
'U_VIEWPROFILE' => append_sid("profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=$user_id"),
            ]
        );

        $i++;
    } while (false !== ($row = $db->sql_fetchrow($result)));
}
if ('topten' != $mode || $board_config['topics_per_page'] < 10) {
    $sql = 'SELECT count(*) AS total
FROM ' . USERS_TABLE . '
WHERE uid <> ' . ANONYMOUS;

    if (!($result = $db->sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
    }

    if ($total = $db->sql_fetchrow($result)) {
        $total_members = $total['total'];

        $pagination = generate_pagination("memberlist.$phpEx?mode=$mode&amp;order=$sort_order", $total_members, $board_config['topics_per_page'], $start) . '&nbsp;';
    }
} else {
    $pagination = '&nbsp;';

    $total_members = 10;
}
$template->assign_vars(
    [
        'PAGINATION' => $pagination,
'PAGE_NUMBER' => sprintf($lang['Page_of'], (floor($start / $board_config['topics_per_page']) + 1), ceil($total_members / $board_config['topics_per_page'])),
'L_GOTO_PAGE' => $lang['Goto_page'],
    ]
);
$template->pparse('body');
include $phpbb_root_path . 'includes/page_tail.' . $phpEx;
