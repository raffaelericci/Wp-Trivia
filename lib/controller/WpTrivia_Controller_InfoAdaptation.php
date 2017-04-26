<?php

class WpTrivia_Controller_InfoAdaptation extends WpTrivia_Controller_Controller
{

    public function route()
    {
        $this->showAction();
    }

    private function showAction()
    {
        $view = new WpTrivia_View_InfoAdaptation();

        $view->show();
    }
}