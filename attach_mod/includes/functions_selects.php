<?php

/***************************************************************************
 * functions_selects.php
 * -------------------
 * begin : Saturday, Mar 30, 2002
 * copyright : (C) 2002 Meik Sievertsen
 * email : acyd.burn@gmx.de
 *
 * $Id: functions_selects.php,v 1.9 2002/12/12 19:47:29 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 **************************************************************************
 * @param     $select_name
 * @param int $default_group
 * @return string
 */
//
// Functions to build select boxes ;)
//
function group_select($select_name, $default_group = -1)
{
    global $db, $lang;

    $sql = 'SELECT group_id, group_name
FROM ' . EXTENSION_GROUPS_TABLE . '
ORDER BY group_name';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, "Couldn't query Extension Groups Table", '', __LINE__, __FILE__, $sql);
    }

    $group_select = '<select name="' . $select_name . '">';

    if (($db->sql_numrows($result)) > 0) {
        $group_name = $db->sql_fetchrowset($result);

        $group_name[$db->sql_numrows($result)]['group_id'] = 0;

        $group_name[$db->sql_numrows($result)]['group_name'] = $lang['Not_assigned'];

        for ($i = 0, $iMax = count($group_name); $i < $iMax; $i++) {
            if ((-1 == $default_group)) {
                $selected = (0 == $i) ? ' selected="selected"' : '';
            } else {
                $selected = ($group_name[$i]['group_id'] == $default_group) ? ' selected="selected"' : '';
            }

            $group_select .= '<option value="' . $group_name[$i]['group_id'] . '"' . $selected . '>' . $group_name[$i]['group_name'] . '</option>';
        }
    }

    $group_select .= '</select>';

    return ($group_select);
}

function download_select($select_name, $group_id = -1)
{
    global $db, $types_download, $modes_download;

    if (-1 != $group_id) {
        $sql = 'SELECT download_mode
FROM ' . EXTENSION_GROUPS_TABLE . '
WHERE group_id = ' . $group_id;

        if (!($result = attach_sql_query($sql))) {
            message_die(GENERAL_ERROR, "Couldn't query Extension Groups Table", '', __LINE__, __FILE__, $sql);
        }

        $row = $db->sql_fetchrow($result);

        if (0 == $db->sql_numrows($result)) {
            return ('');
        }

        $download_mode = $row['download_mode'];
    }

    $group_select = '<select name="' . $select_name . '">';

    for ($i = 0, $iMax = count($types_download); $i < $iMax; $i++) {
        if (-1 == $group_id) {
            $selected = (INLINE_LINK == $types_download[$i]) ? ' selected="selected"' : '';
        } else {
            $selected = ($row['download_mode'] == $types_download[$i]) ? ' selected="selected"' : '';
        }

        $group_select .= '<option value="' . $types_download[$i] . '"' . $selected . '>' . $modes_download[$i] . '</option>';
    }

    $group_select .= '</select>';

    return ($group_select);
}

function category_select($select_name, $group_id = -1)
{
    global $db, $types_category, $modes_category;

    $sql = 'SELECT group_id, cat_id
FROM ' . EXTENSION_GROUPS_TABLE;

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, "Couldn't select Category", '', __LINE__, __FILE__, $sql);
    }

    $rows = $db->sql_fetchrowset($result);

    $num_rows = $db->sql_numrows($result);

    $type_category = -1;

    if ($num_rows > 0) {
        for ($i = 0; $i < $num_rows; $i++) {
            if ($group_id == $rows[$i]['group_id']) {
                $category_type = $rows[$i]['cat_id'];
            }
        }
    }

    $types = [NONE_CAT];

    $modes = ['none'];

    for ($i = 0, $iMax = count($types_category); $i < $iMax; $i++) {
        $types[] = $types_category[$i];

        $modes[] = $modes_category[$i];
    }

    $group_select = '<select name="' . $select_name . '" style="width:100px">';

    for ($i = 0, $iMax = count($types); $i < $iMax; $i++) {
        if (-1 == $group_id) {
            $selected = (NONE_CAT == $types[$i]) ? ' selected="selected"' : '';
        } else {
            $selected = ($types[$i] == $category_type) ? ' selected="selected"' : '';
        }

        $group_select .= '<option value="' . $types[$i] . '"' . $selected . '>' . $modes[$i] . '</option>';
    }

    $group_select .= '</select>';

    return ($group_select);
}

function size_select($select_name, $size_compare)
{
    global $lang;

    $size_types_text = [$lang['Bytes'], $lang['KB'], $lang['MB']];

    $size_types = ['b', 'kb', 'mb'];

    $select_field = '<select name="' . $select_name . '">';

    for ($i = 0, $iMax = count($size_types_text); $i < $iMax; $i++) {
        $selected = ($size_compare == $size_types[$i]) ? ' selected="selected"' : '';

        $select_field .= '<option value="' . $size_types[$i] . '"' . $selected . '>' . $size_types_text[$i] . '</option>';
    }

    $select_field .= '</select>';

    return ($select_field);
}

function quota_limit_select($select_name, $default_quota = -1)
{
    global $db, $lang;

    $sql = 'SELECT quota_limit_id, quota_desc
FROM ' . QUOTA_LIMITS_TABLE . '
ORDER BY quota_limit ASC';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, "Couldn't query Quota Limits Table", '', __LINE__, __FILE__, $sql);
    }

    $quota_select = '<select name="' . $select_name . '">';

    $quota_name[0]['quota_limit_id'] = -1;

    $quota_name[0]['quota_desc'] = $lang['Not_assigned'];

    if (($db->sql_numrows($result)) > 0) {
        $rows = $db->sql_fetchrowset($result);

        for ($i = 0, $iMax = count($rows); $i < $iMax; $i++) {
            $quota_name[] = $rows[$i];
        }
    }

    for ($i = 0, $iMax = count($quota_name); $i < $iMax; $i++) {
        $selected = ($quota_name[$i]['quota_limit_id'] == $default_quota) ? ' selected="selected"' : '';

        $quota_select .= '<option value="' . $quota_name[$i]['quota_limit_id'] . '"' . $selected . '>' . $quota_name[$i]['quota_desc'] . '</option>';
    }

    $quota_select .= '</select>';

    return ($quota_select);
}

function default_quota_limit_select($select_name, $default_quota = 0)
{
    global $db, $lang;

    $sql = 'SELECT quota_limit_id, quota_desc
FROM ' . QUOTA_LIMITS_TABLE . '
ORDER BY quota_limit ASC';

    if (!($result = attach_sql_query($sql))) {
        message_die(GENERAL_ERROR, "Couldn't query Quota Limits Table", '', __LINE__, __FILE__, $sql);
    }

    $quota_select = '<select name="' . $select_name . '">';

    $quota_name[0]['quota_limit_id'] = 0;

    $quota_name[0]['quota_desc'] = $lang['No_quota_limit'];

    if (($db->sql_numrows($result)) > 0) {
        $rows = $db->sql_fetchrowset($result);

        for ($i = 0, $iMax = count($rows); $i < $iMax; $i++) {
            $quota_name[] = $rows[$i];
        }
    }

    for ($i = 0, $iMax = count($quota_name); $i < $iMax; $i++) {
        $selected = ($quota_name[$i]['quota_limit_id'] == $default_quota) ? ' selected="selected"' : '';

        $quota_select .= '<option value="' . $quota_name[$i]['quota_limit_id'] . '"' . $selected . '>' . $quota_name[$i]['quota_desc'] . '</option>';
    }

    $quota_select .= '</select>';

    return ($quota_select);
}
