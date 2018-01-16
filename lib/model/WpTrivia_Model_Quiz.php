<?php

class WpTrivia_Model_Quiz extends WpTrivia_Model_Model
{

    const QUIZ_RUN_ONCE_TYPE_ALL = 1;
    const QUIZ_RUN_ONCE_TYPE_ONLY_USER = 2;
    const QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM = 3;

    const QUIZ_TOPLIST_TYPE_ALL = 1;
    const QUIZ_TOPLIST_TYPE_ONLY_USER = 2;
    const QUIZ_TOPLIST_TYPE_ONLY_ANONYM = 3;

    const QUIZ_TOPLIST_SORT_BEST = 1;
    const QUIZ_TOPLIST_SORT_NEW = 2;
    const QUIZ_TOPLIST_SORT_OLD = 3;

    const QUIZ_TOPLIST_SHOW_IN_NONE = 0;
    const QUIZ_TOPLIST_SHOW_IN_NORMAL = 1;
    const QUIZ_TOPLIST_SHOW_IN_BUTTON = 2;

    const QUIZ_EMAIL_NOTE_NONE = 0;
    const QUIZ_EMAIL_NOTE_REG_USER = 1;
    const QUIZ_EMAIL_NOTE_ALL = 2;

    const QUIZ_FORM_POSITION_START = 0;
    const QUIZ_FORM_POSITION_END = 1;

    protected $_id = 0;
    protected $_name = '';
    protected $_text = '';
    protected $_finalText;
    protected $_timeLimit = 0;
    protected $_statisticsOn = false;
    protected $_statisticsIpLock = 1440;
    protected $_showPoints = false;
    protected $_quizRunOnce = false;
    protected $_quizRunOnceType = 0;
    protected $_quizRunOnceCookie = false;
    protected $_quizRunOnceTime = 0;
    protected $_numberedAnswer = false;
    protected $_hideAnswerMessageBox = false;
    protected $_disabledAnswerMark = false;
    protected $_showMaxQuestion = false;
    protected $_showMaxQuestionValue = 1;
    protected $_showMaxQuestionPercent = false;

    //0.19
    protected $_toplistActivated = false;
    protected $_toplistDataAddPermissions = 1;
    protected $_toplistDataSort = 1;
    protected $_toplistDataAddMultiple = false;
    protected $_toplistDataAddBlock = 1;
    protected $_toplistDataShowLimit = 1;
    protected $_toplistDataShowQuizResult = false;
    protected $_toplistDataShowIn = 0;
    protected $_toplistDataCaptcha = false;

    protected $_toplistData = array();

    protected $_prerequisite = false;

    //0.22
    protected $_toplistDataAddAutomatic = false;
    protected $_emailNotification = 0;

    //0.24
    protected $_userEmailNotification = false;

    //0.25
    protected $_forcingQuestionSolve = false;
    protected $_hideQuestionPositionOverview = false;
    protected $_hideQuestionNumbering = false;

    //0.27
    protected $_formActivated = false;
    protected $_formShowPosition = 0;
    protected $_startOnlyRegisteredUser = false;
    protected $_questionsPerPage = 0;

    //0.29
    protected $_adminEmail = null;
    protected $_userEmail = null;

    //0.33
    protected $_pluginContainer = null;

    //0.34
    protected $_validFromDate = null;
    protected $_validToDate = null;

    public function setId($_id)
    {
        $this->_id = (int)$_id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setName($_name)
    {
        $this->_name = (string)$_name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setText($_text)
    {
        $this->_text = (string)$_text;

        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setFinalText($_finalText)
    {
        $this->_finalText = $_finalText;

        return $this;
    }

    public function getFinalText()
    {
        return $this->_finalText;
    }

    public function setTimeLimit($_timeLimit)
    {
        $this->_timeLimit = (int)$_timeLimit;

        return $this;
    }

    public function getTimeLimit()
    {
        return $this->_timeLimit;
    }

    public function setStatisticsOn($_statisticsOn)
    {
        $this->_statisticsOn = (bool)$_statisticsOn;

        return $this;
    }

    public function isStatisticsOn()
    {
        return $this->_statisticsOn;
    }

    public function setStatisticsIpLock($_statisticsIpLock)
    {
        $this->_statisticsIpLock = (int)$_statisticsIpLock;

        return $this;
    }

    public function getStatisticsIpLock()
    {
        return $this->_statisticsIpLock;
    }

    public function setShowPoints($_showPoints)
    {
        $this->_showPoints = (bool)$_showPoints;

        return $this;
    }

    public function isShowPoints()
    {
        return $this->_showPoints;
    }

    public function fetchSumQuestionPoints()
    {
        $m = new WpTrivia_Model_QuizMapper();

        return $m->sumQuestionPoints($this->_id);
    }

    public function fetchCountQuestions()
    {
        $m = new WpTrivia_Model_QuizMapper();

        return $m->countQuestion($this->_id);
    }

    public function setQuizRunOnce($_quizRunOnce)
    {
        $this->_quizRunOnce = (bool)$_quizRunOnce;

        return $this;
    }

    public function isQuizRunOnce()
    {
        return $this->_quizRunOnce;
    }

    public function setQuizRunOnceCookie($_quizRunOnceCookie)
    {
        $this->_quizRunOnceCookie = (bool)$_quizRunOnceCookie;

        return $this;
    }

    public function isQuizRunOnceCookie()
    {
        return $this->_quizRunOnceCookie;
    }

    public function setQuizRunOnceType($_quizRunOnceType)
    {
        $this->_quizRunOnceType = (int)$_quizRunOnceType;

        return $this;
    }

    public function getQuizRunOnceType()
    {
        return $this->_quizRunOnceType;
    }

    public function setQuizRunOnceTime($_quizRunOnceTime)
    {
        $this->_quizRunOnceTime = (int)$_quizRunOnceTime;

        return $this;
    }

    public function getQuizRunOnceTime()
    {
        return $this->_quizRunOnceTime;
    }

    public function setNumberedAnswer($_numberedAnswer)
    {
        $this->_numberedAnswer = (bool)$_numberedAnswer;

        return $this;
    }

    public function isNumberedAnswer()
    {
        return $this->_numberedAnswer;
    }

    public function setHideAnswerMessageBox($_hideAnswerMessageBox)
    {
        $this->_hideAnswerMessageBox = (bool)$_hideAnswerMessageBox;

        return $this;
    }

    public function isHideAnswerMessageBox()
    {
        return $this->_hideAnswerMessageBox;
    }

    public function setDisabledAnswerMark($_disabledAnswerMark)
    {
        $this->_disabledAnswerMark = (bool)$_disabledAnswerMark;

        return $this;
    }

    public function isDisabledAnswerMark()
    {
        return $this->_disabledAnswerMark;
    }

    public function setShowMaxQuestion($_showMaxQuestion)
    {
        $this->_showMaxQuestion = (bool)$_showMaxQuestion;

        return $this;
    }

    public function isShowMaxQuestion()
    {
        return $this->_showMaxQuestion;
    }

    public function setShowMaxQuestionValue($_showMaxQuestionValue)
    {
        $this->_showMaxQuestionValue = (int)$_showMaxQuestionValue;

        return $this;
    }

    public function getShowMaxQuestionValue()
    {
        return $this->_showMaxQuestionValue;
    }

    public function setShowMaxQuestionPercent($_showMaxQuestionPercent)
    {
        $this->_showMaxQuestionPercent = (bool)$_showMaxQuestionPercent;

        return $this;
    }

    public function isShowMaxQuestionPercent()
    {
        return $this->_showMaxQuestionPercent;
    }

    public function setToplistActivated($_toplistActivated)
    {
        $this->_toplistActivated = (bool)$_toplistActivated;

        return $this;
    }

    public function isToplistActivated()
    {
        return $this->_toplistActivated;
    }

    public function setToplistDataAddPermissions($_toplistDataAddPermissions)
    {
        $this->_toplistDataAddPermissions = (int)$_toplistDataAddPermissions;

        return $this;
    }

    public function getToplistDataAddPermissions()
    {
        return $this->_toplistDataAddPermissions;
    }

    public function setToplistDataSort($_toplistDataSort)
    {
        $this->_toplistDataSort = (int)$_toplistDataSort;

        return $this;
    }

    public function getToplistDataSort()
    {
        return $this->_toplistDataSort;
    }

    public function setToplistDataAddMultiple($_toplistDataAddMultiple)
    {
        $this->_toplistDataAddMultiple = (bool)$_toplistDataAddMultiple;

        return $this;
    }

    public function isToplistDataAddMultiple()
    {
        return $this->_toplistDataAddMultiple;
    }

    public function setToplistDataAddBlock($_toplistDataAddBlock)
    {
        $this->_toplistDataAddBlock = (int)$_toplistDataAddBlock;

        return $this;
    }

    public function getToplistDataAddBlock()
    {
        return $this->_toplistDataAddBlock;
    }

    public function setToplistDataShowLimit($_toplistDataShowLimit)
    {
        $this->_toplistDataShowLimit = (int)$_toplistDataShowLimit;

        return $this;
    }

    public function getToplistDataShowLimit()
    {
        return $this->_toplistDataShowLimit;
    }

    public function setToplistData($_toplistData)
    {
        if (!empty($_toplistData)) {
            $d = unserialize($_toplistData);

            if ($d !== false) {
                $this->setModelData($d);
            }
        }

        return $this;
    }

    public function getToplistData()
    {

        $a = array(
            'toplistDataAddPermissions' => $this->getToplistDataAddPermissions(),
            'toplistDataSort' => $this->getToplistDataSort(),
            'toplistDataAddMultiple' => $this->isToplistDataAddMultiple(),
            'toplistDataAddBlock' => $this->getToplistDataAddBlock(),
            'toplistDataShowLimit' => $this->getToplistDataShowLimit(),
            'toplistDataShowIn' => $this->getToplistDataShowIn(),
            'toplistDataCaptcha' => $this->isToplistDataCaptcha(),
            'toplistDataAddAutomatic' => $this->isToplistDataAddAutomatic()
        );

        return serialize($a);
    }

    public function setToplistDataShowIn($_toplistDataShowIn)
    {
        $this->_toplistDataShowIn = (int)$_toplistDataShowIn;

        return $this;
    }

    public function getToplistDataShowIn()
    {
        return $this->_toplistDataShowIn;
    }

    public function setToplistDataCaptcha($_toplistDataCaptcha)
    {
        $this->_toplistDataCaptcha = (bool)$_toplistDataCaptcha;

        return $this;
    }

    public function isToplistDataCaptcha()
    {
        return $this->_toplistDataCaptcha;
    }

    public function setPrerequisite($_prerequisite)
    {
        $this->_prerequisite = (bool)$_prerequisite;

        return $this;
    }

    public function isPrerequisite()
    {
        return $this->_prerequisite;
    }

    public function setToplistDataAddAutomatic($_toplistDataAddAutomatic)
    {
        $this->_toplistDataAddAutomatic = (bool)$_toplistDataAddAutomatic;

        return $this;
    }

    public function isToplistDataAddAutomatic()
    {
        return $this->_toplistDataAddAutomatic;
    }

    public function setEmailNotification($_emailNotification)
    {
        $this->_emailNotification = (int)$_emailNotification;

        return $this;
    }

    public function getEmailNotification()
    {
        return $this->_emailNotification;
    }

    public function setUserEmailNotification($_userEmailNotification)
    {
        $this->_userEmailNotification = (bool)$_userEmailNotification;

        return $this;
    }

    public function isUserEmailNotification()
    {
        return $this->_userEmailNotification;
    }

    public function setForcingQuestionSolve($_forcingQuestionSolve)
    {
        $this->_forcingQuestionSolve = (bool)$_forcingQuestionSolve;

        return $this;
    }

    public function isForcingQuestionSolve()
    {
        return $this->_forcingQuestionSolve;
    }

    public function setHideQuestionPositionOverview($_hideQuestionPositionOverview)
    {
        $this->_hideQuestionPositionOverview = (bool)$_hideQuestionPositionOverview;

        return $this;
    }

    public function isHideQuestionPositionOverview()
    {
        return $this->_hideQuestionPositionOverview;
    }

    public function setHideQuestionNumbering($_hideQuestionNumbering)
    {
        $this->_hideQuestionNumbering = (bool)$_hideQuestionNumbering;

        return $this;
    }

    public function isHideQuestionNumbering()
    {
        return $this->_hideQuestionNumbering;
    }

    public function setFormActivated($_formActivated)
    {
        $this->_formActivated = (bool)$_formActivated;

        return $this;
    }

    public function isFormActivated()
    {
        return $this->_formActivated;
    }

    public function setFormShowPosition($_formShowPosition)
    {
        $this->_formShowPosition = (int)$_formShowPosition;

        return $this;
    }

    public function getFormShowPosition()
    {
        return $this->_formShowPosition;
    }

    public function setStartOnlyRegisteredUser($_startOnlyRegisteredUser)
    {
        $this->_startOnlyRegisteredUser = (bool)$_startOnlyRegisteredUser;

        return $this;
    }

    public function isStartOnlyRegisteredUser()
    {
        return $this->_startOnlyRegisteredUser;
    }

    public function setQuestionsPerPage($_questionsPerPage)
    {
        $this->_questionsPerPage = (int)$_questionsPerPage;

        return $this;
    }

    public function getQuestionsPerPage()
    {
        return $this->_questionsPerPage;
    }

    public function setAdminEmail($_adminEmail)
    {
        $this->_adminEmail = $_adminEmail;

        return $this;
    }
    
    public function setValidFromDate($date) {
        $this->_validFromDate = $date;
    }
    
    public function getValidFromDate() {
        return $this->_validFromDate;
    }
    
    public function setValidToDate($date) {
        $this->_validToDate = $date;
    }
    
    public function getValidToDate() {
        return $this->_validToDate;
    }

    public function isValidToday() {
        $validFromDate = strtotime($this->getValidFromDate());
        $validToDate = strtotime($this->getValidToDate());
        $today = strtotime('today');
        if (!$validFromDate && !$validToDate) {
            return true;
        } elseif ($validFromDate && !$validToDate) {
            return $today >= $validFromDate; 
        } elseif (!$validFromDate && $validToDate) {
            return $today <= $validToDate;
        } else {
            return $today >= $validFromDate && $today <= $validToDate;
        }
    }

    /**
     * @param bool|false $serialize
     * @return null|string|WpTrivia_Model_Email
     */
    public function getAdminEmail($serialize = false)
    {
        if ($this->_adminEmail === null) {
            return null;
        }

        if (is_object($this->_adminEmail) || $this->_adminEmail instanceof WpTrivia_Model_Email) {
            if ($serialize) {
                return @serialize($this->_adminEmail);
            }
        } else {
            if (!$serialize) {
                if (WpTrivia_Helper_Until::saveUnserialize($this->_adminEmail, $into) === false) {
                    return null;
                }

                $this->_adminEmail = $into;
            }
        }

        return $this->_adminEmail;
    }

    public function setUserEmail($_userEmail)
    {
        $this->_userEmail = $_userEmail;

        return $this;
    }

    /**
     * @param bool|false $serialize
     * @return null|string|WpTrivia_Model_Email
     */
    public function getUserEmail($serialize = false)
    {
        if ($this->_userEmail === null) {
            return null;
        }

        if (is_object($this->_userEmail) || $this->_userEmail instanceof WpTrivia_Model_Email) {
            if ($serialize) {
                return @serialize($this->_userEmail);
            }
        } else {
            if (!$serialize) {
                if (WpTrivia_Helper_Until::saveUnserialize($this->_userEmail, $into) === false) {
                    return null;
                }

                $this->_userEmail = $into;
            }
        }

        return $this->_userEmail;
    }

    public function setPluginContainer($_pluginContainer)
    {
        $this->_pluginContainer = $_pluginContainer;

        return $this;
    }

    public function getPluginContainer($serialize = false)
    {
        if ($this->_pluginContainer === null) {
            $this->_pluginContainer = new WpTrivia_Model_PluginContainer();
        }

        if (is_object($this->_pluginContainer) || $this->_pluginContainer instanceof WpTrivia_Model_PluginContainer) {
            if ($serialize) {
                return @serialize($this->_pluginContainer);
            }
        } else {
            if (!$serialize) {
                if (WpTrivia_Helper_Until::saveUnserialize($this->_pluginContainer, $into) === false) {
                    return null;
                }

                $this->_pluginContainer = $into;
            }
        }

        return $this->_pluginContainer;
    }
}
