<?php

class WpTrivia_View_StyleManager extends WpTrivia_View_View
{

    public function show()
    {

        ?>


        <div class="wrap">
            <h2 style="margin-bottom: 10px;"><?php echo $this->header; ?></h2>
            <a class="button-secondary" href="admin.php?page=wpTrivia"><?php _e('back to overview',
                    'wp-trivia'); ?></a>

            <form method="post">
                <div id="poststuff">
                    <div class="postbox">
                        <h3 class="hndle"><?php _e('Front', 'wp-trivia'); ?></h3>

                        <div class="wrap wpTrivia_quizEdit">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <td width="50%">


                                    </td>
                                    <td>


                                        <div style="" class="wpTrivia_quiz">
                                            <ol class="wpTrivia_list">


                                                <li class="wpTrivia_listItem" style="display: list-item;">
                                                    <div class="wpTrivia_progress_header">
                                                        Frage <span>4</span> von <span>7</span>
                                                        <span style="float:right;">1 Punkte</span>

                                                        <div style="clear: right;"></div>
                                                    </div>
                                                    <h3><span>4</span>. Frage</h3>

                                                    <div class="wpTrivia_question" style="margin: 10px 0px 0px 0px;">
                                                        <div class="wpTrivia_question_text">
                                                            <p>Frage3</p>
                                                        </div>
                                                        <ul class="wpTrivia_questionList">


                                                            <li class="wpTrivia_questionListItem" style="">
                                                                <label>
                                                                    <input class="wpTrivia_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="2"> Test </label>
                                                            </li>
                                                            <li class="wpTrivia_questionListItem" style="">
                                                                <label>
                                                                    <input class="wpTrivia_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="1"> Test </label>
                                                            </li>
                                                            <li class="wpTrivia_questionListItem" style="">
                                                                <label>
                                                                    <input class="wpTrivia_questionInput"
                                                                           type="checkbox" name="question_5_26"
                                                                           value="3"> Test </label>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="wpTrivia_response" style="">
                                                        <div style="" class="wpTrivia_correct">
						<span>
							Korrekt						</span>

                                                            <p>
                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="wpTrivia_tipp" style="display: none;">
                                                        <h3>Tipp</h3>
                                                    </div>
                                                    <input type="button" name="check" value="Prüfen"
                                                           class="wpTrivia_QuestionButton"
                                                           style="float: left !important; margin-right: 10px !important;">
                                                    <input type="button" name="back" value="Zurück"
                                                           class="wpTrivia_QuestionButton"
                                                           style="float: left !important; margin-right: 10px !important; ">
                                                    <input type="button" name="next" value="Nächste Frage"
                                                           class="wpTrivia_QuestionButton" style="float: right; ">

                                                    <div style="clear: both;"></div>
                                                </li>
                                            </ol>
                                        </div>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <?php
    }
}