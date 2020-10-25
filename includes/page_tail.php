<?php

/***************************************************************************
 * page_tail.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: page_tail.php,v 1.27.2.2 2002/11/26 11:42:12 psotfx Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
if (!defined('IN_PHPBB')) {
    die('Hacking attempt');
}
//
// Show the overall footer.
//
$admin_link = (ADMIN == $userdata['user_level']) ? '<a href="admin/index.'
                                                   . $phpEx
                                                   . '?sid='
                                                   . $userdata['sess_id']
                                                   . '">'
                                                   . $lang['Admin_panel']
                                                   . '</a><br><br>Integrated by <a target="_blank" href="http://www.koudanshi.net">Koudanshi</a> '
                                                   . $board_config['version_bb']
                                                   . ' &copy; 2005 Bulletin Board module.<br>' : 'Integrated by <a target="_blank" href="http://www.koudanshi.net">Koudanshi</a> ' . $board_config['version_bb'] . ' &copy; 2005 Bulletin Board module.<br>';
$template->set_filenames(
    [
        'overall_footer' => (empty($gen_simple_header)) ? 'overall_footer.tpl' : 'simple_footer.tpl',
    ]
);
$template->assign_vars(
    [
        'PHPBB_VERSION' => '2' . $board_config['version'],
        'TRANSLATION_INFO' => $lang['TRANSLATION_INFO'] ?? '',
        'ADMIN_LINK' => $admin_link,
    ]
);
$template->pparse('overall_footer');
// Close our DB connection.
//
$db->sql_close();
//
// Compress buffered output if required and send to browser
//
if ($do_gzip_compress) {
    // Borrowed from php.net!

    $gzip_contents = ob_get_contents();

    ob_end_clean();

    $gzip_size = mb_strlen($gzip_contents);

    $gzip_crc = crc32($gzip_contents);

    $gzip_contents = gzcompress($gzip_contents, 9);

    $gzip_contents = mb_substr($gzip_contents, 0, -4);

    echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";

    echo $gzip_contents;

    echo pack('V', $gzip_crc);

    echo pack('V', $gzip_size);
}
//XOOPS
include './../../footer.php';

exit;
