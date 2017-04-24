<?php

class WpProQuiz_Helper_DbUpgrade
{

    const WPPROQUIZ_DB_VERSION = 25;

    private $_wpdb;
    private $_prefix;

    public function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
    }

    public function upgrade($version)
    {
        @set_time_limit(300);

        if ($version === false || ((int)$version) > WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION) {
            $this->install();

            return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
        }

        $version = (int)$version;

        if ($version === WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION) {
            return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
        }

        do {
            $f = 'upgradeDbV' . $version;

            if (method_exists($this, $f)) {
                $version = $this->$f();
            } else {
                die("WpProQuiz upgrade error");
            }
        } while ($version < WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION);

        return WpProQuiz_Helper_DbUpgrade::WPPROQUIZ_DB_VERSION;
    }

    public function delete()
    {
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_category`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_form`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_lock`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_master`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_prerequisite`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_question`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_statistic`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_statistic_ref`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_template`');
        $this->_wpdb->query('DROP TABLE IF EXISTS `' . $this->_wpdb->prefix . 'wp_pro_quiz_toplist`');
    }

    private function install()
    {
        $this->delete();
        $this->databaseDelta();
    }

    public function databaseDelta()
    {
        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }

        dbDelta("
			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_category (
			  category_id int(10) unsigned NOT NULL AUTO_INCREMENT,
			  category_name varchar(200) NOT NULL,
			  type enum('QUESTION','QUIZ') NOT NULL DEFAULT 'QUESTION',
			  PRIMARY KEY  (category_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_form (
			  form_id int(11) NOT NULL AUTO_INCREMENT,
			  quiz_id int(11) NOT NULL,
			  fieldname varchar(100) NOT NULL,
			  type tinyint(4) NOT NULL,
			  required tinyint(1) unsigned NOT NULL,
			  sort tinyint(4) NOT NULL,
			  show_in_statistic tinyint(1) unsigned NOT NULL,
			  data mediumtext,
			  PRIMARY KEY  (form_id),
			  KEY quiz_id (quiz_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_lock (
			  quiz_id int(11) NOT NULL,
			  lock_ip varchar(100) NOT NULL,
			  user_id bigint(20) unsigned NOT NULL,
			  lock_type tinyint(3) unsigned NOT NULL,
			  lock_date int(11) NOT NULL,
			  PRIMARY KEY  (quiz_id,lock_ip,user_id,lock_type)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_master (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  name varchar(200) NOT NULL,
			  text text NOT NULL,
			  result_text text NOT NULL,
			  result_grade_enabled tinyint(1) NOT NULL,
			  time_limit int(11) NOT NULL,
			  statistics_on tinyint(1) NOT NULL,
			  statistics_ip_lock int(10) unsigned NOT NULL,
			  show_points tinyint(1) NOT NULL,
			  quiz_run_once tinyint(1) NOT NULL,
			  quiz_run_once_type tinyint(4) NOT NULL,
			  quiz_run_once_cookie tinyint(1) NOT NULL,
			  quiz_run_once_time int(10) unsigned NOT NULL,
			  numbered_answer tinyint(1) NOT NULL,
			  hide_answer_message_box tinyint(1) NOT NULL,
			  disabled_answer_mark tinyint(1) NOT NULL,
			  show_max_question tinyint(1) NOT NULL,
			  show_max_question_value int(10) unsigned NOT NULL,
			  show_max_question_percent tinyint(1) NOT NULL,
			  toplist_activated tinyint(1) NOT NULL,
			  toplist_data text NOT NULL,
			  show_average_result tinyint(1) NOT NULL,
			  prerequisite tinyint(1) NOT NULL,
			  show_review_question tinyint(1) NOT NULL,
			  quiz_summary_hide tinyint(1) NOT NULL,
			  skip_question_disabled tinyint(1) NOT NULL,
			  email_notification tinyint(3) unsigned NOT NULL,
			  user_email_notification tinyint(1) unsigned NOT NULL,
			  show_category_score tinyint(1) unsigned NOT NULL,
			  hide_result_correct_question tinyint(1) unsigned NOT NULL DEFAULT '0',
			  hide_result_quiz_time tinyint(1) unsigned NOT NULL DEFAULT '0',
			  hide_result_points tinyint(1) unsigned NOT NULL DEFAULT '0',
			  autostart tinyint(1) unsigned NOT NULL DEFAULT '0',
			  forcing_question_solve tinyint(1) unsigned NOT NULL DEFAULT '0',
			  hide_question_position_overview tinyint(1) unsigned NOT NULL DEFAULT '0',
			  hide_question_numbering tinyint(1) unsigned NOT NULL DEFAULT '0',
			  form_activated tinyint(1) unsigned NOT NULL,
			  form_show_position tinyint(3) unsigned NOT NULL,
			  start_only_registered_user tinyint(1) unsigned NOT NULL,
			  questions_per_page tinyint(3) unsigned NOT NULL,
			  sort_categories tinyint(1) unsigned NOT NULL,
			  show_category tinyint(1) unsigned NOT NULL,
			  category_id int(10) unsigned NOT NULL,
			  admin_email text NOT NULL,
  			  user_email text NOT NULL,
			  plugin_container text,
			  PRIMARY KEY  (id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_prerequisite (
			  prerequisite_quiz_id int(11) NOT NULL,
			  quiz_id int(11) NOT NULL,
			  PRIMARY KEY  (prerequisite_quiz_id,quiz_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_question (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  quiz_id int(11) NOT NULL,
			  online tinyint(1) unsigned NOT NULL,
			  sort smallint(5) unsigned NOT NULL,
			  title varchar(200) NOT NULL,
			  points int(11) NOT NULL,
			  question text NOT NULL,
			  correct_msg text NOT NULL,
			  incorrect_msg text NOT NULL,
			  correct_same_text tinyint(1) NOT NULL,
			  tip_enabled tinyint(1) NOT NULL,
			  tip_msg text NOT NULL,
			  answer_type varchar(50) NOT NULL,
			  show_points_in_box tinyint(1) NOT NULL,
			  answer_points_activated tinyint(1) NOT NULL,
			  answer_data longtext NOT NULL,
			  category_id int(10) unsigned NOT NULL,
			  answer_points_diff_modus_activated tinyint(1) unsigned NOT NULL,
			  disable_correct tinyint(1) unsigned NOT NULL,
			  matrix_sort_answer_criteria_width tinyint(3) unsigned NOT NULL,
			  PRIMARY KEY  (id),
			  KEY quiz_id (quiz_id),
			  KEY category_id (category_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_statistic (
			  statistic_ref_id int(10) unsigned NOT NULL,
			  question_id int(11) NOT NULL,
			  correct_count int(10) unsigned NOT NULL,
			  incorrect_count int(10) unsigned NOT NULL,
			  hint_count int(10) unsigned NOT NULL,
			  solved_count tinyint(1) NOT NULL,
			  points int(10) unsigned NOT NULL,
			  question_time int(10) unsigned NOT NULL,
			  answer_data text,
			  PRIMARY KEY  (statistic_ref_id,question_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_statistic_ref (
			  statistic_ref_id int(10) unsigned NOT NULL AUTO_INCREMENT,
			  quiz_id int(11) NOT NULL,
			  user_id bigint(20) unsigned NOT NULL,
			  create_time int(11) NOT NULL,
			  is_old tinyint(1) unsigned NOT NULL,
			  form_data text,
			  PRIMARY KEY  (statistic_ref_id),
			  KEY quiz_id (quiz_id,user_id),
			  KEY time (create_time)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_template (
			  template_id int(11) NOT NULL AUTO_INCREMENT,
			  name varchar(200) NOT NULL,
			  type tinyint(3) unsigned NOT NULL,
			  data text NOT NULL,
			  PRIMARY KEY  (template_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

			CREATE TABLE {$this->_wpdb->prefix}wp_pro_quiz_toplist (
			  toplist_id int(11) NOT NULL AUTO_INCREMENT,
			  quiz_id int(11) NOT NULL,
			  date int(10) unsigned NOT NULL,
			  user_id bigint(20) unsigned NOT NULL,
			  name varchar(30) NOT NULL,
			  email varchar(200) NOT NULL,
			  points int(10) unsigned NOT NULL,
			  result float unsigned NOT NULL,
			  ip varchar(100) NOT NULL,
			  PRIMARY KEY  (toplist_id,quiz_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		");
    }
}
