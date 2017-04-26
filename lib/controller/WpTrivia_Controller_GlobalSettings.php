<?php

class WpTrivia_Controller_GlobalSettings extends WpTrivia_Controller_Controller
{

    public function route()
    {
        $this->edit();
    }

    private function edit()
    {

        if (!current_user_can('wpProQuiz_change_settings')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpTrivia_Model_GlobalSettingsMapper();
        $categoryMapper = new WpTrivia_Model_CategoryMapper();
        $templateMapper = new WpTrivia_Model_TemplateMapper();

        $view = new WpTrivia_View_GobalSettings();

        if (isset($this->_post['submit'])) {
            $mapper->save(new WpTrivia_Model_GlobalSettings($this->_post));
            WpTrivia_View_View::admin_notices(__('Settings saved', 'wp-trivia'), 'info');

            $toplistDateFormat = $this->_post['toplist_date_format'];

            if ($toplistDateFormat == 'custom') {
                $toplistDateFormat = trim($this->_post['toplist_date_format_custom']);
            }

            $statisticTimeFormat = $this->_post['statisticTimeFormat'];

            if (add_option('wpProQuiz_toplistDataFormat', $toplistDateFormat) === false) {
                update_option('wpProQuiz_toplistDataFormat', $toplistDateFormat);
            }

            if (add_option('wpProQuiz_statisticTimeFormat', $statisticTimeFormat, '', 'no') === false) {
                update_option('wpProQuiz_statisticTimeFormat', $statisticTimeFormat);
            }
        } else {
            if (isset($this->_post['databaseFix'])) {
                WpTrivia_View_View::admin_notices(__('Database repaired', 'wp-trivia'), 'info');

                $DbUpgradeHelper = new WpTrivia_Helper_DbUpgrade();
                $DbUpgradeHelper->databaseDelta();
            }
        }

        $view->settings = $mapper->fetchAll();
        $view->isRaw = !preg_match('[raw]', apply_filters('the_content', '[raw]a[/raw]'));
        $view->category = $categoryMapper->fetchAll();
        $view->categoryQuiz = $categoryMapper->fetchAll(WpTrivia_Model_Category::CATEGORY_TYPE_QUIZ);
        $view->email = $mapper->getEmailSettings();
        $view->userEmail = $mapper->getUserEmailSettings();
        $view->templateQuiz = $templateMapper->fetchAll(WpTrivia_Model_Template::TEMPLATE_TYPE_QUIZ, false);
        $view->templateQuestion = $templateMapper->fetchAll(WpTrivia_Model_Template::TEMPLATE_TYPE_QUESTION, false);

        $view->toplistDataFormat = get_option('wpProQuiz_toplistDataFormat', 'Y/m/d g:i A');
        $view->statisticTimeFormat = get_option('wpProQuiz_statisticTimeFormat', 'Y/m/d g:i A');

        $view->show();
    }
}