<?php

class WpTrivia_View_QuestionOverallTable extends WP_List_Table
{
    /** @var  WpTrivia_Model_Question[] */
    private $questionItems;

    private $questionCount;
    private $perPage;

    public static function getColumnDefs()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'wp-trivia'),
            'points' => __('Points', 'wp-trivia')
        );

        return $columns;
    }

    function __construct($questionItems, $questionCount, $perPage)
    {
        parent::__construct(array(
            'singular' => __('Quiz', 'wp-trivia'),
            'plural' => __('Quiz', 'wp-trivia'),
            'ajax' => false,
            'screen' => get_current_screen()->id
        ));

        $this->questionItems = $questionItems;
        $this->questionCount = $questionCount;
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
        $actions = array();

        if (current_user_can('wpTrivia_edit_quiz')) {
            $actions['wpTrivia_edit'] = sprintf('<a href="?page=wpTrivia&module=question&action=addEdit&quiz_id=%1$s&questionId=%2$s">' . __('Edit',
                    'wp-trivia') . '</a>',
                $item['quizId'], $item['ID']);
        }

        if (current_user_can('wpTrivia_delete_quiz')) {
            $actions['wpTrivia_delete'] = sprintf('<a style="color: red;" href="?page=wpTrivia&module=question&action=delete&quiz_id=%1$s&id=%2$s">' . __('Delete',
                    'wp-trivia') . '</a>',
                $item['quizId'], $item['ID']);
        }

        if (current_user_can('wpTrivia_edit_quiz')) {
            return sprintf('<a class="row-title" href="?page=wpTrivia&module=question&action=addEdit&quiz_id=%1$s&questionId=%2$s">%3$s</a> %4$s',
                $item['quizId'], $item['ID'], $item['name'], $this->row_actions($actions));
        } else {
            return sprintf('<a class="row-title" href="#">%2$s</a> %3$s', $item['ID'], $item['name'],
                $this->row_actions($actions));
        }
    }

    function get_bulk_actions()
    {
        $actions = array();

        if (current_user_can('wpTrivia_delete_quiz')) {
            $actions['delete'] = __('Delete', 'wp-trivia');
        }

        return $actions;
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="questions[]" value="%1$s" />',
            $item['ID']
        );
    }

    function prepare_items()
    {
        $this->set_pagination_args(array(
            'total_items' => $this->questionCount,
            'per_page' => $this->perPage
        ));

        $items = array();

        foreach ($this->questionItems as $i => $q) {
            $items[] = array(
                'ID' => $q->getId(),
                'quizId' => $q->getQuizId(),
                'name' => $q->getTitle(),
                'points' => $q->getPoints(),
                'sort' => $q->getSort()
            );
        }

        $this->items = $items;
    }


}
