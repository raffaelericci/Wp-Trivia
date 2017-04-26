<?php

class WpTrivia_Plugin_BpAchievementsV1 extends DPA_Extension
{

    public function __construct()
    {
        $this->actions = array(
            'wp_pro_quiz_completed_quiz' => __('The user completed a quiz.', 'wp-trivia'),
            'wp_pro_quiz_completed_quiz_100_percent' => __('The user completed a quiz with 100 percent.', 'wp-trivia')
        );

        $this->contributors = array(
            array(
                'name' => 'Julius Fischer',
                'gravatar_url' => 'http://gravatar.com/avatar/c3736cd18c273f32569726c93f76244d',
                'profile_url' => 'http://profiles.wordpress.org/xeno010',
            )
        );

        $this->description = __('A powerful and beautiful quiz plugin for WordPress.', 'wp-trivia');
        $this->id = 'wp-trivia';
        $this->image_url = WPPROQUIZ_URL . '/img/wp_pro_quiz.jpg';
        $this->name = __('WP-Pro-Quiz', 'wp-trivia');
        //$this->rss_url         = '';
        $this->small_image_url = WPPROQUIZ_URL . '/img/wp_pro_quiz_small.jpg';
        $this->version = 5;
        $this->wporg_url = 'http://wordpress.org/extend/plugins/wp-trivia/';
    }

    public function do_update()
    {
        $this->insertTerm();
    }

    public function insertTerm()
    {
        if (function_exists('dpa_get_event_tax_id')) {
            $taxId = dpa_get_event_tax_id();

            foreach ($this->actions as $actionName => $desc) {
                $e = term_exists($actionName, $taxId);

                if ($e === 0 || $e === null) {
                    wp_insert_term($actionName, $taxId, array('description' => $desc));
                }
            }
        }
    }
}
