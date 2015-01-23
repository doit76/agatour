<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \Phalcon\Mvc\Model;

    class Season extends Model
    {

        public function getSource(){
            return "wp_posts";
        }
    
        public function afterFetch(){
            $this->start = PostMeta::get($this->ID, 'start');
            $this->end = PostMeta::get($this->ID, 'end'); 
            $this->updated = get_post_meta($this->ID, 'updated', TRUE); 
            $this->reset = get_post_meta($this->ID, 'reset_points', TRUE); 
        }
        
        public function get($date = NULL){
            if($date == NULL){
                $date = date('Y-m-d');
            }
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Season.* FROM Season
            LEFT OUTER JOIN PostMeta as meta1
            ON Season.ID = meta1.post_id
            LEFT OUTER JOIN PostMeta as meta2
            ON Season.ID = meta2.post_id
            WHERE Season.post_type = 'season'
            AND (meta1.meta_key = 'start' AND meta1.meta_value <= '$date')
            AND (meta2.meta_key = 'end' AND meta2.meta_value >= '$date')
            LIMIT 1", $this->getDI());
            
            $season = $query->execute()[0];
            $season->update_standings();
            $season->update_season_stats();
            return $season;
        }
        
        public function get_by_slug($slug){
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT * FROM Season
            WHERE post_type = 'season'
            AND post_name = '$slug'
            LIMIT 1", $this->getDI());
            $season = $query->execute()[0];
            $season->update_standings();
            $season->update_season_stats();
            return $season;
        }
        
        public function get_standings(){

            $events = $this->post_name . '_events';
            $wins = $this->post_name . '_wins';
            $top_2 = $this->post_name . '_top2';
            $points = $this->post_name . '_points';
            $position = $this->post_name . '_position';
            $playoff_points = $this->post_name . '_playoff_points';
            $playoff_position = $this->post_name . '_playoff_position';    
            
            if($this->reset){
               $order = 'ORDER BY meta7.meta_value ASC'; 
            }else{
               $order = 'ORDER BY meta2.meta_value ASC'; 
            }
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Player.post_title as name, 
            meta1.meta_value as points, 
            meta2.meta_value as position, 
            meta3.meta_value as wins, 
            meta4.meta_value as events, 
            meta5.meta_value as top2,    
            meta6.meta_value as playoff_points, 
            meta7.meta_value as playoff_position   
            FROM Player
            LEFT OUTER JOIN PostMeta as meta1
            ON Player.ID = meta1.post_id
            LEFT OUTER JOIN PostMeta as meta2
            ON Player.ID = meta2.post_id
            LEFT OUTER JOIN PostMeta as meta3
            ON Player.ID = meta3.post_id
            LEFT OUTER JOIN PostMeta as meta4
            ON Player.ID = meta4.post_id
            LEFT OUTER JOIN PostMeta as meta5
            ON Player.ID = meta5.post_id
            LEFT OUTER JOIN PostMeta as meta6
            ON Player.ID = meta6.post_id
            LEFT OUTER JOIN PostMeta as meta7
            ON Player.ID = meta7.post_id
            WHERE Player.post_type = 'player'
            AND Player.post_status = 'publish'
            AND meta1.meta_key = '$points'
            AND meta2.meta_key = '$position'
            AND meta3.meta_key = '$wins'
            AND meta4.meta_key = '$events'
            AND meta5.meta_key = '$top_2'
            AND meta6.meta_key = '$playoff_points'
            AND meta7.meta_key = '$playoff_position'
            $order", $this->getDI());
            return $query->execute();
        }
    
        public function update_standings(){
            if($this->updated < strtotime('- 7 days')){
                $players = Player::all();
                $points_array = array();
                $playoff_points_array = array();
                foreach($players as $player){
                    $top_2 = 0;
                    $events = 0;
                    $wins = 0;
                    $rounds = Round::by_player($player->ID, $this->end, $this->start, $this->post_name);
                    $points = 0;
                    $playoff_points = 0;
                    foreach($rounds as $round){
                        $round_details = Round::single($round->id);
                        $tournament_details = Tournament::single_by_id($round_details->tournament_id);
                        if($tournament_details->type == 'tour' && !$tournament_details->playoff){
                            $points += $round->points;    
                            $events++;

                            if($round->position <= 2){
                                if($round->position == 1){
                                    $wins++; 
                                }
                                $top_2++;   
                            }
                        }
                        if($tournament_details->playoff){
                            $playoff_points += $round->points;    
                            $events++;

                            if($round->position <= 2){
                                if($round->position == 1){
                                    $wins++; 
                                }
                                $top_2++;   
                            }
                        }
                    }
                    $playoff_points_array[$player->ID] = $playoff_points;    
                    $points_array[$player->ID] = $points;
                    update_post_meta($player->ID, $this->post_name . '_events', $events);
                    update_post_meta($player->ID, $this->post_name . '_wins', $wins);
                    update_post_meta($player->ID, $this->post_name . '_top2', $top_2);
                    update_post_meta($player->ID, $this->post_name . '_points', $points);
                }
                arsort($points_array);
                $tie = array_count_values($points_array);
                $position = 0;
                $last = 9999999;
                $winner = array();
                $reset_points_array = array(2500,2000,1500,1000,750,500,250,125,100,90,80,70);
                foreach($points_array as $player_id => $points){
                    $current_player = Player::single_by_id($player_id);
                    if($position == 0){
                        $position++;
                    }elseif($points != $last){
                        $position += $last_tie;
                    }
                    if($position == 1){                    
                        $winner[] = $current_player->post_title;
                    }
                    $reset_points = 0;
                    for($start = $position; $start < $position + $tie[$points]; $start++){
                        $reset_points +=  $reset_points_array[$start -1];
                    }
                    $total_reset_points = $playoff_points_array[$player_id] + $reset_points;
                    update_post_meta($player_id, $this->post_name . '_position', $position);
                    update_post_meta($player_id, $this->post_name . '_playoff_points', $total_reset_points);
                    $last = $points;
                    $last_tie = $tie[$points];
                    $playoff_points_array_final[$player_id] = $total_reset_points;
                }
                update_post_meta($this->ID, $this->post_name . '_winner', join(", ", $winner));
                arsort($playoff_points_array_final);
                $tie = array_count_values($playoff_points_array_final);
                $position = 0;
                $last = 9999999;
                $winner = array();
                foreach($playoff_points_array_final as $player_id => $points){
                    $current_player = Player::single_by_id($player_id);
                    if($position == 0)
                    {
                        $position++;
                    }elseif($points != $last){
                        $position += $last_tie;
                    }
                    if($position == 1){                    
                        $winner[] = $current_player->post_title;
                    }  
                    $last = $points;
                    $last_tie = $tie[$points];       
                    update_post_meta($player_id, $this->post_name . '_playoff_position', $position);
                }
                update_post_meta($this->ID, $this->post_name . '_playoff_winner', join(", ", $winner));
                update_post_meta($this->ID, 'updated', time());
            }
        } 
        
        public function get_rankings(){
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Player.post_title as name, 
            meta1.meta_value as points, 
            meta2.meta_value as position, 
            meta3.meta_value as wins, 
            meta4.meta_value as events, 
            meta5.meta_value as top2 
            FROM Player
            LEFT OUTER JOIN PostMeta as meta1
            ON Player.ID = meta1.post_id
            LEFT OUTER JOIN PostMeta as meta2
            ON Player.ID = meta2.post_id
            LEFT OUTER JOIN PostMeta as meta3
            ON Player.ID = meta3.post_id
            LEFT OUTER JOIN PostMeta as meta4
            ON Player.ID = meta4.post_id
            LEFT OUTER JOIN PostMeta as meta5
            ON Player.ID = meta5.post_id
            WHERE Player.post_type = 'player'
            AND Player.post_status = 'publish'
            AND meta1.meta_key = 'rank_points'
            AND meta2.meta_key = 'rank_position'
            AND meta3.meta_key = 'rank_wins'
            AND meta4.meta_key = 'rank_events'
            AND meta5.meta_key = 'rank_top2'
            ORDER BY meta2.meta_value ASC", $this->getDI());
            $rankings = $query->execute();
            $rank_array = array();
            
            foreach($rankings as $player){
                $rank_array[$player->position] = $player;
            }
            
            ksort($rank_array);
            
            return $rank_array;
        }
    
        
        public function update_rankings(){
            if($this->updated < strtotime('- 7 days')){
                $players = Player::all();
                $points_array = array();

                foreach($players as $player){
                    $top_2 = 0;
                    $events = 0;
                    $wins = 0;
                    $rounds = Round::by_player($player->ID, date('Y-m-d'), date('Y-m-d',strtotime('- 1 year')));
                    $points = 0;
                    foreach($rounds as $round){
                        $round_details = Round::single($round->id);
                        $tournament_details = Tournament::single_by_id($round_details->tournament_id);
                        if($tournament_details->type != 'practice'){
                           
                            
                        $days_since = date('U', strtotime($tournament_details->full_date))/60/60/24;
                        $days_ago = time()/60/60/24;
                        
                        $points += floor($round->points * ((365 - ($days_ago-$days_since)) / 365));
                            //$points += floor($round->points * ratio);    
                            $events++;

                            if($round->position <= 2){
                                if($round->position == 1){
                                    $wins++; 
                                }
                                $top_2++;   
                            }
                        }
                    }
                    $points_array[$player->ID] = (int) $points;
                    update_post_meta($player->ID, 'rank_events', $events);
                    update_post_meta($player->ID, 'rank_wins', $wins);
                    update_post_meta($player->ID, 'rank_top2', $top_2);
                    update_post_meta($player->ID, 'rank_points', $points);
                }
                arsort($points_array);
                $tie = array_count_values($points_array);
                $position = 0;
                $last = 9999999;
                $winner = array();
                foreach($points_array as $player_id => $points){
                    $current_player = Player::single_by_id($player_id);
                    if($position == 0){
                        $position++;
                    }elseif($points != $last){
                        $position += $last_tie;
                    }
                    if($position == 1){                    
                        $winner[] = $current_player->post_title;
                    }

                    update_post_meta($player_id, 'rank_position', $position);
                    $last = $points;
                    $last_tie = $tie[$points];
                }
            }
        } 
        
        
        public function update_season_stats(){
            if($this->updated < strtotime('- 7 days')){
                $players = Player::all();

                $year = date('Y', strtotime($this->start));
                foreach($players as $player){
                    $top_2 = 0;
                    $events = 0;
                    $wins = 0;
                    $rounds = Round::by_player($player->ID, $this->end, $this->start);
                    $points = 0;
                    foreach($rounds as $round){
                        $round_details = Round::single($round->id);
                        $tournament_details = Tournament::single_by_id($round_details->tournament_id);
                        if($tournament_details->type != 'practice'){
                        
                            $points += $round->points;
                            $events++;

                            if($round->position <= 2){
                                if($round->position == 1){
                                    $wins++; 
                                }
                                $top_2++;   
                            }
                        }
                    }
                    update_post_meta($player->ID, 'season_' . $year . '_events', $events);
                    update_post_meta($player->ID, 'season_' . $year . '_wins', $wins);
                    update_post_meta($player->ID, 'season_' . $year . '_top2', $top_2);
                    update_post_meta($player->ID, 'season_' . $year . '_points', $points);
                }
            }
        } 
    }