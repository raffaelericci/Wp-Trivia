<?php
/*
Plugin Name: Wp-Trivia
Plugin URI: http://wordpress.org/extend/plugins/wp-trivia
Description: A powerful and beautiful quiz plugin for WordPress.
Version: 1.0.0
Author: Julius Fischer
Author URI: http://www.it-gecko.de
Text Domain: wp-trivia
Domain Path: /languages
*/

define('WPPROQUIZ_VERSION', '1.0.0');

define('WPPROQUIZ_DEV', false);

define('WPPROQUIZ_PATH', dirname(__FILE__));
define('WPPROQUIZ_URL', plugins_url('', __FILE__));
define('WPPROQUIZ_FILE', __FILE__);
define('WPPROQUIZ_PPATH', dirname(plugin_basename(__FILE__)));
define('WPPROQUIZ_PLUGIN_PATH', WPPROQUIZ_PATH . '/plugin');

$uploadDir = wp_upload_dir();

define('WPPROQUIZ_CAPTCHA_DIR', $uploadDir['basedir'] . '/wp_pro_quiz_captcha');
define('WPPROQUIZ_CAPTCHA_URL', $uploadDir['baseurl'] . '/wp_pro_quiz_captcha');

spl_autoload_register('wpProQuiz_autoload');

register_activation_hook(__FILE__, array('WpTrivia_Helper_Upgrade', 'upgrade'));

add_action('plugins_loaded', 'wpProQuiz_pluginLoaded');

if (is_admin()) {
    new WpTrivia_Controller_Admin();
} else {
    new WpTrivia_Controller_Front();
}

function wpProQuiz_autoload($class)
{
    $c = explode('_', $class);

    if ($c === false || count($c) != 3 || $c[0] !== 'WpTrivia') {
        return;
    }

    switch ($c[1]) {
        case 'View':
            $dir = 'view';
            break;
        case 'Model':
            $dir = 'model';
            break;
        case 'Helper':
            $dir = 'helper';
            break;
        case 'Controller':
            $dir = 'controller';
            break;
        case 'Plugin':
            $dir = 'plugin';
            break;
        default:
            return;
    }

    $classPath = WPPROQUIZ_PATH . '/lib/' . $dir . '/' . $class . '.php';

    if (file_exists($classPath)) {
        /** @noinspection PhpIncludeInspection */
        include_once $classPath;
    }
}

function wpProQuiz_pluginLoaded()
{
    load_plugin_textdomain('wp-trivia', false, WPPROQUIZ_PPATH . '/languages');

    if (get_option('wpProQuiz_version') !== WPPROQUIZ_VERSION) {
        WpTrivia_Helper_Upgrade::upgrade();
    }
}

function wpProQuiz_achievementsV1()
{
    if (function_exists('achievements')) {
        achievements()->extensions->wp_pro_quiz = new WpTrivia_Plugin_BpAchievementsV1();

        do_action('wpProQuiz_achievementsV1');
    }
}

add_action('dpa_ready', 'wpProQuiz_achievementsV1');
