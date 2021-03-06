<?php

class WpTrivia_View_QuizOverallTable extends WP_List_Table
{
    /** @var  WpTrivia_Model_Quiz[] */
    private $quizItems;

    private $quizCount;
    private $perPage;

    public static function getColumnDefs()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'wp-trivia'),
            'shortcode' => __('Shortcode', 'wp-trivia'),
            'shortcode_leaderboard' => __('Shortcode-Leaderboard', 'wp-trivia')
        );

        return $columns;
    }

    function __construct($quizItems, $quizCount, $perPage)
    {
        parent::__construct(array(
            'singular' => __('Quiz', 'wp-trivia'),
            'plural' => __('Quiz', 'wp-trivia'),
            'ajax' => false,
            'screen' => 'toplevel_page_wpproquiz'
        ));

        $this->quizItems = $quizItems;
        $this->quizCount = $quizCount;
        $this->perPage = $perPage;
    }

    function no_items()
    {
        _e('No data available', 'wp-trivia');
    }

    function column_default($item, $column_name)
    {
        return isset($item[$column_name]) ? $item[$column_name] : '';
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', false)
        );

        return $sortable_columns;
    }

    function get_columns()
    {
        return get_column_headers(get_current_screen());
    }

    function column_name($item)
    {
        $actions = array(
            'wpTrivia_questions' => sprintf('<a href="?page=wpTrivia&module=question&quiz_id=%s">' . __('Questions',
                    'wp-trivia') . '</a>', $item['ID']),
        );

        if (current_user_can('wpTrivia_edit_quiz')) {
            $actions['wpTrivia_edit'] = sprintf('<a href="?page=wpTrivia&action=addEdit&quizId=%s">' . __('Edit',
                    'wp-trivia') . '</a>', $item['ID']);
        }

        if (current_user_can('wpTrivia_delete_quiz')) {
            $actions['wpTrivia_delete'] = sprintf('<a style="color: red;" href="?page=wpTrivia&action=delete&id=%s">' . __('Delete',
                    'wp-trivia') . '</a>', $item['ID']);
        }

        $actions['wpTrivia_preview'] = sprintf('<a href="?page=wpTrivia&module=preview&id=%s">' . __('Preview',
                'wp-trivia') . '</a>', $item['ID']);

        if (current_user_can('wpTrivia_show_statistics')) {
            $actions['wpTrivia_statistics'] = sprintf('<a href="?page=wpTrivia&module=statistics&id=%s">' . __('Statistics',
                    'wp-trivia') . '</a>', $item['ID']);
        }

        if (current_user_can('wpTrivia_toplist_edit')) {
            $actions['wpTrivia_leaderboard'] = sprintf('<a href="?page=wpTrivia&module=toplist&id=%s">' . __('Leaderboard',
                    'wp-trivia') . '</a>', $item['ID']);
        }

        return sprintf('<a class="row-title" href="?page=wpTrivia&module=question&quiz_id=%1$s">%2$s</a> %3$s',
            $item['ID'], $item['name'], $this->row_actions($actions));
    }

    function get_bulk_actions()
    {
        $actions = array();

        if (current_user_can('wpTrivia_delete_quiz')) {
            $actions['delete'] = __('Delete', 'wp-trivia');
        }

        if (current_user_can('wpTrivia_export')) {
            $actions['export'] = __('Export', 'wp-trivia');
        }

        return $actions;
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="quiz[]" value="%s" />', $item['ID']
        );
    }

    function prepare_items()
    {
        $this->set_pagination_args(array(
            'total_items' => $this->quizCount,
            'per_page' => $this->perPage
        ));

        $items = array();

        foreach ($this->quizItems as $q) {
            $items[] = array(
                'ID' => $q->getId(),
                'name' => $q->getName(),
                'shortcode' => '[WpTrivia ' . $q->getId() . ']',
                'shortcode_leaderboard' => $q->isToplistActivated() ? '[WpTrivia_toplist ' . $q->getId() . ']' : ''
            );
        }

        $this->items = $items;
    }
}
