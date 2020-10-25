#
# Table structure for table 'pbb_attachments_config'
#
CREATE TABLE pbb_attachments_config (
    config_name  VARCHAR(255) NOT NULL,
    config_value VARCHAR(255) NOT NULL,
    PRIMARY KEY (config_name)
);
#
# Table structure for table 'pbb_forbidden_extensions'
#
CREATE TABLE pbb_forbidden_extensions (
    ext_id    MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    extension VARCHAR(100)          NOT NULL,
    PRIMARY KEY (ext_id)
);
#
# Table structure for table 'pbb_extension_groups'
#
CREATE TABLE pbb_extension_groups (
    group_id          MEDIUMINT(8)                    NOT NULL AUTO_INCREMENT,
    group_name        CHAR(20)                        NOT NULL,
    cat_id            TINYINT(2)          DEFAULT '0' NOT NULL,
    allow_group       TINYINT(1)          DEFAULT '0' NOT NULL,
    download_mode     TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL,
    upload_icon       VARCHAR(100)        DEFAULT '',
    max_filesize      INT(20)             DEFAULT '0' NOT NULL,
    forum_permissions VARCHAR(255)        DEFAULT ''  NOT NULL,
    PRIMARY KEY group_id (group_id)
);
#
# Table structure for table 'pbb_extensions'
#
CREATE TABLE pbb_extensions (
    ext_id    MEDIUMINT(8) UNSIGNED             NOT NULL AUTO_INCREMENT,
    group_id  MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    extension VARCHAR(100)                      NOT NULL,
    comment   VARCHAR(100),
    PRIMARY KEY ext_id (ext_id)
);
#
# Table structure for table 'pbb_attachments_desc'
#
CREATE TABLE pbb_attachments_desc (
    attach_id         MEDIUMINT(8) UNSIGNED             NOT NULL AUTO_INCREMENT,
    physical_filename VARCHAR(255)                      NOT NULL,
    real_filename     VARCHAR(255)                      NOT NULL,
    download_count    MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    comment           VARCHAR(255),
    extension         VARCHAR(100),
    mimetype          VARCHAR(100),
    filesize          INT(20)                           NOT NULL,
    filetime          INT(11)               DEFAULT '0' NOT NULL,
    thumbnail         TINYINT(1)            DEFAULT '0' NOT NULL,
    PRIMARY KEY (attach_id),
    KEY filetime (filetime),
    KEY physical_filename (physical_filename(10)),
    KEY filesize (filesize)
);
#
# Table structure for table 'pbb_attachments'
#
CREATE TABLE pbb_attachments (
    attach_id   MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    post_id     MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    privmsgs_id MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    user_id_1   MEDIUMINT(8)                      NOT NULL,
    user_id_2   MEDIUMINT(8)                      NOT NULL,
    KEY attach_id_post_id (attach_id, post_id),
    KEY attach_id_privmsgs_id (attach_id, privmsgs_id)
);
#
# Table structure for table 'pbb_quota_limits'
#
CREATE TABLE pbb_quota_limits (
    quota_limit_id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    quota_desc     VARCHAR(20)           NOT NULL DEFAULT '',
    quota_limit    BIGINT(20) UNSIGNED   NOT NULL DEFAULT '0',
    PRIMARY KEY (quota_limit_id)
);
#
# Table structure for table 'pbb_attach_quota'
#
CREATE TABLE pbb_attach_quota (
    user_id        MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    group_id       MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    quota_type     SMALLINT(2)           NOT NULL DEFAULT '0',
    quota_limit_id MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    KEY quota_type (quota_type)
);
# ----------------------
# Users table modify
# ----------------------
ALTER TABLE users
    CHANGE user_avatar user_avatar   VARCHAR(100),
    CHANGE user_aim user_aim         VARCHAR(255)                             NULL,
    CHANGE user_yim user_yim         VARCHAR(255)                             NULL,
    CHANGE user_msnm user_msnm       VARCHAR(255)                             NULL,
    CHANGE rank rank                 INT(11)                                  NULL,
    CHANGE user_intrest user_intrest VARCHAR(150)                             NULL,
    CHANGE user_sig user_sig         TEXT                                     NULL,
    CHANGE user_from user_from       VARCHAR(100)                             NULL,
    CHANGE user_icq user_icq         VARCHAR(15)                              NULL,
    CHANGE user_occ user_occ         VARCHAR(100)                             NULL,
    CHANGE bio bio                   TINYTEXT                                 NULL,
    ADD user_lastvisit               INT(11)              DEFAULT '0'         NOT NULL,
    ADD user_session_time            INT(11)              DEFAULT '0'         NOT NULL,
    ADD user_session_page            SMALLINT(5)          DEFAULT '0'         NOT NULL,
    ADD user_level                   TINYINT(4)           DEFAULT '0',
    ADD user_style                   TINYINT(4),
    ADD user_lang                    VARCHAR(255),
    ADD user_dateformat              VARCHAR(14)          DEFAULT 'd M Y H:i' NOT NULL,
    ADD user_new_privmsg             SMALLINT(5) UNSIGNED DEFAULT '0'         NOT NULL,
    ADD user_unread_privmsg          SMALLINT(5) UNSIGNED DEFAULT '0'         NOT NULL,
    ADD user_last_privmsg            INT(11)              DEFAULT '0'         NOT NULL,
    ADD user_emailtime               INT(11),
    ADD user_allowhtml               TINYINT(1)           DEFAULT '1',
    ADD user_allowbbcode             TINYINT(1)           DEFAULT '1',
    ADD user_allowsmile              TINYINT(1)           DEFAULT '1',
    ADD user_allowavatar             TINYINT(1)           DEFAULT '1'         NOT NULL,
    ADD user_allow_pm                TINYINT(1)           DEFAULT '1'         NOT NULL,
    ADD user_allow_viewonline        TINYINT(1)           DEFAULT '1'         NOT NULL,
    ADD user_notify                  TINYINT(1)           DEFAULT '1'         NOT NULL,
    ADD user_notify_pm               TINYINT(1)           DEFAULT '0'         NOT NULL,
    ADD user_popup_pm                TINYINT(1)           DEFAULT '1'         NOT NULL,
    ADD user_avatar_type             TINYINT(4)           DEFAULT '3'         NOT NULL,
    ADD user_sig_bbcode_uid          CHAR(10),
    ADD user_newpasswd               VARCHAR(32),
    ADD KEY user_session_time (user_session_time)
;
# --------------------------------------------------------
#
# Table structure for table 'pbb_sessions'
#
ALTER TABLE session
    ADD session_user_id   MEDIUMINT(8) DEFAULT '0' NOT NULL,
    ADD session_start     INT(11)      DEFAULT '0' NOT NULL,
    ADD session_page      INT(11)      DEFAULT '0' NOT NULL,
    ADD session_logged_in TINYINT(1)   DEFAULT '0' NOT NULL,
    ADD KEY session_user_id (session_user_id),
    ADD KEY session_id_ip_user_id (sess_id, sess_ip, session_user_id)
;
#
# Table structure for table 'pbb_auth_access'
#
CREATE TABLE pbb_auth_access (
    group_id         MEDIUMINT(8)         DEFAULT '0' NOT NULL,
    forum_id         SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
    auth_view        TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_read        TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_post        TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_reply       TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_edit        TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_delete      TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_sticky      TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_announce    TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_vote        TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_pollcreate  TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_attachments TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_mod         TINYINT(1)           DEFAULT '0' NOT NULL,
    auth_download    TINYINT(1)           DEFAULT '0' NOT NULL,
    KEY group_id (group_id),
    KEY forum_id (forum_id)
);
#
# Table structure for table 'pbb_user_group'
#
CREATE TABLE pbb_user_group (
    group_id     MEDIUMINT(8) DEFAULT '0' NOT NULL,
    user_id      MEDIUMINT(8) DEFAULT '0' NOT NULL,
    user_pending TINYINT(1),
    KEY group_id (group_id),
    KEY user_id (user_id)
);
#
# Table structure for table 'pbb_groups'
#
CREATE TABLE pbb_groups (
    group_id          MEDIUMINT(8)             NOT NULL AUTO_INCREMENT,
    group_type        TINYINT(4)   DEFAULT '1' NOT NULL,
    group_name        VARCHAR(40)              NOT NULL,
    group_description VARCHAR(255)             NOT NULL,
    group_moderator   MEDIUMINT(8) DEFAULT '0' NOT NULL,
    group_single_user TINYINT(1)   DEFAULT '1' NOT NULL,
    PRIMARY KEY (group_id),
    KEY group_single_user (group_single_user)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_banlist'
#
CREATE TABLE pbb_banlist (
    ban_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    ban_userid MEDIUMINT(8)          NOT NULL,
    ban_ip     CHAR(11)              NOT NULL,
    ban_email  VARCHAR(255),
    PRIMARY KEY (ban_id),
    KEY ban_ip_user_id (ban_ip, ban_userid)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_categories'
#
CREATE TABLE pbb_categories (
    cat_id    MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    cat_title VARCHAR(100),
    cat_order MEDIUMINT(8) UNSIGNED NOT NULL,
    PRIMARY KEY (cat_id),
    KEY cat_order (cat_order)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_config'
#
CREATE TABLE pbb_config (
    config_name  VARCHAR(255) NOT NULL,
    config_value VARCHAR(255) NOT NULL,
    PRIMARY KEY (config_name)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_confirm'
#
CREATE TABLE pbb_confirm (
    confirm_id CHAR(32) DEFAULT '' NOT NULL,
    session_id CHAR(32) DEFAULT '' NOT NULL,
    code       CHAR(6)  DEFAULT '' NOT NULL,
    PRIMARY KEY (session_id, confirm_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_disallow'
#
CREATE TABLE pbb_disallow (
    disallow_id       MEDIUMINT(8) UNSIGNED  NOT NULL AUTO_INCREMENT,
    disallow_username VARCHAR(25) DEFAULT '' NOT NULL,
    PRIMARY KEY (disallow_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_forum_prune'
#
CREATE TABLE pbb_forum_prune (
    prune_id   MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    forum_id   SMALLINT(5) UNSIGNED  NOT NULL,
    prune_days SMALLINT(5) UNSIGNED  NOT NULL,
    prune_freq SMALLINT(5) UNSIGNED  NOT NULL,
    PRIMARY KEY (prune_id),
    KEY forum_id (forum_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_forums'
#
CREATE TABLE pbb_forums (
    forum_id           SMALLINT(5) UNSIGNED              NOT NULL,
    cat_id             MEDIUMINT(8) UNSIGNED             NOT NULL,
    forum_name         VARCHAR(150),
    forum_desc         TEXT,
    forum_status       TINYINT(4)            DEFAULT '0' NOT NULL,
    forum_order        MEDIUMINT(8) UNSIGNED DEFAULT '1' NOT NULL,
    forum_posts        MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    forum_topics       MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    forum_last_post_id MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    prune_next         INT(11),
    prune_enable       TINYINT(1)            DEFAULT '0' NOT NULL,
    auth_view          TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_read          TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_post          TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_reply         TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_edit          TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_delete        TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_sticky        TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_announce      TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_vote          TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_pollcreate    TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_attachments   TINYINT(2)            DEFAULT '0' NOT NULL,
    auth_download      TINYINT(2)            DEFAULT '0' NOT NULL,
    PRIMARY KEY (forum_id),
    KEY forums_order (forum_order),
    KEY cat_id (cat_id),
    KEY forum_last_post_id (forum_last_post_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_posts'
#
CREATE TABLE pbb_posts (
    post_id         MEDIUMINT(8) UNSIGNED             NOT NULL AUTO_INCREMENT,
    topic_id        MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    forum_id        SMALLINT(5) UNSIGNED  DEFAULT '0' NOT NULL,
    poster_id       MEDIUMINT(8)          DEFAULT '0' NOT NULL,
    post_attachment TINYINT(1)            DEFAULT '0' NOT NULL,
    post_time       INT(11)               DEFAULT '0' NOT NULL,
    poster_ip       CHAR(11)                          NOT NULL,
    post_username   VARCHAR(25),
    enable_bbcode   TINYINT(1)            DEFAULT '1' NOT NULL,
    enable_html     TINYINT(1)            DEFAULT '0' NOT NULL,
    enable_smilies  TINYINT(1)            DEFAULT '1' NOT NULL,
    enable_sig      TINYINT(1)            DEFAULT '1' NOT NULL,
    post_edit_time  INT(11),
    post_edit_count SMALLINT(5) UNSIGNED  DEFAULT '0' NOT NULL,
    PRIMARY KEY (post_id),
    KEY forum_id (forum_id),
    KEY topic_id (topic_id),
    KEY poster_id (poster_id),
    KEY post_time (post_time)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_posts_text'
#
CREATE TABLE pbb_posts_text (
    post_id      MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    bbcode_uid   CHAR(10)                          NOT NULL,
    post_subject CHAR(60),
    post_text    TEXT,
    PRIMARY KEY (post_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_privmsgs'
#
CREATE TABLE pbb_privmsgs (
    privmsgs_id             MEDIUMINT(8) UNSIGNED    NOT NULL AUTO_INCREMENT,
    privmsgs_type           TINYINT(4)   DEFAULT '0' NOT NULL,
    privmsgs_subject        VARCHAR(255) DEFAULT '0' NOT NULL,
    privmsgs_from_userid    MEDIUMINT(8) DEFAULT '0' NOT NULL,
    privmsgs_to_userid      MEDIUMINT(8) DEFAULT '0' NOT NULL,
    privmsgs_attachment     TINYINT(1)   DEFAULT '0' NOT NULL,
    privmsgs_date           INT(11)      DEFAULT '0' NOT NULL,
    privmsgs_ip             CHAR(11)                 NOT NULL,
    privmsgs_enable_bbcode  TINYINT(1)   DEFAULT '1' NOT NULL,
    privmsgs_enable_html    TINYINT(1)   DEFAULT '0' NOT NULL,
    privmsgs_enable_smilies TINYINT(1)   DEFAULT '1' NOT NULL,
    privmsgs_attach_sig     TINYINT(1)   DEFAULT '1' NOT NULL,
    PRIMARY KEY (privmsgs_id),
    KEY privmsgs_from_userid (privmsgs_from_userid),
    KEY privmsgs_to_userid (privmsgs_to_userid)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_privmsgs_text'
#
CREATE TABLE pbb_privmsgs_text (
    privmsgs_text_id    MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    privmsgs_bbcode_uid CHAR(10)              DEFAULT '0' NOT NULL,
    privmsgs_text       TEXT,
    PRIMARY KEY (privmsgs_text_id)
);
# --------------------------------------------------------
#
# Table structure for table `pbb_search_results`
#
CREATE TABLE pbb_search_results (
    search_id    INT(11) UNSIGNED NOT NULL DEFAULT '0',
    session_id   CHAR(32)         NOT NULL DEFAULT '',
    search_array TEXT             NOT NULL,
    PRIMARY KEY (search_id),
    KEY session_id (session_id)
);
# --------------------------------------------------------
#
# Table structure for table `pbb_search_wordlist`
#
CREATE TABLE pbb_search_wordlist (
    word_text   VARCHAR(50) BINARY    NOT NULL DEFAULT '',
    word_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    word_common TINYINT(1) UNSIGNED   NOT NULL DEFAULT '0',
    PRIMARY KEY (word_text),
    KEY word_id (word_id)
);
# --------------------------------------------------------
#
# Table structure for table `pbb_search_wordmatch`
#
CREATE TABLE pbb_search_wordmatch (
    post_id     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    word_id     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    title_match TINYINT(1)            NOT NULL DEFAULT '0',
    KEY post_id (post_id),
    KEY word_id (word_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_themes'
#
CREATE TABLE pbb_themes (
    themes_id        MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    template_name    VARCHAR(30)           NOT NULL DEFAULT '',
    style_name       VARCHAR(30)           NOT NULL DEFAULT '',
    head_stylesheet  VARCHAR(100)                   DEFAULT NULL,
    body_background  VARCHAR(100)                   DEFAULT NULL,
    body_bgcolor     VARCHAR(6)                     DEFAULT NULL,
    body_text        VARCHAR(6)                     DEFAULT NULL,
    body_link        VARCHAR(6)                     DEFAULT NULL,
    body_vlink       VARCHAR(6)                     DEFAULT NULL,
    body_alink       VARCHAR(6)                     DEFAULT NULL,
    body_hlink       VARCHAR(6)                     DEFAULT NULL,
    tr_color1        VARCHAR(6)                     DEFAULT NULL,
    tr_color2        VARCHAR(6)                     DEFAULT NULL,
    tr_color3        VARCHAR(6)                     DEFAULT NULL,
    tr_class1        VARCHAR(25)                    DEFAULT NULL,
    tr_class2        VARCHAR(25)                    DEFAULT NULL,
    tr_class3        VARCHAR(25)                    DEFAULT NULL,
    th_color1        VARCHAR(6)                     DEFAULT NULL,
    th_color2        VARCHAR(6)                     DEFAULT NULL,
    th_color3        VARCHAR(6)                     DEFAULT NULL,
    th_class1        VARCHAR(25)                    DEFAULT NULL,
    th_class2        VARCHAR(25)                    DEFAULT NULL,
    th_class3        VARCHAR(25)                    DEFAULT NULL,
    td_color1        VARCHAR(6)                     DEFAULT NULL,
    td_color2        VARCHAR(6)                     DEFAULT NULL,
    td_color3        VARCHAR(6)                     DEFAULT NULL,
    td_class1        VARCHAR(25)                    DEFAULT NULL,
    td_class2        VARCHAR(25)                    DEFAULT NULL,
    td_class3        VARCHAR(25)                    DEFAULT NULL,
    fontface1        VARCHAR(50)                    DEFAULT NULL,
    fontface2        VARCHAR(50)                    DEFAULT NULL,
    fontface3        VARCHAR(50)                    DEFAULT NULL,
    fontsize1        TINYINT(4)                     DEFAULT NULL,
    fontsize2        TINYINT(4)                     DEFAULT NULL,
    fontsize3        TINYINT(4)                     DEFAULT NULL,
    fontcolor1       VARCHAR(6)                     DEFAULT NULL,
    fontcolor2       VARCHAR(6)                     DEFAULT NULL,
    fontcolor3       VARCHAR(6)                     DEFAULT NULL,
    span_class1      VARCHAR(25)                    DEFAULT NULL,
    span_class2      VARCHAR(25)                    DEFAULT NULL,
    span_class3      VARCHAR(25)                    DEFAULT NULL,
    img_size_poll    SMALLINT(5) UNSIGNED,
    img_size_privmsg SMALLINT(5) UNSIGNED,
    PRIMARY KEY (themes_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_themes_name'
#
CREATE TABLE pbb_themes_name (
    themes_id        SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL,
    tr_color1_name   CHAR(50),
    tr_color2_name   CHAR(50),
    tr_color3_name   CHAR(50),
    tr_class1_name   CHAR(50),
    tr_class2_name   CHAR(50),
    tr_class3_name   CHAR(50),
    th_color1_name   CHAR(50),
    th_color2_name   CHAR(50),
    th_color3_name   CHAR(50),
    th_class1_name   CHAR(50),
    th_class2_name   CHAR(50),
    th_class3_name   CHAR(50),
    td_color1_name   CHAR(50),
    td_color2_name   CHAR(50),
    td_color3_name   CHAR(50),
    td_class1_name   CHAR(50),
    td_class2_name   CHAR(50),
    td_class3_name   CHAR(50),
    fontface1_name   CHAR(50),
    fontface2_name   CHAR(50),
    fontface3_name   CHAR(50),
    fontsize1_name   CHAR(50),
    fontsize2_name   CHAR(50),
    fontsize3_name   CHAR(50),
    fontcolor1_name  CHAR(50),
    fontcolor2_name  CHAR(50),
    fontcolor3_name  CHAR(50),
    span_class1_name CHAR(50),
    span_class2_name CHAR(50),
    span_class3_name CHAR(50),
    PRIMARY KEY (themes_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_topics'
#
CREATE TABLE pbb_topics (
    topic_id            MEDIUMINT(8) UNSIGNED             NOT NULL AUTO_INCREMENT,
    forum_id            SMALLINT(8) UNSIGNED  DEFAULT '0' NOT NULL,
    topic_title         CHAR(60)                          NOT NULL,
    topic_poster        MEDIUMINT(8)          DEFAULT '0' NOT NULL,
    topic_attachment    TINYINT(1)            DEFAULT '0' NOT NULL,
    topic_time          INT(11)               DEFAULT '0' NOT NULL,
    topic_views         MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    topic_replies       MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    topic_status        TINYINT(3)            DEFAULT '0' NOT NULL,
    topic_vote          TINYINT(1)            DEFAULT '0' NOT NULL,
    topic_type          TINYINT(3)            DEFAULT '0' NOT NULL,
    topic_first_post_id MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    topic_last_post_id  MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    topic_moved_id      MEDIUMINT(8) UNSIGNED DEFAULT '0' NOT NULL,
    PRIMARY KEY (topic_id),
    KEY forum_id (forum_id),
    KEY topic_moved_id (topic_moved_id),
    KEY topic_status (topic_status),
    KEY topic_type (topic_type)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_topics_watch'
#
CREATE TABLE pbb_topics_watch (
    topic_id      MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    user_id       MEDIUMINT(8)          NOT NULL DEFAULT '0',
    notify_status TINYINT(1)            NOT NULL DEFAULT '0',
    KEY topic_id (topic_id),
    KEY user_id (user_id),
    KEY notify_status (notify_status)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_vote_desc'
#
CREATE TABLE pbb_vote_desc (
    vote_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    topic_id    MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    vote_text   TEXT                  NOT NULL,
    vote_start  INT(11)               NOT NULL DEFAULT '0',
    vote_length INT(11)               NOT NULL DEFAULT '0',
    PRIMARY KEY (vote_id),
    KEY topic_id (topic_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_vote_results'
#
CREATE TABLE pbb_vote_results (
    vote_id          MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    vote_option_id   TINYINT(4) UNSIGNED   NOT NULL DEFAULT '0',
    vote_option_text VARCHAR(255)          NOT NULL,
    vote_result      INT(11)               NOT NULL DEFAULT '0',
    KEY vote_option_id (vote_option_id),
    KEY vote_id (vote_id)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_vote_voters'
#
CREATE TABLE pbb_vote_voters (
    vote_id      MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    vote_user_id MEDIUMINT(8)          NOT NULL DEFAULT '0',
    vote_user_ip CHAR(11)              NOT NULL,
    KEY vote_id (vote_id),
    KEY vote_user_id (vote_user_id),
    KEY vote_user_ip (vote_user_ip)
);
# --------------------------------------------------------
#
# Table structure for table 'pbb_words'
#
CREATE TABLE pbb_words (
    word_id     MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    word        CHAR(100)             NOT NULL,
    replacement CHAR(100)             NOT NULL,
    PRIMARY KEY (word_id)
);
#
# Basic DB data for phpBB2 devel
#
# $Id: mysql_basic.sql,v 1.29.2.2 2002/12/21 18:31:54 psotfx Exp $
# -- Config
INSERT INTO pbb_config (config_name, config_value)
VALUES ('config_id', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_disable', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('sitename', 'yourdomain.com');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('site_desc', 'A _little_ text to describe your forum');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('cookie_name', 'phpbb2mysql');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('cookie_path', '/');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('cookie_domain', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('cookie_secure', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('session_length', '3600');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_html', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_html_tags', 'b,i,u,pre');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_bbcode', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_smilies', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_sig', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_namechange', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_theme_create', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_avatar_local', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_avatar_remote', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('allow_avatar_upload', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('enable_confirm', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('override_user_style', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('posts_per_page', '15');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('topics_per_page', '50');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('hot_threshold', '25');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('max_poll_options', '10');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('max_sig_chars', '255');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('max_inbox_privmsgs', '50');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('max_sentbox_privmsgs', '25');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('max_savebox_privmsgs', '50');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_email_sig', 'Thanks, The Management');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_email', 'youraddress@yourdomain.com');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('smtp_delivery', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('smtp_host', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('smtp_username', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('smtp_password', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('sendmail_fix', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('require_activation', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('flood_interval', '15');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_email_form', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('avatar_filesize', '6144');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('avatar_max_width', '80');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('avatar_max_height', '80');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('avatar_path', '../../uploads');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('avatar_gallery_path', '../../uploads');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('smilies_path', '../../uploads');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('default_style', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('default_dateformat', 'D M d, Y g:i a');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_timezone', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('prune_enable', '1');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('privmsg_disable', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('gzip_compress', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('coppa_fax', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('coppa_mail', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('record_online_users', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('record_online_date', '0');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('server_name', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('server_port', '80');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('script_path', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('version', '.0.5');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('version_bb', '1.13');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('board_startdate', '');
INSERT INTO pbb_config (config_name, config_value)
VALUES ('default_lang', 'english');
# -- Users
INSERT INTO users (uid, uname, user_level, user_regdate, pass, email, user_icq, url, user_occ, user_from, user_intrest, user_sig, user_viewemail, user_style, user_aim, user_yim, user_msnm, posts, attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_pm, user_notify_pm,
                   user_allow_viewonline, rank, user_avatar, user_lang, timezone_offset, user_dateformat, actkey, user_newpasswd, user_notify, level)
VALUES (0, 'Anonymous', 0, 0, '', '', '', '', '', '', '', '', 0, NULL, '', '', '', 0, 0, 1, 0, 1, 0, 1, 1, NULL, '', '', 0, '', '', '', 0, 0);
UPDATE users
   SET uid = 0
 WHERE uname = 'Anonymous';
# -- Themes
INSERT INTO pbb_themes (themes_id, template_name, style_name, head_stylesheet, body_background, body_bgcolor, body_text, body_link, body_vlink, body_alink, body_hlink, tr_color1, tr_color2, tr_color3, tr_class1, tr_class2, tr_class3, th_color1, th_color2, th_color3, th_class1, th_class2, th_class3,
                        td_color1, td_color2, td_color3, td_class1, td_class2, td_class3, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, span_class1, span_class2, span_class3)
VALUES (1, 'subSilver', 'subSilver', 'subSilver.css', '', 'E5E5E5', '000000', '006699', '5493B4', '', 'DD6900', 'EFEFEF', 'DEE3E7', 'D1D7DC', '', '', '', '98AAB1', '006699', 'FFFFFF', 'cellpic1.gif', 'cellpic3.gif', 'cellpic2.jpg', 'FAFAFA', 'FFFFFF', '', 'row1', 'row2', '',
        'Verdana, Arial, Helvetica, sans-serif', 'Trebuchet MS', 'Courier, \'Courier New\', sans-serif', 10, 11, 12, '444444', '006600', 'FFA34F', '', '', '');
INSERT INTO pbb_themes_name (themes_id, tr_color1_name, tr_color2_name, tr_color3_name, tr_class1_name, tr_class2_name, tr_class3_name, th_color1_name, th_color2_name, th_color3_name, th_class1_name, th_class2_name, th_class3_name, td_color1_name, td_color2_name, td_color3_name, td_class1_name,
                             td_class2_name, td_class3_name, fontface1_name, fontface2_name, fontface3_name, fontsize1_name, fontsize2_name, fontsize3_name, fontcolor1_name, fontcolor2_name, fontcolor3_name, span_class1_name, span_class2_name, span_class3_name)
VALUES (1, 'The lightest row colour', 'The medium row color', 'The darkest row colour', '', '', '', 'Border round the whole page', 'Outer table border', 'Inner table border', 'Silver gradient picture', 'Blue gradient picture', 'Fade-out gradient on index', 'Background for quote boxes',
        'All white areas', '', 'Background for topic posts', '2nd background for topic posts', '', 'Main fonts', 'Additional topic title font', 'Form fonts', 'Smallest font size', 'Medium font size', 'Normal font size (post body etc)', 'Quote & copyright text', 'Code text colour',
        'Main table header text colour', '', '', '');
# -- wordlist
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (1, 'example', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (2, 'post', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (3, 'phpbb', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (4, 'installation', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (5, 'delete', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (6, 'topic', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (7, 'forum', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (8, 'since', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (9, 'everything', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (10, 'seems', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (11, 'working', 0);
INSERT INTO pbb_search_wordlist (word_id, word_text, word_common)
VALUES (12, 'welcome', 0);
# -- wordmatch
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (1, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (2, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (3, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (4, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (5, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (6, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (7, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (8, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (9, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (10, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (11, 1, 0);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (12, 1, 1);
INSERT INTO pbb_search_wordmatch (word_id, post_id, title_match)
VALUES (3, 1, 1);
#
# Basic DB data for Attachment Mod
#
# $Id: attach_mysql_basic.sql,v 1.6 2003/06/18 19:48:46 acydburn Exp $
# 
# -- attachments_config
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('upload_dir', 'files');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('upload_img', 'images/icon_clip.gif');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('topic_icon', 'images/icon_clip.gif');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('display_order', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('max_filesize', '262144');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('attachment_quota', '52428800');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('max_filesize_pm', '262144');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('max_attachments', '3');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('max_attachments_pm', '1');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('disable_mod', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('allow_pm_attach', '1');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('attachment_topic_review', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('allow_ftp_upload', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('show_apcp', '1');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('attach_version', '2.3.8');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('default_upload_quota', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('default_pm_quota', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('ftp_server', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('ftp_path', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('download_path', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('ftp_user', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('ftp_pass', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('ftp_pasv_mode', '1');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_display_inlined', '1');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_max_width', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_max_height', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_link_width', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_link_height', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_create_thumbnail', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_min_thumb_filesize', '12000');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('img_imagick', '');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('wma_autoplay', '0');
INSERT INTO pbb_attachments_config (config_name, config_value)
VALUES ('flash_autoplay', '0');
# -- forbidden_extensions
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (1, 'php');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (2, 'php3');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (3, 'php4');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (4, 'phtml');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (5, 'pl');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (6, 'asp');
INSERT INTO pbb_forbidden_extensions (ext_id, extension)
VALUES (7, 'cgi');
# -- extension_groups
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (1, 'Images', 1, 1, 1, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (2, 'Archives', 0, 1, 1, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (3, 'Plain Text', 0, 0, 1, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (4, 'Documents', 0, 0, 1, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (5, 'Real Media', 0, 0, 2, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (6, 'Streams', 2, 0, 1, '', 0, '');
INSERT INTO pbb_extension_groups (group_id, group_name, cat_id, allow_group, download_mode, upload_icon, max_filesize, forum_permissions)
VALUES (7, 'Flash Files', 3, 0, 1, '', 0, '');
# -- extensions
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (1, 1, 'gif', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (2, 1, 'png', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (3, 1, 'jpeg', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (4, 1, 'jpg', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (5, 1, 'tif', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (6, 1, 'tga', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (7, 2, 'gtar', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (8, 2, 'gz', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (9, 2, 'tar', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (10, 2, 'zip', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (11, 2, 'rar', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (12, 2, 'ace', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (13, 3, 'txt', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (14, 3, 'c', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (15, 3, 'h', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (16, 3, 'cpp', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (17, 3, 'hpp', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (18, 3, 'diz', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (19, 4, 'xls', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (20, 4, 'doc', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (21, 4, 'dot', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (22, 4, 'pdf', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (23, 4, 'ai', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (24, 4, 'ps', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (25, 4, 'ppt', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (26, 5, 'rm', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (27, 6, 'wma', '');
INSERT INTO pbb_extensions (ext_id, group_id, extension, comment)
VALUES (28, 7, 'swf', '');
# -- default quota limits
INSERT INTO pbb_quota_limits (quota_limit_id, quota_desc, quota_limit)
VALUES (1, 'Low', 262144);
INSERT INTO pbb_quota_limits (quota_limit_id, quota_desc, quota_limit)
VALUES (2, 'Medium', 2097152);
INSERT INTO pbb_quota_limits (quota_limit_id, quota_desc, quota_limit)
VALUES (3, 'High', 5242880);
