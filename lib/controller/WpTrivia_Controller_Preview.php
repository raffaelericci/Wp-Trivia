<?php

class WpTrivia_Controller_Preview extends WpTrivia_Controller_Controller
{

    public function route()
    {

        wp_enqueue_script(
            'wpTrivia_front_javascript',
            plugins_url('js/wpTrivia_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
            array('jquery', 'jquery-ui-sortable'),
            WPPROQUIZ_VERSION
        );

        wp_localize_script('wpTrivia_front_javascript', 'WpTriviaGlobal', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'loadData' => __('Loading', 'wp-trivia'),
            'questionNotSolved' => __('You must answer this question.', 'wp-trivia'),
            'questionsNotSolved' => __('You must answer all questions before you can completed the quiz.',
                'wp-trivia'),
            'fieldsNotFilled' => __('All fields have to be filled.', 'wp-trivia')
        ));

        wp_enqueue_style(
            'wpTrivia_front_style',
            plugins_url('css/wpTrivia_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.css', WPPROQUIZ_FILE),
            array(),
            WPPROQUIZ_VERSION
        );

        $this->showAction($_GET['id']);
    }

    public function showAction($id)
    {
        $view = new WpTrivia_View_FrontQuiz();

        $quizMapper = new WpTrivia_Model_QuizMapper();
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $formMapper = new WpTrivia_Model_FormMapper();

        $quiz = $quizMapper->fetch($id);

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        $view->quiz = $quiz;
        $view->question = $question;
        $view->forms = $formMapper->fetch($quiz->getId());

        $view->show(true);
    }
}
