<?php

  use Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class Round extends \Phalcon\Mvc\Model
{

    public function getSource()
    {
        return "wp_posts";
    }
    
    public function afterFetch()
    {
        $this->tournament_id = $this->tournament_id();
        $this->player_id = $this->player_id();
        $this->course_id = $this->course_id();
        
        $this->updated = get_post_meta($this->ID, 'updated', TRUE );

        //$this->tournament = $this->get_tournament();

        $this->score = get_post_meta($this->ID, 'scorecard', TRUE );
        $this->strokes = $this->score();
        $this->adjusted = get_post_meta($this->ID, 'adjusted', TRUE );

        if($this->score('holes') == 9)
        {
            $this->total = sprintf("%+d",$this->score() - $this->get_course()->par / 2);
        }else
        {
            $this->total = sprintf("%+d",$this->score() - $this->get_course()->par);
        }

        $this->position = get_post_meta($this->ID, 'position', TRUE );
        $this->points = get_post_meta($this->ID, 'points', TRUE );

        $this->team = get_post_meta($this->ID, 'team', TRUE );
        $this->matchup = get_post_meta($this->ID, 'matchup', TRUE );

        $this->date = get_the_time( 'Y-m-d' , $this->tournament_id);
        //$this->tournament = Tournament::get_by_id(get_post_meta($this->ID, 'tournament', TRUE));
        //$this->player = get_post_meta($this->ID, 'player', TRUE );
        
    }
    
    public function score($return = 'strokes')
    {
        $score_array = explode(" ", $this->score);
        if($return == 'array')
        {
            return $score_array;
        }elseif($return == 'front'){
            return array_sum(array_slice($score_array, 0, 9));
        }elseif($return == 'back'){
            return array_sum(array_slice($score_array, 9, 9));
        }elseif($return == 'holes')
        {
            return count($score_array);
        }else{
            $strokes = array_sum($score_array);
            return $strokes;
        }
    }
    
    public function hole_status($return = 'value', $hole)
    {
        $status = $this->score('array')[$hole - 1] - $this->get_course()->scorecard('par', $hole);
        if($return == 'value')
        {
            return sprintf("%+d", $status);   
        }elseif($return == 'class')
        {
            if($status == -2)
            {
                $class = 'eagle';  
            }elseif($status == -1)
            {
                $class = 'birdie';  
            }elseif($status == 0)
            {
                $class = 'par';  
            }elseif($status == 1)
            {
                $class = 'bogey';  
            }elseif($status == 2)
            {
                $class = 'double-bogey';  
            }elseif($status >= 3)
            {
                $class = 'threeplus';  
            }
            
            return $class;
        }
        
    }
        
    public function tournament_id()
    { 
        $id_array = get_post_meta($this->ID, 'tournament', TRUE );
        return $id_array[0];    
    }
    
    public function player_id()
    { 
        $id_array = get_post_meta($this->ID, 'player', TRUE );
        return $id_array[0];    
    }
    
    public function all()
    {
      
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'round' 
               AND post_status = 'publish'";      
      
        $rounds = new Round();
        $result = new Resultset(null, $rounds, $rounds->getReadConnection()->query($sql)); 
        return $result;
    }
    
    public function season($season, $type)
    {
        $term_id = Term::id($type);
        $sql = "SELECT * FROM wp_posts as posts
                LEFT JOIN wp_term_relationships as terms 
                ON terms.object_id = posts.ID
                WHERE posts.post_type = 'tournament' 
                AND posts.post_status = 'publish'
                AND posts.post_date >= '$season->start'
                AND posts.post_date <= '$season->end'
                AND terms.term_taxonomy_id = '$term_id'
                ORDER BY post_date ASC";      
                
        $tournaments = new Tournament();
        $result = new Resultset(null, $tournaments, $tournaments->getReadConnection()->query($sql)); 
        
        return $result;
    }
    
    public function single($id)
    {
        $sql = "SELECT * FROM wp_posts
               WHERE ID = $id LIMIT 1";      
      
        $round = new Round();
        $result = new Resultset(null, $round, $round->getReadConnection()->query($sql)); 
        
        return $result[0];
    }    
    
    public function player()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE ID = $this->player_id";      
      
        $player = new Player();
        $result = new Resultset(null, $player, $player->getReadConnection()->query($sql)); 
        
        return $result[0];
    }   
    
    public function get_tournament()
    {   
        $sql = "SELECT * FROM wp_posts
               WHERE ID = '$this->tournament_id'";      
      
        $tournament = new Tournament();
        $result = new Resultset(null, $tournament, $tournament->getReadConnection()->query($sql));
        
        return $result[0];        
    }
    
    public function course_id()
    { 
        $id_array = get_post_meta($this->tournament_id, 'course', TRUE );
        return $id_array[0];    
    }
    
    public function get_course()
    {   
        $sql = "SELECT * FROM wp_posts
               WHERE ID = '$this->course_id'";      
      
        $course = new Course();
        $result = new Resultset(null, $course, $course->getReadConnection()->query($sql));
        
        return $result[0];        
    }
}
