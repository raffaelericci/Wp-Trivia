<?php

class WpTrivia_View_InfoAdaptation extends WpTrivia_View_View
{
    public function show()
    {
        ?>

        <div class="wrap">
            <h2><?php _e('Wp-Trivia special modification', 'wp-trivia'); ?></h2>

            <p><?php _e('You need special Wp-Trivia modification for your website?', 'wp-trivia'); ?></p>

            <h3><?php _e('We offer you:', 'wp-trivia'); ?></h3>
            <ol style="list-style-type: disc;">
                <li><?php _e('Design adaption for your theme', 'wp-trivia'); ?></li>
                <li><?php _e('Creation of additional modules for your needs', 'wp-trivia'); ?></li>
                <li style="display: none;"><?php _e('Premium Support', 'wp-trivia'); ?></li>
            </ol>

            <h3><?php _e('Contact us:', 'wp-trivia'); ?></h3>
            <ol style="list-style-type: disc;">
                <li><?php _e('Send us an e-mail', 'wp-trivia'); ?> <a href="mailto:wp-trivia@it-gecko.de"
                                                                        style="font-weight: bold;">wp-trivia@it-gecko.de</a>
                </li>
                <li><?php _e('The e-mail must be written in english or german', 'wp-trivia'); ?></li>
                <li><?php _e('Explain your wish detailed and exactly as possible', 'wp-trivia'); ?>
                    <ol style="list-style-type: disc;">
                        <li><?php _e('You can send us screenshots, sketches and attachments', 'wp-trivia'); ?></li>
                    </ol>
                </li>
                <li><?php _e('Send us your full name and your web address (webpage-URL)', 'wp-trivia'); ?></li>
                <li><?php _e('If you wish design adaption, we additionally need the name of your theme',
                        'wp-trivia'); ?></li>
            </ol>

            <p>
                <?php _e('After receiving your e-mail we will verify your request on feasibility. After this you will receive e-mail from us with further details and offer.',
                    'wp-trivia'); ?>
            </p>

            <p>
                <?php _e('Extended support in first 6 months. Reported bugs and updates of WP Pro Quiz are supported. Exception are major releases (update of main version)',
                    'wp-trivia'); ?>
            </p>
        </div>

        <?php
    }
}