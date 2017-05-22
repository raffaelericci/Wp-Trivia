<?php

class WpTrivia_Controller_Admin
{

    protected $_ajax;

    public function __construct()
    {

        $this->_ajax = new WpTrivia_Controller_Ajax();
        $this->_ajax->init();

        add_action('admin_menu', array($this, 'register_page'));

        add_filter('set-screen-option', array($this, 'setScreenOption'), 10, 3);
    }

    public function setScreenOption($status, $option, $value)
    {
        if (in_array($option, array('wp_trivia_quiz_overview_per_page', 'wp_trivia_question_overview_per_page'))) {
            return $value;
        }

        return $status;
    }

    private function localizeScript()
    {
        global $wp_locale;

        $isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;

        $translation_array = array(
            'delete_msg' => __('Do you really want to delete the quiz/question?', 'wp-trivia'),
            'no_title_msg' => __('Title is not filled!', 'wp-trivia'),
            'no_question_msg' => __('No question deposited!', 'wp-trivia'),
            'no_correct_msg' => __('Correct answer was not selected!', 'wp-trivia'),
            'no_answer_msg' => __('No answer deposited!', 'wp-trivia'),
            'no_quiz_start_msg' => __('No quiz description filled!', 'wp-trivia'),
            'no_nummber_points' => __('No number in the field "Points" or less than 1', 'wp-trivia'),
            'no_nummber_points_new' => __('No number in the field "Points" or less than 0', 'wp-trivia'),
            'no_selected_quiz' => __('No quiz selected', 'wp-trivia'),
            'reset_statistics_msg' => __('Do you really want to reset the statistic?', 'wp-trivia'),
            'no_data_available' => __('No data available', 'wp-trivia'),
            'no_sort_element_criterion' => __('No sort element in the criterion', 'wp-trivia'),
            'dif_points' => __('"Different points for every answer" is not possible at "Free" choice', 'wp-trivia'),
            'confirm_delete_entry' => __('This entry should really be deleted?', 'wp-trivia'),
            'not_all_fields_completed' => __('Not all fields completed.', 'wp-trivia'),
            'temploate_no_name' => __('You must specify a template name.', 'wp-trivia'),
            'closeText' => __('Close', 'wp-trivia'),
            'currentText' => __('Today', 'wp-trivia'),
            'monthNames' => array_values($wp_locale->month),
            'monthNamesShort' => array_values($wp_locale->month_abbrev),
            'dayNames' => array_values($wp_locale->weekday),
            'dayNamesShort' => array_values($wp_locale->weekday_abbrev),
            'dayNamesMin' => array_values($wp_locale->weekday_initial),
            'dateFormat' => 'mm/dd/yy',
            'firstDay' => get_option('start_of_week'),
            'isRTL' => $isRtl
        );

        wp_localize_script('wpTrivia_admin_javascript', 'wpTriviaLocalize', $translation_array);
    }

    public function enqueueScript()
    {
        wp_enqueue_script(
            'wpTrivia_admin_javascript',
            plugins_url('js/wpTrivia_admin' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
            WPPROQUIZ_VERSION
        );

        wp_enqueue_style('jquery-ui',
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        $this->localizeScript();
    }

    public function register_page()
    {
        $pages = array();

        $pages[] = add_menu_page(
            'Wp-Trivia',
            'Wp-Trivia',
            'wpTrivia_show',
            'wpTrivia',
            array($this, 'route'));

        $pages[] = add_submenu_page(
            'wpTrivia',
            __('Global settings', 'wp-trivia'),
            __('Global settings', 'wp-trivia'),
            'wpTrivia_change_settings',
            'wpTrivia_glSettings',
            array($this, 'route'));

        $pages[] = add_submenu_page(
            'wpTrivia',
            __('Support & More', 'wp-trivia'),
            __('Support & More', 'wp-trivia'),
            'wpTrivia_show',
            'wpTrivia_wpq_support',
            array($this, 'route'));

        foreach ($pages as $p) {
            add_action('admin_print_scripts-' . $p, array($this, 'enqueueScript'));
            add_action('load-' . $p, array($this, 'routeLoadAction'));
        }
    }

    public function routeLoadAction()
    {
        $screen = get_current_screen();

        if (!empty($screen)) {
            // Workaround for wp_ajax_hidden_columns() with sanitize_key()
            $name = strtolower($screen->id);

            if (!empty($_GET['module'])) {
                $name .= '_' . strtolower($_GET['module']);
            }

            set_current_screen($name);

            $screen = get_current_screen();
        }

        $helperView = new WpTrivia_View_GlobalHelperTabs();

        $screen->add_help_tab($helperView->getHelperTab());
        $screen->set_help_sidebar($helperView->getHelperSidebar());

        $this->_route(true);
    }

    public function route()
    {
        $this->_route();
    }

    private function _route($routeAction = false)
    {
        $module = isset($_GET['module']) ? $_GET['module'] : 'overallView';

        if (isset($_GET['page'])) {
            if (preg_match('#wpTrivia_(.+)#', trim($_GET['page']), $matches)) {
                $module = $matches[1];
            }
        }

        $c = null;

        switch ($module) {
            case 'overallView':
                $c = new WpTrivia_Controller_Quiz();
                break;
            case 'question':
                $c = new WpTrivia_Controller_Question();
                break;
            case 'preview':
                $c = new WpTrivia_Controller_Preview();
                break;
            case 'statistics':
                $c = new WpTrivia_Controller_Statistics();
                break;
            case 'importExport':
                $c = new WpTrivia_Controller_ImportExport();
                break;
            case 'glSettings':
                $c = new WpTrivia_Controller_GlobalSettings();
                break;
            case 'styleManager':
                $c = new WpTrivia_Controller_StyleManager();
                break;
            case 'toplist':
                $c = new WpTrivia_Controller_Toplist();
                break;
            case 'wpq_support':
                $c = new WpTrivia_Controller_WpqSupport();
                break;
            case 'info_adaptation':
                $c = new WpTrivia_Controller_InfoAdaptation();
                break;
        }

        if ($c !== null) {
            if ($routeAction) {
                if (method_exists($c, 'routeAction')) {
                    $c->routeAction();
                }
            } else {
                $c->route();
            }
        }
    }
}
