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
        wtf.currentQuestion = null;
        wtf.currentQuestionId = 0;
        wtf.currentAnswersList = null;
        wtf.currentAnswerType = '';
        wtf.globalNames = {
            answersList: '.wpTrivia_answersList',
            inputTypes: {
                'singleMulti': '.wpTrivia_answerInput_singleMulti'
            }
        };
        wtf.globalElements = {};

        wtf.updateRefs = function() {
            wtf.globalElements = {
                questionList: wtf._e.find('.wpTrivia_list'),
                listItems: wtf._e.find('.wpTrivia_list .wpTrivia_listItem'),
                check: wtf._e.find('input.check'),
                prev: wtf._e.find('.wpTrivia_button.prev'),
                next: wtf._e.find('.wpTrivia_button.next'),
                quizName: $('#quizName'),
                questionCount: $('#questionCount'),
                /*
                results: wtf._e.find('.wpTrivia_results'),
                timelimit: wtf._e.find('.wpTrivia_time_limit'),
                toplistShowInButton: wtf._e.find('.wpTrivia_toplistShowInButton')
                */
            };
        };

        wtf.setAnswersHandlers = function() {
            // [Single and multi answer types] answer selection
            switch(wtf.currentAnswerType) {
                case 'single':
                case 'multi':
                    wtf.currentAnswersList.find(wtf.globalNames.inputTypes.singleMulti).click(function() {
                        if (wtf.currentAnswerType == 'single') {
                            if (!wtf.currentAnswersList.hasClass('solved')) {
                                wtf.currentAnswersList.find(wtf.globalNames.inputTypes.singleMulti).removeClass('selected');
                                $(this).addClass('selected');
                            }
                        } else {
                            // TODO - To test
                            if (!wtf.currentAnswersList.hasClass('solved')) {
                                if ($(this).hasClass('selected')) {
                                    $(this).removeClass('selected');
                                } else {
                                    $(this).addClass('selected');
                                }
                            }
                        }
                    });
                    break;
                // TODO - Implement other question types
            }
        };

        wtf.initQuestion = function(questionIdx) {

            wtf.updateRefs();

            if (!wtf.started) {
                wtf.started = true;
                wtf.globalElements.questionList.slick({
                    infinite: false,
                    adaptiveHeight: true,
                    prevArrow: wtf.globalElements.prev,
                    nextArrow: wtf.globalElements.next
                });
            }

            wtf.currentQuestion = wtf.globalElements.listItems.eq(questionIdx);
            wtf.currentAnswersList = wtf.currentQuestion.find(wtf.globalNames.answersList);
            wtf.currentQuestionId = wtf.currentAnswersList.data('question_id');
            wtf.currentAnswerType = wtf.currentAnswersList.data('type');

            wtf.setAnswersHandlers();
        };

        /* Initialize first question */
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
                case 'multi':
                    var inputs = wtf.currentAnswersList.find(wtf.globalNames.inputTypes.singleMulti);
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
                case "multi":
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
         * Retrieve and append next question
         */
        wtf.loadNextQuestion = function() {
            $.post(WpTriviaGlobal.ajaxurl, {
                action: "wp_trivia_admin_ajax",
                func: "loadNextQuestion",
                data: {
                    questionId: wtf.currentQuestionId
                }
            }, function(question) {
                var html = '';
                question = JSON.parse(question);
                if (question.ended) {
                    // TODO - Load/Draw final page
                    // html = wtf.drawFinalPage()
                    alert('TODO - Load/Draw final page');
                } else {
                    html = wtf.drawQuestion(question);
                }
                wtf.globalElements.questionList.slick('slickAdd', html);
            }).fail(function(err) {
                console.log(err);
                alert(WpTriviaGlobal.connectionError);
            });
        };

        /* Check button */
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
                var inputs = wtf.currentAnswersList.find(wtf.globalNames.inputTypes.singleMulti);
                for (var r in res.correctAnswer) {
                    if (res.correctAnswer[r]) {
                        inputs.eq(r).addClass('correct');
                    } else if (!res.correctAnswer[r] && inputs.eq(r).hasClass('selected')) {
                        inputs.eq(r).addClass('wrong');
                    }
                    inputs.eq(r).removeClass('selected');
                }
                wtf.currentAnswersList.addClass('solved');
                wtf.globalElements.check.hide();
                wtf.globalElements.next.show();
                wtf.loadNextQuestion();
            }).fail(function(err) {
                console.log(err);
                alert(WpTriviaGlobal.connectionError);
            });
        });

        /* Slide change handler */
        $(wtf.globalElements.questionList).on('beforeChange', function(event, slick, currentSlide, nextSlide){
            wtf.globalElements.check.show();
            if (nextSlide > 0) wtf.globalElements.prev.show();
            if (nextSlide == slick.rowCount) wtf.globalElements.next.hide();
            if (wtf.currentAnswersList.hasClass('solved')) {
                wtf.globalElements.next.show();
            }
            wtf.initQuestion(nextSlide);
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
