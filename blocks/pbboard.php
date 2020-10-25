<?php

// $Id: pbboard.php,v 1.2 18/06/2005 11:21:27 Koudanshi modified
include ' ' . XOOPS_URL . '/modules/pbboard/includes/constant.php ';
function pbboard_show($options)
{
    global $sid_bb, $meminfo, $board_config, $uid_bb;

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $myts = MyTextSanitizer::getInstance();

    $block = [];

    switch ($options[2]) {
        case 'views':
            $order = 't.topic_views';
            break;
        case 'replies':
            $order = 't.topic_replies';
            break;
        case 'time':
        default:
            $order = 'p.post_time';
            break;
    }

    $query = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_last_post_id, t.topic_views, t.topic_replies,
t.forum_id, p.post_username, p.poster_id, p.post_time, f.forum_id, f.forum_name, f.auth_read
FROM ' . $db->prefix('pbb_topics') . ' t, ' . $db->prefix('pbb_posts') . ' p, ' . $db->prefix('pbb_forums') . ' f
WHERE f.forum_id = t.forum_id
AND (t.topic_id = p.topic_id)
ORDER BY ' . $order . ' DESC';

    if (!$result = $db->query($query, $options[0], 0)) {
        return false;
    }

    if (0 != $options[1]) {
        $block['full_view'] = true;
    } else {
        $block['full_view'] = false;
    }

    $block['lang_forum'] = _MB_PBBOARD_FORUM;

    $block['lang_topic'] = _MB_PBBOARD_TOPIC;

    $block['lang_replies'] = _MB_PBBOARD_RPLS;

    $block['lang_views'] = _MB_PBBOARD_VIEWS;

    $block['lang_by'] = _MB_PBBOARD_BY;

    $block['lang_lastpost'] = _MB_PBBOARD_LPOST;

    $block['lang_visitforums'] = _MB_PBBOARD_VSTFRMS;

    //------------------------

    // Set board_config array

    //------------------------

    $board_config = [];

    $config = $db->query('SELECT * FROM ' . $db->prefix('pbb_config') . ' ');

    while (false !== ($bconfig = $db->fetchArray($config))) {
        $board_config[$bconfig['config_name']] = $bconfig['config_value'];
    }

    //------------------------

    // End set board_config

    //------------------------

    $tracking_topics = (isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t'])) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_t']) : '';

    $tracking_forums = (isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f'])) ? unserialize($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f']) : '';

    //------------------------

    // Set template name

    //------------------------

    $tpl_user = $db->fetchArray($db->query('SELECT template_name FROM ' . $db->prefix('pbb_themes') . ' WHERE themes_id = ' . $meminfo['user_style'] . ''));

    $tpl_default = $db->fetchArray($db->query('SELECT template_name FROM ' . $db->prefix('pbb_themes') . ' WHERE themes_id = ' . $board_config['default_style'] . ''));

    if ($board_config['override_user_style'] or empty($tpl_user['template_name'])) {
        $tpl_name = $tpl_default['template_name'];
    } else {
        $tpl_name = $tpl_user['template_name'];
    }

    //------------------------

    // END set template name

    //------------------------

    while (false !== ($arr = $db->fetchArray($result))) {
        $uname = $db->fetchArray($db->query('SELECT uname FROM ' . $db->prefix('users') . " WHERE uid = '" . $arr['poster_id'] . "' "));

        $auth = $db->fetchArray(
            $db->query(
                'SELECT aa.forum_id, aa.auth_read, aa.auth_mod, ug.user_id, ug.group_id
FROM ' . $db->prefix('pbb_user_group') . ' ug, ' . $db->prefix('pbb_auth_access') . ' aa
WHERE ug.group_id = aa.group_id
AND ug.user_id = ' . $uid_bb . '
AND aa.forum_id = ' . $arr['forum_id'] . '
'
            )
        );

        if (!$arr['auth_read'] or isset($auth['auth_read']) or isset($auth['auth_mod'])) {
            //------------------------

            // Folder picture start

            //------------------------

            if (TOPIC_MOVED == $arr['topic_status']) {
                $icon_name = 'folder';
            } else {
                if (POST_ANNOUNCE == $arr['topic_type']) {
                    $img_name = 'folder_announce';
                } elseif (POST_STICKY == $arr['topic_type']) {
                    $img_name = 'folder_sticky';

                    $img_name_new = 'folder_sticky_new';
                } elseif (TOPIC_LOCKED == $arr['topic_status']) {
                    $img_name = 'folder_locked';

                    $img_name_new = 'folder_locked_new';
                } else {
                    if ($arr['topic_replies'] >= $board_config['hot_threshold']) {
                        $img_name = 'folder_hot';

                        $img_name_new = 'folder_hot_new';
                    } else {
                        $img_name = 'folder';

                        $img_name_new = 'folder_new';
                    }
                }

                if ($uid_bb) {
                    if ($arr['post_time'] > $meminfo['last_login']) {
                        if (!empty($tracking_topics) || !empty($tracking_forums) || isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f_all'])) {
                            $unread_topics = true;

                            if (!empty($tracking_topics[$arr['topic_id']])) {
                                if ($tracking_topics[$arr['topic_id']] >= $arr['post_time']) {
                                    $unread_topics = false;
                                }
                            }

                            if (!empty($tracking_forums[$arr['forum_id']])) {
                                if ($tracking_forums[$arr['forum_id']] >= $arr['post_time']) {
                                    $unread_topics = false;
                                }
                            }

                            if (isset($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f_all'])) {
                                if ($HTTP_COOKIE_VARS[$board_config['cookie_name'] . '_f_all'] >= $arr['post_time']) {
                                    $unread_topics = false;
                                }
                            }

                            if ($unread_topics) {
                                $icon_name = $img_name_new;
                            } else {
                                $icon_name = $img_name;
                            }
                        } else {
                            $icon_name = $img_name_new;
                        }
                    } else {
                        $icon_name = $img_name;
                    } // End tracking = true
                } else {
                    $icon_name = $img_name;
                } //End uid_bb
            } // End topic_movie
            //------------------------
            //End hack image_icon_show
            //------------------------
            $topic['forum_id'] = $arr['forum_id'];

            $topic['forum_name'] = htmlspecialchars($arr['forum_name'], ENT_QUOTES | ENT_HTML5);

            $topic['id'] = $arr['topic_id'];

            $topic['title'] = htmlspecialchars($arr['topic_title'], ENT_QUOTES | ENT_HTML5);

            $topic['replies'] = $arr['topic_replies'];

            $topic['views'] = $arr['topic_views'];

            $topic['time'] = formatTimestamp($arr['post_time'], 'm');

            $topic['sess_id'] = $sid_bb;

            $topic['last_post_name'] = $uname['uname'];

            $topic['last_post_id'] = $arr['topic_last_post_id'];

            $topic['pages'] = show_page($arr['topic_replies'], $arr['topic_id']);

            $topic['img_dir'] = XOOPS_URL . '/modules/pbboard/templates/' . $tpl_name . '/images/' . $icon_name . '.gif';

            $block['topics'][] = &$topic;

            unset($topic);
        } // End Check auth
    } // End while
    return $block;
} // End function
function pbboard_edit($options)
{
    $inputtag = "<input type='text' name='options[0]' value='" . $options[0] . "'>";

    $form = sprintf(_MB_IPBOARD_DISPLAY, $inputtag);

    $form .= '<br>' . _MB_IPBOARD_DISPLAYF . "&nbsp;<input type='radio' name='options[1]' value='1'";

    if (1 == $options[1]) {
        $form .= ' checked';
    }

    $form .= '>&nbsp;' . _YES . "<input type='radio' name='options[1]' value='0'";

    if (0 == $options[1]) {
        $form .= ' checked';
    }

    $form .= '>&nbsp;' . _NO;

    $form .= '<input type="hidden" name="options[2]" value="' . $options[2] . '">';

    return $form;
}

function show_page($data, $t)
{
    global $sid_bb, $board_config;

    $pages = 1;

    if (0 == (($data + 1) % $board_config['posts_per_page'])) {
        $pages = ($data + 1) / $board_config['posts_per_page'];
    } else {
        $number = (($data + 1) / $board_config['posts_per_page']);

        $pages = ceil($number);
    }

    $pages_link = '';

    if ($pages > 1) {
        $pages_link = "<span style='font-size:10px; font-weight:bold; font-family:verdana,tahoma;'>(" . _MB_PBBOARD_PAGES . ' ';

        for ($i = 0; $i < $pages; ++$i) {
            $real_no = $i * $board_config['posts_per_page'];

            $page_no = $i + 1;

            if (4 == $page_no) {
                $pages_link .= "<a href='" . XOOPS_URL . "/modules/pbboard/viewtopic.php?t=$t&start=" . ($pages - 1) * $board_config['posts_per_page'] . "'>... $pages</a>";

                break;
            }

            $pages_link .= "<a href='" . XOOPS_URL . "/modules/pbboard/viewtopic.php?t=$t&start=$real_no'> $page_no </a>";
        }

        $pages_link .= ')</span>';
    }

    return $pages_link;
}
