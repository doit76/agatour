<?php

use Phalcon\Mvc\Controller;
  use Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class TournamentController extends Controller
{

    public function indexAction()
	{
        
        if($this->dispatcher->getParam("type")){
            $type = $this->dispatcher->getParam("type");
        }else{
            $type = 'tour';
        }
        
        if($this->dispatcher->getParam("year")){
            $year = $this->dispatcher->getParam("year") . '-07-01';
        }else{
            $year = date('Y-m-d', time());
        }
        
        $season = Season::get_by_year($year);
        $this->view->season = $season;
        $this->view->year = date('Y', strtotime($year));
        $this->view->type = $type;
        $this->view->tournaments = Tournament::season($season, $type);
        
	}
    
	public function singleAction()
	{
        $this->view->fullwidth = TRUE;
        $slug = $this->dispatcher->getParam("slug");
        $tournament = Tournament::single($slug);
        $this->view->single_title = $tournament->post_title;
        $this->view->tournament = $tournament;
	}

    public function updateallAction()
	{
        $term_id = Term::id('practice');
        $sql = "SELECT * FROM wp_posts as posts
                LEFT JOIN wp_term_relationships as terms 
                ON terms.object_id = posts.ID
                WHERE posts.post_type = 'tournament' 
                AND posts.post_status = 'publish'
                AND terms.term_taxonomy_id = '$term_id'
                ORDER BY post_date ASC";      
                
        $tournaments = new Tournament();
        $result = new Resultset(null, $tournaments, $tournaments->getReadConnection()->query($sql)); 

        foreach($result as $tournament)
        {
            if($tournament->date <= date('Y-m-d', time()))
            {
                echo $tournament->post_title . '<br>';
                $tournament->update_rounds();
                echo "done";
                update_post_meta($tournament->ID, 'updated', time());
            }
        }
        return $result;
	}
}