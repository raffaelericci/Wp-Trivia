<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

include_once 'lib/helper/WpTrivia_Helper_DbUpgrade.php';

$db = new WpTrivia_Helper_DbUpgrade();
$db->delete();

delete_option('wpTrivia_dbVersion');
delete_option('wpTrivia_version');

delete_option('wpTrivia_addRawShortcode');
delete_option('wpTrivia_jsLoadInHead');
delete_option('wpTrivia_touchLibraryDeactivate');
delete_option('wpTrivia_corsActivated');
delete_option('wpTrivia_toplistDataFormat');
delete_option('wpTrivia_emailSettings');
delete_option('wpTrivia_statisticTimeFormat');