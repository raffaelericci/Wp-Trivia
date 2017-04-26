<?php

class WpTrivia_Controller_WpqSupport extends WpTrivia_Controller_Controller
{

    public function route()
    {
        $this->showView();
    }

    private function showView()
    {
        $view = new WpTrivia_View_WpqSupport();

        $view->show();
    }
}