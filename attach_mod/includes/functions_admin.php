<?php

/***************************************************************************
 * functions_admin.php
 * -------------------
 * begin : Sunday, Mar 31, 2002
 * copyright : (C) 2002 Meik Sievertsen
 * email : acyd.burn@gmx.de
 *
 * $Id: functions_admin.php,v 1.13 2005/01/28 16:32:31 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 **************************************************************************
 * @param     $mode
 * @param     $id
 * @param     $quota_type
 * @param int $quota_limit_id
 */
//
// All Attachment Functions only needed in Admin
//
//
// Set/Change Quotas
//
function process_quota_settings($mode, $id, $quota_type, $quota_limit_id = -1)
{
    global $db;

    if ('user' == $mode) {
        if (-1 == $quota_limit_id) {
            $sql = 'DELETE FROM ' . QUOTA_TABLE . ' WHERE user_id = ' . $id . ' AND quota_type = ' . $quota_type;
        } else {
            // Check if user is already entered

            $sql = 'SELECT user_id FROM ' . QUOTA_TABLE . ' WHERE user_id = ' . $id . ' AND quota_type = ' . $quota_type;

            if (!($result = attach_sql_query($sql))) {
                message_die(GENERAL_ERROR, 'Could not get Entry', '', __LINE__, __FILE__, $sql);
            }

            if (0 == $db->sql_numrows($result)) {
                $sql = 'INSERT INTO ' . QUOTA_TABLE . ' (user_id, group_id, quota_type, quota_limit_id) 
VALUES (' . $id . ', 0, ' . $quota_type . ', ' . $quota_limit_id . ')';
            } else {
                $sql = 'UPDATE ' . QUOTA_TABLE . ' SET quota_limit_id = ' . $quota_limit_id . ' WHERE user_id = ' . $id . ' AND quota_type = ' . $quota_type;
            }
        }

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Unable to update quota Settings', '', __LINE__, __FILE__, $sql);
        }
    } elseif ('group' == $mode) {
        if (-1 == $quota_limit_id) {
            $sql = 'DELETE FROM ' . QUOTA_TABLE . ' WHERE group_id = ' . $id . ' AND quota_type = ' . $quota_type;

            if (!($result = attach_sql_query($sql))) {
                message_die(GENERAL_ERROR, 'Unable to delete quota Settings', '', __LINE__, __FILE__, $sql);
            }
        } else {
            // Check if user is already entered

            $sql = 'SELECT group_id FROM ' . QUOTA_TABLE . ' WHERE group_id = ' . $id . ' AND quota_type = ' . $quota_type;

            if (!($result = attach_sql_query($sql))) {
                message_die(GENERAL_ERROR, 'Could not get Entry', '', __LINE__, __FILE__, $sql);
            }

            if (0 == $db->sql_numrows($result)) {
                $sql = 'INSERT INTO ' . QUOTA_TABLE . ' (user_id, group_id, quota_type, quota_limit_id) 
VALUES (0, ' . $id . ', ' . $quota_type . ', ' . $quota_limit_id . ')';
            } else {
                $sql = 'UPDATE ' . QUOTA_TABLE . ' SET quota_limit_id = ' . $quota_limit_id . ' WHERE group_id = ' . $id . ' AND quota_type = ' . $quota_type;
            }

            if (!($result = attach_sql_query($sql))) {
                message_die(GENERAL_ERROR, 'Unable to update quota Settings', '', __LINE__, __FILE__, $sql);
            }
        }
    }
}

//
// sort multi-dimensional Array
//
function sort_multi_array($sort_array, $key, $sort_order, $pre_string_sort = -1)
{
    $last_element = count($sort_array) - 1;

    if (-1 == $pre_string_sort) {
        $string_sort = (is_string($sort_array[$last_element - 1][$key])) ? true : false;
    } else {
        $string_sort = $pre_string_sort;
    }

    for ($i = 0; $i < $last_element; $i++) {
        $num_iterations = $last_element - $i;

        for ($j = 0; $j < $num_iterations; $j++) {
            $next = 0;

            // do checks based on key

            $switch = false;

            if (!($string_sort)) {
                if ((('DESC' == $sort_order) && ((int)$sort_array[$j][$key] < (int)$sort_array[$j + 1][$key])) || (('ASC' == $sort_order) && ((int)$sort_array[$j][$key] > (int)$sort_array[$j + 1][$key]))) {
                    $switch = true;
                }
            } else {
                if ((('DESC' == $sort_order) && (strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) < 0)) || (('ASC' == $sort_order) && (strcasecmp($sort_array[$j][$key], $sort_array[$j + 1][$key]) > 0))) {
                    $switch = true;
                }
            }

            if ($switch) {
                $temp = $sort_array[$j];

                $sort_array[$j] = $sort_array[$j + 1];

                $sort_array[$j + 1] = $temp;
            }
        }
    }

    return ($sort_array);
}

//
// See if a post or pm really exist
//
function entry_exists($attach_id)
{
    global $db;

    if (empty($attach_id)) {
        return (false);
    }

    $sql = 'SELECT post_id, privmsgs_id
FROM ' . ATTACHMENTS_TABLE . '
WHERE attach_id = ' . $attach_id;

    if (!attach_sql_query($sql)) {
        message_die(GENERAL_ERROR, 'Could not get Entry', '', __LINE__, __FILE__, $sql);
    }

    $ids = $db->sql_fetchrowset($result);

    $num_ids = $db->sql_numrows($result);

    $exists = false;

    for ($i = 0; $i < $num_ids; $i++) {
        if (0 != (int)$ids[$i]['post_id']) {
            $sql = 'SELECT post_id
FROM ' . POSTS_TABLE . '
WHERE post_id = ' . (int)$ids[$i]['post_id'];
        } elseif (0 != (int)$ids[$i]['privmsgs_id']) {
            $sql = 'SELECT privmsgs_id
FROM ' . PRIVMSGS_TABLE . '
WHERE privmsgs_id = ' . (int)$ids[$i]['privmsgs_id'];
        }

        if (!attach_sql_query($sql)) {
            message_die(GENERAL_ERROR, 'Could not get Entry', '', __LINE__, __FILE__, $sql);
        }

        if (($db->sql_numrows($result)) > 0) {
            $exists = true;

            break;
        }
    }

    return ($exists);
}

//
// Collect all Attachments in Filesystem
//
function collect_attachments()
{
    global $upload_dir, $attach_config;

    $file_attachments = [];

    if (!(int)$attach_config['allow_ftp_upload']) {
        if ($dir = @opendir($upload_dir)) {
            while ($file = @readdir($dir)) {
                if (('index.php' != $file) && ('.htaccess' != $file) && (!is_dir($upload_dir . '/' . $file)) && (!is_link($upload_dir . '/' . $file))) {
                    $file_attachments[] = trim($file);
                }
            }

            closedir($dir);
        } else {
            message_die(GENERAL_ERROR, 'Is Safe Mode Restriction in effect ? The Attachment Mod seems to be unable to collect the Attachments within the upload Directory. Try to use FTP Upload to circumvent this error.');
        }
    } else {
        $conn_id = attach_init_ftp();

        $file_listing = [];

        $file_listing = @ftp_rawlist($conn_id, '');

        if (!$file_listing) {
            message_die(GENERAL_ERROR, 'Unable to get Raw File Listing. Please be sure the LIST command is enabled at your FTP Server.');
        }

        for ($i = 0, $iMax = count($file_listing); $i < $iMax; $i++) {
            if (preg_match('([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)', $file_listing[$i], $regs)) {
                if ('d' == $regs[1]) {
                    $dirinfo[0] = 1; // Directory == 1
                }

                $dirinfo[1] = $regs[2]; // Size
                $dirinfo[2] = $regs[3]; // Date
                $dirinfo[3] = $regs[4]; // Filename
                $dirinfo[4] = $regs[5]; // Time
            }

            if ((1 != $dirinfo[0]) && ('index.php' != $dirinfo[4]) && ('.htaccess' != $dirinfo[4])) {
                $file_attachments[] = trim($dirinfo[4]);
            }
        }

        @ftp_quit($conn_id);
    }

    return ($file_attachments);
}

//
// Returns the filesize of the upload directory in human readable format
//
function get_formatted_dirsize()
{
    global $attach_config, $upload_dir, $lang;

    $upload_dir_size = 0;

    if (!(int)$attach_config['allow_ftp_upload']) {
        if ($dirname = @opendir($upload_dir)) {
            while ($file = @readdir($dirname)) {
                if (('index.php' != $file) && ('.htaccess' != $file) && (!is_dir($upload_dir . '/' . $file)) && (!is_link($upload_dir . '/' . $file))) {
                    $upload_dir_size += @filesize($upload_dir . '/' . $file);
                }
            }

            @closedir($dirname);
        } else {
            $upload_dir_size = $lang['Not_available'];

            return ($upload_dir_size);
        }
    } else {
        $conn_id = attach_init_ftp();

        $file_listing = [];

        $file_listing = @ftp_rawlist($conn_id, '');

        if (!$file_listing) {
            $upload_dir_size = $lang['Not_available'];

            return ($upload_dir_size);
        }

        for ($i = 0, $iMax = count($file_listing); $i < $iMax; $i++) {
            if (preg_match('([-d])[rwxst-]{9}.* ([0-9]*) ([a-zA-Z]+[0-9: ]*[0-9]) ([0-9]{2}:[0-9]{2}) (.+)', $file_listing[$i], $regs)) {
                if ('d' == $regs[1]) {
                    $dirinfo[0] = 1; // Directory == 1
                }

                $dirinfo[1] = $regs[2]; // Size
                $dirinfo[2] = $regs[3]; // Date
                $dirinfo[3] = $regs[4]; // Filename
                $dirinfo[4] = $regs[5]; // Time
            }

            if ((1 != $dirinfo[0]) && ('index.php' != $dirinfo[4]) && ('.htaccess' != $dirinfo[4])) {
                $upload_dir_size += $dirinfo[1];
            }
        }

        @ftp_quit($conn_id);
    }

    if ($upload_dir_size >= 1048576) {
        $upload_dir_size = round($upload_dir_size / 1048576 * 100) / 100 . ' ' . $lang['MB'];
    } elseif ($upload_dir_size >= 1024) {
        $upload_dir_size = round($upload_dir_size / 1024 * 100) / 100 . ' ' . $lang['KB'];
    } else {
        $upload_dir_size .= ' ' . $lang['Bytes'];
    }

    return ($upload_dir_size);
}

//
// Build SQL-Statement for the search feature
//
function search_attachments($order_by, &$total_rows)
{
    global $db, $_POST, $_GET, $lang;

    $where_sql = [];

    // Get submitted Vars

    $search_vars = ['search_keyword_fname', 'search_keyword_comment', 'search_author', 'search_size_smaller', 'search_size_greater', 'search_count_smaller', 'search_count_greater', 'search_days_greater', 'search_forum', 'search_cat'];

    for ($i = 0, $iMax = count($search_vars); $i < $iMax; $i++) {
        if (isset($_POST[$search_vars[$i]]) || isset($_GET[$search_vars[$i]])) {
            $$search_vars[$i] = $_POST[$search_vars[$i]] ?? $_GET[$search_vars[$i]];
        } else {
            $$search_vars[$i] = '';
        }
    }

    // Author name search

    if ('' != $search_author) {
        $search_author = str_replace('*', '%', trim(str_replace("\'", "''", $search_author)));

        // We need the post_id's, because we want to query the Attachment Table

        $sql = 'SELECT uid
FROM ' . USERS_TABLE . '
WHERE uname LIKE \'' . $search_author . '\'';

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Couldn\'t obtain list of matching users (searching for: ' . $search_author . ')', '', __LINE__, __FILE__, $sql);
        }

        $matching_userids = '';

        if ($row = $db->sql_fetchrow($result)) {
            do {
                $matching_userids .= (('' != $matching_userids) ? ', ' : '') . $row['uid'];
            } while (false !== ($row = $db->sql_fetchrow($result)));
        } else {
            message_die(GENERAL_MESSAGE, $lang['No_attach_search_match']);
        }

        $where_sql[] = ' (t.user_id_1 IN (' . $matching_userids . ')) ';
    }

    // Search Keyword

    if ('' != $search_keyword_fname) {
        $match_word = str_replace('*', '%', $search_keyword_fname);

        $where_sql[] = ' (a.real_filename LIKE \'' . $match_word . '\') ';
    }

    if ('' != $search_keyword_comment) {
        $match_word = str_replace('*', '%', $search_keyword_comment);

        $where_sql[] = ' (a.comment LIKE \'' . $match_word . '\') ';
    }

    // Search Download Count

    if ('' != $search_count_smaller || '' != $search_count_greater) {
        if ('' != $search_count_smaller) {
            $where_sql[] = ' (a.download_count < ' . $search_count_smaller . ') ';
        } elseif ('' != $search_count_greater) {
            $where_sql[] = ' (a.download_count > ' . $search_count_greater . ') ';
        }
    }

    // Search Filesize

    if ('' != $search_size_smaller || '' != $search_size_greater) {
        if ('' != $search_size_smaller) {
            $where_sql[] = ' (a.filesize < ' . $search_size_smaller . ') ';
        } elseif ('' != $search_size_greater) {
            $where_sql[] = ' (a.filesize > ' . $search_size_greater . ') ';
        }
    }

    // Search Attachment Time

    if ('' != $search_days_greater) {
        $where_sql[] = ' (a.filetime < ' . (time() - ($search_days_greater * 86400)) . ') ';
    }

    $sql = 'SELECT a.*, t.post_id, p.post_time, p.topic_id
FROM ' . ATTACHMENTS_TABLE . ' t, ' . ATTACHMENTS_DESC_TABLE . ' a, ' . POSTS_TABLE . ' p WHERE ';

    if (count($where_sql) > 0) {
        $sql .= implode('AND', $where_sql) . ' AND ';
    }

    $sql .= '(t.post_id = p.post_id) AND (a.attach_id = t.attach_id) ';

    $total_rows_sql = $sql;

    $sql .= $order_by;

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Couldn\'t query attachments', '', __LINE__, __FILE__, $sql);
    }

    $attachments = $db->sql_fetchrowset($result);

    $num_attach = $db->sql_numrows($result);

    if (0 == $num_attach) {
        message_die(GENERAL_MESSAGE, $lang['No_attach_search_match']);
    }

    if (!($result = attach_sql_query($total_rows_sql))) {
        message_die(GENERAL_ERROR, 'Could not query attachments', '', __LINE__, __FILE__, $sql);
    }

    $total_rows = $db->sql_numrows($result);

    return ($attachments);
}

//
// perform LIMIT statement on arrays
//
function limit_array($array, $start, $pagelimit)
{
    // array from start - start+pagelimit

    $limit = (count($array) < $start + $pagelimit) ? count($array) : $start + $pagelimit;

    $limit_array = [];

    for ($i = $start; $i < $limit; $i++) {
        $limit_array[] = $array[$i];
    }

    return $limit_array;
}
