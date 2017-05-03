<?php

class WpTrivia_Model_Model
{

    /**
     * @var WpTrivia_Model_QuizMapper
     */
    protected $_mapper = null;

    public function __construct($array = null)
    {
        $this->setModelData($array);
    }

    public function setModelData($array)
    {
        if ($array != null) {
            //3,4x faster
            $n = explode(' ', implode('', array_map('ucfirst', explode('_', implode(' _', array_keys($array))))));

            $a = array_combine($n, $array);

            foreach ($a as $k => $v) {
                $this->{'set' . $k}($v);
            }
        }
    }

    public function __call($name, $args)
    {
    }

    /**
     *
     * @return WpTrivia_Model_QuizMapper
     */
    public function getMapper()
    {
        if ($this->_mapper === null) {
            $this->_mapper = new WpTrivia_Model_QuizMapper();
        }

        return $this->_mapper;
    }

    /**
     * @param WpTrivia_Model_QuizMapper $mapper
     * @return WpTrivia_Model_Model
     */
    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }
}
