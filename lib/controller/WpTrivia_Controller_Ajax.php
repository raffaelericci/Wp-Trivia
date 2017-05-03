<?php

/**
 * @since 0.23
 */
class WpTrivia_Controller_Ajax
{

    private $_adminCallbacks = array();
    private $_frontCallbacks = array();

    public function init()
    {
        $this->initCallbacks();

        add_action('wp_ajax_wp_trivia_admin_ajax', array($this, 'adminAjaxCallback'));
        add_action('wp_ajax_nopriv_wp_trivia_admin_ajax', array($this, 'frontAjaxCallback'));
    }

    public function adminAjaxCallback()
    {
        $this->ajaxCallbackHandler(true);
    }

    public function frontAjaxCallback()
    {
        $this->ajaxCallbackHandler(false);
    }

    private function ajaxCallbackHandler($admin)
    {
        $func = isset($_POST['func']) ? $_POST['func'] : '';
        $data = isset($_POST['data']) ? $_POST['data'] : null;
        $calls = $admin ? $this->_adminCallbacks : $this->_frontCallbacks;

        if (isset($calls[$func])) {
            $r = call_user_func($calls[$func], $data, $func);

            if ($r !== null) {
                wp_die($r);
            }
        }
        wp_die(0);
    }

    private function initCallbacks()
    {
        $this->_adminCallbacks = array(
            'categoryAdd' => array('WpTrivia_Controller_Category', 'ajaxAddCategory'),
            'categoryDelete' => array('WpTrivia_Controller_Category', 'ajaxDeleteCategory'),
            'categoryEdit' => array('WpTrivia_Controller_Category', 'ajaxEditCategory'),
            'statisticLoadHistory' => array('WpTrivia_Controller_Statistics', 'ajaxLoadHistory'),
            'statisticLoadUser' => array('WpTrivia_Controller_Statistics', 'ajaxLoadStatisticUser'),
            'statisticResetNew' => array('WpTrivia_Controller_Statistics', 'ajaxRestStatistic'),
            'statisticLoadOverviewNew' => array('WpTrivia_Controller_Statistics', 'ajaxLoadStatsticOverviewNew'),
            'templateEdit' => array('WpTrivia_Controller_Template', 'ajaxEditTemplate'),
            'templateDelete' => array('WpTrivia_Controller_Template', 'ajaxDeleteTemplate'),
            'quizLoadData' => array('WpTrivia_Controller_Front', 'ajaxQuizLoadData'),
            'setQuizMultipleCategories' => array('WpTrivia_Controller_Quiz', 'ajaxSetQuizMultipleCategories'),
            'setQuestionMultipleCategories' => array(
                'WpTrivia_Controller_Question',
                'ajaxSetQuestionMultipleCategories'
            ),
            'loadQuestionsSort' => array('WpTrivia_Controller_Question', 'ajaxLoadQuestionsSort'),
            'questionSaveSort' => array('WpTrivia_Controller_Question', 'ajaxSaveSort'),
            'questionaLoadCopyQuestion' => array('WpTrivia_Controller_Question', 'ajaxLoadCopyQuestion'),

            // TODO - in progress
            'checkAnswer' => array('WpTrivia_Controller_Question', 'ajaxCheckAnswer'),

            'loadQuizData' => array('WpTrivia_Controller_Quiz', 'ajaxLoadQuizData'),
            'resetLock' => array('WpTrivia_Controller_Quiz', 'ajaxResetLock'),
            'adminToplist' => array('WpTrivia_Controller_Toplist', 'ajaxAdminToplist'),
            'completedQuiz' => array('WpTrivia_Controller_Quiz', 'ajaxCompletedQuiz'),
            'quizCheckLock' => array('WpTrivia_Controller_Quiz', 'ajaxQuizCheckLock'),
            'addInToplist' => array('WpTrivia_Controller_Toplist', 'ajaxAddInToplist'),
            'showFrontToplist' => array('WpTrivia_Controller_Toplist', 'ajaxShowFrontToplist')
        );

        //nopriv
        $this->_frontCallbacks = array(
            'quizLoadData' => array('WpTrivia_Controller_Front', 'ajaxQuizLoadData'),
            'loadQuizData' => array('WpTrivia_Controller_Quiz', 'ajaxLoadQuizData'),
            'completedQuiz' => array('WpTrivia_Controller_Quiz', 'ajaxCompletedQuiz'),
            'quizCheckLock' => array('WpTrivia_Controller_Quiz', 'ajaxQuizCheckLock'),
            'addInToplist' => array('WpTrivia_Controller_Toplist', 'ajaxAddInToplist'),
            'showFrontToplist' => array('WpTrivia_Controller_Toplist', 'ajaxShowFrontToplist')
        );
    }
}
