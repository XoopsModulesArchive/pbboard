<?php

/***************************************************************************
 * functions_attach.php
 * -------------------
 * begin : Friday, Mar 29, 2002
 * copyright : (C) 2002 Meik Sievertsen
 * email : acyd.burn@gmx.de
 *
 * $Id: functions_attach.php,v 1.33 2005/02/01 03:09:46 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 **************************************************************************
 * @param $number
 * @return string|void
 */
//
// All Attachment Functions needed everywhere
//
//
// A simple dectobase64 function
//
function base64_pack($number)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';

    $base = mb_strlen($chars);

    if ($number > 4096) {
        return;
    } elseif ($number < $base) {
        return $chars[$number];
    }

    $hexval = '';

    while ($number > 0) {
        $remainder = $number % $base;

        if ($remainder < $base) {
            $hexval = $chars[$remainder] . $hexval;
        }

        $number = floor($number / $base);
    }

    return $hexval;
}

//
// base64todec function
//
function base64_unpack($string)
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-';

    $base = mb_strlen($chars);

    $length = mb_strlen($string);

    $number = 0;

    for ($i = 1; $i <= $length; $i++) {
        $pos = $length - $i;

        $operand = mb_strpos($chars, mb_substr($string, $pos, 1));

        $exponent = pow($base, $i - 1);

        $decValue = $operand * $exponent;

        $number += $decValue;
    }

    return $number;
}

//
// Per Forum based Extension Group Permissions (Encode Number) -> Theoretically up to 158 Forums saveable. :)
// We are using a base of 64, but splitting it to one-char and two-char numbers. :)
//
function auth_pack($auth_array)
{
    $one_char_encoding = '#';

    $two_char_encoding = '.';

    $one_char = false;

    $two_char = false;

    $auth_cache = '';

    for ($i = 0, $iMax = count($auth_array); $i < $iMax; $i++) {
        $val = base64_pack((int)$auth_array[$i]);

        if ((1 == mb_strlen($val)) && (false === $one_char)) {
            $auth_cache .= $one_char_encoding;

            $one_char = true;
        } elseif ((2 == mb_strlen($val)) && (false === $two_char)) {
            $auth_cache .= $two_char_encoding;

            $two_char = true;
        }

        $auth_cache .= $val;
    }

    return $auth_cache;
}

//
// Reverse the auth_pack process
//
function auth_unpack($auth_cache)
{
    $one_char_encoding = '#';

    $two_char_encoding = '.';

    $auth = [];

    $auth_len = 1;

    for ($pos = 0, $posMax = mb_strlen($auth_cache); $pos < $posMax; $pos += $auth_len) {
        $forum_auth = mb_substr($auth_cache, $pos, 1);

        if ($forum_auth == $one_char_encoding) {
            $auth_len = 1;

            continue;
        } elseif ($forum_auth == $two_char_encoding) {
            $auth_len = 2;

            $pos--;

            continue;
        }

        $forum_auth = mb_substr($auth_cache, $pos, $auth_len);

        $forum_id = base64_unpack($forum_auth);

        $auth[] = (int)$forum_id;
    }

    return $auth;
}

//
// Used for determining if Forum ID is authed, please use this Function on all Posting Screens
//
function is_forum_authed($auth_cache, $check_forum_id)
{
    $one_char_encoding = '#';

    $two_char_encoding = '.';

    if ('' == trim($auth_cache)) {
        return (true);
    }

    $auth = [];

    $auth_len = 1;

    for ($pos = 0, $posMax = mb_strlen($auth_cache); $pos < $posMax; $pos += $auth_len) {
        $forum_auth = mb_substr($auth_cache, $pos, 1);

        if ($forum_auth == $one_char_encoding) {
            $auth_len = 1;

            continue;
        } elseif ($forum_auth == $two_char_encoding) {
            $auth_len = 2;

            $pos--;

            continue;
        }

        $forum_auth = mb_substr($auth_cache, $pos, $auth_len);

        $forum_id = base64_unpack($forum_auth);

        if ((int)$forum_id == (int)$check_forum_id) {
            return (true);
        }
    }

    return (false);
}

//
// Init FTP Session
//
function attach_init_ftp($mode = false)
{
    global $lang, $attach_config;

    $server = ('' == trim($attach_config['ftp_server'])) ? 'localhost' : trim($attach_config['ftp_server']);

    $ftp_path = (MODE_THUMBNAIL == $mode) ? trim($attach_config['ftp_path']) . '/' . THUMB_DIR : trim($attach_config['ftp_path']);

    $conn_id = @ftp_connect($server);

    if (!$conn_id) {
        message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_connect'], $server));
    }

    $login_result = @ftp_login($conn_id, $attach_config['ftp_user'], $attach_config['ftp_pass']);

    if ((!$login_result)) {
        message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_login'], $attach_config['ftp_user']));
    }

    if (!@ftp_pasv($conn_id, (int)$attach_config['ftp_pasv_mode'])) {
        message_die(GENERAL_ERROR, $lang['Ftp_error_pasv_mode']);
    }

    $result = @ftp_chdir($conn_id, $ftp_path);

    if (!$result) {
        message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_path'], $ftp_path));
    }

    return ($conn_id);
}

//
// Deletes an Attachment
//
function unlink_attach($filename, $mode = false)
{
    global $upload_dir, $attach_config, $lang;

    if (!(int)$attach_config['allow_ftp_upload']) {
        if (MODE_THUMBNAIL == $mode) {
            $filename = $upload_dir . '/' . THUMB_DIR . '/t_' . $filename;
        } else {
            $filename = $upload_dir . '/' . $filename;
        }

        $deleted = @unlink($filename);

        if (@file_exists(@amod_realpath($filename))) {
            $filesys = eregi_replace('/', '\\', $filename);

            $deleted = @system("del $filesys");

            if (@file_exists(@amod_realpath($filename))) {
                $deleted = @chmod($filename, 0775);

                $deleted = @unlink($filename);

                $deleted = @system("del $filesys");
            }
        }
    } else {
        $conn_id = attach_init_ftp($mode);

        if (MODE_THUMBNAIL == $mode) {
            $filename = 't_' . $filename;
        }

        $res = @ftp_delete($conn_id, $filename);

        if (!$res) {
            if (ATTACH_DEBUG) {
                $add = (MODE_THUMBNAIL == $mode) ? ('/' . THUMB_DIR) : '';

                message_die(GENERAL_ERROR, sprintf($lang['Ftp_error_delete'], $attach_config['ftp_path'] . $add));
            }

            return ($deleted);
        }

        @ftp_quit($conn_id);

        $deleted = true;
    }

    return ($deleted);
}

//
// FTP File to Location
//
function ftp_file($source_file, $dest_file, $mimetype, $disable_error_mode = false)
{
    global $attach_config, $lang, $error, $error_msg;

    $conn_id = attach_init_ftp();

    // Binary or Ascii ?

    $mode = FTP_BINARY;

    if ((false !== stripos($mimetype, "text")) || (false !== stripos($mimetype, "html"))) {
        $mode = FTP_ASCII;
    }

    $res = @ftp_put($conn_id, $dest_file, $source_file, $mode);

    if ((!$res) && (!$disable_error_mode)) {
        $error = true;

        if (!empty($error_msg)) {
            $error_msg .= '<br>';
        }

        $error_msg = sprintf($lang['Ftp_error_upload'], $attach_config['ftp_path']) . '<br>';

        @ftp_quit($conn_id);

        return (false);
    }

    if (!$res) {
        return (false);
    }

    @ftp_site($conn_id, 'CHMOD 0644 ' . $dest_file);

    @ftp_quit($conn_id);

    return (true);
}

//
// Check if Attachment exist
//
function attachment_exists($filename)
{
    global $upload_dir, $attach_config;

    if (!(int)$attach_config['allow_ftp_upload']) {
        if (!@file_exists(@amod_realpath($upload_dir . '/' . $filename))) {
            return (false);
        }

        return (true);
    }

    $found = false;

    $conn_id = attach_init_ftp();

    $file_listing = [];

    $file_listing = @ftp_rawlist($conn_id, $filename);

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

        if ((1 != $dirinfo[0]) && ($dirinfo[4] == $filename)) {
            $found = true;
        }
    }

    @ftp_quit($conn_id);

    return ($found);
}

//
// Check if Thumbnail exist
//
function thumbnail_exists($filename)
{
    global $upload_dir, $attach_config;

    if (!(int)$attach_config['allow_ftp_upload']) {
        if (!@file_exists(@amod_realpath($upload_dir . '/' . THUMB_DIR . '/t_' . $filename))) {
            return (false);
        }

        return (true);
    }

    $found = false;

    $conn_id = attach_init_ftp(MODE_THUMBNAIL);

    $file_listing = [];

    $filename = 't_' . $filename;

    $file_listing = @ftp_rawlist($conn_id, $filename);

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

        if ((1 != $dirinfo[0]) && ($dirinfo[4] == $filename)) {
            $found = true;
        }
    }

    @ftp_quit($conn_id);

    return ($found);
}

//
// Physical Filename stored already ?
//
function physical_filename_already_stored($filename)
{
    global $db;

    if ('' == $filename) {
        return (false);
    }

    $sql = 'SELECT attach_id FROM ' . ATTACHMENTS_DESC_TABLE . "
WHERE physical_filename = '" . $filename . "' LIMIT 1";

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not get attachment information for filename: ' . $filename, '', __LINE__, __FILE__, $sql);
    }

    return (0 == $db->sql_numrows($result)) ? false : true;
}

//
// Determine if an Attachment exist in a post/pm
//
function attachment_exists_db($post_id, $page = -1)
{
    global $db;

    if (PAGE_PRIVMSGS == $page) {
        $sql_id = 'privmsgs_id';
    } else {
        $sql_id = 'post_id';
    }

    $sql = 'SELECT attach_id
FROM ' . ATTACHMENTS_TABLE . '
WHERE ' . $sql_id . ' = ' . $post_id . ' LIMIT 1';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not get attachment informations for specific posts', '', __LINE__, __FILE__, $sql);
    }

    if (($db->sql_numrows($result)) > 0) {
        return (true);
    }

    return (false);
}

//
// get all attachments from a post (could be an post array too)
//
function get_attachments_from_post($post_id_array)
{
    global $db, $attach_config;

    $attachments = [];

    if (!is_array($post_id_array)) {
        if (empty($post_id_array)) {
            return ($attachments);
        }

        $post_id = (int)$post_id_array;

        $post_id_array = [];

        $post_id_array[] = $post_id;
    }

    $post_id_array = implode(', ', $post_id_array);

    if ('' == $post_id_array) {
        return ($attachments);
    }

    $display_order = (0 == (int)$attach_config['display_order']) ? 'DESC' : 'ASC';

    $sql = 'SELECT a.post_id, d.*
FROM ' . ATTACHMENTS_TABLE . ' a, ' . ATTACHMENTS_DESC_TABLE . ' d
WHERE ( a.post_id IN (' . $post_id_array . ')) AND (a.attach_id = d.attach_id)
ORDER BY d.filetime ' . $display_order;

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not get Attachment Informations for post number ' . $post_id_array, '', __LINE__, __FILE__, $sql);
    }

    if (0 == ($db->sql_numrows($result))) {
        return ($attachments);
    }

    $attachments = $db->sql_fetchrowset($result);

    return ($attachments);
}

//
// get all attachments from a pm
//
function get_attachments_from_pm($privmsgs_id_array)
{
    global $db, $attach_config;

    $attachments = [];

    if (!is_array($privmsgs_id_array)) {
        if (empty($privmsgs_id_array)) {
            return ($attachments);
        }

        $privmsgs_id = (int)$privmsgs_id_array;

        $privmsgs_id_array = [];

        $privmsgs_id_array[] = $privmsgs_id;
    }

    $privmsgs_id_array = implode(', ', $privmsgs_id_array);

    if ('' == $privmsgs_id_array) {
        return ($attachments);
    }

    $display_order = (0 == (int)$attach_config['display_order']) ? 'DESC' : 'ASC';

    $sql = 'SELECT a.privmsgs_id, d.*
FROM ' . ATTACHMENTS_TABLE . ' a, ' . ATTACHMENTS_DESC_TABLE . ' d
WHERE ( a.privmsgs_id IN (' . $privmsgs_id_array . ')) AND (a.attach_id = d.attach_id)
ORDER BY d.filetime ' . $display_order;

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not get Attachment Informations for private message number ' . $privmsgs_id_array, '', __LINE__, __FILE__, $sql);
    }

    if (0 == ($db->sql_numrows($result))) {
        return ($attachments);
    }

    $attachments = $db->sql_fetchrowset($result);

    return ($attachments);
}

//
// Count Filesize of Attachments in Database based on the attachment id
//
function get_total_attach_filesize($attach_ids)
{
    global $db;

    $sql = 'SELECT filesize
FROM ' . ATTACHMENTS_DESC_TABLE . '
WHERE attach_id IN (' . $attach_ids . ')';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not query Total Filesize', '', __LINE__, __FILE__, $sql);
    }

    $filesizes = $db->sql_fetchrowset($result);

    $num_filesizes = $db->sql_numrows($result);

    $total_filesize = 0;

    if ($num_filesizes > 0) {
        for ($i = 0; $i < $num_filesizes; $i++) {
            $total_filesize += (int)$filesizes[$i]['filesize'];
        }
    }

    return ($total_filesize);
}

//
// Count Filesize for Attachments in Users PM Boxes (Do not count the SENT Box)
//
function get_total_attach_pm_filesize($direction, $user_id)
{
    global $db;

    if (('from_user' != $direction) && ('to_user' != $direction)) {
        return (0);
    }

    $user_sql = ('from_user' == $direction) ? '(a.user_id_1 = ' . (int)$user_id . ')' : '(a.user_id_2 = ' . (int)$user_id . ')';

    $sql = 'SELECT a.attach_id
FROM ' . ATTACHMENTS_TABLE . ' a, ' . PRIVMSGS_TABLE . ' p
WHERE ' . $user_sql . ' AND (a.privmsgs_id != 0) AND (a.privmsgs_id = p.privmsgs_id) AND (p.privmsgs_type != ' . PRIVMSGS_SENT_MAIL . ')';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not query Attachment Informations', '', __LINE__, __FILE__, $sql);
    }

    $pm_filesize_total = 0;

    $rows = $db->sql_fetchrowset($result);

    $num_rows = $db->sql_numrows($result);

    $attach_id = [];

    if (0 == $num_rows) {
        return ($pm_filesize_total);
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $attach_id[] = $rows[$i]['attach_id'];
    }

    $attach_id = implode(', ', $attach_id);

    return (get_total_attach_filesize($attach_id));
}

//
// Prune Attachments <- called from includes/prune.php
//
function prune_attachments($sql_post)
{
    // Yeah, prune it.

    delete_attachment($sql_post);
}

//
// Get allowed Extensions and their respective Values
//
function get_extension_informations()
{
    global $db;

    $extensions = [];

    // Don't count on forbidden extensions table, because it is not allowed to allow forbidden extensions at all

    $sql = 'SELECT e.extension, g.cat_id, g.download_mode, g.upload_icon
FROM ' . EXTENSIONS_TABLE . ' e, ' . EXTENSION_GROUPS_TABLE . ' g
WHERE (e.group_id = g.group_id) AND (g.allow_group = 1)';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not query Allowed Extensions.', '', __LINE__, __FILE__, $sql);
    }

    $extensions = $db->sql_fetchrowset($result);

    return ($extensions);
}

//
// Sync Topic
//
function attachment_sync_topic($topic_id)
{
    global $db;

    $sql = 'SELECT post_id FROM ' . POSTS_TABLE . ' WHERE topic_id = ' . $topic_id . '
GROUP BY post_id';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Couldn\'t select Post ID\'s', '', __LINE__, __FILE__, $sql);
    }

    $post_list = $db->sql_fetchrowset($result);

    $num_posts = $db->sql_numrows($result);

    if (0 == $num_posts) {
        return;
    }

    $post_ids = [];

    for ($i = 0; $i < $num_posts; $i++) {
        $post_ids[] = (int)$post_list[$i]['post_id'];
    }

    $post_id_sql = implode(', ', $post_ids);

    if ('' == $post_id_sql) {
        return;
    }

    $sql = 'SELECT attach_id FROM ' . ATTACHMENTS_TABLE . ' WHERE post_id IN (' . $post_id_sql . ') LIMIT 1';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Couldn\'t select Attachment ID\'s', '', __LINE__, __FILE__, $sql);
    }

    $set_id = (0 == $db->sql_numrows($result)) ? 0 : 1;

    $sql = 'UPDATE ' . TOPICS_TABLE . ' SET topic_attachment = ' . $set_id . ' WHERE topic_id = ' . $topic_id;

    if (!(attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Couldn\'t update Topics Table', '', __LINE__, __FILE__, $sql);
    }

    for ($i = 0, $iMax = count($post_ids); $i < $iMax; $i++) {
        $sql = 'SELECT attach_id FROM ' . ATTACHMENTS_TABLE . ' WHERE post_id = ' . $post_ids[$i] . ' LIMIT 1';

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Couldn\'t select Attachment ID\'s', '', __LINE__, __FILE__, $sql);
        }

        $set_id = (0 == $db->sql_numrows($result)) ? 0 : 1;

        $sql = 'UPDATE ' . POSTS_TABLE . ' SET post_attachment = ' . $set_id . ' WHERE post_id = ' . $post_ids[$i];

        if (!(attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Couldn\'t update Posts Table', '', __LINE__, __FILE__, $sql);
        }
    }
}

//
// Get Extension
//
function get_extension($filename)
{
    $extension = mb_strrchr(mb_strtolower($filename), '.');

    $extension[0] = ' ';

    $extension = mb_strtolower(trim($extension));

    if (is_array($extension)) {
        return ('');
    }

    return ($extension);
}

//
// Delete Extension
//
function delete_extension($filename)
{
    return (mb_substr($filename, 0, mb_strrpos(mb_strtolower(trim($filename)), '.')));
}

//
// Check if a user is within Group
//
function user_in_group($user_id, $group_id)
{
    global $db;

    if ((empty($user_id)) || (empty($group_id))) {
        return (false);
    }

    $sql = 'SELECT u.group_id FROM ' . USER_GROUP_TABLE . ' u, ' . GROUPS_TABLE . ' g 
WHERE (g.group_single_user = 0) AND (u.group_id = g.group_id) AND (u.user_id = ' . $user_id . ') AND (g.group_id = ' . $group_id . ')
LIMIT 1';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, 'Could not get User Group', '', __LINE__, __FILE__, $sql);
    }

    if (0 == $db->sql_numrows($result)) {
        return (false);
    }

    return (true);
}

// -> from phpBB 2.0.7
// This function is for compatibility with PHP 4.x's realpath()
// function. In later versions of PHP, it needs to be called
// to do checks with some functions. Older versions of PHP don't
// seem to need this, so we'll just return the original value.
// dougk_ff7 <October 5, 2002>
function amod_realpath($path)
{
    global $phpbb_root_path, $phpEx;

    return (!@function_exists('realpath') || !@realpath($phpbb_root_path . 'includes/functions.' . $phpEx)) ? $path : @realpath($path);
}
