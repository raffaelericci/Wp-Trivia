<?php

class WpTrivia_View_Statistics extends WpTrivia_View_View
{
    /**
     * @var WpTrivia_Model_Quiz
     */
    public $quiz;


    public function show()
    {

        ?>

        <style>
            .wpTrivia_blueBox {
                padding: 20px;
                background-color: rgb(223, 238, 255);
                border: 1px dotted;
                margin-top: 10px;
            }

            .categoryTr th {
                background-color: #F1F1F1;
            }
        </style>


        <div class="wrap wpTrivia_statistics">
            <input type="hidden" id="quizId" value="<?php echo $this->quiz->getId(); ?>" name="quizId">

            <h2><?php printf(__('Quiz: %s - Statistics', 'wp-trivia'), $this->quiz->getName()); ?></h2>

            <p><a class="button-secondary" href="admin.php?page=wpTrivia"><?php _e('back to overview',
                        'wp-trivia'); ?></a></p>

            <?php if (!$this->quiz->isStatisticsOn()) { ?>
                <p style="padding: 30px; background: #F7E4E4; border: 1px dotted; width: 300px;">
                    <span style="font-weight: bold; padding-right: 10px;"><?php _e('Stats not enabled',
                            'wp-trivia'); ?></span>
                    <a class="button-secondary"
                       href="admin.php?page=wpTrivia&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>"><?php _e('Activate statistics',
                            'wp-trivia'); ?></a>
                </p>
                <?php return;
            } ?>

            <div style="padding: 10px 0px;">
                <a class="button-primary wpTrivia_tab" id="wpTrivia_typeUser" href="#"><?php _e('Users',
                        'wp-trivia'); ?></a>
                <a class="button-secondary wpTrivia_tab" id="wpTrivia_typeOverview" href="#"><?php _e('Overview',
                        'wp-trivia'); ?></a>
                <a class="button-secondary wpTrivia_tab" id="wpTrivia_typeForm" href="#"><?php _e('Custom fields',
                        'wp-trivia'); ?></a>
            </div>

            <div id="wpTrivia_loadData" class="wpTrivia_blueBox" style="background-color: #F8F5A8; display: none;">
                <img alt="load"
                     src="data:image/gif;base64,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">
                <?php _e('Loading', 'wp-trivia'); ?>
            </div>

            <div id="wpTrivia_content" style="display: none;">

                <?php $this->tabUser(); ?>
                <?php $this->tabOverview(); ?>
                <?php $this->tabForms(); ?>

            </div>

        </div>

        <?php
    }

    private function tabUser()
    {
        ?>
        <div id="wpTrivia_tabUsers" class="wpTrivia_tabContent">
            <div class="wpTrivia_blueBox" id="wpTrivia_userBox" style="margin-bottom: 20px;">
                <div style="float: left;">
                    <div style="padding-top: 6px;">
                        <?php _e('Please select user name:', 'wp-trivia'); ?>
                    </div>

                    <div style="padding-top: 6px;">
                        <?php _e('Select a test:', 'wp-trivia'); ?>
                    </div>

                </div>

                <div style="float: left;">
                    <div>
                        <select name="userSelect" id="userSelect">
                            <?php foreach ($this->users as $user) {
                                if ($user->ID == 0) {
                                    echo '<option value="0">=== ', __('Anonymous user', 'wp-trivia'), ' ===</option>';
                                } else {
                                    echo '<option value="', $user->ID, '">', $user->user_login, ' (', $user->display_name, ')</option>';
                                }
                            } ?>
                        </select>
                    </div>

                    <div>
                        <select id="testSelect">
                            <option value="0">=== <?php _e('average', 'wp-trivia'); ?> ===</option>
                        </select>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>

            <?php $this->formTable(); ?>

            <table class="wp-list-table widefat">
                <thead>
                <tr>
                    <th scope="col" style="width: 50px;"></th>
                    <th scope="col"><?php _e('Question', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Points', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Hints used', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Time', 'wp-trivia'); ?> <span
                            style="font-size: x-small;">(hh:mm:ss)</span></th>
                    <th scope="col" style="width: 100px;"><?php _e('Points scored', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 60px;"><?php _e('Results', 'wp-trivia'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $gPoints = 0;
                foreach ($this->questionList as $k => $ql) {
                    $index = 1;
                    $cPoints = 0;
                    ?>

                    <tr class="categoryTr">
                        <th colspan="9">
                            <span><?php _e('Category', 'wp-trivia'); ?>:</span>
                            <span
                                style="font-weight: bold;"><?php echo $this->categoryList[$k]->getCategoryName(); ?></span>
                        </th>
                    </tr>

                    <?php foreach ($ql as $q) {
                        $gPoints += $q->getPoints();
                        $cPoints += $q->getPoints();
                        ?>
                        <tr id="wpTrivia_tr_<?php echo $q->getId(); ?>">
                            <th><?php echo $index++; ?></th>
                            <th><?php echo $q->getTitle(); ?></th>
                            <th class="wpTrivia_points"><?php echo $q->getPoints(); ?></th>
                            <th class="wpTrivia_cCorrect" style="color: green;"></th>
                            <th class="wpTrivia_cIncorrect" style="color: red;"></th>
                            <th class="wpTrivia_cTip"></th>
                            <th class="wpTrivia_cTime"></th>
                            <th class="wpTrivia_cPoints"></th>
                            <th></th>
                        </tr>
                    <?php } ?>

                    <tr class="categoryTr" id="wpTrivia_ctr_<?php echo $k; ?>">
                        <th colspan="2">
                            <span><?php _e('Sub-Total: ', 'wp-trivia'); ?></span>
                        </th>
                        <th class="wpTrivia_points"><?php echo $cPoints; ?></th>
                        <th class="wpTrivia_cCorrect" style="color: green;"></th>
                        <th class="wpTrivia_cIncorrect" style="color: red;"></th>
                        <th class="wpTrivia_cTip"></th>
                        <th class="wpTrivia_cTime"></th>
                        <th class="wpTrivia_cPoints"></th>
                        <th class="wpTrivia_cResult" style="font-weight: bold;"></th>
                    </tr>

                    <tr>
                        <th colspan="9"></th>
                    </tr>

                <?php } ?>
                </tbody>

                <tfoot>
                <tr id="wpTrivia_tr_0">
                    <th></th>
                    <th><?php _e('Total', 'wp-trivia'); ?></th>
                    <th class="wpTrivia_points"><?php echo $gPoints; ?></th>
                    <th class="wpTrivia_cCorrect" style="color: green;"></th>
                    <th class="wpTrivia_cIncorrect" style="color: red;"></th>
                    <th class="wpTrivia_cTip"></th>
                    <th class="wpTrivia_cTime"></th>
                    <th class="wpTrivia_cPoints"></th>
                    <th class="wpTrivia_cResult" style="font-weight: bold;"></th>
                </tr>
                </tfoot>
            </table>

            <div style="margin-top: 10px;">
                <div style="float: left;">
                    <a class="button-secondary wpTrivia_update" href="#"><?php _e('Refresh', 'wp-trivia'); ?></a>
                </div>
                <div style="float: right;">
                    <?php if (current_user_can('wpTrivia_reset_statistics')) { ?>
                        <a class="button-secondary" href="#" id="wpTrivia_reset"><?php _e('Reset statistics',
                                'wp-trivia'); ?></a>
                        <a class="button-secondary" href="#" id="wpTrivia_resetUser"><?php _e('Reset user statistics',
                                'wp-trivia'); ?></a>
                        <a class="button-secondary wpTrivia_resetComplete" href="#"><?php _e('Reset entire statistic',
                                'wp-trivia'); ?></a>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

        <?php
    }

    private function tabOverview()
    {

        ?>

        <div id="wpTrivia_tabOverview" class="wpTrivia_tabContent" style="display: none;">

            <input type="hidden" value="<?php echo 0; ?>" name="gPoints" id="wpTrivia_gPoints">

            <div id="poststuff">
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Filter', 'wp-trivia'); ?></h3>

                    <div class="inside">
                        <ul>
                            <li>
                                <label>
                                    <?php _e('Show only users, who solved the quiz:', 'wp-trivia'); ?>
                                    <input type="checkbox" value="1" id="wpTrivia_onlyCompleted">
                                </label>
                            </li>
                            <li>
                                <label>
                                    <?php _e('How many entries should be shown on one page:', 'wp-trivia'); ?>
                                    <select id="wpTrivia_pageLimit">
                                        <option>1</option>
                                        <option>10</option>
                                        <option>50</option>
                                        <option selected="selected">100</option>
                                        <option>500</option>
                                        <option>1000</option>
                                    </select>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <table class="wp-list-table widefat">
                <thead>
                <tr>
                    <th scope="col"><?php _e('User', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Points', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Hints used', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 100px;"><?php _e('Time', 'wp-trivia'); ?> <span
                            style="font-size: x-small;">(hh:mm:ss)</span></th>
                    <th scope="col" style="width: 60px;"><?php _e('Results', 'wp-trivia'); ?></th>
                </tr>
                </thead>
                <tbody id="wpTrivia_statistics_overview_data">
                <tr style="display: none;">
                    <th><a href="#"></a></th>
                    <th class="wpTrivia_cPoints"></th>
                    <th class="wpTrivia_cCorrect" style="color: green;"></th>
                    <th class="wpTrivia_cIncorrect" style="color: red;"></th>
                    <th class="wpTrivia_cTip"></th>
                    <th class="wpTrivia_cTime"></th>
                    <th class="wpTrivia_cResult" style="font-weight: bold;"></th>
                </tr>
                </tbody>
            </table>

            <div style="margin-top: 10px;">
                <div style="float: left;">
                    <input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button"
                           id="wpTrivia_pageLeft">
                    <select id="wpTrivia_currentPage">
                        <option value="1">1</option>
                    </select>
                    <input style="font-weight: bold;" class="button-secondary" value="&gt;" type="button"
                           id="wpTrivia_pageRight">
                </div>
                <div style="float: right;">
                    <a class="button-secondary wpTrivia_update" href="#"><?php _e('Refresh', 'wp-trivia'); ?></a>
                    <?php if (current_user_can('wpTrivia_reset_statistics')) { ?>
                        <a class="button-secondary wpTrivia_resetComplete" href="#"><?php _e('Reset entire statistic',
                                'wp-trivia'); ?></a>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
            </div>

        </div>

        <?php
    }

    private function tabForms()
    {
        ?>

        <div id="wpTrivia_tabFormOverview" class="wpTrivia_tabContent" style="display: none;">

            <div id="poststuff">
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Filter', 'wp-trivia'); ?></h3>

                    <div class="inside">
                        <ul>
                            <li>
                                <label>
                                    <?php _e('Which users should be displayed:', 'wp-trivia'); ?>
                                    <select id="wpTrivia_formUser">
                                        <option value="0"><?php _e('all', 'wp-trivia'); ?></option>
                                        <option value="1"><?php _e('only registered users', 'wp-trivia'); ?></option>
                                        <option value="2"><?php _e('only anonymous users', 'wp-trivia'); ?></option>
                                    </select>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <?php _e('How many entries should be shown on one page:', 'wp-trivia'); ?>
                                    <select id="wpTrivia_fromPageLimit">
                                        <option>1</option>
                                        <option>10</option>
                                        <option>50</option>
                                        <option selected="selected">100</option>
                                        <option>500</option>
                                        <option>1000</option>
                                    </select>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <table class="wp-list-table widefat">
                <thead>
                <tr>
                    <th scope="col"><?php _e('Username', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 200px;"><?php _e('Date', 'wp-trivia'); ?></th>
                    <th scope="col" style="width: 60px;"><?php _e('Results', 'wp-trivia'); ?></th>
                </tr>
                </thead>
                <tbody id="wpTrivia_statistics_form_data">
                <tr style="display: none;">
                    <th><a href="#" class="wpTrivia_cUsername"></a></th>
                    <th class="wpTrivia_cCreateTime"></th>
                    <th class="wpTrivia_cResult" style="font-weight: bold;"></th>
                </tr>
                </tbody>
            </table>

            <div style="margin-top: 10px;">
                <div style="float: left;">
                    <input style="font-weight: bold;" class="button-secondary" value="&lt;" type="button"
                           id="wpTrivia_formPageLeft">
                    <select id="wpTrivia_formCurrentPage">
                        <option value="1">1</option>
                    </select>
                    <input style="font-weight: bold;" class="button-secondary" value="&gt;" type="button"
                           id="wpTrivia_formPageRight">
                </div>
                <div style="float: right;">
                    <a class="button-secondary wpTrivia_update" href="#"><?php _e('Refresh', 'wp-trivia'); ?></a>
                    <?php if (current_user_can('wpTrivia_reset_statistics')) { ?>
                        <a class="button-secondary wpTrivia_resetComplete" href="#"><?php _e('Reset entire statistic',
                                'wp-trivia'); ?></a>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
            </div>

        </div>


        <?php
    }

    private function formTable()
    {
        if (!$this->quiz->isFormActivated()) {
            return;
        }
        ?>
        <div id="wpTrivia_form_box">
            <div id="poststuff">
                <div class="postbox">
                    <h3 class="hndle"><?php _e('Custom fields', 'wp-trivia'); ?></h3>

                    <div class="inside">
                        <table>
                            <tbody>
                            <?php foreach ($this->forms as $form) {
                                /* @var $form WpTrivia_Model_Form */
                                ?>
                                <tr>
                                    <td style="padding: 5px;"><?php echo esc_html($form->getFieldname()); ?></td>
                                    <td id="form_id_<?php echo $form->getFormId(); ?>">asdfffffffffffffffffffffsadfsdfa
                                        sf asd fas
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}