<?php

    use Phalcon\Mvc\Controller;

    class IndexController extends Controller
    {

        public function indexAction(){
        }

        public function aboutAction(){
            $page = Page::get_by_slug('about');
            $this->view->pick("page/index");
            $this->view->title = $page->post_title;
            $this->view->page = $page;
        }

        public function contactAction(){
            $page = Page::get_by_slug('contact');
            $this->view->pick("page/index");
            $this->view->title = $page->post_title;
            $this->view->page = $page;
        }
        
        public function historyAction(){
            $page = Page::get_by_slug('history');
            $this->view->pick("page/index");
            $this->view->title = $page->post_title;
            $this->view->page = $page;
        }
        
        public function awardsAction(){
            $page = Page::get_by_slug('awards');
            $this->view->pick("page/index");
            $this->view->title = $page->post_title;
            $this->view->page = $page;
        }        

    }