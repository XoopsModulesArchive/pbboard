<?php

/***************************************************************************
 * common.php
 * -------------------
 * begin : Saturday, Feb 23, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 *
 * $Id: common.php,v 1.74.2.10 2005/06/04 17:41:39 acydburn Exp $
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
error_reporting(E_ERROR | E_WARNING | E_PARSE); // This will NOT report uninitialized variables
set_magic_quotes_runtime(0); // Disable magic_quotes_runtime
//
// addslashes to vars if magic_quotes_gpc is off
// this is a security precaution to prevent someone
// trying to break out of a SQL statement.
//
if (!get_magic_quotes_gpc()) {
    if (is_array($_GET)) {
        while (list($k, $v) = each($_GET)) {
            if (is_array($_GET[$k])) {
                while (list($k2, $v2) = each($_GET[$k])) {
                    $_GET[$k][$k2] = addslashes($v2);
                }

                @reset($_GET[$k]);
            } else {
                $_GET[$k] = addslashes($v);
            }
        }

        @reset($_GET);
    }

    if (is_array($_POST)) {
        while (list($k, $v) = each($_POST)) {
            if (is_array($_POST[$k])) {
                while (list($k2, $v2) = each($_POST[$k])) {
                    $_POST[$k][$k2] = addslashes($v2);
                }

                @reset($_POST[$k]);
            } else {
                $_POST[$k] = addslashes($v);
            }
        }

        @reset($_POST);
    }

    if (is_array($HTTP_COOKIE_VARS)) {
        while (list($k, $v) = each($HTTP_COOKIE_VARS)) {
            if (is_array($HTTP_COOKIE_VARS[$k])) {
                while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k])) {
                    $HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2);
                }

                @reset($HTTP_COOKIE_VARS[$k]);
            } else {
                $HTTP_COOKIE_VARS[$k] = addslashes($v);
            }
        }

        @reset($HTTP_COOKIE_VARS);
    }
}
//
// Define some basic configuration arrays this also prevents
// malicious rewriting of language and otherarray values via
// URI params
//
$board_config = [];
$userdata = [];
$theme = [];
$images = [];
$lang = [];
$gen_simple_header = false;
/*
include $phpbb_root_path . 'config.'.$phpEx;
if( !defined("PHPBB_INSTALLED") )
{
header("Location: install/install.$phpEx");
exit;
}
*/
//XOOPS mainfile
if (file_exists(__DIR__ . '/../../mainfile.' . $phpEx)) {
    require dirname(__DIR__, 2) . '/mainfile.' . $phpEx;
} else {
    require dirname(__DIR__, 3) . '/mainfile.' . $phpEx;
}
// Define config informations
$dbms = 'mysql4';
$dbhost = XOOPS_DB_HOST;
$dbname = XOOPS_DB_NAME;
$dbuser = XOOPS_DB_USER;
$dbpasswd = XOOPS_DB_PASS;
$table_prefix = '' . XOOPS_DB_PREFIX . '_pbb_';

include $phpbb_root_path . 'includes/constants.' . $phpEx;
include $phpbb_root_path . 'includes/template.' . $phpEx;
include $phpbb_root_path . 'includes/sessions.' . $phpEx;
include $phpbb_root_path . 'includes/auth.' . $phpEx;
include $phpbb_root_path . 'includes/functions.' . $phpEx;
include $phpbb_root_path . 'includes/db.' . $phpEx;
//
// Obtain and encode users IP
//
if ('' != getenv('HTTP_X_FORWARDED_FOR')) {
    $client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);

    $entries = explode(',', getenv('HTTP_X_FORWARDED_FOR'));

    reset($entries);

    while (list(, $entry) = each($entries)) {
        $entry = trim($entry);

        if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
            $private_ip = ['/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', '/^10\..*/', '/^224\..*/', '/^240\..*/'];

            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

            if ($client_ip != $found_ip) {
                $client_ip = $found_ip;

                break;
            }
        }
    }
} else {
    $client_ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : $REMOTE_ADDR);
}
$user_ip = encode_ip($client_ip);
//
// Setup forum wide options, if this fails
// then we output a CRITICAL_ERROR since
// basic forum information is not available
//
$sql = 'SELECT *
FROM ' . CONFIG_TABLE;
if (!($result = $db->sql_query($sql))) {
    message_die(CRITICAL_ERROR, 'Could not query config information', '', __LINE__, __FILE__, $sql);
}
while (false !== ($row = $db->sql_fetchrow($result))) {
    $board_config[$row['config_name']] = $row['config_value'];
}
include $phpbb_root_path . 'attach_mod/attachment_mod.' . $phpEx;
//----------------------------------
// Update config table by Koudanshi
//----------------------------------
if (empty($board_config['server_name'])) {
    if (!($db->sql_query('UPDATE ' . CONFIG_TABLE . " SET config_value = '" . XOOPS_DB_HOST . "' WHERE config_name = 'server_name'"))) {
        message_die(CRITICAL_ERROR, 'Could not update <font color=red>server_name</font> config information in the first time', '', __LINE__, __FILE__, $sql);
    }

    if (!($db->sql_query('UPDATE ' . CONFIG_TABLE . " SET config_value = '" . dirname($HTTP_SERVER_VARS['PHP_SELF']) . "/' WHERE config_name = 'script_path'"))) {
        message_die(CRITICAL_ERROR, 'Could not update <font color=red>script_path</font> config information in the first time', '', __LINE__, __FILE__, $sql);
    }

    if (!($db->sql_query('UPDATE ' . CONFIG_TABLE . " SET config_value = '" . time() . "' WHERE config_name = 'board_startdate'"))) {
        message_die(CRITICAL_ERROR, 'Could not update <font color=red>script_path</font> config information in the first time', '', __LINE__, __FILE__, $sql);
    }

    //----------------------------------

    // Update user groups table of PBBM

    //----------------------------------

    $query = $db->sql_query('SELECT uid FROM ' . USERS_TABLE . ' ');

    echo(' <Font color = blue size=2>Updating groups user table...</font>');

    while (false !== ($row = $db->sql_fetchrow($query))) {
        $sql = 'INSERT INTO ' . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator) VALUES ('', 'Personal User', 1, 0)";

        if (!($result = $db->sql_query($sql))) {
            message_die(GENERAL_ERROR, 'Could not insert data into groups table', '', __LINE__, __FILE__, $sql);
        }

        $group_id = $db->sql_nextid();

        $sql = 'INSERT INTO ' . USER_GROUP_TABLE . ' (user_id, group_id, user_pending) VALUES (' . $row['uid'] . ", $group_id, 0)";

        if (!($result = $db->sql_query($sql, END_TRANSACTION))) {
            message_die(GENERAL_ERROR, 'Could not insert data into user_group table', '', __LINE__, __FILE__, $sql);
        }
    }

    echo(' <Font color = blue size=2>done.</font> <br>Can use with your own board.;)');

    //----------------------------------

    // Update Administrator permission

    //----------------------------------

    $sql3 = $db->sql_query('SELECT uid FROM ' . XOOPS_DB_PREFIX . "_groups_users_link WHERE groupid = '1' ");

    while (false !== ($row = $db->sql_fetchrow($sql3))) {
        $db->sql_query('UPDATE ' . USERS_TABLE . " SET user_level = '1' WHERE uid = '" . $row['uid'] . "' ");
    }
}// End if updating.
if (file_exists('install') || file_exists('contrib')) {
    message_die(GENERAL_MESSAGE, 'Please ensure both the install/ and contrib/ directories are deleted');
}
//
// Show 'Board is disabled' message if needed.
//
if ($board_config['board_disable'] && !defined('IN_ADMIN') && !defined('IN_LOGIN')) {
    message_die(GENERAL_MESSAGE, 'Board_disable', 'Information');
}
