<?php

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View;

class AjaxController extends Controller 
{
	public function roundAction()
	{
      //$this->view->round = Round::findFirst($this->dispatcher->getParam("id"));
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
	}
}
