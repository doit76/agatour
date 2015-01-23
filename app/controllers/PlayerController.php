<?php

    use Phalcon\Mvc\Controller;

    class PlayerController extends Controller
    {

        public function indexAction(){
            $players = Player::all();
            $this->view->fullwidth = TRUE;
            $this->view->title = 'Players'; 
            $this->view->players = $players;
        }

        public function singleAction(){
            $slug = $this->dispatcher->getParam("slug");
            $player = Player::single($slug);
            $this->view->title = $player->post_title;
            $this->view->image = $player->photo_url;
            $this->view->description = strip_tags($player->post_content);
            $this->view->player = $player;
        }

    }