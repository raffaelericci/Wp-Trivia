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
        var currentQuestion = null;
        var currentQuestionId = 0;

        var globalNames = {
            questionList: '.wpTrivia_list'
        };

        var globalElements = {
            answersList: $e.find('.wpTrivia_answersList'),
            questionList: $e.find('.wpTrivia_list'),
            listItems: $e.find('.wpTrivia_list > li'),
            check: $e.find('input[name="check"]'),
            next: $e.find('input[name="next"]'),
            quiz: $e.find('.wpTrivia_quiz'),

            results: $e.find('.wpTrivia_results'),
            timelimit: $e.find('.wpTrivia_time_limit'),
            toplistShowInButton: $e.find('.wpTrivia_toplistShowInButton')
        };

        this.startQuiz = function(loadData) {

            startTime = Date.now();

            var $listItem = globalElements.questionList.children();
            currentQuestion = $listItem.eq(0).show();
            currentQuestionId = currentQuestion.find(globalNames.questionList).data('question_id');

            quizSolved = [];

            results = {
                comp: {
                    points: 0,
                    correctQuestions: 0,
                    quizTime: 0
                }
            };

            $e.find('.wpTrivia_answersList').each(function () {
                var questionId = $(this).data('question_id');

                results[questionId] = {
                    time: 0,
                    solved: 0
                };
            });

            $e.find('.wpTrivia_loadQuiz').hide();
            globalElements.quiz.show();
            this.setHandlers();
        };

        /**
         * Get the user answer for the current question.
         *
         * @param  {String} questionType
         * @return {Object[]} answer
         */
        this.getUserAnswer = function(questionType) {
            var answer = [];
            switch (questionType) {
                case 'single':
                case 'multi':
                    var input = globalElements.questionList.find('.wpTrivia_questionInput');
                    input.each(function(i) {
                        var checked = input.eq(i).is(':checked');
                        if (checked) {
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
                    var value = $li.find('.wpTrivia_questionInput').attr('disabled', 'disabled').val();

                    if ($.inArray($.trim(value).toLowerCase(), data.correct) >= 0) {
                        plugin.methode.marker($li, true);
                    } else {
                        plugin.methode.marker($li, false);
                        correct = false;
                    }
                break;
                */
            }
            return answer;
        };

        /* Handlers */
        this.setHandlers = function() {

            var questionType = globalElements.answersList.data('type');
            var inputType = '';
            switch(questionType) {
                case 'single':
                case 'multi':
                    inputType = '.wpTrivia_questionInput_singleMulti';
                    break;
            }
            globalElements.answersList.find('> li ' + inputType).click(function() {
                console.log("wewe");
            });

            globalElements.check.click(function() {
                // TODO -
                var questionType = globalElements.answersList.data('type');
                var userAnswer = wpTriviaFront.getUserAnswer(questionType);
                // TODO - localizzare e capire come mostrare errori di questo tipo
                if (!userAnswer.length) alert('Non hai risposto alla domanda');
                $.post(WpTriviaGlobal.ajaxurl, {
                    action: "wp_trivia_admin_ajax",
                    func: "checkAnswer",
                    data: {
                        questionId: currentQuestionId,
                        questionType: questionType,
                        answer: userAnswer
                    }
                }, function(res) {
                    // TODO - colorare
                    if (res.isCorrect) {

                    } else {

                    }
                }).fail(function(err) {
                    console.log(err);
                });
            });

            globalElements.next.click(function() {
                // TODO - Passa alla prossima domanda
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

        /* Init */
        wpTriviaFront.startQuiz();
    };

    $.fn.wpTriviaFront = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('wpTriviaFront')) {
                $(this).data('wpTriviaFront', new $.wpTriviaFront(this, options));
            }
        });
    };
})(jQuery);
