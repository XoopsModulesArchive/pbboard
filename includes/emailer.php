<?php

/***************************************************************************
 * emailer.php
 * -------------------
 * begin : Sunday Aug. 12, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : support@phpbb.com
 * $Id: emailer.php,v 1.15.2.29 2005/06/15 12:08:20 acydburn Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
//
// The emailer class has support for attaching files, that isn't implemented
// in the 2.0 release but we can probable find some way of using it in a future
// release
//
class emailer
{
    public $msg;

    public $subject;

    public $extra_headers;

    public $addresses;

    public $reply_to;

    public $from;

    public $use_smtp;

    public $tpl_msg = [];

    public function __construct($use_smtp)
    {
        $this->reset();

        $this->use_smtp = $use_smtp;
    }

    // Resets all the data (address, template file, etc etc to default

    public function reset()
    {
        $this->addresses = [];

        $this->vars = $this->msg = $this->extra_headers = $this->replyto = $this->from = '';
    }

    // Sets an email address to send to

    public function email_address($address, $realname = '')
    {
        $pos = count($this->addresses['to']);

        $this->addresses['to'][$pos]['email'] = trim($address);

        $this->addresses['to'][$pos]['name'] = trim($realname);
    }

    public function cc($address, $realname = '')
    {
        $pos = count($this->addresses['cc']);

        $this->addresses['cc'][$pos]['email'] = trim($address);

        $this->addresses['cc'][$pos]['name'] = trim($realname);
    }

    public function bcc($address, $realname = '')
    {
        $pos = count($this->addresses['bcc']);

        $this->addresses['bcc'][$pos]['email'] = trim($address);

        $this->addresses['bcc'][$pos]['name'] = trim($realname);
    }

    public function replyto($address)
    {
        $this->replyto = trim($address);
    }

    public function from($address)
    {
        $this->from = trim($address);
    }

    // set up subject for mail

    public function set_subject($subject = '')
    {
        $this->subject = trim($subject);
    }

    // set up extra mail headers

    public function extra_headers($headers)
    {
        $this->extra_headers .= trim($headers) . "\n";
    }

    public function use_template($template_file, $template_lang = '')
    {
        global $board_config, $phpbb_root_path;

        if ('' == trim($template_file)) {
            message_die(GENERAL_ERROR, 'No template file set', '', __LINE__, __FILE__);
        }

        if ('' == trim($template_lang)) {
            $template_lang = $board_config['default_lang'];
        }

        if (empty($this->tpl_msg[$template_lang . $template_file])) {
            $tpl_file = $phpbb_root_path . 'language/lang_' . $template_lang . '/email/' . $template_file . '.tpl';

            if (!@file_exists(@phpbb_realpath($tpl_file))) {
                $tpl_file = $phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/email/' . $template_file . '.tpl';

                if (!@file_exists(@phpbb_realpath($tpl_file))) {
                    message_die(GENERAL_ERROR, 'Could not find email template file :: ' . $template_file, '', __LINE__, __FILE__);
                }
            }

            if (!($fd = @fopen($tpl_file, 'rb'))) {
                message_die(GENERAL_ERROR, 'Failed opening template file :: ' . $tpl_file, '', __LINE__, __FILE__);
            }

            $this->tpl_msg[$template_lang . $template_file] = fread($fd, filesize($tpl_file));

            fclose($fd);
        }

        $this->msg = $this->tpl_msg[$template_lang . $template_file];

        return true;
    }

    // assign variables

    public function assign_vars($vars)
    {
        $this->vars = (empty($this->vars)) ? $vars : $this->vars . $vars;
    }

    // Send the mail out to the recipients set previously in var $this->address

    public function send()
    {
        global $board_config, $lang, $phpEx, $phpbb_root_path, $db;

        // Escape all quotes, else the eval will fail.

        $this->msg = str_replace("'", "\'", $this->msg);

        $this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

        // Set vars

        reset($this->vars);

        while (list($key, $val) = each($this->vars)) {
            $$key = $val;
        }

        eval("\$this->msg = '$this->msg';");

        // Clear vars

        reset($this->vars);

        while (list($key, $val) = each($this->vars)) {
            unset($$key);
        }

        // We now try and pull a subject from the email body ... if it exists,

        // do this here because the subject may contain a variable

        $drop_header = '';

        $match = [];

        if (preg_match('#^(Subject:(.*?))$#m', $this->msg, $match)) {
            $this->subject = ('' != trim($match[2])) ? trim($match[2]) : (('' != $this->subject) ? $this->subject : 'No Subject');

            $drop_header .= '[\r\n]*?' . phpbb_preg_quote($match[1], '#');
        } else {
            $this->subject = (('' != $this->subject) ? $this->subject : 'No Subject');
        }

        if (preg_match('#^(Charset:(.*?))$#m', $this->msg, $match)) {
            $this->encoding = ('' != trim($match[2])) ? trim($match[2]) : trim($lang['ENCODING']);

            $drop_header .= '[\r\n]*?' . phpbb_preg_quote($match[1], '#');
        } else {
            $this->encoding = trim($lang['ENCODING']);
        }

        if ('' != $drop_header) {
            $this->msg = trim(preg_replace('#' . $drop_header . '#s', '', $this->msg));
        }

        $to = $cc = $bcc = '';

        // Build to, cc and bcc strings

        @reset($this->addresses);

        while (list($type, $address_ary) = each($this->addresses)) {
            @reset($address_ary);

            while (list(, $which_ary) = each($address_ary)) {
                $$type .= (('' != $$type) ? ',' : '') . (('' != $which_ary['name']) ? '"' . $this->encode($which_ary['name']) . '" <' . $which_ary['email'] . '>' : '<' . $which_ary['email'] . '>');
            }
        }

        // Build header

        $this->extra_headers = (('' != $this->replyto) ? "Reply-to: <$this->replyto>\n" : '')
                               . (('' != $this->from) ? "From: <$this->from>\n" : 'From: <' . $board_config['board_email'] . ">\n")
                               . 'Return-Path: <'
                               . $board_config['board_email']
                               . ">\nMessage-ID: <"
                               . md5(uniqid(time()))
                               . '@'
                               . $board_config['server_name']
                               . ">\nMIME-Version: 1.0\nContent-type: text/plain; charset="
                               . $this->encoding
                               . "\nContent-transfer-encoding: 8bit\nDate: "
                               . gmdate('D, d M Y H:i:s Z', time())
                               . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\nX-MimeOLE: Produced By phpBB2\n"
                               . trim($this->extra_headers)
                               . (('' != $cc) ? "Cc:$cc\n" : '')
                               . (('' != $bcc) ? "Bcc:$bcc\n" : '');

        $empty_to_header = ('' == $to) ? true : false;

        $to = ('' == $to) ? (($board_config['sendmail_fix'] && !$this->use_smtp) ? ' ' : 'Undisclosed-recipients:;') : $to;

        // Send message ... removed $this->encode() from subject for time being

        if ($this->use_smtp) {
            if (!defined('SMTP_INCLUDED')) {
                include $phpbb_root_path . 'includes/smtp.' . $phpEx;
            }

            $result = smtpmail($to, $this->subject, $this->msg, $this->extra_headers);
        } else {
            $result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);

            if (!$result && !$board_config['sendmail_fix'] && $empty_to_header) {
                $to = ' ';

                $sql = 'UPDATE ' . CONFIG_TABLE . "
SET config_value = '1'
WHERE config_name = 'sendmail_fix'";

                if (!$db->sql_query($sql)) {
                    message_die(GENERAL_ERROR, 'Unable to update config table', '', __LINE__, __FILE__, $sql);
                }

                $board_config['sendmail_fix'] = 1;

                $result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
            }
        }

        // Did it work?

        if (!$result) {
            message_die(GENERAL_ERROR, 'Failed sending email :: ' . (($this->use_smtp) ? 'SMTP' : 'PHP') . ' :: ' . $result, '', __LINE__, __FILE__);
        }

        return true;
    }

    // Encodes the given string for proper display for this encoding ... nabbed

    // from php.net and modified. There is an alternative encoding method which

    // may produce lesd output but it's questionable as to its worth in this

    // scenario IMO

    public function encode($str)
    {
        if ('' == $this->encoding) {
            return $str;
        }

        // define start delimimter, end delimiter and spacer

        $end = '?=';

        $start = "=?$this->encoding?B?";

        $spacer = "$end\r\n $start";

        // determine length of encoded text within chunks and ensure length is even

        $length = 75 - mb_strlen($start) - mb_strlen($end);

        $length = floor($length / 2) * 2;

        // encode the string and split it into chunks with spacers after each chunk

        $str = chunk_preg_split(base64_encode($str), $length, $spacer);

        // remove trailing spacer and add start and end delimiters

        $str = preg_replace('#' . phpbb_preg_quote($spacer) . '$#', '', $str);

        return $start . $str . $end;
    }

    // Attach files via MIME.

    public function attachFile($filename, $mimetype, $szFromAddress, $szFilenameToDisplay)
    {
        global $lang;

        $mime_boundary = '--==================_846811060==_';

        $this->msg = '--' . $mime_boundary . "\nContent-Type: text/plain;\n\tcharset=\"" . $lang['ENCODING'] . "\"\n\n" . $this->msg;

        if ($mime_filename) {
            $filename = $mime_filename;

            $encoded = $this->encode_file($filename);
        }

        $fd = fopen($filename, 'rb');

        $contents = fread($fd, filesize($filename));

        $this->mimeOut = '--' . $mime_boundary . "\n";

        $this->mimeOut .= 'Content-Type: ' . $mimetype . ";\n\tname=\"$szFilenameToDisplay\"\n";

        $this->mimeOut .= "Content-Transfer-Encoding: quoted-printable\n";

        $this->mimeOut .= "Content-Disposition: attachment;\n\tfilename=\"$szFilenameToDisplay\"\n\n";

        if ('message/rfc822' == $mimetype) {
            $this->mimeOut .= 'From: ' . $szFromAddress . "\n";

            $this->mimeOut .= 'To: ' . $this->emailAddress . "\n";

            $this->mimeOut .= 'Date: ' . date('D, d M Y H:i:s') . " UT\n";

            $this->mimeOut .= 'Reply-To:' . $szFromAddress . "\n";

            $this->mimeOut .= 'Subject: ' . $this->mailSubject . "\n";

            $this->mimeOut .= 'X-Mailer: PHP/' . phpversion() . "\n";

            $this->mimeOut .= "MIME-Version: 1.0\n";
        }

        $this->mimeOut .= $contents . "\n";

        $this->mimeOut .= '--' . $mime_boundary . '--' . "\n";

        return $out;
        // added -- to notify email client attachment is done
    }

    public function getMimeHeaders($filename, $mime_filename = '')
    {
        $mime_boundary = '--==================_846811060==_';

        if ($mime_filename) {
            $filename = $mime_filename;
        }

        $out = "MIME-Version: 1.0\n";

        $out .= "Content-Type: multipart/mixed;\n\tboundary=\"$mime_boundary\"\n\n";

        $out .= "This message is in MIME format. Since your mail reader does not understand\n";

        $out .= 'this format, some or all of this message may not be legible.';

        return $out;
    }

    // Split string by RFC 2045 semantics (76 chars per line, end with \r\n).

    public function myChunkpreg_split($str)
    {
        $stmp = $str;

        $len = mb_strlen($stmp);

        $out = '';

        while ($len > 0) {
            if ($len >= 76) {
                $out .= mb_substr($stmp, 0, 76) . "\r\n";

                $stmp = mb_substr($stmp, 76);

                $len -= 76;
            } else {
                $out .= $stmp . "\r\n";

                $stmp = '';

                $len = 0;
            }
        }

        return $out;
    }

    // Split the specified file up into a string and return it

    public function encode_file($sourcefile)
    {
        if (is_readable(phpbb_realpath($sourcefile))) {
            $fd = fopen($sourcefile, 'rb');

            $contents = fread($fd, filesize($sourcefile));

            $encoded = $this->myChunkpreg_split(base64_encode($contents));

            fclose($fd);
        }

        return $encoded;
    }
} // class emailer
?>
