<?php

use Phalcon\Mvc\Controller;

class CourseController extends Controller
{

    public function indexAction()
	{        
        $this->view->courses = Course::get();
	}
    

	public function singleAction()
	{
        $slug = $this->dispatcher->getParam("slug");
        $this->view->fullwidth = TRUE;
        $this->view->course = Course::single($slug);
	}

}