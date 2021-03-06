<?php

/***************************************************************************
 * functions_thumbs.php
 * -------------------
 * begin : Sat, Jul 27, 2002
 * copyright : (C) 2002 Meik Sievertsen
 * email : acyd.burn@gmx.de
 *
 * $Id: functions_thumbs.php,v 1.23 2005/06/18 17:53:01 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
//
// All Attachment Functions needed to create Thumbnails
//
if (!defined('IN_PHPBB')) {
    die('Hacking attempt');

    exit;
}
$imagick = '';
//
// Calculate the needed size for Thumbnail
//
function get_img_size_format($width, $height)
{
    // Change these two values to define the Thumbnail Size

    $max_width = 300;

    $max_height = 85;

    if ($width > $max_width) {
        $tag_height = ($max_width / $width) * $height;

        $tag_width = $max_width;

        if ($tag_height > $max_height) {
            $tag_width = ($max_height / $tag_height) * $tag_width;

            $tag_height = $max_height;
        }
    } elseif ($height > $max_height) {
        $tag_width = ($max_height / $height) * $width;

        $tag_height = $max_height;

        if ($tag_width > $max_width) {
            $tag_height = ($max_width / $tag_width) * $tag_height;

            $tag_width = $max_width;
        }
    } else {
        $tag_width = $width;

        $tag_height = $height;
    }

    $tag_width = round($tag_width);

    $tag_height = round($tag_height);

    return [
        $tag_width,
        $tag_height,
    ];
}

//
// Check if imagick is present
//
function is_imagick()
{
    global $imagick, $attach_config;

    if ('' != $attach_config['img_imagick']) {
        $imagick = $attach_config['img_imagick'];

        return (true);
    }

    return (false);
}

function get_supported_image_types()
{
    $types = [];

    if (extension_loaded('gd')) {
        if (function_exists('imagegif')) {
            $types[] = '1';
        }

        if (function_exists('imagejpeg')) {
            $types[] = '2';
        }

        if (function_exists('imagepng')) {
            $types[] = '3';
        }
    }

    return ($types);
}

function create_thumbnail($source, $new_file, $mimetype)
{
    global $attach_config, $imagick;

    $source = amod_realpath($source);

    $min_filesize = (int)$attach_config['img_min_thumb_filesize'];

    $img_filesize = (@file_exists(@amod_realpath($source))) ? filesize($source) : false;

    if (!$img_filesize || $img_filesize <= $min_filesize) {
        return (false);
    }

    $size = image_getdimension($source);

    if ((0 == $size[0]) && (0 == $size[1])) {
        return (false);
    }

    $new_size = get_img_size_format($size[0], $size[1]);

    $tmp_path = '';

    $old_file = '';

    if ((int)$attach_config['allow_ftp_upload']) {
        $old_file = $new_file;

        $tmp_path = explode('/', $source);

        $tmp_path[count($tmp_path) - 1] = '';

        $tmp_path = implode('/', $tmp_path);

        if ('' == $tmp_path) {
            $tmp_path = '/tmp';
        }

        $value = trim($tmp_path);

        if ('/' == $value[mb_strlen($value) - 1]) {
            $value[mb_strlen($value) - 1] = ' ';
        }

        $new_file = trim($value) . '/t00000';
    }

    $used_imagick = false;

    if (is_imagick()) {
        if (is_array($size) && count($size) > 0) {
            @exec($imagick . ' -quality 75 -antialias -sample ' . $new_size[0] . 'x' . $new_size[1] . ' ' . $source . ' +profile "*" ' . $new_file);

            if (@file_exists(@amod_realpath($new_file))) {
                $used_imagick = true;
            }
        }
    }

    if (!$used_imagick) {
        $type = $size[2];

        $supported_types = get_supported_image_types();

        if (in_array($type, $supported_types, true)) {
            switch ($type) {
                case '1':
                    $im = imagecreatefromgif($source);
                    $new_im = imagecreate($new_size[0], $new_size[1]);
                    imagecopyresized($new_im, $im, 0, 0, 0, 0, $new_size[0], $new_size[1], $size[0], $size[1]);
                    imagegif($new_im, $new_file);
                    break;
                case '2':
                    $im = imagecreatefromjpeg($source);
                    $new_im = imagecreate($new_size[0], $new_size[1]);
                    imagecopyresized($new_im, $im, 0, 0, 0, 0, $new_size[0], $new_size[1], $size[0], $size[1]);
                    imagejpeg($new_im, $new_file, 90);
                    break;
                case '3':
                    $im = imagecreatefrompng($source);
                    $new_im = imagecreate($new_size[0], $new_size[1]);
                    imagecopyresized($new_im, $im, 0, 0, 0, 0, $new_size[0], $new_size[1], $size[0], $size[1]);
                    imagepng($new_im, $new_file);
                    break;
            }
        }
    }

    if (!@file_exists(@amod_realpath($new_file))) {
        return (false);
    }

    if ((int)$attach_config['allow_ftp_upload']) {
        $result = ftp_file($new_file, $old_file, $this->type, true); // True for disable error-mode

        if (!$result) {
            return (false);
        }
    } else {
        @chmod($new_file, 0666);
    }

    return (true);
}
