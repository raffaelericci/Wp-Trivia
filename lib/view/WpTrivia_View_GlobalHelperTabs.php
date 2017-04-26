<?php

class WpTrivia_View_GlobalHelperTabs
{


    public function getHelperSidebar()
    {
        ob_start();

        $this->showHelperSidebar();

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    public function getHelperTab()
    {
        ob_start();

        $this->showHelperTabContent();

        $content = ob_get_contents();

        ob_end_clean();

        return array(
            'id' => 'wp_trivia_help_tab_1',
            'title' => __('Wp-Trivia', 'wp-trivia'),
            'content' => $content,
        );
    }

    private function showHelperTabContent()
    {
        ?>

        <h2>Wp-Trivia</h2>

        <h4>Wp-Trivia on Github</h4>

        <iframe src="https://ghbtns.com/github-btn.html?user=ps1dr3x&repo=Wp-Trivia&type=star&count=true" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=ps1dr3x&repo=Wp-Trivia&type=watch&count=true&v=2" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
        <iframe src="https://ghbtns.com/github-btn.html?user=ps1dr3x&repo=Wp-Trivia&type=fork&count=true" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>

        <?php
    }

    private function showHelperSidebar()
    {
        ?>

        <p>
            <strong><?php _e('For more information:'); ?></strong>
        </p>
        <p>
            <a href="admin.php?page=wpTrivia_wpq_support"><?php _e('Support', 'wp-trivia'); ?></a>
        </p>
        <p>
            <a href="https://github.com/ps1dr3x/Wp-Trivia" target="_blank">Github</a>
        </p>

        <?php
    }
}
