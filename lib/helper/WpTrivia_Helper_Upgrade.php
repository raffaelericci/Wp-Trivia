<?php

class WpTrivia_Helper_Upgrade
{

    public static function upgrade()
    {

        WpTrivia_Helper_Upgrade::updateDb();

        $oldVersion = get_option('wpTrivia_version');

        switch ($oldVersion) {
            case '1.0.0':
                break;
            default:
                WpTrivia_Helper_Upgrade::install();
                break;
        }

        if (add_option('wpTrivia_version', WPPROQUIZ_VERSION) === false) {
            update_option('wpTrivia_version', WPPROQUIZ_VERSION);
        }
    }

    private static function install()
    {
        $role = get_role('administrator');

        $role->add_cap('wpTrivia_show');
        $role->add_cap('wpTrivia_add_quiz');
        $role->add_cap('wpTrivia_edit_quiz');
        $role->add_cap('wpTrivia_delete_quiz');
        $role->add_cap('wpTrivia_show_statistics');
        $role->add_cap('wpTrivia_reset_statistics');
        $role->add_cap('wpTrivia_import');
        $role->add_cap('wpTrivia_export');
        $role->add_cap('wpTrivia_change_settings');
        $role->add_cap('wpTrivia_toplist_edit');
    }

    private static function updateDb()
    {
        $db = new WpTrivia_Helper_DbUpgrade();
        $v = $db->upgrade(get_option('wpTrivia_dbVersion', false));

        if (add_option('wpTrivia_dbVersion', $v) === false) {
            update_option('wpTrivia_dbVersion', $v);
        }
    }

    public static function deinstall()
    {

    }
}
