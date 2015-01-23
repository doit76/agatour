<?php

    use Phalcon\Mvc\Controller;

    class BlogController extends Controller{

        public function indexAction(){    
            if($this->dispatcher->getParam("page")){
                $currentPage = $this->dispatcher->getParam("page");}else{$currentPage = 1;}
            $paginator = new \Phalcon\Paginator\Adapter\Model(
                array("data" => Post::all(),"limit"=> 5,"page" => $currentPage));
            $page = $paginator->getPaginate();
            $this->view->pagination = 'blog';
            $this->view->page = $paginator->getPaginate();        
        }

        public function tagAction(){    
            if($this->dispatcher->getParam("page")){
                $currentPage = $this->dispatcher->getParam("page");}else{$currentPage = 1;}

            $paginator = new \Phalcon\Paginator\Adapter\Model(
                array("data" => Post::by_term_slug($this->dispatcher->getParam("tag")),"limit"=> 5,"page" => $currentPage));
            $this->view->pick("blog/index");
            $this->view->pagination = 'tag/'.$this->dispatcher->getParam("tag");
            $this->view->page = $paginator->getPaginate();
        }

        public function categoryAction(){    
            if($this->dispatcher->getParam("page")){
                $currentPage = $this->dispatcher->getParam("page");}else{$currentPage = 1;}
            $paginator = new \Phalcon\Paginator\Adapter\Model(
                array("data" => Post::by_term_slug($this->dispatcher->getParam("category")),"limit"=> 5,"page" => $currentPage));
            $this->view->pick("blog/index");
            $this->view->pagination = 'category/'.$this->dispatcher->getParam("category");
            $this->view->page = $paginator->getPaginate();
        }

        public function singleAction(){
            $slug = $this->dispatcher->getParam("slug");
            $post = Post::single($slug);
            $this->view->title = $post->post_title;
            if($post->has_image()){
                $this->view->image = $post->photo_url;
            }
            $this->view->description = $post->excerpt;
            $this->view->post = $post;
            
            $this->view->pick($post->single_format());
        }
    }