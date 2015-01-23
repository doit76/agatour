<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
        \Phalcon\Mvc\Model;

    class Tournament extends Model{

        public function getSource(){
            return "wp_posts";
        }
    
        public function afterFetch(){
            
            $this->date = date('d-M', strtotime($this->post_date));
            $this->full_date = date('Y-m-d', strtotime($this->post_date));
            $this->type = $this->get_type();
            $this->scoring = PostMeta::get($this->ID, 'type');

            $this->holes_played = $this->check_round();

            $this->completed = get_post_meta($this->ID, 'completed', TRUE );
            $this->winner = get_post_meta($this->ID, 'winner', TRUE );
            $this->score = get_post_meta($this->ID, 'score', TRUE );
            $this->points = get_post_meta($this->ID, 'points', TRUE );

            $this->playoff = get_post_meta($this->ID, 'playoff', TRUE );            

        }
        
        public function check_round(){
            $completed = get_post_meta($this->ID, 'completed', TRUE );
            if($completed){
                    foreach($this->get_rounds() as $r):
                        $scorecard = explode(" ", $r->scorecard);
                        $check = count($scorecard);
                        if($count > 0):
                            break;
                        endif;
                    endforeach;
            }else{
                $check = 18;
            }
            
            return $check;
        }

        public function all(){
                
            $date = date('Y-m-d', strtotime('+1 day'));
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT * FROM Tournament
            WHERE (Tournament.post_status = 'publish' OR Tournament.post_status = 'future')
            AND Tournament.post_type = 'tournament'
            AND Tournament.post_date <= '$date'
            ORDER BY Tournament.post_date ASC", $this->getDI());
            return $query->execute();
        }
        
        public function latest(){
                
            $date = date('Y-m-d');
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Tournament.* FROM Tournament
            LEFT JOIN TermRelationship
            ON Tournament.ID = TermRelationship.object_ID
            LEFT JOIN Term
            ON Term.term_id = TermRelationship.term_taxonomy_id
            WHERE (Term.slug = 'tour' OR Term.slug = 'exhibition' OR Term.slug = 'practice')
            AND (Tournament.post_status = 'publish')
            AND Tournament.post_type = 'tournament'
            AND Tournament.post_date <= '$date'
            ORDER BY Tournament.post_date DESC LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }
        
        public function single_by_id($tournamend_id){
                            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT * FROM Tournament
            WHERE Tournament.ID = $tournamend_id", $this->getDI());
            return $query->execute()[0];
        }
        
        
        public function by_type($type, $season){
                
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Tournament.* FROM Tournament
            LEFT JOIN TermRelationship
            ON Tournament.ID = TermRelationship.object_ID
            LEFT JOIN Term
            ON Term.term_id = TermRelationship.term_taxonomy_id
            WHERE Term.slug = '$type'
            AND (Tournament.post_status = 'publish' OR Tournament.post_status = 'future')
            AND Tournament.post_type = 'tournament'
            AND Tournament.post_date >= '$season->start'
            AND Tournament.post_date <= '$season->end'
            ORDER BY Tournament.post_date DESC", $this->getDI());
            return $query->execute();
        }
        
        public function single($slug){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Tournament WHERE post_name = '$slug' AND (Tournament.post_status = 'publish' OR Tournament.post_status = 'future') LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }

        public function get_course(){
            $course = get_post_meta($this->ID, 'course', TRUE );
            return Course::get_by_id($course[0]);
        }
        
        public function get_rounds(){
            return Round::get_by_tournament($this->ID);
        }
        
        public function get_type(){
            $meta_data = TermRelationship::get_terms($this->ID);
            foreach($meta_data as $meta){
                $term = Term::get_by_id($meta->term_taxonomy_id);
            }
            return $term->slug;
        }
        
        public function matchplay(){
            return get_post_meta($this->ID, 'matchplay', TRUE );
        }
        
        public function winner(){
            return get_post_meta($this->ID, 'winner', TRUE );
        }
    
        public function players(){
            return get_post_meta($this->ID, 'players', TRUE );
        }
        
        public function progress(){
            return get_post_meta($this->ID, 'progress', TRUE );
        }
    
        public function leader(){
            return get_post_meta($this->ID, 'leader', TRUE );
        }
        
        public function finished(){
            return get_post_meta($this->ID, 'finished', TRUE );
        }
                        
        /*--------------------------------------------
            GET TEAM NAME/COLOUR/PLAYERS
        --------------------------------------------*/
        public function get_team($number){
            
            $team_name = 'Team ' .$number;
            $players = get_post_meta($this->ID, 'team_'.$number, TRUE );
            $matchup = array();
            foreach($players as $player_id){
                $player = Player::single_by_id($player_id);
                $matchup[$player_id] = $player->post_title; 
                $player_ids[] = $player_id; 
            }            
        
            $team['name'] = PostMeta::get($this->ID, 'team_'.$number.'_name');
            $team['colour'] = get_post_meta($this->ID, 'team_'.$number.'_colour', TRUE );
            $team['players'] = $matchup;
            
            $total = 0;
        
            for($i = 1; $i <= 4; $i++){

                $fourball = get_post_meta($this->ID, 'fourball_' . $i, TRUE);
                $foursomes = get_post_meta($this->ID, 'foursomes_' . $i, TRUE);
                $matchup = get_post_meta($this->ID, 'headtohead_' . $i, TRUE);
 
                if($matchup){
                    foreach($matchup as $player_id){
                        if(in_array($player_id, $player_ids)){
                            $team['headtohead_' . $i] = $team['players'][$player_id];    
                        }
                    }
                }
                
                if($fourball){                        
                    foreach($fourball as $player_id){
                        if(in_array($player_id, $player_ids)){
                            $team['fourball_' . $i][] = $team['players'][$player_id];    
                        }
                    }
                    
                    $team['fourball_' . $i] = join('/', $team['fourball_' . $i]);
                }
                
                if($foursomes){
                    foreach($foursomes as $player_id){
                        if(in_array($player_id, $player_ids)){
                            $team['foursomes_' . $i][] = $team['players'][$player_id];    
                        }
                    }
                    $team['foursomes_' . $i] = join('/', $team['foursomes_' . $i]);
                }        
            }
               
            return $team;
        }        
        
        public function get_matchup_result(){
            
            $team_1 = get_post_meta($this->ID, 'team_1', TRUE );
            $team_2 = get_post_meta($this->ID, 'team_2', TRUE );
            
            $team_1_total = 0;
            $team_2_total = 0;

            $query = new Phalcon\Mvc\Model\Query(
            "SELECT meta2.meta_value as scorecard, 
            meta3.meta_value as player, 
            meta4.meta_value as matchup  
            FROM Round
            LEFT OUTER JOIN PostMeta as meta1
            ON Round.ID = meta1.post_id
            LEFT OUTER JOIN PostMeta as meta2
            ON Round.ID = meta2.post_id
            LEFT OUTER JOIN PostMeta as meta3
            ON Round.ID = meta3.post_id
            LEFT OUTER JOIN PostMeta as meta4
            ON Round.ID = meta4.post_id
            WHERE Round.post_type = 'round'
            AND (meta1.meta_key = 'tournament' AND meta1.meta_value = 'a:1:{i:0;s:4:\"$this->ID\";}')
            AND meta2.meta_key = 'scorecard'
            AND meta3.meta_key = 'player'
            AND meta4.meta_key = 'matchup'", $this->getDI());
            $rounds = $query->execute();
                        
            foreach($rounds as $round){
                
                $round->scorecard = explode(" ", $round->scorecard);
                $player = explode("\"", $round->player);
                $round->player = $player[1];
                
                for($i = 1; $i <= 4; $i++){                    
                    if($round->matchup == 'fourball ' . $i){
                        $matchup['fourball_' . $i][] = $round;
                    }

                    if($round->matchup == 'foursomes ' . $i){
                        $matchup['foursomes_' . $i][] = $round;
                    }

                    if($round->matchup == 'headtohead ' . $i){
                        $matchup['headtohead_' . $i][] = $round;
                    }
                }   
            }   
            
            for($i = 1; $i <= 4; $i++){         
                if($matchup['foursomes_' . $i]){
                    foreach($matchup['foursomes_' . $i] as $round){  
                        if(in_array($round->player, $team_1)){
                            $scorecard_1 = $round->scorecard;
                        }elseif(in_array($round->player, $team_2)){
                            $scorecard_2 = $round->scorecard;
                        }                   
                    }      
                    $matchup['foursomes_' . $i]['winner'] = $this->get_matchup_winner($scorecard_1, $scorecard_2);
                    
                    if($matchup['foursomes_' . $i]['winner']['team'] == 'Team 1'){
                        $team_1_total++;
                    }elseif($matchup['foursomes_' . $i]['winner']['team'] == 'Team 2'){
                        $team_2_total ++;
                    }elseif($matchup['foursomes_' . $i]['winner']['team'] == 'AS'){
                        $team_1_total += 0.5;
                        $team_2_total += 0.5;
                    }
                }
                
                if($matchup['fourball_' . $i]){
                    $scorecard_1_array = array();
                    $scorecard_2_array = array();        
                        
                    foreach($matchup['fourball_' . $i] as $round){  

                        if(in_array($round->player, $team_1)){
                            $scorecard_1_array[] = $round->scorecard;
                        }elseif(in_array($round->player, $team_2)){
                            $scorecard_2_array[] = $round->scorecard;
                        }                   
                    }
                    
                    for($hole = 1; $hole <= 18; $hole++){
                        
                        if($scorecard_1_array[2]){
                                $scorecard_1[$hole - 1] = min($scorecard_1_array[0][$hole-1], $scorecard_1_array[1][$hole-1], $scorecard_1_array[2][$hole-1]);
                        }elseif($scorecard_1_array[1]){
                                $scorecard_1[$hole - 1] = min($scorecard_1_array[0][$hole-1], $scorecard_1_array[1][$hole-1]);

                        }else{
                            $scorecard_1 = $scorecard_1_array[0];
                        }
                        
                        if($scorecard_2_array[1]){
                                $scorecard_2[$hole - 1] = min($scorecard_2_array[0][$hole-1], $scorecard_2_array[1][$hole-1]);

                        }else{
                            $scorecard_2 = $scorecard_2_array[0];
                        }     
                    }
                           
                    $matchup['fourball_' . $i]['winner'] = $this->get_matchup_winner($scorecard_1, $scorecard_2);

                    if($matchup['fourball_' . $i]['winner']['team'] == 'Team 1'){
                        $team_1_total++;
                    }elseif($matchup['fourball_' . $i]['winner']['team'] == 'Team 2'){
                        $team_2_total ++;
                    }elseif($matchup['fourball_' . $i]['winner']['team'] == 'AS'){
                        $team_1_total += 0.5;
                        $team_2_total += 0.5;
                    }
                }
                
                if($matchup['headtohead_' . $i]){
                    foreach($matchup['headtohead_' . $i] as $round){  
                        if(in_array($round->player, $team_1)){
                            $scorecard_1 = $round->scorecard;
                        }elseif(in_array($round->player, $team_2)){
                            $scorecard_2 = $round->scorecard;
                        }                   
                    }      
                    $matchup['headtohead_' . $i]['winner'] = $this->get_matchup_winner($scorecard_1, $scorecard_2);      
                    if($matchup['headtohead_' . $i]['winner']['team'] == 'Team 1'){
                        $team_1_total++;
                    }elseif($matchup['headtohead_' . $i]['winner']['team'] == 'Team 2'){
                        $team_2_total ++;
                    }elseif($matchup['headtohead_' . $i]['winner']['team'] == 'AS'){
                        $team_1_total += 0.5;
                        $team_2_total += 0.5;
                    }
                }
                                
            }   
            
            $matchup['team_1'] = $team_1_total;
            $matchup['team_2'] = $team_2_total; 

            if($this->type == 'cup'){
                if($matchup['team_1'] > 4){
                    update_post_meta($this->ID, 'winner', 'Team 1');
                    update_post_meta($this->ID, 'score', $matchup['team_1']);               
                }elseif($matchup['team_2'] > 4){
                    update_post_meta($this->ID, 'winner', 'Team 2');
                    update_post_meta($this->ID, 'score', $matchup['team_2']);               
                }elseif($matchup['team_1'] == 4 && $matchup['team_2'] == 4){
                    update_post_meta($this->ID, 'winner', 'Halved');
                    update_post_meta($this->ID, 'score', '-');               
                }else{
                    update_post_meta($this->ID, 'winner', 'Live');
                    update_post_meta($this->ID, 'score', $matchup['team_1'] . ' - ' . $matchup['team_2']);    
                }
            }
            
            return $matchup;
        }
        
        public function get_matchup_winner($scorecard_1, $scorecard_2){
        
            $team_1_score = 0;
            $team_2_score = 0;
            $done = FALSE;

            for($hole = 1; $hole <=18; $hole++){
                
                if(!isset($scorecard_1[$hole-1]) || !isset($scorecard_2[$hole-1])){
                    $holes_played = '(' . ($hole - 1) . ')';  
                    break;
                }
                
                if($scorecard_1[$hole-1] < $scorecard_2[$hole-1]){
                    $team_1_score++;
                }elseif($scorecard_1[$hole-1] > $scorecard_2[$hole-1]){
                    $team_2_score++; 
                }                 

                $total_score = abs($team_1_score - $team_2_score);
                $holes_to_go = 18 - $hole;

                if(($holes_to_go > 0) && ($holes_to_go < $total_score)){

                    if($team_1_score > $team_2_score){
                        $winner['team'] = 'Team 1';
                    }
                    elseif($team_1_score < $team_2_score){
                        $winner['team'] = 'Team 2';
                    }

                    $winner['score'] = $total_score . '&' . $holes_to_go;
                    $done = TRUE;
                    break;
                }
            }
            
            if(!$done){
                                
                $winner['score'] = $total_score . ' UP';
                
                if($team_1_score > $team_2_score){
                    $winner['team'] = 'Team 1';
                }
                elseif($team_1_score < $team_2_score){
                    $winner['team'] = 'Team 2';
                }elseif($team_1_score == $team_2_score){
                    $winner['team'] = 'AS';
                    $winner['score'] = '';
                }               
            }
            
            $winner['result'] = $winner['team'] . '<br>' . $winner['score'] . ' ' . (($holes_played) ? $holes_played : '');
            
            update_post_meta($this->ID, 'winner', $winner['team']);
            update_post_meta($this->ID, 'score', $winner['score']);

            if($scorecard_1[17] && $scorecard_2[17]){
                 update_post_meta($this->ID, 'completed', TRUE);
            }
            return $winner;
        }
        
        public function average_handicap(){
              
                $average = 0;
                $players_to_count = 0;
                $all_players = Player::all();
                foreach($all_players as $player){
                   $player_handicap = $player->handicap($this->full_date)['handicap'];
                    
                    if($player_handicap < 71){
                        $average += $player_handicap;
                        $players_to_count++;
                    }
                }
                
                if($players_to_count == 0){
                    $players_to_count = 1;
                }             
            return ($average / $players_to_count);
            
        }
    }