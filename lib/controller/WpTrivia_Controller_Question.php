<?php

class WpTrivia_Controller_Question extends WpTrivia_Controller_Controller
{
    private $_quizId;

    public function route()
    {
        if (!isset($_GET['quiz_id']) || empty($_GET['quiz_id'])) {
            WpTrivia_View_View::admin_notices(__('Quiz not found', 'wp-trivia'), 'error');

            return;
        }

        $this->_quizId = (int)$_GET['quiz_id'];
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        $m = new WpTrivia_Model_QuizMapper();

        if ($m->exists($this->_quizId) == 0) {
            WpTrivia_View_View::admin_notices(__('Quiz not found', 'wp-trivia'), 'error');

            return;
        }

        switch ($action) {
            case 'show':
                $this->showAction();
                break;
            case 'addEdit':
                $this->addEditQuestion((int)$_GET['quiz_id']);
                break;
            case 'delete':
                $this->deleteAction($_GET['id']);
                break;
            case 'delete_multi':
                $this->deleteMultiAction();
                break;
            case 'save_sort':
                $this->saveSort();
                break;
            case 'load_question':
                $this->loadQuestion($_GET['quiz_id']);
                break;
            case 'copy_question':
                $this->copyQuestion($_GET['quiz_id']);
                break;
            default:
                $this->showAction();
                break;
        }
    }

    public function routeAction()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        switch ($action) {
            default:
                $this->showActionHook();
                break;
        }
    }

    private function showActionHook()
    {
        if (!empty($_REQUEST['_wp_http_referer'])) {
            wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI'])));
            exit;
        }

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        add_filter('manage_' . get_current_screen()->id . '_columns',
            array('WpTrivia_View_QuestionOverallTable', 'getColumnDefs'));

        add_screen_option('per_page', array(
            'label' => __('Questions', 'wp-trivia'),
            'default' => 20,
            'option' => 'wp_trivia_question_overview_per_page'
        ));
    }

    private function addEditQuestion($quizId)
    {
        $questionId = isset($_GET['questionId']) ? (int)$_GET['questionId'] : 0;

        if ($questionId) {
            if (!current_user_can('wpTrivia_edit_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        } else {
            if (!current_user_can('wpTrivia_add_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }

        $quizMapper = new WpTrivia_Model_QuizMapper();
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $templateMapper = new WpTrivia_Model_TemplateMapper();

        if ($questionId && $questionMapper->existsAndWritable($questionId) == 0) {
            WpTrivia_View_View::admin_notices(__('Question not found', 'wp-trivia'), 'error');

            return;
        }

        $question = new WpTrivia_Model_Question();

        if (isset($this->_post['template']) || (isset($this->_post['templateLoad']) && isset($this->_post['templateLoadId']))) {
            if (isset($this->_post['template'])) {
                $template = $this->saveTemplate();
            } else {
                $template = $templateMapper->fetchById($this->_post['templateLoadId']);
            }

            $data = $template->getData();

            if ($data !== null) {
                /** @var WpTrivia_Model_Question $question */
                $question = $data['question'];
                $question->setId($questionId);
                $question->setQuizId($quizId);
            }
        } else {
            if (isset($this->_post['submit'])) {
                if ($questionId) {
                    WpTrivia_View_View::admin_notices(__('Question edited', 'wp-trivia'), 'info');
                } else {
                    WpTrivia_View_View::admin_notices(__('Question added', 'wp-trivia'), 'info');
                }

                $question = $questionMapper->save($this->getPostQuestionModel($quizId, $questionId), true);
                $questionId = $question->getId();

            } else {
                if ($questionId) {
                    $question = $questionMapper->fetch($questionId);
                }
            }
        }

        $view = new WpTrivia_View_QuestionEdit();
        $view->quiz = $quizMapper->fetch($quizId);
        $view->templates = $templateMapper->fetchAll(WpTrivia_Model_Template::TEMPLATE_TYPE_QUESTION, false);
        $view->question = $question;
        $view->answerData = $this->setAnswerObject($question);

        $view->header = $questionId ? __('Edit question', 'wp-trivia') : __('New question', 'wp-trivia');

        if ($view->question->isAnswerPointsActivated()) {
            $view->question->setPoints(1);
        }

        $view->show();
    }

    private function saveTemplate()
    {
        $questionModel = $this->getPostQuestionModel(0, 0);

        $templateMapper = new WpTrivia_Model_TemplateMapper();
        $template = new WpTrivia_Model_Template();

        if ($this->_post['templateSaveList'] == '0') {
            $template->setName(trim($this->_post['templateName']));
        } else {
            $template = $templateMapper->fetchById($this->_post['templateSaveList'], false);
        }

        $template->setType(WpTrivia_Model_Template::TEMPLATE_TYPE_QUESTION);

        $template->setData(array(
            'question' => $questionModel
        ));

        return $templateMapper->save($template);
    }

    private function getPostQuestionModel($quizId, $questionId)
    {
        $questionMapper = new WpTrivia_Model_QuestionMapper();

        $post = WpTrivia_Controller_Request::getPost();

        $post['id'] = $questionId;
        $post['quizId'] = $quizId;
        $post['title'] = isset($post['title']) ? trim($post['title']) : '';

        $clearPost = $this->clearPost($post);

        $post['answerData'] = $clearPost['answerData'];

        if (empty($post['title'])) {
            $count = $questionMapper->count($quizId);

            $post['title'] = sprintf(__('Question: %d', 'wp-trivia'), $count + 1);
        }

        if (isset($post['answerPointsActivated'])) {
            if (isset($post['answerPointsDiffModusActivated'])) {
                $post['points'] = $clearPost['maxPoints'];
            } else {
                $post['points'] = $clearPost['points'];
            }
        }

        return new WpTrivia_Model_Question($post);
    }

    public function copyQuestion($quizId)
    {

        if (!current_user_can('wpTrivia_edit_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpTrivia_Model_QuestionMapper();

        $questions = $m->fetchById($this->_post['copyIds']);

        foreach ($questions as $question) {
            $question->setId(0);
            $question->setQuizId($quizId);

            $m->save($question);
        }

        WpTrivia_View_View::admin_notices(__('questions copied', 'wp-trivia'), 'info');

        $this->showAction();
    }

    public function loadQuestion($quizId)
    {

        if (!current_user_can('wpTrivia_edit_quiz')) {
            echo json_encode(array());
            exit;
        }

        $quizMapper = new WpTrivia_Model_QuizMapper();
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $data = array();

        $quiz = $quizMapper->fetchAll();

        foreach ($quiz as $qz) {

            if ($qz->getId() == $quizId) {
                continue;
            }

            $question = $questionMapper->fetchAll($qz->getId());
            $questionArray = array();

            foreach ($question as $qu) {
                $questionArray[] = array(
                    'name' => $qu->getTitle(),
                    'id' => $qu->getId()
                );
            }

            $data[] = array(
                'name' => $qz->getName(),
                'id' => $qz->getId(),
                'question' => $questionArray
            );
        }

        echo json_encode($data);

        exit;
    }

    public function saveSort()
    {

        if (!current_user_can('wpTrivia_edit_quiz')) {
            exit;
        }

        $mapper = new WpTrivia_Model_QuestionMapper();
        $map = $this->_post['sort'];

        foreach ($map as $k => $v) {
            $mapper->updateSort($v, $k);
        }

        exit;
    }

    public function deleteAction($id)
    {

        if (!current_user_can('wpTrivia_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpTrivia_Model_QuestionMapper();
        $mapper->setOnlineOff($id);

        $this->showAction();
    }

    public function deleteMultiAction()
    {
        if (!current_user_can('wpTrivia_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $mapper = new WpTrivia_Model_QuestionMapper();

        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $id) {
                $mapper->setOnlineOff($id);
            }
        }

        $this->showAction();
    }

    private function setAnswerObject(WpTrivia_Model_Question $question = null)
    {
        //Defaults
        $data = array(
            'sort_answer' => array(new WpTrivia_Model_AnswerTypes()),
            'classic_answer' => array(new WpTrivia_Model_AnswerTypes()),
            'free_answer' => array(new WpTrivia_Model_AnswerTypes())
        );

        if ($question !== null) {
            $type = $question->getAnswerType();
            $type = ($type == 'single' || $type == 'multiple') ? 'classic_answer' : $type;
            $answerData = $question->getAnswerData();

            if (isset($data[$type]) && $answerData !== null) {
                $data[$type] = $question->getAnswerData();
            }
        }

        return $data;
    }

    public function clearPost($post)
    {
        $answerData = array();
        $points = 0;
        $maxPoints = 0;

        foreach ($post['answerData'] as $k => $v) {
            if (trim($v['answer']) == '') {
                if (trim($v['sort_string']) == '') {
                    continue;
                }
            }

            $answerType = new WpTrivia_Model_AnswerTypes($v);
            $points += $answerType->getPoints();

            $maxPoints = max($maxPoints, $answerType->getPoints());

            $answerData[] = $answerType;
        }

        return array('points' => $points, 'maxPoints' => $maxPoints, 'answerData' => $answerData);
    }

    public function clear($a)
    {
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $a[$k] = $this->clear($a[$k]);
            }

            if (is_string($a[$k])) {
                $a[$k] = trim($a[$k]);

                if ($a[$k] != '') {
                    continue;
                }
            }

            if (empty($a[$k])) {
                unset($a[$k]);
            }
        }

        return $a;
    }

    private function getCurrentPage()
    {
        $pagenum = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 0;

        return max(1, $pagenum);
    }

    public function showAction()
    {
        if (!current_user_can('wpTrivia_show')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpTrivia_Model_QuizMapper();
        $mm = new WpTrivia_Model_QuestionMapper();

        $view = new WpTrivia_View_QuestionOverall();
        $view->quiz = $m->fetch($this->_quizId);

        $per_page = (int)get_user_option('wp_trivia_question_overview_per_page');
        if (empty($per_page) || $per_page < 1) {
            $per_page = 20;
        }

        $current_page = $this->getCurrentPage();
        $search = isset($_GET['s']) ? trim($_GET['s']) : '';
        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
        $order = isset($_GET['order']) ? trim($_GET['order']) : '';
        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;
        $filter = array();

        if (isset($_GET['cat'])) {
            $filter['cat'] = $_GET['cat'];
        }

        $result = $mm->fetchTable($this->_quizId, $orderBy, $order, $search, $limit, $offset, $filter);

        $view->questionItems = $result['questions'];
        $view->questionCount = $result['count'];
        $view->perPage = $per_page;

        $view->show();
    }

    public static function ajaxLoadQuestionsSort($data)
    {
        if (!current_user_can('wpTrivia_edit_quiz')) {
            return json_encode(array());
        }

        $quizMapper = new WpTrivia_Model_QuestionMapper();

        $questions = $quizMapper->fetchAllList($data['quizId'], array('id', 'title'), true);

        return json_encode($questions);
    }

    public static function ajaxSaveSort($data)
    {
        if (!current_user_can('wpTrivia_edit_quiz')) {
            return json_encode(array());
        }

        $mapper = new WpTrivia_Model_QuestionMapper();

        foreach ($data['sort'] as $k => $v) {
            $mapper->updateSort($v, $k);
        }

        return json_encode(array());
    }

    public static function ajaxLoadCopyQuestion($data)
    {
        if (!current_user_can('wpTrivia_edit_quiz')) {
            echo json_encode(array());
            exit;
        }

        $quizId = $data['quizId'];
        $quizMapper = new WpTrivia_Model_QuizMapper();
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $data = array();

        $quiz = $quizMapper->fetchAll();

        foreach ($quiz as $qz) {

            if ($qz->getId() == $quizId) {
                continue;
            }

            $question = $questionMapper->fetchAll($qz->getId());
            $questionArray = array();

            foreach ($question as $qu) {
                $questionArray[] = array(
                    'name' => $qu->getTitle(),
                    'id' => $qu->getId()
                );
            }

            $data[] = array(
                'name' => $qz->getName(),
                'id' => $qz->getId(),
                'question' => $questionArray
            );
        }

        return json_encode($data);
    }

    /**
     * Checks the user answer and calls "wptrivia_after_answer" action
     *
     * @param  {array} $data | questionId, questionType, array answer
     * @return {array} $res  | isCorrect, array correctAnswer
     */
    public static function ajaxCheckAnswer($data) {
        // TODO - Post-action callback
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $correctAnswers = $questionMapper->fetch($data['questionId'])->getAnswerData();
        $res = [
            "isCorrect" => true,
            "correctAnswer" => null
        ];
        switch($data['questionType']) {
            case 'single':
            case 'multi':
                foreach($correctAnswers as $index => $a) {
                    $res['correctAnswer'][] = $a->isCorrect();
                    if ($data['answer'][$index] && ($data['answer'][$index] != $a->isCorrect())) {
                        $res['isCorrect'] = false;
                    }
                }
                break;
            // TODO - Implement other question types
        }
        do_action('wptrivia_after_answer', $data, $res);
        return json_encode($res);
    }

    /**
     * TODO - End quiz callback
     * Load next question
     *
     * @param  {array} $data | questionId
     * @return
     */
    public static function ajaxLoadNextQuestion($data) {
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $nextQuestion = $questionMapper->fetchNext($data['questionId']);
        if (!$nextQuestion) {
            // Quiz ended
            // TODO -
            //$questionMapper->fetchFinalPage($data['questionId']);
            return json_encode(["ended" => true]);
        }
        return $nextQuestion->getPublicJson();
    }
}
