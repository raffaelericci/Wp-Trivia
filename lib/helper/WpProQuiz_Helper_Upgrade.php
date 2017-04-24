<?php

class WpProQuiz_Helper_Upgrade
{

    public static function upgrade()
    {

        WpProQuiz_Helper_Upgrade::updateDb();

        $oldVersion = get_option('wpProQuiz_version');

        switch ($oldVersion) {
            case '1.0.0':
                break;
            default:
                WpProQuiz_Helper_Upgrade::install();
                break;
        }

        if (add_option('wpProQuiz_version', WPPROQUIZ_VERSION) === false) {
            update_option('wpProQuiz_version', WPPROQUIZ_VERSION);
        }
    }

    private static function install()
    {
        $role = get_role('administrator');

        $role->add_cap('wpProQuiz_show');
        $role->add_cap('wpProQuiz_add_quiz');
        $role->add_cap('wpProQuiz_edit_quiz');
        $role->add_cap('wpProQuiz_delete_quiz');
        $role->add_cap('wpProQuiz_show_statistics');
        $role->add_cap('wpProQuiz_reset_statistics');
        $role->add_cap('wpProQuiz_import');
        $role->add_cap('wpProQuiz_export');
        $role->add_cap('wpProQuiz_change_settings');
        $role->add_cap('wpProQuiz_toplist_edit');
    }

    private static function updateDb()
    {
        $db = new WpProQuiz_Helper_DbUpgrade();
        $v = $db->upgrade(get_option('wpProQuiz_dbVersion', false));

        if (add_option('wpProQuiz_dbVersion', $v) === false) {
            update_option('wpProQuiz_dbVersion', $v);
        }
    }

    public static function deinstall()
    {

    }
}
