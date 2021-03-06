<?php

class WpTrivia_Model_QuizMapper extends WpTrivia_Model_Mapper
{
    protected $_table;

    function __construct()
    {
        parent::__construct();

        $this->_table = $this->_prefix . "master";
    }

    public function delete($id)
    {
        $this->_wpdb->delete($this->_table, array(
            'id' => $id
        ),
            array('%d'));
    }

    public function exists($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE id = %d", $id));
    }

    /**
    * @param $id
    * @return WpTrivia_Model_Quiz
    */
    public function fetch($id) {
        $results = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "
                SELECT  m.*
                FROM    {$this->_table} AS m
                WHERE   id = %d
                ",
                $id
            ),
            ARRAY_A
        );
        return new WpTrivia_Model_Quiz($results);
    }

    /**
     * @return WpTrivia_Model_Quiz[]
     */
    public function fetchAll()
    {
        $r = array();

        $results = $this->_wpdb->get_results(
            "
				SELECT
					m.*
				FROM
					{$this->_table} AS m
			"
            , ARRAY_A);

        foreach ($results as $row) {
            $r[] = new WpTrivia_Model_Quiz($row);
        }

        return $r;
    }

    /**
     * @param $orderBy
     * @param $order
     * @param $search
     * @param $limit
     * @param $offset
     * @param $filter
     *
     * @return array
     */
    public function fetchTable($orderBy, $order, $search, $limit, $offset, $filter)
    {
        $r = array();

        switch ($orderBy) {
            default:
                $_orderBy = 'm.name';
                break;
        }

        $whereFilter = '';

        $results = $this->_wpdb->get_results($this->_wpdb->prepare(
            "
				SELECT
					m.*
				FROM
					{$this->_table} AS m
				WHERE
					m.name LIKE %s
					{$whereFilter}
				ORDER BY
					{$_orderBy} " . ($order == 'asc' ? 'asc' : 'desc') . "
				LIMIT %d, %d
			",
            array(
                '%' . $search . '%',
                $offset,
                $limit
            )), ARRAY_A);

        foreach ($results as $row) {
            $r[] = new WpTrivia_Model_Quiz($row);
        }

        $count = $this->_wpdb->get_var($this->_wpdb->prepare(
            "
				SELECT
					COUNT(*) as count_rows
				FROM
					{$this->_table} AS m
				WHERE
					m.name LIKE %s
					{$whereFilter}
			",
            array(
                '%' . $search . '%'
            )));

        return array(
            'quiz' => $r,
            'count' => $count ? $count : 0
        );
    }

    public function save(WpTrivia_Model_Quiz $data) {

        $set = array(
            'name' => $data->getName(),
            'text' => $data->getText(),
            'final_text' => $data->getFinalText(),
            'time_limit' => (int)$data->getTimeLimit(),
            'statistics_on' => (int)$data->isStatisticsOn(),
            'statistics_ip_lock' => (int)$data->getStatisticsIpLock(),
            'show_points' => (int)$data->isShowPoints(),
            'quiz_run_once' => (int)$data->isQuizRunOnce(),
            'quiz_run_once_type' => $data->getQuizRunOnceType(),
            'quiz_run_once_cookie' => (int)$data->isQuizRunOnceCookie(),
            'quiz_run_once_time' => (int)$data->getQuizRunOnceTime(),
            'numbered_answer' => (int)$data->isNumberedAnswer(),
            'hide_answer_message_box' => (int)$data->isHideAnswerMessageBox(),
            'disabled_answer_mark' => (int)$data->isDisabledAnswerMark(),
            'show_max_question' => (int)$data->isShowMaxQuestion(),
            'show_max_question_value' => (int)$data->getShowMaxQuestionValue(),
            'show_max_question_percent' => (int)$data->isShowMaxQuestionPercent(),
            'toplist_activated' => (int)$data->isToplistActivated(),
            'toplist_data' => $data->getToplistData(),
            'prerequisite' => (int)$data->isPrerequisite(),
            'email_notification' => $data->getEmailNotification(),
            'user_email_notification' => (int)$data->isUserEmailNotification(),
            'forcing_question_solve' => (int)$data->isForcingQuestionSolve(),
            'hide_question_position_overview' => (int)$data->isHideQuestionPositionOverview(),
            'hide_question_numbering' => (int)$data->isHideQuestionNumbering(),
            'form_activated' => (int)$data->isFormActivated(),
            'form_show_position' => $data->getFormShowPosition(),
            'start_only_registered_user' => (int)$data->isStartOnlyRegisteredUser(),
            'questions_per_page' => $data->getQuestionsPerPage(),
            'admin_email' => $data->getAdminEmail(true),
            'user_email' => $data->getUserEmail(true),
            'plugin_container' => $data->getPluginContainer(true),
            'valid_from_date' => $data->getValidFromDate(),
            'valid_to_date' => $data->getValidToDate()
        );

        $format = [
            '%s',		
            '%s',		
            '%s',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%s',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ];

        if ($data->getId() != 0) {
            $result = $this->_wpdb->update(
                $this->_table,
                $set,
                array('id' => $data->getId()),
                $format,
                array('%d')
            );
        } else {
            $result = $this->_wpdb->insert(
                $this->_table,
                $set,
                $format
            );
            $data->setId($this->_wpdb->insert_id);
        }

        if ($result === false) {
            return null;
        } else {
            return $data;
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function sumQuestionPoints($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT SUM(points) FROM {$this->_tableQuestion} WHERE quiz_id = %d AND online = 1",
            $id));
    }

    public function countQuestion($id)
    {
        return $this->_wpdb->get_var($this->_wpdb->prepare("SELECT COUNT(*) FROM {$this->_tableQuestion} WHERE quiz_id = %d AND online = 1",
            $id));
    }

    public function fetchAllAsArray($list, $outIds = array())
    {
        $where = ' 1 ';

        if (!empty($outIds)) {
            $where .= ' AND id NOT IN(' . implode(', ', array_map('intval', (array)$outIds)) . ') ';
        }

        return $this->_wpdb->get_results(
            "SELECT " . implode(', ', (array)$list) . " FROM {$this->_tableMaster} WHERE $where ORDER BY name",
            ARRAY_A
        );
    }

    public function fetchCol($ids, $col)
    {
        $ids = implode(', ', array_map('intval', (array)$ids));

        return $this->_wpdb->get_col("SELECT {$col} FROM {$this->_tableMaster} WHERE id IN({$ids})");
    }

    public function activateStatitic($quizIds, $lockIpTime)
    {
        $quizIds = implode(', ', array_map('intval', (array)$quizIds));

        return $this->_wpdb->query($this->_wpdb->prepare(
            "UPDATE {$this->_tableMaster}
			SET `statistics_on` = 1, `statistics_ip_lock` = %d
			WHERE `statistics_on` = 0 AND id IN(" . $quizIds . ")"
            , $lockIpTime));
    }

    public function deleteAll($quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare(
                "DELETE
					m, q, l, p, t, f, sr, s
				FROM
					{$this->_tableMaster} AS m
					LEFT JOIN {$this->_tableQuestion} AS q ON(q.quiz_id = m.id)
					LEFT JOIN {$this->_tableLock} AS l ON(l.quiz_id = m.id)
					LEFT JOIN {$this->_tablePrerequisite} AS p ON(p.prerequisite_quiz_id = m.id)
					LEFT JOIN {$this->_tableToplist} AS t ON(t.quiz_id = m.id)
					LEFT JOIN {$this->_tableForm} AS f ON(f.quiz_id = m.id)
					LEFT JOIN {$this->_tableStatisticRef} AS sr ON(sr.quiz_id = m.id)
						LEFT JOIN {$this->_tableStatistic} AS s ON(s.statistic_ref_id = sr.statistic_ref_id)
				WHERE
					m.id = %d"
                , $quizId)
        );
    }
}
