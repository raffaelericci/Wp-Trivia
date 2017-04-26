<?php

/**
 * @property bool error
 * @property string importType
 * @property bool finish
 * @property array import
 * @property string importData
 */
class WpTrivia_View_Import extends WpTrivia_View_View
{

    public function show()
    {
        ?>
        <style>
            .wpProQuiz_importList {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .wpProQuiz_importList li {
                float: left;
                padding: 5px;
                border: 1px solid #B3B3B3;
                margin-right: 5px;
                background-color: #DAECFF;
            }
        </style>
        <div class="wrap wpProQuiz_importOverall">
            <h2><?php _e('Import', 'wp-trivia'); ?></h2>

            <p><a class="button-secondary" href="admin.php?page=wpProQuiz"><?php _e('back to overview',
                        'wp-trivia'); ?></a></p>
            <?php if ($this->error) { ?>
                <div style="padding: 10px; background-color: rgb(255, 199, 199); margin-top: 20px; border: 1px dotted;">
                    <h3 style="margin-top: 0;"><?php _e('Error', 'wp-trivia'); ?></h3>

                    <div>
                        <?php echo $this->error; ?>
                    </div>
                </div>
            <?php } else {
                if ($this->finish) { ?>
                    <div style="padding: 10px; background-color: #C7E4FF; margin-top: 20px; border: 1px dotted;">
                        <h3 style="margin-top: 0;"><?php _e('Successfully', 'wp-trivia'); ?></h3>

                        <div>
                            <?php _e('Import completed successfully', 'wp-trivia'); ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <form method="post">
                        <table class="wp-list-table widefat">
                            <thead>
                            <tr>
                                <th scope="col" width="30px"></th>
                                <th scope="col" width="40%"><?php _e('Quiz name', 'wp-trivia'); ?></th>
                                <th scope="col"><?php _e('Questions', 'wp-trivia'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($this->import['master'] as $master) { ?>
                                <tr>
                                    <th>
                                        <input type="checkbox" name="importItems[]"
                                               value="<?php echo $master->getId(); ?>" checked="checked">
                                    </th>
                                    <th><?php echo $master->getName(); ?></th>
                                    <th>
                                        <ul class="wpProQuiz_importList">
                                            <?php if (isset($this->import['question'][$master->getId()])) { ?>
                                                <?php foreach ($this->import['question'][$master->getId()] as $question) { ?>
                                                    <li><?php echo $question->getTitle(); ?></li>
                                                <?php }
                                            } ?>
                                        </ul>
                                        <div style="clear: both;"></div>
                                    </th>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <input name="importData" value="<?php echo $this->importData; ?>" type="hidden">
                        <input name="importType" value="<?php echo $this->importType; ?>" type="hidden">
                        <input style="margin-top: 20px;" class="button-primary" name="importSave"
                               value="<?php echo __('Start import', 'wp-trivia'); ?>" type="submit">
                    </form>
                <?php }
            } ?>
        </div>

        <?php
    }
}