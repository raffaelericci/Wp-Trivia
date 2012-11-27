<?php
class WpProQuiz_Controller_Preview extends WpProQuiz_Controller_Controller {
	
	private $_plugin_file;
	
	public function __construct($plugin_file) {
		parent::__construct();
		

		$this->_plugin_file = $plugin_file;
	}
	
	public function route() {
		
		wp_enqueue_script(
			'wpProQuiz_fron_javascript', 
			plugins_url('js/wpProQuiz_front.min.js', $this->_plugin_file),
			array('jquery', 'jquery-ui-sortable'),
			WPPROQUIZ_VERSION
		);
		
		wp_enqueue_style(
			'wpProQuiz_front_style', 
			plugins_url('css/wpProQuiz_front.min.css', $this->_plugin_file),
			array(),
			WPPROQUIZ_VERSION
		);
		
		$this->showAction($_GET['id']);
	}
	
	public function showAction($id) {
		$view = new WpProQuiz_View_FrontQuiz();
		
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		
		$view->quiz = $quizMapper->fetch($id);
		$view->question = $questionMapper->fetchAll($id);
		
		$view->show(true);
	}
}