<?php

// $Id: search.inc.php, v 1.1 2005/06/18 12:56:28 Modified by Koudanshi
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System //
// Copyright (c) 2000 xoopscube.org //
// <http://xoopscube.org> //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify //
// it under the terms of the GNU General Public License as published by //
// the Free Software Foundation; either version 2 of the License, or //
// (at your option) any later version.  //
//   //
// You may not change or alter any portion of this comment or credits //
// of supporting developers from this source code or any supporting //
// source code which is considered copyrighted (c) material of the //
// original comment or credit authors.  //
//   //
// This program is distributed in the hope that it will be useful, //
// but WITHOUT ANY WARRANTY; without even the implied warranty of //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the //
// GNU General Public License for more details. //
//   //
// You should have received a copy of the GNU General Public License //
// along with this program; if not, write to the Free Software //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)  //
// URL: http://www.myweb.ne.jp/, http://xoopscube.org/, http://jp.xoopscube.org/ //
// Project: The XOOPS Project  //
// ------------------------------------------------------------------------- //
function pbboard_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB, $sid_bb;

    $sql = 'SELECT pt.post_text, pt.post_subject, p.post_username, p.post_time, p.poster_id, t.topic_id, t.topic_title, t.forum_id
FROM ' . $xoopsDB->prefix('pbb_posts') . ' p,
' . $xoopsDB->prefix('pbb_topics') . ' t,
' . $xoopsDB->prefix('pbb_posts_text') . ' pt,
' . $xoopsDB->prefix('pbb_forums') . ' f
WHERE t.topic_id = p.topic_id
AND p.post_id = pt.post_id
AND f.forum_id = t.forum_id
';

    if (0 != $userid) {
        $sql .= ' AND poster_id = ' . $userid . ' ';
    }

    // because count() returns 1 even if a supplied variable

    // is not an array, we must check if $querryarray is really an array

    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((post_text LIKE '%$queryarray[0]%' OR post_username LIKE '%$queryarray[0]%' OR post_subject LIKE '%$queryarray[0]%' OR topic_title LIKE '%$queryarray[0]%' )";

        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";

            $sql .= "(post_text LIKE '%$queryarray[$i]%' OR post_username LIKE '%$queryarray[$i]%' OR post_subject LIKE '%$queryarray[$i]%' OR topic_title LIKE '%$queryarray[$i]%' )";
        }

        $sql .= ') ';
    }

    $sql .= 'ORDER BY post_time DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $ret = [];

    $i = 0;

    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret[$i]['image'] = 'images/search.gif';

        $ret[$i]['link'] = 'viewtopic.php?t=' . $myrow['topic_id'] . "&sid=$sid_bb ";

        $ret[$i]['title'] = $myrow['topic_title'];

        $ret[$i]['time'] = $myrow['post_time'];

        $ret[$i]['uid'] = $myrow['poster_id'];

        $i++;
    }

    return $ret;
}
