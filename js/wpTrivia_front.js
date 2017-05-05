/** TODO - Work in progress: complete frontend js rewrite **/

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

        var wpTriviaFront = this;
        var $e = $(element);
        var startTime = 0;
        var questionIndex = 0;
        var currentAnswersList = null;
        var currentQuestion = null;
        var currentQuestionId = 0;
        var currentAnswerType = '';
        var results = {};

        var globalNames = {
            answersList: '.wpTrivia_answersList',
            inputTypes: {
                'singleMulti': '.wpTrivia_answerInput_singleMulti'
            }
        };

        var globalElements = {
            questionList: $e.find('.wpTrivia_list'),
            listItems: $e.find('.wpTrivia_list > li'),
            check: $e.find('input[name="check"]'),
            prev: $e.find('.wpTrivia_button.prev'),
            next: $e.find('.wpTrivia_button.next'),
            quiz: $e.find('.wpTrivia_quiz'),

            results: $e.find('.wpTrivia_results'),
            timelimit: $e.find('.wpTrivia_time_limit'),
            toplistShowInButton: $e.find('.wpTrivia_toplistShowInButton')
        };

        this.init = function() {
            startTime = Date.now();

            var $listItem = globalElements.questionList.children();
            currentQuestion = $listItem.eq(questionIndex).show();
            currentAnswersList = currentQuestion.find(globalNames.answersList);
            currentQuestionId = currentAnswersList.data('question_id');
            currentAnswerType = currentAnswersList.data('type');

            $e.find('.wpTrivia_loadQuiz').hide();
            globalElements.next.hide();
            if (!results.length) {
                globalElements.prev.hide();
            }
            globalElements.quiz.show();
            wpTriviaFront.setHandlers();
        };

        /**
         * Get the user answer for the current question.
         *
         * @return {Object[]} answer
         */
        this.getUserAnswer = function() {
            var answered = false;
            var answer = [];
            switch (currentAnswerType) {
                case 'single':
                case 'multi':
                    var inputs = currentAnswersList.find(globalNames.inputTypes.singleMulti);
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
                // TODO - Implementare altre tipologie
                /*
                case 'sort_answer':
                    var $items = globalElements.questionList.children();

                    $items.each(function (i, v) {
                        var $this = $(this);

                        statistcAnswerData[i] = $this.data('pos');

                        if (i == $this.data('pos')) {
                            plugin.methode.marker($this, true);

                            if (isDiffPoints) {
                                points += data.points[i];
                            }
                        } else {
                            plugin.methode.marker($this, false);
                            correct = false;
                        }
                    });

                    $items.children().css({
                        'box-shadow': '0 0',
                        'cursor': 'auto'
                    });

                    globalElements.questionList.sortable("destroy");

                    $items.sort(function (a, b) {
                        return $(a).data('pos') > $(b).data('pos') ? 1 : -1;
                    });

                    globalElements.questionList.append($items);
                break;
                case 'free_answer':
                    var $li = globalElements.questionList.children();
                    var value = $li.find('.wpTrivia_answerInput').attr('disabled', 'disabled').val();

                    if ($.inArray($.trim(value).toLowerCase(), data.correct) >= 0) {
                        plugin.methode.marker($li, true);
                    } else {
                        plugin.methode.marker($li, false);
                        correct = false;
                    }
                break;
                */
            }
            return answered ? answer : [];
        };

        /* Handlers */
        this.setHandlers = function() {

            // [Single and multi answer types] answer selection
            if (currentAnswerType == 'single' || currentAnswerType == 'multi') {
                currentAnswersList.find(globalNames.inputTypes.singleMulti).click(function() {
                    switch(currentAnswerType) {
                        case 'single':
                            currentAnswersList.find(globalNames.inputTypes.singleMulti).removeClass('selected');
                            $(this).addClass('selected');
                            break;
                        case 'multi':
                            // TODO
                            break;
                    }
                });
            }

            // Answer check
            globalElements.check.click(function() {
                var userAnswer = wpTriviaFront.getUserAnswer();
                // TODO - Ragionare a come/dove mostrare gli errori
                if (!userAnswer.length) {
                    alert(WpTriviaGlobal.questionNotSolved);
                    return;
                }
                $.post(WpTriviaGlobal.ajaxurl, {
                    action: "wp_trivia_admin_ajax",
                    func: "checkAnswer",
                    data: {
                        questionId: currentQuestionId,
                        questionType: currentAnswerType,
                        answer: userAnswer
                    }
                }, function(res) {
                    res = JSON.parse(res);
                    var inputs = currentAnswersList.find(globalNames.inputTypes.singleMulti);
                    for (var r in res.correctAnswers) {
                        if (res.correctAnswers[r]) {
                            inputs.eq(r).addClass('correct');
                        } else if (!res.correctAnswers[r] && inputs.eq(r).hasClass('selected')) {
                            inputs.eq(r).addClass('wrong');
                        }
                        inputs.eq(r).removeClass('selected');
                    }
                    globalElements.check.hide();
                    globalElements.next.show();
                }).fail(function(err) {
                    console.log(err);
                    alert(WpTriviaGlobal.connectionError);
                });
            });

            globalElements.next.click(function() {
                // TODO - Passa alla prossima domanda
                /* aggiornare var globali
                questionIndex++;
                currentQuestion = $listItem.eq(questionIndex).show();
                currentQuestionId = currentQuestion.find(globalNames.questionList).data('question_id');
                currentAnswerType = globalElements.answersList.data('type');
                */
                /*
                if (bitOptions.forcingQuestionSolve
                    && !quizSolved[currentQuestion.index()]
                    && (bitOptions.quizSummeryHide || !bitOptions.reviewQustion)) {
                    alert(WpTriviaGlobal.questionNotSolved);
                    return false;
                }
                plugin.methode.checkQuestion();
                */
            });
        };

        /* Initialize question */
        wpTriviaFront.init();
    };

    $.fn.wpTriviaFront = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('wpTriviaFront')) {
                $(this).data('wpTriviaFront', new $.wpTriviaFront(this, options));
            }
        });
    };
})(jQuery);
