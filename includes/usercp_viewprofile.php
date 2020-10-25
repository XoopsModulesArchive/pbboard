<?php

/***************************************************************************
 * usercp_viewprofile.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: usercp_viewprofile.php,v 1.5.2.1 2005/02/25 23:28:30 acydburn Exp $
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
if (empty($_GET[POST_USERS_URL]) || ANONYMOUS == $_GET[POST_USERS_URL]) {
    message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
}
$profiledata = get_userdata($_GET[POST_USERS_URL]);
$sql = 'SELECT *
FROM ' . RANKS_TABLE . '
ORDER BY rank_special, rank_min';
if (!($result = $db->sql_query($sql))) {
    message_die(GENERAL_ERROR, 'Could not obtain ranks information', '', __LINE__, __FILE__, $sql);
}
while (false !== ($row = $db->sql_fetchrow($result))) {
    $ranksrow[] = $row;
}
$db->sql_freeresult($result);
//
// Output page header and profile_view template
//
$template->set_filenames(
    [
        'body' => 'profile_view_body.tpl',
    ]
);
make_jumpbox('viewforum.' . $phpEx);
//
// Calculate the number of days this user has been a member ($memberdays)
// Then calculate their posts per day
//
$regdate = $profiledata['user_regdate'];
$memberdays = max(1, round((time() - $regdate) / 86400));
$posts_per_day = $profiledata['posts'] / $memberdays;
// Get the users percentage of total posts
if (0 != $profiledata['posts']) {
    $total_posts = get_db_stat('postcount');

    $percentage = ($total_posts) ? min(100, ($profiledata['posts'] / $total_posts) * 100) : 0;
} else {
    $percentage = 0;
}
$avatar_img = '';
if ($profiledata['user_avatar_type'] && $profiledata['user_allowavatar']) {
    switch ($profiledata['user_avatar_type']) {
        case USER_AVATAR_UPLOAD:
            $avatar_img = ($board_config['allow_avatar_upload']) ? '<img src="' . $board_config['avatar_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0">' : '';
            break;
        case USER_AVATAR_REMOTE:
            $avatar_img = ($board_config['allow_avatar_remote']) ? '<img src="' . $profiledata['user_avatar'] . '" alt="" border="0">' : '';
            break;
        case USER_AVATAR_GALLERY:
            $avatar_img = ($board_config['allow_avatar_local']) ? '<img src="' . $board_config['avatar_gallery_path'] . '/' . $profiledata['user_avatar'] . '" alt="" border="0">' : '';
            break;
    }
}
$poster_rank = '';
$rank_image = '';
if ($profiledata['rank']) {
    for ($i = 0, $iMax = count($ranksrow); $i < $iMax; $i++) {
        if ($profiledata['rank'] == $ranksrow[$i]['rank_id'] && $ranksrow[$i]['rank_special']) {
            $poster_rank = $ranksrow[$i]['rank_title'];

            $rank_image = ($ranksrow[$i]['rank_image']) ? '<img src="' . $ranksrow[$i]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0"><br>' : '';
        }
    }
} else {
    for ($i = 0, $iMax = count($ranksrow); $i < $iMax; $i++) {
        if ($profiledata['posts'] >= $ranksrow[$i]['rank_min'] && !$ranksrow[$i]['rank_special']) {
            $poster_rank = $ranksrow[$i]['rank_title'];

            $rank_image = ($ranksrow[$i]['rank_image']) ? '<img src="' . $ranksrow[$i]['rank_image'] . '" alt="' . $poster_rank . '" title="' . $poster_rank . '" border="0"><br>' : '';
        }
    }
}
$temp_url = append_sid("privmsg.$phpEx?mode=post&amp;" . POST_USERS_URL . '=' . $profiledata['uid']);
$pm_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_pm'] . '" alt="' . $lang['Send_private_message'] . '" title="' . $lang['Send_private_message'] . '" border="0"></a>';
$pm = '<a href="' . $temp_url . '">' . $lang['Send_private_message'] . '</a>';
if (!empty($profiledata['user_viewemail']) || ADMIN == $userdata['user_level']) {
    $email_uri = ($board_config['board_email_form']) ? append_sid("profile.$phpEx?mode=email&amp;" . POST_USERS_URL . '=' . $profiledata['uid']) : 'mailto:' . $profiledata['email'];

    $email_img = '<a href="' . $email_uri . '"><img src="' . $images['icon_email'] . '" alt="' . $lang['Send_email'] . '" title="' . $lang['Send_email'] . '" border="0"></a>';

    $email = '<a href="' . $email_uri . '">' . $lang['Send_email'] . '</a>';
} else {
    $email_img = '&nbsp;';

    $email = '&nbsp;';
}
$www_img = ($profiledata['url']) ? '<a href="' . $profiledata['url'] . '" target="_userwww"><img src="' . $images['icon_www'] . '" alt="' . $lang['Visit_website'] . '" title="' . $lang['Visit_website'] . '" border="0"></a>' : '&nbsp;';
$www = ($profiledata['url']) ? '<a href="' . $profiledata['url'] . '" target="_userwww">' . $profiledata['url'] . '</a>' : '&nbsp;';
if (!empty($profiledata['user_icq'])) {
    $icq_status_img = '<a href="http://wwp.icq.com/' . $profiledata['user_icq'] . '#pager"><img src="http://web.icq.com/whitepages/online?icq=' . $profiledata['user_icq'] . '&img=5" width="18" height="18" border="0"></a>';

    $icq_img = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $profiledata['user_icq'] . '"><img src="' . $images['icon_icq'] . '" alt="' . $lang['ICQ'] . '" title="' . $lang['ICQ'] . '" border="0"></a>';

    $icq = '<a href="http://wwp.icq.com/scripts/search.dll?to=' . $profiledata['user_icq'] . '">' . $lang['ICQ'] . '</a>';
} else {
    $icq_status_img = '&nbsp;';

    $icq_img = '&nbsp;';

    $icq = '&nbsp;';
}
$aim_img = ($profiledata['user_aim']) ? '<a href="aim:goim?screenname=' . $profiledata['user_aim'] . '&amp;message=Hello+Are+you+there?"><img src="' . $images['icon_aim'] . '" alt="' . $lang['AIM'] . '" title="' . $lang['AIM'] . '" border="0"></a>' : '&nbsp;';
$aim = ($profiledata['user_aim']) ? '<a href="aim:goim?screenname=' . $profiledata['user_aim'] . '&amp;message=Hello+Are+you+there?">' . $lang['AIM'] . '</a>' : '&nbsp;';
$msn_img = ($profiledata['user_msnm']) ?: '&nbsp;';
$msn = $msn_img;
$yim_img = ($profiledata['user_yim']) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $profiledata['user_yim'] . '&amp;.src=pg"><img src="' . $images['icon_yim'] . '" alt="' . $lang['YIM'] . '" title="' . $lang['YIM'] . '" border="0"></a>' : '';
$yim = ($profiledata['user_yim']) ? '<a href="http://edit.yahoo.com/config/send_webmesg?.target=' . $profiledata['user_yim'] . '&amp;.src=pg">' . $lang['YIM'] . '</a>' : '';
$temp_url = append_sid("search.$phpEx?search_author=" . urlencode($profiledata['uname']) . '&amp;showresults=posts');
$search_img = '<a href="' . $temp_url . '"><img src="' . $images['icon_search'] . '" alt="' . $lang['Search_user_posts'] . '" title="' . $lang['Search_user_posts'] . '" border="0"></a>';
$search = '<a href="' . $temp_url . '">' . $lang['Search_user_posts'] . '</a>';
//
// Generate page
//
$page_title = $lang['Viewing_profile'];
include $phpbb_root_path . 'includes/page_header.' . $phpEx;
display_upload_attach_box_limits($profiledata['user_id']);
$template->assign_vars(
    [
        'USERNAME' => $profiledata['uname'],
        'JOINED' => create_date($lang['DATE_FORMAT'], $profiledata['user_regdate'], $board_config['board_timezone']),
        'POSTER_RANK' => $poster_rank,
        'RANK_IMAGE' => $rank_image,
        'POSTS_PER_DAY' => $posts_per_day,
        'POSTS' => $profiledata['posts'],
        'PERCENTAGE' => $percentage . '%',
        'POST_DAY_STATS' => sprintf($lang['User_post_day_stats'], $posts_per_day),
        'POST_PERCENT_STATS' => sprintf($lang['User_post_pct_stats'], $percentage),
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
        'LOCATION' => ($profiledata['user_from']) ?: '&nbsp;',
        'OCCUPATION' => ($profiledata['user_occ']) ?: '&nbsp;',
        'INTERESTS' => ($profiledata['user_intrest']) ?: '&nbsp;',
        'AVATAR_IMG' => $avatar_img,
        'L_VIEWING_PROFILE' => sprintf($lang['Viewing_user_profile'], $profiledata['uname']),
        'L_ABOUT_USER' => sprintf($lang['About_user'], $profiledata['uname']),
        'L_AVATAR' => $lang['Avatar'],
        'L_POSTER_RANK' => $lang['Poster_rank'],
        'L_JOINED' => $lang['Joined'],
        'L_TOTAL_POSTS' => $lang['Total_posts'],
        'L_SEARCH_USER_POSTS' => sprintf($lang['Search_user_posts'], $profiledata['uname']),
        'L_CONTACT' => $lang['Contact'],
        'L_EMAIL_ADDRESS' => $lang['Email_address'],
        'L_EMAIL' => $lang['Email'],
        'L_PM' => $lang['Private_Message'],
        'L_ICQ_NUMBER' => $lang['ICQ'],
        'L_YAHOO' => $lang['YIM'],
        'L_AIM' => $lang['AIM'],
        'L_MESSENGER' => $lang['MSNM'],
        'L_WEBSITE' => $lang['Website'],
        'L_LOCATION' => $lang['Location'],
        'L_OCCUPATION' => $lang['Occupation'],
        'L_INTERESTS' => $lang['Interests'],
        'U_SEARCH_USER' => append_sid("search.$phpEx?search_author=" . urlencode($profiledata['uname'])),
        'S_PROFILE_ACTION' => append_sid("profile.$phpEx"),
    ]
);
$template->pparse('body');
include $phpbb_root_path . 'includes/page_tail.' . $phpEx;
