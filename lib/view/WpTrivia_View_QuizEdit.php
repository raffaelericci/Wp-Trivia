<?php

/**
 * @property WpTrivia_Model_Form[] forms
 * @property WpTrivia_Model_Quiz quiz
 * @property array prerequisiteQuizList
 * @property WpTrivia_Model_Template[] templates
 * @property array quizList
 * @property bool captchaIsInstalled
 * @property string header
 */
class WpTrivia_View_QuizEdit extends WpTrivia_View_View
{
    public function show()
    {
        ?>
        <style>
            .wpTrivia_demoBox {
                position: relative;
            }
        </style>
        <div class="wrap wpTrivia_quizEdit">
            <h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>

            <form method="post"
                  action="admin.php?page=wpTrivia&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>">

                <input type="hidden" name="ajax_quiz_id" value="<?php echo $this->quiz->getId(); ?>">

                <a style="float: left;" class="button-secondary"
                   href="admin.php?page=wpTrivia"><?php _e('back to overview', 'wp-trivia'); ?></a>

                <div style="float: right;">
                    <select name="templateLoadId">
                        <?php
                        foreach ($this->templates as $template) {
                            echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" name="templateLoad" value="<?php _e('load template', 'wp-trivia'); ?>"
                           class="button-primary">
                </div>
                <div style="clear: both;"></div>
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Quiz title', 'wp-trivia'); ?><?php _e('(required)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <input name="name" id="wpTrivia_title" type="text" class="regular-text"
                                   value="<?php echo htmlspecialchars($this->quiz->getName(), ENT_QUOTES); ?>">
                        </div>
                    </div>

                    <?php do_action('wpTrivia_action_plugin_quizEdit', $this); ?>

                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Options', 'wp-trivia'); ?></h3>

                        <div class="inside">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Time limit', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Time limit', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label for="time_limit">
                                                <input type="number" min="0" class="small-text" id="time_limit"
                                                       value="<?php echo $this->quiz->getTimeLimit(); ?>"
                                                       name="timeLimit"> <?php _e('Seconds', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('0 = no limit', 'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Statistics', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Statistics', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label for="statistics_on">
                                                <input type="checkbox" id="statistics_on" value="1"
                                                       name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : ''; ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Statistics about right or wrong answers. Statistics will be saved by completed quiz, not after every question. The statistics is only visible over administration menu. (internal statistics)',
                                                    'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr id="statistics_ip_lock_tr" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Statistics IP-lock', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Statistics IP-lock', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label for="statistics_ip_lock">
                                                <input type="number" min="0" class="small-text" id="statistics_ip_lock"
                                                       value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 1440 : $this->quiz->getStatisticsIpLock(); ?>"
                                                       name="statisticsIpLock">
                                                <?php _e('in minutes (recommended 1440 minutes = 1 day)',
                                                    'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Protect the statistics from spam. Result will only be saved every X minutes from same IP. (0 = deactivated)',
                                                    'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Execute quiz only once', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>

                                            <legend class="screen-reader-text">
                                                <span><?php _e('Execute quiz only once', 'wp-trivia'); ?></span>
                                            </legend>

                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : '' ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you activate this option, the user can complete the quiz only once. Afterwards the quiz is blocked for this user.',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <div id="wpTrivia_quiz_run_once_type"
                                                 style="margin-bottom: 5px; display: none;">
                                                <?php _e('This option applies to:', 'wp-trivia');

                                                $quizRunOnceType = $this->quiz->getQuizRunOnceType();
                                                $quizRunOnceType = ($quizRunOnceType == 0) ? 1 : $quizRunOnceType;

                                                ?>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="1" <?php echo ($quizRunOnceType == 1) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('all users', 'wp-trivia'); ?>
                                                </label>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="2" <?php echo ($quizRunOnceType == 2) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('registered useres only', 'wp-trivia'); ?>
                                                </label>
                                                <label>
                                                    <input name="quizRunOnceType" type="radio"
                                                           value="3" <?php echo ($quizRunOnceType == 3) ? 'checked="checked"' : ''; ?>>
                                                    <?php _e('anonymous users only', 'wp-trivia'); ?>
                                                </label>

                                                <div id="wpTrivia_quiz_run_once_cookie" style="margin-top: 10px;">
                                                    <label>
                                                        <input type="checkbox" value="1"
                                                               name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : '' ?>>
                                                        <?php _e('user identification by cookie', 'wp-trivia'); ?>
                                                    </label>

                                                    <p class="description">
                                                        <?php _e('If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.',
                                                            'wp-trivia'); ?>
                                                    </p>
                                                </div>

                                                <div style="margin-top: 15px;">
                                                    <input class="button-secondary" type="button" name="resetQuizLock"
                                                           value="<?php _e('Reset the user identification',
                                                               'wp-trivia'); ?>">
                                                    <span id="resetLockMsg"
                                                          style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php _e('User identification has been reset.'); ?></span>

                                                    <p class="description">
                                                        <?php _e('Resets user identification for all users.',
                                                            'wp-trivia'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Show only specific number of questions', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Show only specific number of questions',
                                                        'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="showMaxQuestion" <?php echo $this->quiz->isShowMaxQuestion() ? 'checked="checked"' : '' ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, maximum number of displayed questions will be X from X questions. (The output of questions is random)',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <div id="wpTrivia_showMaxBox" style="display: none;">
                                                <label>
                                                    <?php _e('How many questions should be displayed simultaneously:',
                                                        'wp-trivia'); ?>
                                                    <input class="small-text" type="text" name="showMaxQuestionValue"
                                                           value="<?php echo $this->quiz->getShowMaxQuestionValue(); ?>">
                                                </label>
                                                <label>
                                                    <input type="checkbox" value="1"
                                                           name="showMaxQuestionPercent" <?php echo $this->quiz->isShowMaxQuestionPercent() ? 'checked="checked"' : '' ?>>
                                                    <?php _e('in percent', 'wp-trivia'); ?>
                                                </label>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Prerequisites', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Prerequisites', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="prerequisite" <?php $this->checked($this->quiz->isPrerequisite()); ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, you can choose quiz, which user have to finish before he can start this quiz.',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <p class="description">
                                                <?php _e('In all selected quizzes statistic function have to be active. If it is not it will be activated automatically.',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <div id="prerequisiteBox" style="display: none;">
                                                <table>
                                                    <tr>
                                                        <th style="width: 120px; padding: 0;"><?php _e('Quiz',
                                                                'wp-trivia'); ?></th>
                                                        <th style="padding: 0; width: 50px;"></th>
                                                        <th style="padding: 0; width: 400px;"><?php _e('Prerequisites (This quiz have to be finished)',
                                                                'wp-trivia'); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 0;">
                                                            <select multiple="multiple" size="8" style="width: 200px;"
                                                                    name="quizList">
                                                                <?php foreach ($this->quizList as $list) {
                                                                    if (in_array($list['id'],
                                                                        $this->prerequisiteQuizList)) {
                                                                        continue;
                                                                    }

                                                                    echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
                                                                } ?>
                                                            </select>
                                                        </td>
                                                        <td style="padding: 0; text-align: center;">
                                                            <div>
                                                                <input type="button" id="btnPrerequisiteAdd"
                                                                       value="&gt;&gt;">
                                                            </div>
                                                            <div>
                                                                <input type="button" id="btnPrerequisiteDelete"
                                                                       value="&lt;&lt;">
                                                            </div>
                                                        </td>
                                                        <td style="padding: 0;">
                                                            <select multiple="multiple" size="8" style="width: 200px"
                                                                    name="prerequisiteList[]">
                                                                <?php foreach ($this->quizList as $list) {
                                                                    if (!in_array($list['id'],
                                                                        $this->prerequisiteQuizList)
                                                                    ) {
                                                                        continue;
                                                                    }

                                                                    echo '<option value="' . $list['id'] . '">' . $list['name'] . '</option>';
                                                                } ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Question overview', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Question overview', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="showReviewQuestion" <?php $this->checked($this->quiz->isShowReviewQuestion()); ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('Add at the top of the quiz a question overview, which allows easy navigation. Additional questions can be marked "to review".',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <p class="description">
                                                <?php _e('Additional quiz overview will be displayed, before quiz is finished.',
                                                    'wp-trivia'); ?>
                                            </p>

                                            <div class="wpTrivia_demoBox">
                                                <?php _e('Question overview', 'wp-trivia'); ?>: <a
                                                    href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                                <div
                                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                    <img alt=""
                                                         src="<?php echo WPPROQUIZ_URL . '/img/questionOverview.png'; ?> ">
                                                </div>
                                            </div>
                                            <div class="wpTrivia_demoBox">
                                                <?php _e('Quiz-summary', 'wp-trivia'); ?>: <a
                                                    href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                                <div
                                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                                    <img alt=""
                                                         src="<?php echo WPPROQUIZ_URL . '/img/quizSummary.png'; ?> ">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr class="wpTrivia_reviewQuestionOptions" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Quiz-summary', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Quiz-summary', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="quizSummaryHide" <?php $this->checked($this->quiz->isQuizSummaryHide()); ?>>
                                                <?php _e('Deactivate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enalbe this option, no quiz overview will be displayed, before finishing quiz.',
                                                    'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr class="wpTrivia_reviewQuestionOptions" style="display: none;">
                                    <th scope="row">
                                        <?php _e('Skip question', 'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Skip question', 'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" value="1"
                                                       name="skipQuestionDisabled" <?php $this->checked($this->quiz->isSkipQuestionDisabled()); ?>>
                                                <?php _e('Deactivate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, user won\'t be able to skip question. (only in "Overview -> next" mode). User still will be able to navigate over "Question-Overview"',
                                                    'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <?php _e('Only registered users are allowed to start the quiz',
                                            'wp-trivia'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text">
                                                <span><?php _e('Only registered users are allowed to start the quiz',
                                                        'wp-trivia'); ?></span>
                                            </legend>
                                            <label>
                                                <input type="checkbox" name="startOnlyRegisteredUser"
                                                       value="1" <?php $this->checked($this->quiz->isStartOnlyRegisteredUser()); ?>>
                                                <?php _e('Activate', 'wp-trivia'); ?>
                                            </label>

                                            <p class="description">
                                                <?php _e('If you enable this option, only registered users allowed start the quiz.',
                                                    'wp-trivia'); ?>
                                            </p>
                                        </fieldset>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->questionOptions(); ?>
                    <?php $this->resultOptions(); ?>
                    <?php $this->leaderboardOptions(); ?>
                    <?php $this->form(); ?>
                    <?php $this->adminEmailOption(); ?>
                    <?php $this->userEmailOption(); ?>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Quiz description', 'wp-trivia'); ?><?php _e('(required)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('This text will be displayed before start of the quiz.', 'wp-trivia'); ?>
                            </p>
                            <?php
                            wp_editor($this->quiz->getText(), "text");
                            ?>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Results text', 'wp-trivia'); ?><?php _e('(optional)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('This text will be displayed at the end of the quiz (in results). (this text is optional)',
                                    'wp-trivia'); ?>
                            </p>

                            <div style="padding-top: 10px; padding-bottom: 10px;">
                                <label for="wpTrivia_resultGradeEnabled">
                                    <?php _e('Activate graduation', 'wp-trivia'); ?>
                                    <input type="checkbox" name="resultGradeEnabled" id="wpTrivia_resultGradeEnabled"
                                           value="1" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?>>
                                </label>
                            </div>
                            <div style="display: none;" id="resultGrade">
                                <div>
                                    <strong><?php _e('Hint:', 'wp-trivia'); ?></strong>
                                    <ul style="list-style-type: square; padding: 5px; margin-left: 20px; margin-top: 0;">
                                        <li><?php _e('Maximal 15 levels', 'wp-trivia'); ?></li>
                                        <li>
                                            <?php printf(__('Percentages refer to the total score of the quiz. (Current total %d points in %d questions.',
                                                'wp-trivia'),
                                                $this->quiz->fetchSumQuestionPoints(),
                                                $this->quiz->fetchCountQuestions()); ?>
                                        </li>
                                        <li><?php _e('Values can also be mixed up', 'wp-trivia'); ?></li>
                                        <li><?php _e('10,15% or 10.15% allowed (max. two digits after the decimal point)',
                                                'wp-trivia'); ?></li>
                                    </ul>

                                </div>
                                <div>
                                    <ul id="resultList">
                                        <?php
                                        $resultText = $this->quiz->getResultText();

                                        for ($i = 0; $i < 15; $i++) {

                                            if ($this->quiz->isResultGradeEnabled() && isset($resultText['text'][$i])) {
                                                ?>
                                                <li style="padding: 5px; border: 1px dotted;">
                                                    <div
                                                        style="margin-bottom: 5px;"><?php wp_editor($resultText['text'][$i],
                                                            'resultText_' . $i, array(
                                                                'textarea_rows' => 3,
                                                                'textarea_name' => 'resultTextGrade[text][]'
                                                            )); ?></div>
                                                    <div
                                                        style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
                                                        <?php _e('from:', 'wp-trivia'); ?> <input type="text"
                                                                                                    name="resultTextGrade[prozent][]"
                                                                                                    class="small-text"
                                                                                                    value="<?php echo $resultText['prozent'][$i] ?>"> <?php _e('percent',
                                                            'wp-trivia'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)',
                                                            'wp-trivia'), $resultText['prozent'][$i]); ?>
                                                        <input type="button" style="float: right;"
                                                               class="button-primary deleteResult"
                                                               value="<?php _e('Delete graduation', 'wp-trivia'); ?>">

                                                        <div style="clear: right;"></div>
                                                        <input type="hidden" value="1" name="resultTextGrade[activ][]">
                                                    </div>
                                                </li>

                                            <?php } else { ?>
                                                <li style="padding: 5px; border: 1px dotted; <?php echo $i ? 'display:none;' : '' ?>">
                                                    <div style="margin-bottom: 5px;"><?php wp_editor('',
                                                            'resultText_' . $i, array(
                                                                'textarea_rows' => 3,
                                                                'textarea_name' => 'resultTextGrade[text][]'
                                                            )); ?></div>
                                                    <div
                                                        style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
                                                        <?php _e('from:', 'wp-trivia'); ?> <input type="text"
                                                                                                    name="resultTextGrade[prozent][]"
                                                                                                    class="small-text"
                                                                                                    value="0"> <?php _e('percent',
                                                            'wp-trivia'); ?> <?php printf(__('(Will be displayed, when result-percent is >= <span class="resultProzent">%s</span>%%)',
                                                            'wp-trivia'), '0'); ?>
                                                        <input type="button" style="float: right;"
                                                               class="button-primary deleteResult"
                                                               value="<?php _e('Delete graduation', 'wp-trivia'); ?>">

                                                        <div style="clear: right;"></div>
                                                        <input type="hidden" value="<?php echo $i ? '0' : '1' ?>"
                                                               name="resultTextGrade[activ][]">
                                                    </div>
                                                </li>
                                            <?php }
                                        } ?>
                                    </ul>
                                    <input type="button" class="button-primary addResult"
                                           value="<?php _e('Add graduation', 'wp-trivia'); ?>">
                                </div>
                            </div>
                            <div id="resultNormal">
                                <?php

                                $resultText = is_array($resultText) ? '' : $resultText;
                                wp_editor($resultText, 'resultText', array('textarea_rows' => 10));
                                ?>
                            </div>

                            <h4><?php _e('Custom fields - Variables', 'wp-trivia'); ?></h4>
                            <ul class="formVariables"></ul>

                        </div>
                    </div>
                    <div style="float: left;">
                        <input type="submit" name="submit" class="button-primary" id="wpTrivia_save"
                               value="<?php _e('Save', 'wp-trivia'); ?>">
                    </div>
                    <div style="float: right;">
                        <input type="text" placeholder="<?php _e('template name', 'wp-trivia'); ?>"
                               class="regular-text" name="templateName" style="border: 1px solid rgb(255, 134, 134);">
                        <select name="templateSaveList">
                            <option value="0">=== <?php _e('Create new template', 'wp-trivia'); ?> ===</option>
                            <?php
                            foreach ($this->templates as $template) {
                                echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
                            }
                            ?>
                        </select>

                        <input type="submit" name="template" class="button-primary" id="wpTrivia_saveTemplate"
                               value="<?php _e('Save as template', 'wp-trivia'); ?>">
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </form>
        </div>
        <?php
    }

    private function resultOptions()
    {
        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Result-Options', 'wp-trivia'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Show average points', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Show average points', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('Statistics-function must be enabled.', 'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/averagePoints.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide correct questions - display', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide correct questions - display', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultCorrectQuestion"
                                           value="1" <?php $this->checked($this->quiz->isHideResultCorrectQuestion()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you select this option, no longer the number of correctly answered questions are displayed on the results page.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>

                            <div class="wpTrivia_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideCorrectQuestion.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide quiz time - display', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide quiz time - display', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultQuizTime"
                                           value="1" <?php $this->checked($this->quiz->isHideResultQuizTime()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the time for finishing the quiz won\'t be displayed on the results page anymore.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>

                            <div class="wpTrivia_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizTime.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide score - display', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide score - display', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="hideResultPoints"
                                           value="1" <?php $this->checked($this->quiz->isHideResultPoints()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, final score won\'t be displayed on the results page anymore.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>

                            <div class="wpTrivia_demoBox">
                                <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                <div
                                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                    <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizPoints.png'; ?> ">
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }

    private function questionOptions()
    {
        ?>

        <div class="postbox">
            <h3 class="hndle"><?php _e('Question-Options', 'wp-trivia'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Show points', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Show points', 'wp-trivia'); ?></span>
                                </legend>
                                <label for="show_points">
                                    <input type="checkbox" id="show_points" value="1"
                                           name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('Shows in quiz, how many points are reachable for respective question.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Number answers', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Number answers', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="numberedAnswer" <?php echo $this->quiz->isNumberedAnswer() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If this option is activated, all answers are numbered (only single and multiple choice)',
                                        'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/numbering.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide correct- and incorrect message', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide correct- and incorrect message', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, no correct- or incorrect message will be displayed.',
                                        'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideAnswerMessageBox.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Correct and incorrect answer mark', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Correct and incorrect answer mark', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Deactivate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, answers won\'t be color highlighted as correct or incorrect. ',
                                        'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/mark.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Force user to answer each question', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Force user to answer each question', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="forcingQuestionSolve" <?php $this->checked($this->quiz->isForcingQuestionSolve()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the user is forced to answer each question.',
                                        'wp-trivia'); ?> <br>
                                    <?php _e('If the option "Question overview" is activated, this notification will appear after end of the quiz, otherwise after each question.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide question position overview', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide question position overview', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideQuestionPositionOverview" <?php $this->checked($this->quiz->isHideQuestionPositionOverview()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the question position overview is hidden.',
                                        'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionPositionOverview.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Hide question numbering', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Hide question numbering', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" value="1"
                                           name="hideQuestionNumbering" <?php $this->checked($this->quiz->isHideQuestionNumbering()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, the question numbering is hidden.',
                                        'wp-trivia'); ?>
                                </p>

                                <div class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionNumbering.png'; ?> ">
                                    </div>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
    }

    private function leaderboardOptions()
    {
        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Leaderboard', 'wp-trivia'); ?><?php _e('(optional)', 'wp-trivia'); ?></h3>

            <div class="inside">
                <p>
                    <?php _e('The leaderboard allows users to enter results in public list and to share the result this way.',
                        'wp-trivia'); ?>
                </p>

                <p>
                    <?php _e('The leaderboard works independent from internal statistics function.', 'wp-trivia'); ?>
                </p>
                <table class="form-table">
                    <tbody id="toplistBox">
                    <tr>
                        <th scope="row">
                            <?php _e('Leaderboard', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toplistActivated"
                                       value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>>
                                <?php _e('Activate', 'wp-trivia'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Who can sign up to the list', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
                                <?php _e('all users', 'wp-trivia'); ?>
                            </label>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
                                <?php _e('registered useres only', 'wp-trivia'); ?>
                            </label>
                            <label>
                                <input name="toplistDataAddPermissions" type="radio"
                                       value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
                                <?php _e('anonymous users only', 'wp-trivia'); ?>
                            </label>

                            <p class="description">
                                <?php _e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)',
                                    'wp-trivia'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('insert automatically', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataAddAutomatic" type="checkbox"
                                       value="1" <?php $this->checked($this->quiz->isToplistDataAddAutomatic()); ?>>
                                <?php _e('Activate', 'wp-trivia'); ?>
                            </label>

                            <p class="description">
                                <?php _e('If you enable this option, logged in users will be automatically entered into leaderboard',
                                    'wp-trivia'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('display captcha', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toplistDataCaptcha"
                                       value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>>
                                <?php _e('Activate', 'wp-trivia'); ?>
                            </label>

                            <p class="description">
                                <?php _e('If you enable this option, additional captcha will be displayed for users who are not registered.',
                                    'wp-trivia'); ?>
                            </p>

                            <p class="description" style="color: red;">
                                <?php _e('This option requires additional plugin:', 'wp-trivia'); ?>
                                <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really
                                    Simple CAPTCHA</a>
                            </p>
                            <?php if ($this->captchaIsInstalled) { ?>
                                <p class="description" style="color: green;">
                                    <?php _e('Plugin has been detected.', 'wp-trivia'); ?>
                                </p>
                            <?php } else { ?>
                                <p class="description" style="color: red;">
                                    <?php _e('Plugin is not installed.', 'wp-trivia'); ?>
                                </p>
                            <?php } ?>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Sort list by', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="1" <?php echo ($this->quiz->getToplistDataSort() == 1) ? 'checked="checked"' : ''; ?>>
                                <?php _e('best user', 'wp-trivia'); ?>
                            </label>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="2" <?php echo ($this->quiz->getToplistDataSort() == 2) ? 'checked="checked"' : ''; ?>>
                                <?php _e('newest entry', 'wp-trivia'); ?>
                            </label>
                            <label>
                                <input name="toplistDataSort" type="radio"
                                       value="3" <?php echo ($this->quiz->getToplistDataSort() == 3) ? 'checked="checked"' : ''; ?>>
                                <?php _e('oldest entry', 'wp-trivia'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Users can apply multiple times', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <div>
                                <label>
                                    <input type="checkbox" name="toplistDataAddMultiple"
                                           value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>
                            </div>
                            <div id="toplistDataAddBlockBox" style="display: none;">
                                <label>
                                    <?php _e('User can apply after:', 'wp-trivia'); ?>
                                    <input type="number" min="0" class="small-text" name="toplistDataAddBlock"
                                           value="<?php echo $this->quiz->getToplistDataAddBlock(); ?>">
                                    <?php _e('minute', 'wp-trivia'); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('How many entries should be displayed', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <div>
                                <label>
                                    <input type="number" min="0" class="small-text" name="toplistDataShowLimit"
                                           value="<?php echo $this->quiz->getToplistDataShowLimit(); ?>">
                                    <?php _e('Entries', 'wp-trivia'); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Automatically display leaderboard in quiz result', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <div style="margin-top: 6px;">
                                <?php _e('Where should leaderboard be displayed:', 'wp-trivia'); ?>
                                <label style="margin-right: 5px; margin-left: 5px;">
                                    <input type="radio" name="toplistDataShowIn"
                                           value="0" <?php echo ($this->quiz->getToplistDataShowIn() == 0) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('don\'t display', 'wp-trivia'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="toplistDataShowIn"
                                           value="1" <?php echo ($this->quiz->getToplistDataShowIn() == 1) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('below the "result text"', 'wp-trivia'); ?>
                                </label>
									<span class="wpTrivia_demoBox" style="margin-right: 5px;">
										<a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>
										<span
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInResultText.png'; ?> ">
										</span>
									</span>
                                <label>
                                    <input type="radio" name="toplistDataShowIn"
                                           value="2" <?php echo ($this->quiz->getToplistDataShowIn() == 2) ? 'checked="checked"' : ''; ?>>
                                    <?php _e('in a button', 'wp-trivia'); ?>
                                </label>
									<span class="wpTrivia_demoBox">
										<a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>
										<span
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
											<img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInButton.png'; ?> ">
										</span>
									</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    private function form()
    {
        $forms = $this->forms;
        $index = 0;

        if (!count($forms)) {
            $forms = array(new WpTrivia_Model_Form(), new WpTrivia_Model_Form());
        } else {
            array_unshift($forms, new WpTrivia_Model_Form());
        }

        ?>
        <div class="postbox">
            <h3 class="hndle"><?php _e('Custom fields', 'wp-trivia'); ?></h3>

            <div class="inside">

                <p class="description">
                    <?php _e('You can create custom fields, e.g. to request the name or the e-mail address of the users.',
                        'wp-trivia'); ?>
                </p>

                <p class="description">
                    <?php _e('The statistic function have to be enabled.', 'wp-trivia'); ?>
                </p>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Custom fields enable', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Custom fields enable', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" id="formActivated" value="1"
                                           name="formActivated" <?php $this->checked($this->quiz->isFormActivated()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, custom fields are enabled.', 'wp-trivia'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Display position', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Display position', 'wp-trivia'); ?></span>
                                </legend>
                                <?php _e('Where should the fileds be displayed:', 'wp-trivia'); ?>
                                <label>
                                    <input type="radio"
                                           value="<?php echo WpTrivia_Model_Quiz::QUIZ_FORM_POSITION_START; ?>"
                                           name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                                        WpTrivia_Model_Quiz::QUIZ_FORM_POSITION_START); ?>>
                                    <?php _e('On the quiz startpage', 'wp-trivia'); ?>

                                    <div style="display: inline-block;" class="wpTrivia_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsFront.png'; ?> ">
                                        </div>
                                    </div>

                                </label>
                                <label>
                                    <input type="radio"
                                           value="<?php echo WpTrivia_Model_Quiz::QUIZ_FORM_POSITION_END; ?>"
                                           name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                                        WpTrivia_Model_Quiz::QUIZ_FORM_POSITION_END); ?> >
                                    <?php _e('At the end of the quiz (before the quiz result)', 'wp-trivia'); ?>

                                    <div style="display: inline-block;" class="wpTrivia_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd1.png'; ?> ">
                                        </div>
                                    </div>

                                    <div style="display: inline-block;" class="wpTrivia_demoBox">
                                        <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                        <div
                                            style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                            <img alt=""
                                                 src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd2.png'; ?> ">
                                        </div>
                                    </div>

                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div style="margin-top: 10px; padding: 10px; border: 1px solid #C2C2C2;">
                    <table style=" width: 100%; text-align: left; " id="form_table">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th><?php _e('Field name', 'wp-trivia'); ?></th>
                            <th><?php _e('Type', 'wp-trivia'); ?></th>
                            <th><?php _e('Required?', 'wp-trivia'); ?></th>
                            <th>
                                <?php _e('Show in statistic table?', 'wp-trivia'); ?>
                                <div style="display: inline-block;" class="wpTrivia_demoBox">
                                    <a href="#"><?php _e('Demo', 'wp-trivia'); ?></a>

                                    <div
                                        style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                                        <img alt=""
                                             src="<?php echo WPPROQUIZ_URL . '/img/formStatisticOverview.png'; ?> ">
                                    </div>
                                </div>
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($forms as $form) {
                            $checkType = $this->selectedArray($form->getType(), array(
                                WpTrivia_Model_Form::FORM_TYPE_TEXT,
                                WpTrivia_Model_Form::FORM_TYPE_TEXTAREA,
                                WpTrivia_Model_Form::FORM_TYPE_CHECKBOX,
                                WpTrivia_Model_Form::FORM_TYPE_SELECT,
                                WpTrivia_Model_Form::FORM_TYPE_RADIO,
                                WpTrivia_Model_Form::FORM_TYPE_NUMBER,
                                WpTrivia_Model_Form::FORM_TYPE_EMAIL,
                                WpTrivia_Model_Form::FORM_TYPE_YES_NO,
                                WpTrivia_Model_Form::FORM_TYPE_DATE
                            ));
                            ?>
                            <tr <?php echo $index++ == 0 ? 'style="display: none;"' : '' ?>>
                                <td>
                                    <?php echo $index - 2; ?>
                                </td>
                                <td>
                                    <input type="text" name="form[][fieldname]"
                                           value="<?php echo esc_attr($form->getFieldname()); ?>"
                                           class="regular-text formFieldName"/>
                                </td>
                                <td style="position: relative;">
                                    <select name="form[][type]">
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_TEXT; ?>" <?php echo $checkType[0]; ?>><?php _e('Text',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_TEXTAREA; ?>" <?php echo $checkType[1]; ?>><?php _e('Textarea',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_CHECKBOX; ?>" <?php echo $checkType[2]; ?>><?php _e('Checkbox',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_SELECT; ?>" <?php echo $checkType[3]; ?>><?php _e('Drop-Down menu',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_RADIO; ?>" <?php echo $checkType[4]; ?>><?php _e('Radio',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_NUMBER; ?>" <?php echo $checkType[5]; ?>><?php _e('Number',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_EMAIL; ?>" <?php echo $checkType[6]; ?>><?php _e('Email',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_YES_NO; ?>" <?php echo $checkType[7]; ?>><?php _e('Yes/No',
                                                'wp-trivia'); ?></option>
                                        <option
                                            value="<?php echo WpTrivia_Model_Form::FORM_TYPE_DATE; ?>" <?php echo $checkType[8]; ?>><?php _e('Date',
                                                'wp-trivia'); ?></option>
                                    </select>

                                    <a href="#" class="editDropDown"><?php _e('Edit list', 'wp-trivia'); ?></a>

                                    <div class="dropDownEditBox"
                                         style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
                                        <h4><?php _e('One entry per line', 'wp-trivia'); ?></h4>

                                        <div>
                                            <textarea rows="5" cols="50"
                                                      name="form[][data]"><?php echo $form->getData() === null ? '' : esc_textarea(implode("\n",
                                                    $form->getData())); ?></textarea>
                                        </div>

                                        <input type="button" value="<?php _e('OK', 'wp-trivia'); ?>"
                                               class="button-primary">
                                    </div>
                                </td>
                                <td>
                                    <input type="checkbox" name="form[][required]"
                                           value="1" <?php $this->checked($form->isRequired()); ?>>
                                </td>
                                <td>
                                    <input type="checkbox" name="form[][show_in_statistic]"
                                           value="1" <?php $this->checked($form->isShowInStatistic()); ?>>
                                </td>
                                <td>
                                    <input type="button" name="form_delete"
                                           value="<?php _e('Delete', 'wp-trivia'); ?>" class="button-secondary">
                                    <a class="form_move button-secondary" href="#" style="cursor:move;"><?php _e('Move',
                                            'wp-trivia'); ?></a>

                                    <input type="hidden" name="form[][form_id]"
                                           value="<?php echo $form->getFormId(); ?>">
                                    <input type="hidden" name="form[][form_delete]" value="0">
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                    <div style="margin-top: 10px;">
                        <input type="button" name="form_add" id="form_add"
                               value="<?php _e('Add field', 'wp-trivia'); ?>" class="button-secondary">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function adminEmailOption()
    {
        /** @var WpTrivia_Model_Email * */
        $email = $this->quiz->getAdminEmail();
        $email = $email === null ? WpTrivia_Model_Email::getDefault(true) : $email;
        ?>
        <div class="postbox" id="adminEmailSettings">
            <h3 class="hndle"><?php _e('Admin e-mail settings', 'wp-trivia'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Admin e-mail notification', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Admin e-mail notification', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_NONE; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_NONE); ?>>
                                    <?php _e('Deactivate', 'wp-trivia'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER); ?>>
                                    <?php _e('for registered users only', 'wp-trivia'); ?>
                                </label>
                                <label>
                                    <input type="radio" name="emailNotification"
                                           value="<?php echo WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_ALL; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
                                        WpTrivia_Model_Quiz::QUIZ_EMAIL_NOTE_ALL); ?>>
                                    <?php _e('for all users', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, you will be informed if a user completes this quiz.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('To:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[to]" value="<?php echo $email->getTo(); ?>"
                                       class="regular-text">
                            </label>

                            <p class="description">
                                <?php _e('Separate multiple email addresses with a comma, e.g. wp@test.com, test@test.com',
                                    'wp-trivia'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('From:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[from]" value="<?php echo $email->getFrom(); ?>"
                                       class="regular-text">
                            </label>
                            <!-- 								<p class="description"> -->
                            <?php //_e('Server-Adresse empfohlen, z.B. info@YOUR-PAGE.com', 'wp-trivia');
                            ?>
                            <!-- 								</p> -->
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Subject:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="adminEmail[subject]"
                                       value="<?php echo $email->getSubject(); ?>" class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('HTML', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="adminEmail[html]"
                                       value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
                                    'wp-trivia'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Message body:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <?php
                            wp_editor($email->getMessage(), 'adminEmailEditor',
                                array('textarea_rows' => 20, 'textarea_name' => 'adminEmail[message]'));
                            ?>

                            <div style="padding-top: 10px;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="padding: 0;">
                                            <?php _e('Allowed variables', 'wp-trivia'); ?>
                                        </th>
                                        <th style="padding: 0;">
                                            <?php _e('Custom fields - Variables', 'wp-trivia'); ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <ul>
                                                <li><span>$userId</span> - <?php _e('User-ID', 'wp-trivia'); ?></li>
                                                <li><span>$username</span> - <?php _e('Username', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$result</span> - <?php _e('Result in precent', 'wp-trivia'); ?></li>
                                                <li><span>$points</span> - <?php _e('Reached points', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$ip</span> - <?php _e('IP-address of the user', 'wp-trivia'); ?></li>
                                            </ul>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <ul class="formVariables"></ul>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <?php

    }

    private function userEmailOption()
    {
        /** @var WpTrivia_Model_Email * */
        $email = $this->quiz->getUserEmail();
        $email = $email === null ? WpTrivia_Model_Email::getDefault(false) : $email;
        $to = $email->getTo();
        ?>
        <div class="postbox" id="userEmailSettings">
            <h3 class="hndle"><?php _e('User e-mail settings', 'wp-trivia'); ?></h3>

            <div class="inside">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('User e-mail notification', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('User e-mail notification', 'wp-trivia'); ?></span>
                                </legend>
                                <label>
                                    <input type="checkbox" name="userEmailNotification"
                                           value="1" <?php $this->checked($this->quiz->isUserEmailNotification()); ?>>
                                    <?php _e('Activate', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, an email is sent with his quiz result to the user.',
                                        'wp-trivia'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('To:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="userEmail[toUser]"
                                       value="1" <?php $this->checked($email->isToUser()); ?>>
                                <?php _e('User Email-Address (only registered users)', 'wp-trivia'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="userEmail[toForm]"
                                       value="1" <?php $this->checked($email->isToForm()); ?>>
                                <?php _e('Custom fields', 'wp-trivia'); ?> :
                                <select name="userEmail[to]" class="emailFormVariables"
                                        data-default="<?php echo empty($to) && $to != 0 ? -1 : $email->getTo(); ?>"></select>
                                <?php _e('(Type Email)', 'wp-trivia'); ?>
                            </label>

                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('From:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="userEmail[from]" value="<?php echo $email->getFrom(); ?>"
                                       class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Subject:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="text" name="userEmail[subject]" value="<?php echo $email->getSubject(); ?>"
                                       class="regular-text">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('HTML', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="userEmail[html]"
                                       value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
                                    'wp-trivia'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e('Message body:', 'wp-trivia'); ?>
                        </th>
                        <td>
                            <?php
                            wp_editor($email->getMessage(), 'userEmailEditor',
                                array('textarea_rows' => 20, 'textarea_name' => 'userEmail[message]'));
                            ?>

                            <div style="padding-top: 10px;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="padding: 0;">
                                            <?php _e('Allowed variables', 'wp-trivia'); ?>
                                        </th>
                                        <th style="padding: 0;">
                                            <?php _e('Custom fields - Variables', 'wp-trivia'); ?>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            <ul>
                                                <li><span>$userId</span> - <?php _e('User-ID', 'wp-trivia'); ?></li>
                                                <li><span>$username</span> - <?php _e('Username', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$result</span> - <?php _e('Result in precent', 'wp-trivia'); ?></li>
                                                <li><span>$points</span> - <?php _e('Reached points', 'wp-trivia'); ?>
                                                </li>
                                                <li><span>$ip</span> - <?php _e('IP-address of the user', 'wp-trivia'); ?></li>
                                            </ul>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <ul class="formVariables"></ul>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
