<?php

/**
 * @property WpTrivia_Model_Category[] categories
 * @property WpTrivia_Model_Quiz quiz
 * @property WpTrivia_Model_Template[] templates
 * @property WpTrivia_Model_Question question
 * @property string header
 * @property array answerData
 */
class WpTrivia_View_QuestionEdit extends WpTrivia_View_View
{
    public function show()
    {

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');

        ?>
        <div class="wrap wpTrivia_questionEdit">
            <h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>
            <!-- <form action="admin.php?page=wpTrivia&module=question&action=show&quiz_id=<?php echo $this->quiz->getId(); ?>" method="POST"> -->
            <form
                action="admin.php?page=wpTrivia&module=question&action=addEdit&quiz_id=<?php echo $this->quiz->getId(); ?>&questionId=<?php echo $this->question->getId(); ?>"
                method="POST">
                <a style="float: left;" class="button-secondary"
                   href="admin.php?page=wpTrivia&module=question&action=show&quiz_id=<?php echo $this->quiz->getId(); ?>"><?php _e('back to overview',
                        'wp-trivia'); ?></a>

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
                <!-- <input type="hidden" value="edit" name="hidden_action">
		<input type="hidden" value="<?php echo $this->question->getId(); ?>" name="questionId">-->
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Title', 'wp-trivia'); ?><?php _e('(optional)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('The title is used for overview, it is not visible in quiz. If you leave the title field empty, a title will be generated.',
                                    'wp-trivia'); ?>
                            </p>
                            <input name="title" class="regular-text" value="<?php echo $this->question->getTitle(); ?>"
                                   type="text">
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Points', 'wp-trivia'); ?><?php _e('(required)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <div>
                                <p class="description">
                                    <?php _e('Points for this question (Standard is 1 point)', 'wp-trivia'); ?>
                                </p>
                                <label>
                                    <input name="points" class="small-text"
                                           value="<?php echo $this->question->getPoints(); ?>" type="number"
                                           min="1"> <?php _e('Points', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('This points will be rewarded, only if the user closes the question correctly.',
                                        'wp-trivia'); ?>
                                </p>
                            </div>
                            <div style="margin-top: 10px;">
                                <label>
                                    <input name="answerPointsActivated" type="checkbox"
                                           value="1" <?php echo $this->question->isAnswerPointsActivated() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Different points for each answer', 'wp-trivia'); ?>
                                </label>

                                <p class="description">
                                    <?php _e('If you enable this option, you can enter different points for every answer.',
                                        'wp-trivia'); ?>
                                </p>
                            </div>
                            <div style="margin-top: 10px; display: none;" id="wpTrivia_showPointsBox">
                                <label>
                                    <input name="showPointsInBox" value="1"
                                           type="checkbox" <?php echo $this->question->isShowPointsInBox() ? 'checked="checked"' : '' ?>>
                                    <?php _e('Show reached points in the correct- and incorrect message?',
                                        'wp-trivia'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Category', 'wp-trivia'); ?><?php _e('(optional)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('You can assign classify category for a question. Categories are e.g. visible in statistics function.',
                                    'wp-trivia'); ?>
                            </p>

                            <p class="description">
                                <?php _e('You can manage categories in global settings.', 'wp-trivia'); ?>
                            </p>

                            <div>
                                <select name="category">
                                    <option value="-1">--- <?php _e('Create new category', 'wp-trivia'); ?>----
                                    </option>
                                    <option
                                        value="0" <?php echo $this->question->getCategoryId() == 0 ? 'selected="selected"' : ''; ?>>
                                        --- <?php _e('No category', 'wp-trivia'); ?> ---
                                    </option>
                                    <?php
                                    foreach ($this->categories as $cat) {
                                        echo '<option ' . ($this->question->getCategoryId() == $cat->getCategoryId() ? 'selected="selected"' : '') . ' value="' . $cat->getCategoryId() . '">' . $cat->getCategoryName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div style="display: none;" id="categoryAddBox">
                                <h4><?php _e('Create new category', 'wp-trivia'); ?></h4>
                                <input type="text" name="categoryAdd" value="">
                                <input type="button" class="button-secondary" name="" id="categoryAddBtn"
                                       value="<?php _e('Create', 'wp-trivia'); ?>">
                            </div>
                            <div id="categoryMsgBox"
                                 style="display:none; padding: 5px; border: 1px solid rgb(160, 160, 160); background-color: rgb(255, 255, 168); font-weight: bold; margin: 5px; ">
                                Kategorie gespeichert
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Question', 'wp-trivia'); ?><?php _e('(required)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <?php
                            wp_editor($this->question->getQuestion(), "question", array('textarea_rows' => 5));
                            ?>
                        </div>
                    </div>
                    <div class="postbox"
                         style="<?php echo $this->quiz->isHideAnswerMessageBox() ? '' : 'display: none;'; ?>">
                        <h3 class="hndle"><?php _e('Message with the correct / incorrect answer',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <?php _e('Deactivated in quiz settings.', 'wp-trivia'); ?>
                        </div>
                    </div>
                    <div style="<?php echo $this->quiz->isHideAnswerMessageBox() ? 'display: none;' : ''; ?>">
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Message with the correct answer',
                                    'wp-trivia'); ?><?php _e('(optional)', 'wp-trivia'); ?></h3>

                            <div class="inside">
                                <p class="description">
                                    <?php _e('This text will be visible if answered correctly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.',
                                        'wp-trivia'); ?>
                                </p>

                                <div style="padding-top: 10px; padding-bottom: 10px;">
                                    <label for="wpTrivia_correctSameText">
                                        <?php _e('Same text for correct- and incorrect-message?', 'wp-trivia'); ?>
                                        <input type="checkbox" name="correctSameText" id="wpTrivia_correctSameText"
                                               value="1" <?php echo $this->question->isCorrectSameText() ? 'checked="checked"' : '' ?>>
                                    </label>
                                </div>
                                <?php
                                wp_editor($this->question->getCorrectMsg(), "correctMsg", array('textarea_rows' => 3));
                                ?>
                            </div>
                        </div>
                        <div class="postbox" id="wpTrivia_incorrectMassageBox">
                            <h3 class="hndle"><?php _e('Message with the incorrect answer',
                                    'wp-trivia'); ?><?php _e('(optional)', 'wp-trivia'); ?></h3>

                            <div class="inside">
                                <p class="description">
                                    <?php _e('This text will be visible if answered incorrectly. It can be used as explanation for complex questions. The message "Right" or "Wrong" is always displayed automatically.',
                                        'wp-trivia'); ?>
                                </p>
                                <?php
                                wp_editor($this->question->getIncorrectMsg(), "incorrectMsg",
                                    array('textarea_rows' => 3));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Hint', 'wp-trivia'); ?><?php _e('(optional)',
                                'wp-trivia'); ?></h3>

                        <div class="inside">
                            <p class="description">
                                <?php _e('Here you can enter solution hint.', 'wp-trivia'); ?>
                            </p>

                            <div style="padding-top: 10px; padding-bottom: 10px;">
                                <label for="wpTrivia_tip">
                                    <?php _e('Activate hint for this question?', 'wp-trivia'); ?>
                                    <input type="checkbox" name="tipEnabled" id="wpTrivia_tip"
                                           value="1" <?php echo $this->question->isTipEnabled() ? 'checked="checked"' : '' ?>>
                                </label>
                            </div>
                            <div id="wpTrivia_tipBox">
                                <?php
                                wp_editor($this->question->getTipMsg(), 'tipMsg', array('textarea_rows' => 3));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Answer type', 'wp-trivia'); ?></h3>

                        <div class="inside">
                            <?php
                            $type = $this->question->getAnswerType();
                            $type = $type === null ? 'single' : $type;
                            ?>
                            <label style="padding-right: 10px;">
                                <input type="radio" name="answerType"
                                       value="single" <?php echo ($type === 'single') ? 'checked="checked"' : ''; ?>>
                                <?php _e('Single choice', 'wp-trivia'); ?>
                            </label>
                            <label style="padding-right: 10px;">
                                <input type="radio" name="answerType"
                                       value="multiple" <?php echo ($type === 'multiple') ? 'checked="checked"' : ''; ?>>
                                <?php _e('Multiple choice', 'wp-trivia'); ?>
                            </label>
                            <label style="padding-right: 10px;">
                                <input type="radio" name="answerType"
                                       value="free_answer" <?php echo ($type === 'free_answer') ? 'checked="checked"' : ''; ?>>
                                <?php _e('"Free" choice', 'wp-trivia'); ?>
                            </label>
                            <label style="padding-right: 10px;">
                                <input type="radio" name="answerType"
                                       value="sort_answer" <?php echo ($type === 'sort_answer') ? 'checked="checked"' : ''; ?>>
                                <?php _e('"Sorting" choice', 'wp-trivia'); ?>
                            </label>
                        </div>
                    </div>
                    <?php $this->singleChoiceOptions(); ?>
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Answers', 'wp-trivia'); ?><?php _e('(required)',
                                'wp-trivia'); ?></h3>

                        <div class="inside answer_felder">
                            <div class="free_answer">
                                <?php $this->freeChoice($this->answerData['free_answer']); ?>
                            </div>
                            <div class="sort_answer">
                                <p class="description">
                                    <?php _e('Please sort the answers in right order with the "Move" - Button. The answers will be displayed randomly.',
                                        'wp-trivia'); ?>
                                </p>
                                <ul class="answerList">
                                    <?php $this->sortingChoice($this->answerData['sort_answer']); ?>
                                </ul>
                                <input type="button" class="button-primary addAnswer"
                                       value="<?php _e('Add new answer', 'wp-trivia'); ?>">
                            </div>
                            <div class="classic_answer">
                                <ul class="answerList">
                                    <?php $this->singleMultiCoice($this->answerData['classic_answer']); ?>
                                </ul>
                                <input type="button" class="button-primary addAnswer"
                                       value="<?php _e('Add new answer', 'wp-trivia'); ?>">
                            </div>
                        </div>
                    </div>

                    <div style="float: left;">
                        <input type="submit" name="submit" id="saveQuestion" class="button-primary"
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

    /**
     * @param WpTrivia_Model_AnswerTypes[] $data
     */
    private function singleMultiCoice($data)
    {
        foreach ($data as $d) {
            ?>

            <li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;" id="TEST">
                <table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                    <tr>
                        <th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php _e('Options',
                                'wp-trivia'); ?></th>
                        <th style="padding: 5px;"><?php _e('Answer', 'wp-trivia'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
                            <div>
                                <label>
                                    <input type="checkbox" class="wpTrivia_classCorrect wpTrivia_checkbox"
                                           name="answerData[][correct]"
                                           value="1" <?php $this->checked($d->isCorrect()); ?>>
                                    <?php _e('Correct', 'wp-trivia'); ?>
                                </label>
                            </div>
                            <div style="padding-top: 5px;">
                                <label>
                                    <input type="checkbox" class="wpTrivia_checkbox" name="answerData[][html]"
                                           value="1" <?php $this->checked($d->isHtml()); ?>>
                                    <?php _e('Allow HTML', 'wp-trivia'); ?>
                                </label>
                            </div>
                            <div style="padding-top: 5px;" class="wpTrivia_answerPoints">
                                <label>
                                    <input type="number" min="0" class="small-text wpTrivia_points"
                                           name="answerData[][points]" value="<?php echo $d->getPoints(); ?>">
                                    <?php _e('Points', 'wp-trivia'); ?>
                                </label>
                            </div>
                        </td>
                        <td style="padding: 5px; vertical-align: top;">
                            <textarea rows="2" cols="50" class="large-text wpTrivia_text" name="answerData[][answer]"
                                      style="resize:vertical;"><?php echo $d->getAnswer(); ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <input type="button" name="submit" class="button-primary deleteAnswer"
                       value="<?php _e('Delete answer', 'wp-trivia'); ?>">
                <input type="button" class="button-secondary addMedia" value="<?php _e('Add Media'); ?>">
                <a href="#" class="button-secondary wpTrivia_move" style="cursor: move;"><?php _e('Move',
                        'wp-trivia'); ?></a>

            </li>

            <?php
        }
    }

    /**
     * @param WpTrivia_Model_AnswerTypes[] $data
     */
    private function sortingChoice($data)
    {
        foreach ($data as $d) {
            ?>
            <li style="border-bottom:1px dotted #ccc; padding-bottom: 5px; background-color: whiteSmoke;">
                <table style="width: 100%;border: 1px solid #9E9E9E;border-collapse: collapse;margin-bottom: 20px;">
                    <thead>
                    <tr>
                        <th width="160px" style=" border-right: 1px solid #9E9E9E; padding: 5px; "><?php _e('Options',
                                'wp-trivia'); ?></th>
                        <th style="padding: 5px;"><?php _e('Answer', 'wp-trivia'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="border-right: 1px solid #9E9E9E; padding: 5px; vertical-align: top;">
                            <div>
                                <label>
                                    <input type="checkbox" class="wpTrivia_checkbox" name="answerData[][html]"
                                           value="1" <?php $this->checked($d->isHtml()); ?>>
                                    <?php _e('Allow HTML', 'wp-trivia'); ?>
                                </label>
                            </div>
                            <div style="padding-top: 5px;" class="wpTrivia_answerPoints">
                                <label>
                                    <input type="number" min="0" class="small-text wpTrivia_points"
                                           name="answerData[][points]" value="<?php echo $d->getPoints(); ?>">
                                    <?php _e('Points', 'wp-trivia'); ?>
                                </label>
                            </div>
                        </td>
                        <td style="padding: 5px; vertical-align: top;">
                            <textarea rows="2" cols="100" class="large-text wpTrivia_text" name="answerData[][answer]"
                                      style="resize:vertical;"><?php echo $d->getAnswer(); ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <input type="button" name="submit" class="button-primary deleteAnswer"
                       value="<?php _e('Delete answer', 'wp-trivia'); ?>">
                <input type="button" class="button-secondary addMedia" value="<?php _e('Add Media'); ?>">
                <a href="#" class="button-secondary wpTrivia_move" style="cursor: move;"><?php _e('Move',
                        'wp-trivia'); ?></a>
            </li>
            <?php
        }
    }

    /**
     * @param WpTrivia_Model_AnswerTypes[] $data
     */
    private function freeChoice($data)
    {
        $single = $data[0];
        ?>
        <div class="answerList">
            <p class="description">
                <?php _e('correct answers (one per line) (answers will be converted to lower case)', 'wp-trivia'); ?>
            </p>

            <p style="border-bottom:1px dotted #ccc;">
                <textarea rows="6" cols="100" class="large-text"
                          name="answerData[][answer]"><?php echo $single->getAnswer(); ?></textarea>
            </p>
        </div>
        <?php
    }

    private function singleChoiceOptions()
    {
        ?>
        <div class="postbox" id="singleChoiceOptions">
            <h3 class="hndle"><?php _e('Single choice options', 'wp-trivia'); ?></h3>

            <div class="inside">
                <p class="description">
                    <?php _e('If "Different points for each answer" is activated, you can activate a special mode.<br> This changes the calculation of the points',
                        'wp-trivia'); ?>
                </p>
                <label>
                    <input type="checkbox" name="answerPointsDiffModusActivated"
                           value="1" <?php $this->checked($this->question->isAnswerPointsDiffModusActivated()); ?>>
                    <?php _e('Different points - modus 2 activate', 'wp-trivia'); ?>
                </label>
                <br><br>

                <p class="description">
                    <?php _e('Disables the distinction between correct and incorrect.', 'wp-trivia'); ?><br>
                </p>
                <label>
                    <input type="checkbox" name=disableCorrect
                           value="1" <?php $this->checked($this->question->isDisableCorrect()); ?>>
                    <?php _e('disable correct and incorrent', 'wp-trivia'); ?>
                </label>

                <div style="padding-top: 20px;">
                    <a href="#" id="clickPointDia"><?php _e('Explanation of points calculation', 'wp-trivia'); ?></a>
                    <?php $this->answerPointDia(); ?>
                </div>
            </div>
        </div>

        <?php
    }

    private function answerPointDia()
    {
        ?>
        <style>
            .pointDia td {
                border: 1px solid #9E9E9E;
                padding: 8px;
            }
        </style>
        <table style="border-collapse: collapse; display: none; margin-top: 10px;" class="pointDia">
            <tr>
                <th>
                    <?php _e('"Different points for each answer" enabled'); ?>
                    <br>
                    <?php _e('"Different points - mode 2" disable', 'wp-trivia'); ?>
                </th>
                <th>
                    <?php _e('"Different points for each answer" enabled'); ?>
                    <br>
                    <?php _e('"Different points - mode 2" enabled', 'wp-trivia'); ?>
                </th>
            </tr>
            <tr>
                <td>
                    <?php
                    echo nl2br('Question - Single Choice - 3 Answers - Diff points mode

			A=3 Points [correct]
			B=2 Points [incorrect]
			C=1 Point [incorrect]

			= 6 Points
			'); ?>

                </td>
                <td>
                    <?php
                    echo nl2br('Question - Single Choice - 3 Answers - Modus 2

			A=3 Points [correct]
			B=2 Points [incorrect]
			C=1 Point [incorrect]

			= 3 Points
			'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    echo nl2br('~~~ User 1: ~~~

			A=checked
			B=unchecked
			C=unchecked

			Result:
			A=correct and checked (correct) = 3 Points
			B=incorrect and unchecked (correct) = 2 Points
			C=incorrect and unchecked (correct) = 1 Points

			= 6 / 6 Points 100%
			'); ?>

                </td>
                <td>
                    <?php
                    echo nl2br('~~~ User 1: ~~~

			A=checked
			B=unchecked
			C=unchecked

			Result:
			A=checked = 3 Points
			B=unchecked = 0 Points
			C=unchecked = 0 Points

			= 3 / 3 Points 100%'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    echo nl2br('~~~ User 2: ~~~

			A=unchecked
			B=checked
			C=unchecked

			Result:
			A=correct and unchecked (incorrect) = 0 Points
			B=incorrect and checked (incorrect) = 0 Points
			C=incorrect and uncecked (correct) = 1 Points

			= 1 / 6 Points 16.67%
			'); ?>

                </td>
                <td>
                    <?php
                    echo nl2br('~~~ User 2: ~~~

			A=unchecked
			B=checked
			C=unchecked

			Result:
			A=unchecked = 0 Points
			B=checked = 2 Points
			C=uncecked = 0 Points

			= 2 / 3 Points 66,67%'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    echo nl2br('~~~ User 3: ~~~

			A=unchecked
			B=unchecked
			C=checked

			Result:
			A=correct and unchecked (incorrect) = 0 Points
			B=incorrect and unchecked (correct) = 2 Points
			C=incorrect and checked (incorrect) = 0 Points

			= 2 / 6 Points 33.33%
			'); ?>

                </td>
                <td>
                    <?php
                    echo nl2br('~~~ User 3: ~~~

			A=unchecked
			B=unchecked
			C=checked

			Result:
			A=unchecked = 0 Points
			B=unchecked = 0 Points
			C=checked = 1 Points

			= 1 / 3 Points 33,33%'); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    echo nl2br('~~~ User 4: ~~~

			A=unchecked
			B=unchecked
			C=unchecked

			Result:
			A=correct and unchecked (incorrect) = 0 Points
			B=incorrect and unchecked (correct) = 2 Points
			C=incorrect and unchecked (correct) = 1 Points

			= 3 / 6 Points 50%
			'); ?>

                </td>
                <td>
                    <?php
                    echo nl2br('~~~ User 4: ~~~

			A=unchecked
			B=unchecked
			C=unchecked

			Result:
			A=unchecked = 0 Points
			B=unchecked = 0 Points
			C=unchecked = 0 Points

			= 0 / 3 Points 0%'); ?>
                </td>
            </tr>
        </table>
        <?php
    }
}
