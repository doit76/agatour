<?php

    use Phalcon\Mvc\Controller;
    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class TourController extends Controller
    {

        public function indexAction()
        {         
            $season = Season::get_by_slug($this->dispatcher->getParam("championship"));
            $this->view->season = $season;
        }
        
        public function rankingsAction()
        {         
            $season = Season::get_by_slug('nandoscup'); 
            $season->update_rankings();
            $this->view->season = $season;
        }
    }