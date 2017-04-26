<?php

class WpTrivia_Model_GlobalSettingsMapper extends WpTrivia_Model_Mapper
{

    public function fetchAll()
    {
        $s = new WpTrivia_Model_GlobalSettings();

        $s->setAddRawShortcode(get_option('wpTrivia_addRawShortcode'))
            ->setJsLoadInHead(get_option('wpTrivia_jsLoadInHead'))
            ->setTouchLibraryDeactivate(get_option('wpTrivia_touchLibraryDeactivate'))
            ->setCorsActivated(get_option('wpTrivia_corsActivated'));

        return $s;
    }

    public function save(WpTrivia_Model_GlobalSettings $settings)
    {

        if (add_option('wpTrivia_addRawShortcode', $settings->isAddRawShortcode()) === false) {
            update_option('wpTrivia_addRawShortcode', $settings->isAddRawShortcode());
        }

        if (add_option('wpTrivia_jsLoadInHead', $settings->isJsLoadInHead()) === false) {
            update_option('wpTrivia_jsLoadInHead', $settings->isJsLoadInHead());
        }

        if (add_option('wpTrivia_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate()) === false) {
            update_option('wpTrivia_touchLibraryDeactivate', $settings->isTouchLibraryDeactivate());
        }

        if (add_option('wpTrivia_corsActivated', $settings->isCorsActivated()) === false) {
            update_option('wpTrivia_corsActivated', $settings->isCorsActivated());
        }
    }

    public function delete()
    {
        delete_option('wpTrivia_addRawShortcode');
        delete_option('wpTrivia_jsLoadInHead');
        delete_option('wpTrivia_touchLibraryDeactivate');
        delete_option('wpTrivia_corsActivated');
    }

    /**
     * @return array
     */
    public function getEmailSettings()
    {
        $e = get_option('wpTrivia_emailSettings', null);

        if ($e === null) {
            $e['to'] = '';
            $e['from'] = '';
            $e['subject'] = __('Wp-Trivia: One user completed a quiz', 'wp-trivia');#
            $e['html'] = false;
            $e['message'] = __('Wp-Trivia

The user "$username" has completed "$quizname" the quiz.

Points: $points
Result: $result

', 'wp-trivia');

        }

        return $e;
    }

    public function saveEmailSettiongs($data)
    {
        if (isset($data['html']) && $data['html']) {
            $data['html'] = true;
        } else {
            $data['html'] = false;
        }

        if (add_option('wpTrivia_emailSettings', $data, '', 'no') === false) {
            update_option('wpTrivia_emailSettings', $data);
        }
    }

    /**
     * @return array
     */
    public function getUserEmailSettings()
    {
        $e = get_option('wpTrivia_userEmailSettings', null);

        if ($e === null) {
            $e['from'] = '';
            $e['subject'] = __('Wp-Trivia: One user completed a quiz', 'wp-trivia');
            $e['html'] = false;
            $e['message'] = __('Wp-Trivia

You have completed the quiz "$quizname".

Points: $points
Result: $result

', 'wp-trivia');

        }

        return $e;

    }

    public function saveUserEmailSettiongs($data)
    {
        if (isset($data['html']) && $data['html']) {
            $data['html'] = true;
        } else {
            $data['html'] = false;
        }

        if (add_option('wpTrivia_userEmailSettings', $data, '', 'no') === false) {
            update_option('wpTrivia_userEmailSettings', $data);
        }
    }
}