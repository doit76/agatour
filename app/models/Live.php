<?php

  use Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class Live extends \Phalcon\Mvc\Model
{

    public function getSource()
    {
        return "wp_posts";
    }
    
    public function afterFetch()
    {
        $this->tournament = get_post_meta($this->ID, 'tournament', TRUE );
    }
    
    
    /*
    public function standings()
    {
        $standings = array();
        foreach($this->players() as $id)
        {
            $standings[$id] = $this->player_score($id);
        }
        
        asort($standings);
        
        $ordered = array();
        
        foreach($standings as $id => $score)
        {
            $player_id = get_post_meta($this->ID, 'player_'.$id, TRUE );
            $player_info = Player::single_by_id($player_id[0]);
            $ordered[$id] = $player_info;
        }
        
        return $ordered;
    }*/
            
    public function players()
    {
        $players = array();
        
        for($i = 1; $i <= 2; $i++)
        {
            $player = get_post_meta($this->ID, 'player_'.$i, TRUE );
            if($player)
            {
                $players[] = $i;
            }
        }
        
        return $players;
    }
    
    
    public function player_score($id, $return = 'total')
    {
        $scorecard = explode(" ", get_post_meta($this->ID, 'player_'.$id.'_score', TRUE ));
        
        if($return == 'played')
        {
            return count($scorecard); 
        }else{
            return $scorecard; 
        }
    }
    
    public function get()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'live'
               AND post_status = 'publish'";      
      
        $live = new Live();
        $result = new Resultset(null, $live, $live->getReadConnection()->query($sql)); 
        
        return $result;
    }
}