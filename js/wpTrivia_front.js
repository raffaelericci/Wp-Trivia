/**
 * TODO
 * Work in progress: frontend js rewrite
 *  - Think about where and how to show error messages
 *  - Implement other question types
 */

(function ($) {

    $(function() {
        var r = window.wpTriviaInitList || [];
        for(var i = 0; i < r.length; i++) {
            $(r[i].id).wpTriviaFront(r[i].init);
        }
    });

    /**
     * @memberOf $
     */
    $.wpTriviaFront = function (element, options) {

        var wtf = this;
        wtf._e = $(element);
        wtf.started = false;
        wtf.quizId = options.quizId;
        wtf.currentQuestion = null;
        wtf.currentQuestionId = 0;
        wtf.currentAnswersList = null;
        wtf.currentAnswerType = '';
        wtf.inputTypes = {
            'singleMulti': '.wpTrivia_answerInput_singleMulti'
        };
        wtf.globalElements = {};

        /**
         * Updates elements references
         */
        wtf.updateRefs = function(questionIdx) {
            wtf.globalElements = {
                questionList: wtf._e.find('.wpTrivia_list'),
                listItems: wtf._e.find('.wpTrivia_list .wpTrivia_listItem'),
                check: wtf._e.find('input.check'),
                prev: wtf._e.find('.wpTrivia_button.prev'),
                next: wtf._e.find('.wpTrivia_button.next'),
                quizName: $('#quizName'),
                questionCount: $('#questionCount'),
                quizProgressBar: wtf._e.find('.wpTrivia_quiz_progress'),
                /*
                results: wtf._e.find('.wpTrivia_results'),
                timelimit: wtf._e.find('.wpTrivia_time_limit'),
                toplistShowInButton: wtf._e.find('.wpTrivia_toplistShowInButton')
                */
            };
            wtf.currentQuestion = wtf.globalElements.listItems.eq(questionIdx);
            wtf.currentAnswersList = wtf.currentQuestion.find('.wpTrivia_answersList');
            wtf.currentQuestionId = wtf.currentAnswersList.data('question_id');
            wtf.currentAnswerType = wtf.currentAnswersList.data('type');
        };

        /**
         * Sets the handlers for the answers selection.
         * The handler namespace is the current question id
         */
        wtf.setAnswersHandlers = function() {
            switch(wtf.currentAnswerType) {
                case 'single':
                case 'multiple':
                    wtf.currentAnswersList.find(wtf.inputTypes.singleMulti).on("click." + wtf.currentQuestionId, function() {
                        if (wtf.currentAnswerType == 'single') {
                            wtf.currentAnswersList.find(wtf.inputTypes.singleMulti).removeClass('selected');
                            $(this).addClass('selected');
                        } else {
                            if ($(this).hasClass('selected')) {
                                $(this).removeClass('selected');
                            } else {
                                $(this).addClass('selected');
                            }
                        }
                    });
                    break;
                // TODO - Implement other question types
            }
        };

        /**
         * Unsets the handlers for the answers selection (by its namespace)
         */
        wtf.unsetAnswersHandlers = function() {
            switch(wtf.currentAnswerType) {
                case 'single':
                case 'multiple':
                    wtf.currentAnswersList.find(wtf.inputTypes.singleMulti).off("click." + wtf.currentQuestionId);
                    break;
            }
        };

        /**
         * Initializes the current question
         */
        wtf.initQuestion = function(questionIdx) {

            wtf.updateRefs(questionIdx);

            if (!wtf.started) {
                wtf.started = true;
                wtf.globalElements.questionList.slick({
                    infinite: false,
                    adaptiveHeight: true,
                    prevArrow: wtf.globalElements.prev,
                    nextArrow: wtf.globalElements.next
                });
                wtf.setAnswersHandlers();
            } else {
                if (questionIdx > 0) {
                    wtf.globalElements.prev.show();
                } else {
                    wtf.globalElements.prev.hide();
                }
                if (wtf.currentQuestion.hasClass('solved')) {
                    wtf.globalElements.next.show();
                    wtf.globalElements.check.hide();
                } else if (wtf.currentQuestion.hasClass('finalPage')) {
                    wtf.globalElements.next.hide();
                    wtf.globalElements.check.hide();
                } else {
                    wtf.globalElements.check.show();
                    wtf.globalElements.next.hide();
                    wtf.setAnswersHandlers();
                }
            }
        };
        // First question
        wtf.initQuestion(0);

        /**
         * Get user answer for the current question.
         *
         * @return {Object[]} answer
         */
        wtf.getUserAnswer = function() {
            var answered = false;
            var answer = [];
            switch (wtf.currentAnswerType) {
                case 'single':
                case 'multiple':
                    var inputs = wtf.currentAnswersList.find(wtf.inputTypes.singleMulti);
                    inputs.each(function(index, el) {
                        var selected = $(el).hasClass('selected');
                        if (selected) {
                            answered = true;
                            answer.push(1);
                        } else {
                            answer.push(0);
                        }
                    });
                    break;
                // TODO - Implement other question types
            }
            return answered ? answer : [];
        };

        /**
         * Draw question html
         *
         * @param  {Question} question
         * @return {HTML}
         */
        wtf.drawQuestion = function(question) {
            var qImage = '';
            if (question.image) {
                qImage += ''
                + '        <div class="wpTrivia_question_image">'
                + '            <img alt="question image" src="' + question.image + '">'
                + '        </div>';
            }
            var html = ''
            + '<li class="wpTrivia_listItem">'
            + '    <div class="wpTrivia_progress_header">'
            + '        <span>' + question.index + '/' + wtf.globalElements.questionCount.text() + '</span> <span>TRIVIA:</span> <span>' + wtf.globalElements.quizName.text() + '</span>'
            + '    </div>'
            + '    <div class="wpTrivia_question">'
            + '        <div class="wpTrivia_question_text">'
            + '            <p>' + question.question + '</p>'
            + '        </div>'
            +          qImage
            +          wtf.drawAnswers(question);
            + '    </div>'
            + '    <div style="clear: both;"></div>'
            + '</li>';
            return html;
        };

        /**
         * Draw answers list html according to type
         *
         * @param  {Question} question
         * @return {HTML}
         */
        wtf.drawAnswers = function(question) {
            var answers = ''
            + '        <ul class="wpTrivia_answersList" data-question_id="' + question.questionId + '" data-type="' + question.answers.type + '">';
            switch(question.answers.type) {
                case "single":
                case "multiple":
                    for (var i in question.answers.list) {
                        answers += ''
                        + '            <li class="wpTrivia_answersListItem">'
                        + '                <div class="wpTrivia_answerInput_singleMulti" value="' + (i + 1) + '" data-pos="' + i + '">' + question.answers.list[i] + '</div>'
                        + '            </li>';
                    }
                    break;
                // TODO - Implement other question types
            }
            answers += ''
            + '        </ul>';
            return answers;
        };

        /**
         * Draw final page html
         *
         * @param  {Question} question
         * @return {HTML}
         */
        wtf.drawFinalPage = function(finalText) {
            var html = ''
            + '<li class="wpTrivia_listItem finalPage">'
            + '    <div class="wpTrivia_finalPage">'
            +          finalText
            + '    </div>'
            + '</li>';
            return html;
        };

        /**
         * Retrieve and append next question
         */
        wtf.loadNextQuestion = function() {
            $.post(WpTriviaGlobal.ajaxurl, {
                action: "wp_trivia_admin_ajax",
                func: "loadNextQuestion",
                data: {
                    quizId: wtf.quizId,
                    questionId: wtf.currentQuestionId
                }
            }, function(question) {
                var html = '';
                question = JSON.parse(question);
                if (question.ended) {
                    html = wtf.drawFinalPage(question.final_text);
                } else {
                    html = wtf.drawQuestion(question);
                }
                wtf.globalElements.check.hide();
                wtf.globalElements.next.show();
                wtf.globalElements.questionList.slick('slickAdd', html);
            }).fail(function(err) {
                console.log(err);
                alert(WpTriviaGlobal.connectionError);
            });
        };

        /**
         * Updates the quiz progression bar
         *
         * @param {boolean} isCorrect
         */
        wtf.updateProgressBar = function(isCorrect) {
            var bar = $(wtf.globalElements.quizProgressBar);
            var currentActive = bar.find('.active');
            currentActive.removeClass('active');
            currentActive.addClass('complete');
            var a = currentActive.find('a');
            if (isCorrect) {
                a.addClass('correct');
            } else {
                a.addClass('wrong');
            }
            var nextActive = bar.find('.wpTrivia_quiz_progress_step').eq(currentActive.index() + 1);
            if (nextActive.length) {
                nextActive.removeClass('disabled');
                nextActive.addClass('active');
            }
        };

        /**
         * Check button handler
         */
        wtf.globalElements.check.click(function() {
            var userAnswer = wtf.getUserAnswer();
            if (!userAnswer.length) {
                alert(WpTriviaGlobal.questionNotSolved);
                return;
            }
            $.post(WpTriviaGlobal.ajaxurl, {
                action: "wp_trivia_admin_ajax",
                func: "checkAnswer",
                data: {
                    questionId: wtf.currentQuestionId,
                    questionType: wtf.currentAnswerType,
                    answer: userAnswer
                }
            }, function(res) {
                res = JSON.parse(res);
                switch(wtf.currentAnswerType) {
                    case 'single':
                        var inputs = wtf.currentAnswersList.find(wtf.inputTypes.singleMulti);
                        for (var r in res.correctAnswer) {
                            if (res.correctAnswer[r]) {
                                inputs.eq(r).addClass('correct');
                            } else if (!res.correctAnswer[r] && inputs.eq(r).hasClass('selected')) {
                                inputs.eq(r).addClass('wrong');
                            }
                            inputs.eq(r).removeClass('selected');
                        }
                        break;
                    case 'multiple':
                        var inputs = wtf.currentAnswersList.find(wtf.inputTypes.singleMulti);
                        for (var r in res.correctAnswer) {
                            if (res.correctAnswer[r] && inputs.eq(r).hasClass('selected')) {
                                inputs.eq(r).addClass('correct');
                            } else if (res.correctAnswer[r] && !inputs.eq(r).hasClass('selected')) {
                                inputs.eq(r).addClass('missing-correct');
                            } else if (!res.correctAnswer[r] && inputs.eq(r).hasClass('selected')) {
                                inputs.eq(r).addClass('wrong');
                            }
                            inputs.eq(r).removeClass('selected');
                        }
                        break;
                    // TODO - Implement other question types
                }
                wtf.currentQuestion.addClass('solved');
                wtf.loadNextQuestion();
                wtf.updateProgressBar(res.isCorrect);
            }).fail(function(err) {
                console.log(err);
                alert(WpTriviaGlobal.connectionError);
            });
        });

        /**
         * Slide change handler
         */
        $(wtf.globalElements.questionList).on('beforeChange', function(event, slick, currentSlide, nextSlide){
            wtf.unsetAnswersHandlers();
            wtf.initQuestion(nextSlide);
        });

        /**
         * Quiz steps handler
         */
        $(wtf.globalElements.quizProgressBar).find('a').on('click', function(event) {
            $(wtf.globalElements.questionList).slick('slickGoTo', $(this).data('step'));
        });
    };

    $.fn.wpTriviaFront = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('wpTriviaFront')) {
                $(this).data('wpTriviaFront', new $.wpTriviaFront(this, options));
            }
        });
    };
})(jQuery);
