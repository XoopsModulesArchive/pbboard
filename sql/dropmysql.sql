# --------------------------------------------------------
#
# Table structure for table 'pbb_users'
#
ALTER TABLE users
    DROP user_session_time,
    DROP user_session_page,
    DROP user_level,
    DROP user_style,
    DROP user_lang,
    DROP user_dateformat,
    DROP user_new_privmsg,
    DROP user_unread_privmsg,
    DROP user_last_privmsg,
    DROP user_emailtime,
    DROP user_allowhtml,
    DROP user_allowbbcode,
    DROP user_allowsmile,
    DROP user_allowavatar,
    DROP user_allow_pm,
    DROP user_allow_viewonline,
    DROP user_notify,
    DROP user_notify_pm,
    DROP user_popup_pm,
    DROP user_avatar_type,
    DROP user_sig_bbcode_uid,
    DROP user_newpasswd,
    DROP user_lastvisit,
    DROP INDEX user_session_time
;
DELETE
  FROM users
 WHERE uname = 'Anonymous';
# --------------------------------------------------------
#
# Table structure for table 'pbb_sessions'
#
ALTER TABLE session
    DROP session_user_id,
    DROP session_start,
    DROP session_page,
    DROP session_logged_in,
    DROP INDEX session_user_id,
    DROP INDEX session_id_ip_user_id
;
