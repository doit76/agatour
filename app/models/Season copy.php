<?php

  use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
  \Phalcon\Mvc\Model;

class Season extends Model
{

    public function getSource()
    {
        return "wp_posts";
    }
    
    public function afterFetch()
    {
        $this->start = get_post_meta($this->ID, 'start', TRUE );
        $this->end = get_post_meta($this->ID, 'end', TRUE );
        $this->current = get_post_meta($this->ID, 'current', TRUE );
        $this->reset = get_post_meta($this->ID, 'reset_points', TRUE );
        $this->updated = get_post_meta($this->ID, 'updated', TRUE );
    }

    public function all()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'season' 
               AND post_status = 'publish'
               ORDER BY post_date DESC";      
      
        $season = new Season();
        $result = new Resultset(null, $season, $season->getReadConnection()->query($sql)); 
        
        return $result;
    }

    public function get_by_year($year)
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'season' 
               AND post_status = 'publish'";
        
        $seasons = new Season();
        $result = new Resultset(null, $seasons, $seasons->getReadConnection()->query($sql)); 
        
        foreach($result as $season)
        {
            if($season->start <= $year && $season->end >= $year)
            {
                return $season;
            }
        }
        
    }
    
    public function get_by_slug($slug)
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'season' 
               AND post_name = '$slug'";      
      
        $season = new Season();
        $result = new Resultset(null, $season, $season->getReadConnection()->query($sql)); 
        
        return $result[0];
    }
    
    public function current()
    {
        foreach(Season::all() as $season){
            if($season->current == TRUE){
                return $season;
            }        
        }
    }
    
    public function get_standings()
    {

        if($this->updated < strtotime('-7 days'))
        {
            $this->update_points();
        } 
        
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'player' 
               AND post_status = 'publish'";      
      
        $players = new Player();
        $result = new Resultset(null, $players, $players->getReadConnection()->query($sql));
        
        $points_array = array();
        
        foreach($result as $player)
        {
            if($player->debut <= $this->end)
            {
                if($this->reset)
                {
                    $points_array[$player->ID] = $player->cup_stats($this)->reset_points;
                }else
                {
                    $points_array[$player->ID] = $player->cup_stats($this)->points;
                }
            }
        }
        
        arsort($points_array);
        
        $cup_standings = array();                              
        foreach($points_array as $id => $points)
        {
            $player = Player::single_by_id($id);
            $cup_standings[] = $player;
        }
        
        return $cup_standings;
    }
        
    public function update_points()
    {
        
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'player' 
               AND post_status = 'publish'";      
      
        $players = new Player();
        $result = new Resultset(null, $players, $players->getReadConnection()->query($sql));
        
        $point_array = array();
        $reset_points_array = array();
        
        foreach($result as $player)
        {
            if($player->debut <= $this->end)
            {
            $events = 0;
            $points = 0;
            $wins = 0;
            $top2 = 0;
            
            foreach($player->get_rounds() as $round)
            {
                $check = FALSE;
                $check_playoff = FALSE;
                if($round->date >= $this->start && $round->date <= $this->end)
                {
                    $type = wp_get_post_terms( $round->tournament_id, 'type' );
                    
                    foreach($type as $tournament_type)
                    {
                        if($tournament_type->slug == 'tour')
                        {
                            $check = TRUE; 
                        }
                        if($tournament_type->slug == 'playoff')
                        {
                            $check_playoff = TRUE; 
                        }
                    }
                    
                    if($check)
                    {
                        if($round->position == 1)
                        {
                            $wins++;   
                        }
                        
                        if($round->position <= 2)
                        {
                            $top2++;   
                        }                        
                        
                        $events ++;
                        
                        if($check_playoff)
                        {
                            $reset_points_array[$player->ID] = $round->points;
                        }else{
                            $points += $round->points;
                        }
                    }
                }
            }
            
            if($events == 0)
            {
                $events = '-';  
            }

            if($wins == 0)
            {
                $wins = '-';  
            }

            if($top2 == 0)
            {
                $top2 = '-';  
            }
            
            if($player->debut <= $this->end)
            {
                $point_array[$player->ID] = $points;
                update_post_meta($player->ID, $this->post_name . '_events', $events);
                update_post_meta($player->ID, $this->post_name . '_wins', $wins);
                update_post_meta($player->ID, $this->post_name . '_top2', $top2);
            }
        }
        }
        arsort($point_array);

        $tie = array_count_values($point_array);

        $position = 0;
        $last = 9999999;

        $reset_allocation = array(2500,2000,1500,1000,750,500,300,190,135,110,100,90,85,80,75,70,65);
        
        foreach($point_array as $id => $points)
        {
            if($position == 0)
            {
                $position++;
            }elseif($points != $last){
                $position += $last_tie;
            }
            
            $aggregate_points = 0;
            
            if($tie[$points] == 1)
            {
                $reset_points = $reset_points_array[$id] + $reset_allocation[$position - 1];
            }else{
                for($p = $position; $p < $position + $tie[$points]; $p++)
                {
                    $aggregate_points += $reset_allocation[$p - 1];
                }
                $reset_points = $reset_points_array[$id] + floor($aggregate_points/$tie[$points]);
            }
                
            
            if($position == 1)
            {
            update_post_meta($this->ID, 'winner', $id);
            }
            
            update_post_meta($id, $this->post_name . '_points', $points);
            update_post_meta($id, $this->post_name . '_reset_points', $reset_points);
            update_post_meta($id, $this->post_name . '_position', $position);
            
            $last = $points;
            $last_tie = $tie[$points];
        }
        
        $reset_points_position = array();
        
        foreach($result as $player)
        {
            $reset_points_position[$player->ID] = $player->cup_stats($this)->reset_points;   
        }
        
        
        arsort($reset_points_position);
        $tie = array_count_values($reset_points_position);
             
        $position = 0;
        $last = 9999999;
        
        foreach($reset_points_position as $id => $points)
        {
            if($position == 0)
            {
                $position++;
            }elseif($points != $last){
                $position += $last_tie;
            }
                                    
            update_post_meta($id, $this->post_name . '_reset_position', $position);
            
            $last = $points;
            $last_tie = $tie[$points];
        }
        update_post_meta($this->ID, 'updated', time());               
    }
    
    public function get_rankings()
    {
        
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'player' 
               AND post_status = 'publish'";      
      
        $players = new Player();
        $result = new Resultset(null, $players, $players->getReadConnection()->query($sql));

        if($result[0]->rankings_updated <= strtotime('+5 days'))
        {
            $this->update_rankings();     
        }
        
        $points_array = array();
        
        foreach($result as $player)
        {
            $points_array[$player->ID] = $player->current_points;
        }
        
        arsort($points_array);
        
        $rankings = array();                              
        foreach($points_array as $id => $points)
        {
            $player = Player::single_by_id($id);
            $rankings[] = $player;
        }
        
        return $rankings;
    }
    
    public function update_rankings()
    {
        
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'player' 
               AND post_status = 'publish'";      
      
        $players = new Player();
        $result = new Resultset(null, $players, $players->getReadConnection()->query($sql));
        
        $point_array = array();
        
        foreach($result as $player)
        {
            $events = 0;
            $points = 0;
            $wins = 0;
            $top2 = 0;
            
            foreach($player->get_rounds() as $round)
            {

                $check = FALSE;

                if(date('U', strtotime($round->date)) >= strtotime('-1 year') && date('U', strtotime($round->date)) <= time())
                {


                    
                    $type = wp_get_post_terms( $round->tournament_id, 'type' );
                    
                    foreach($type as $tournament_type)
                    {
                        if($tournament_type->slug == 'tour' || $tournament_type->slug == 'exhibition')
                        {
                            $check = TRUE; 
                        }
                    }
                    
                    if($check && $round->updated)
                    {
                        if($round->position == 1)
                        {
                            $wins++;   
                        }
                        
                        if($round->position <= 2)
                        {
                            $top2++;   
                        }                        
                        
                        $events ++;
                        
                        $days_since = date('U', strtotime($round->date))/60/60/24;
                        $days_ago = time()/60/60/24;
                        
                        $points += $round->points * ((365 - ($days_ago-$days_since)) / 365);
                    }
                }
            }
            
            if($events == 0)
            {
                $events = '-';  
            }

            if($wins == 0)
            {
                $wins = '-';  
            }

            if($top2 == 0)
            {
                $top2 = '-';  
            }
                         
            $points = floor($points);
            $point_array[$player->ID] = (int) $points;
            update_post_meta($player->ID, 'current_events', $events);
            update_post_meta($player->ID, 'current_wins', $wins);
            update_post_meta($player->ID, 'current_top2', $top2);


        }
        arsort($point_array);
        $tie = array_count_values($point_array);
             
        $position = 0;
        $last = 9999999;
        
        foreach($point_array as $id => $points)
        {
            if($position == 0)
            {
                $position++;
            }elseif($points != $last){
                $position += $last_tie;
            }
                                    
            update_post_meta($id, 'current_points', $points);
            update_post_meta($id, 'current_position', $position);
            
            $last = $points;
            $last_tie = $tie[$points];
            update_post_meta($id, 'rankings_update', time());  
        }
    }
}
