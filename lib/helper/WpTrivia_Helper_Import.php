<?php

class WpTrivia_Helper_Import
{

    private $_content = null;
    private $_error = false;

    public function setImportFileUpload($file)
    {
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->setError(__('File was not uploaded', 'wp-trivia'));

            return false;
        }

        return $this->setImportString(file_get_contents($file['tmp_name']));
    }

    public function setImportString($str)
    {
        $this->_content = trim($str);

        return $this->checkCode();
    }

    private function setError($str)
    {
        $this->_error = $str;
    }

    public function getError()
    {
        return $this->_error;
    }

    private function checkCode()
    {
        $code = substr($this->_content, 0, 13);

        $c = substr($code, 0, 3);
        //$v1 = substr($code, 3, 5);
        $v2 = substr($code, 8, 5);

        if ($c !== 'WPQ') {
            $this->setError(__('File have wrong format', 'wp-trivia'));

            return false;
        }

        if ($v2 < 3) {
            $this->setError(__('File is not compatible with the current version'));

            return false;
        }

        return true;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getImportData()
    {

        if ($this->_content === null) {
            $this->setError(__('File cannot be processed', 'wp-trivia'));

            return false;
        }

        $data = substr($this->_content, 13);

        $b = base64_decode($data);

        if ($b === null) {
            $this->setError(__('File cannot be processed', 'wp-trivia'));

            return false;
        }

        $check = $this->saveUnserialize($b, $o);

        if ($check === false || !is_array($o)) {
            $this->setError(__('File cannot be processed', 'wp-trivia'));

            return false;
        }

        unset($b);

        return $o;
    }

    public function saveImport($ids = false)
    {
        $data = $this->getImportData();

        if ($data === false) {
            return false;
        }

        switch ($data['exportVersion']) {
            case '3':
            case '4':
                return $this->importData($data, $ids, $data['exportVersion']);
                break;
        }

        return false;
    }

    private function importData($o, $ids = false, $version = '1')
    {
        $quizMapper = new WpTrivia_Model_QuizMapper();
        $questionMapper = new WpTrivia_Model_QuestionMapper();
        $categoryMapper = new WpTrivia_Model_CategoryMapper();
        $formMapper = new WpTrivia_Model_FormMapper();

        $categoryArray = $categoryMapper->getCategoryArrayForImport();
        $categoryArrayQuiz = $categoryMapper->getCategoryArrayForImport(WpTrivia_Model_Category::CATEGORY_TYPE_QUIZ);

        foreach ($o['master'] as $master) {
            /** @var WpTrivia_Model_Quiz $master */

            if (get_class($master) !== 'WpTrivia_Model_Quiz') {
                continue;
            }

            $oldId = $master->getId();

            if ($ids !== false) {
                if (!in_array($oldId, $ids)) {
                    continue;
                }
            }

            $master->setId(0);

            $master->setCategoryId(0);

            if (trim($master->getCategoryName()) != '') {
                if (isset($categoryArrayQuiz[strtolower($master->getCategoryName())])) {
                    $master->setCategoryId($categoryArrayQuiz[strtolower($master->getCategoryName())]);
                } else {
                    $categoryModel = new WpTrivia_Model_Category();
                    $categoryModel->setCategoryName($master->getCategoryName());
                    $categoryModel->setType(WpTrivia_Model_Category::CATEGORY_TYPE_QUIZ);

                    $categoryMapper->save($categoryModel);

                    $master->setCategoryId($categoryModel->getCategoryId());

                    $categoryArrayQuiz[strtolower($master->getCategoryName())] = $categoryModel->getCategoryId();
                }
            }

            $quizMapper->save($master);

            if (isset($o['forms']) && isset($o['forms'][$oldId])) {
                foreach ($o['forms'][$oldId] as $form) {
                    /** @var WpTrivia_Model_Form $form * */

                    $form->setFormId(0);
                    $form->setQuizId($master->getId());
                }

                $formMapper->update($o['forms'][$oldId]);
            }

            $sort = 0;

            foreach ($o['question'][$oldId] as $question) {
                /** @var WpTrivia_Model_Question $question */

                if (get_class($question) !== 'WpTrivia_Model_Question') {
                    continue;
                }

                $question->setQuizId($master->getId());
                $question->setId(0);
                $question->setSort($sort++);
                $question->setCategoryId(0);

                if (trim($question->getCategoryName()) != '') {
                    if (isset($categoryArray[strtolower($question->getCategoryName())])) {
                        $question->setCategoryId($categoryArray[strtolower($question->getCategoryName())]);
                    } else {
                        $categoryModel = new WpTrivia_Model_Category();
                        $categoryModel->setCategoryName($question->getCategoryName());
                        $categoryMapper->save($categoryModel);

                        $question->setCategoryId($categoryModel->getCategoryId());

                        $categoryArray[strtolower($question->getCategoryName())] = $categoryModel->getCategoryId();
                    }
                }

                $questionMapper->save($question);
            }
        }

        return true;
    }

    private function saveUnserialize($str, &$into)
    {
        $into = @unserialize($str);

        return $into !== false || rtrim($str) === serialize(false);
    }
}
