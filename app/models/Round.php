<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class Round extends \Phalcon\Mvc\Model
    {

        public function getSource(){
            return "wp_posts";
        }
        
        public function afterFetch(){
            $this->player_id = $this->player_id();
            $this->tournament_id = $this->tournament_id();

            $this->position = get_post_meta($this->ID, 'position', TRUE );
            $this->points = get_post_meta($this->ID, 'points', TRUE );
            $this->scorecard = get_post_meta($this->ID, 'scorecard', TRUE );
            $this->strokes = array_sum(explode(" ", get_post_meta($this->ID, 'scorecard', TRUE )));
            $this->total = get_post_meta($this->ID, 'total', TRUE);
            $this->adjusted = get_post_meta($this->ID, 'adjusted', TRUE);
            $this->differential = get_post_meta($this->ID, 'differential', TRUE);
            $this->official = get_post_meta($this->ID, 'official', TRUE);
            
            $this->matchup = get_post_meta($this->ID, 'matchup', TRUE);

        }

        public function single($round_id){
            $query = new Phalcon\Mvc\Model\Query("
            SELECT * FROM Round 
            WHERE ID = '$round_id'
            LIMIT 1", $this->getDI());
            return $query->execute()[0];
        } 

        public function tournament_id(){ 
            $id_array = get_post_meta($this->ID, 'tournament', TRUE );
            return $id_array[0];    
        }
 
        
        public function tournament(){ 
            return Tournament::single_by_id($this->tournament_id);
        }
        
        
        public function player_id(){ 
            $id_array = get_post_meta($this->ID, 'player', TRUE );
            return $id_array[0];    
        }
        
        public function player(){
            $query = new Phalcon\Mvc\Model\Query("
            SELECT * FROM Player 
            WHERE ID = '$this->player_id'
            LIMIT 1", $this->getDI());
            return $query->execute()[0];
        } 

        public function player_update($player_id){
                       
            $current_p = Player::single_by_id($player_id);
            
            $start = date('Y-m-d', $current_p->rounds_updated);
                            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Round.* FROM Round
            LEFT JOIN PostMeta as meta1
            ON Round.ID = meta1.post_id
            LEFT JOIN PostMeta as meta2
            ON Round.ID = meta2.post_id
            WHERE Round.post_type = 'round' 
            AND Round.post_status = 'publish' 
            AND meta1.meta_key = 'player'
            AND meta2.meta_key = 'date'
            AND meta2.meta_value > '$start'
            AND (meta1.meta_value = 'a:1:{i:0;s:1:\"$player_id\";}'
            OR meta1.meta_value = 'a:1:{i:0;s:2:\"$player_id\";}'
            OR meta1.meta_value = 'a:1:{i:0;s:3:\"$player_id\";}' 
            OR meta1.meta_value = 'a:1:{i:0;s:4:\"$player_id\";}' )
            ", $this->getDI());

            $rounds = $query->execute();
        
            
            $all_rounds = $current_p->all_rounds();
            $scores = array();
            
            foreach($rounds as $round){ 
                
                if($round->tournament()->holes_played == 18 && $round->tournament()->scoring == 'stroke'){  
                    
                    $deets = $round->tournament()->get_course();
                    
                    $slope = $deets->slope;
                    $scratch = $deets->scratch;
                                        
                    $score = array_sum(explode(" ", $round->scorecard));
                    
                    $differential = ($score - $scratch) * (113/$slope);
                    $differential = sprintf("%.1f", $differential);
                    
                    if($differential > 40){
                        $differential = 40;
                    }
                                        
                    $scores[] = $differential;
                    
                    $all_rounds[$round->ID]['differential'] = $differential;
                    $all_rounds[$round->ID]['date'] = $round->tournament()->full_date;
                    
                    echo $round->tournament()->post_title . ' ADDED<br>';
                }
            }
            
            update_post_meta($player_id, 'all_rounds', $all_rounds);
            update_post_meta($player_id, 'rounds_updated', time());
            
            return TRUE;
        }
        
        
        public function by_player_insert($player_id, $end = NULL, $start = NULL, $season = NULL, $return = NULL){
 
            if($start == NULL){
                $start = date('Y-m-d', strtotime('1990-01-01'));
            }
            if($end == NULL){
                $end = date('Y-m-d');
            }  
                                    
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Round.ID as id, 
            meta1.meta_value as date, 
            meta2.meta_value as total, 
            meta4.meta_value as holes_played, 
            meta5.meta_value as points, 
            meta6.meta_value as position,
            meta7.meta_value as scorecard FROM Round
            LEFT JOIN PostMeta as meta1
            ON meta1.post_id = Round.ID
            LEFT JOIN PostMeta as meta2
            ON meta2.post_id = Round.ID
            LEFT JOIN PostMeta as meta3
            ON meta3.post_id = Round.ID
            LEFT JOIN PostMeta as meta4
            ON meta4.post_id = Round.ID
            LEFT JOIN PostMeta as meta5
            ON meta5.post_id = Round.ID
            LEFT JOIN PostMeta as meta6
            ON meta6.post_id = Round.ID
            LEFT JOIN PostMeta as meta7
            ON meta7.post_id = Round.ID
            WHERE meta1.meta_key = 'date' 
            AND (meta1.meta_value >= '$start' AND meta1.meta_value < '$end')
            AND meta2.meta_key = 'total'
            AND meta3.meta_key = 'player' 
            AND (
            meta3.meta_value = 'a:1:{i:0;s:1:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:4:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:2:\"$player_id\";}'
            )
            AND meta4.meta_key = 'holes_played'
            AND meta5.meta_key = 'points'
            AND meta6.meta_key = 'position'
            AND meta7.meta_key = 'scorecard'
            ORDER BY meta1.meta_value ASC", $this->getDI());
            $rounds = $query->execute();
            
            $all_rounds = array();
            
            foreach($rounds as $round){
                
                $all_rounds[] = $round;
                
            }
            
            update_post_meta($player_id, 'all_rounds', $all_rounds);
            
        }
        
        public function by_player_20($player_id, $end = NULL, $start = NULL, $season = NULL, $return = NULL){
 
            if($start == NULL){
                $start = date('Y-m-d', strtotime('1990-01-01'));
            }
            if($end == NULL){
                $end = date('Y-m-d');
            }  
                                    
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Round.ID as id, 
            meta1.meta_value as date, 
            meta2.meta_value as total, 
            meta4.meta_value as holes_played, 
            meta5.meta_value as points, 
            meta6.meta_value as position,
            meta7.meta_value as scorecard, 
            meta8.meta_value as differential 
            FROM Round
            LEFT JOIN PostMeta as meta1
            ON meta1.post_id = Round.ID
            LEFT JOIN PostMeta as meta2
            ON meta2.post_id = Round.ID
            LEFT JOIN PostMeta as meta3
            ON meta3.post_id = Round.ID
            LEFT JOIN PostMeta as meta4
            ON meta4.post_id = Round.ID
            LEFT JOIN PostMeta as meta5
            ON meta5.post_id = Round.ID
            LEFT JOIN PostMeta as meta6
            ON meta6.post_id = Round.ID
            LEFT JOIN PostMeta as meta7
            ON meta7.post_id = Round.ID
            LEFT JOIN PostMeta as meta8
            ON meta8.post_id = Round.ID
            WHERE meta1.meta_key = 'date' 
            AND (meta1.meta_value >= '$start' AND meta1.meta_value < '$end')
            AND meta2.meta_key = 'total'
            AND meta3.meta_key = 'player' 
            AND (
            meta3.meta_value = 'a:1:{i:0;s:1:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:4:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:2:\"$player_id\";}'
            )
            AND meta4.meta_key = 'holes_played'
            AND meta5.meta_key = 'points'
            AND meta6.meta_key = 'position'
            AND meta7.meta_key = 'scorecard'
            AND meta8.meta_key = 'differential'
            ORDER BY meta1.meta_value DESC", $this->getDI());
            $rounds = $query->execute();

            if($return){
                
                    $scores = array('eagle', 'birdie', 'par', 'bogey', 'double-bogey', 'threeplus');
                    $times = array('month', 'half', 'alltime');
                    $course_array = array();

                    for($i = 3; $i <= 5; $i++){
                        foreach($scores as $score){
                            foreach($times as $time){
                                $made['par_'.$i][$time][$score] = 0;
                                $made['par_total_'.$i][$time] = 0;
                                $made[$time]['alltime_holes'] = 0;
                                $made[$time]['alltime_birdies'] = 0;
                                $made[$time]['alltime_pars'] = 0;
                                $made[$time]['alltime_meltdowns'] = 0;
                            }
                        }       
                    }
                
                    for($i = 100; $i <= 600; $i+=100){
                        for($par = 3; $par <= 5; $par++){
                            foreach($times as $time){
                                $made['distance_'.$i.'_'.$par][$time][] = array();
                            }
                        }
                    }
                
                    for($i = 0; $i <= 100; $i+=25){
                        $home['distance_'.$i] = array();
                    }
                
                    foreach($rounds as $round){
                            $current_round = Round::single($round->id);
                            $course_array[] = $current_round->tournament()->get_course()->ID;
                        
                        $distance = $current_round->tournament()->get_course()->distance();
                        
                        if($distance <= 10){
                            $home['distance_0'][] = $round->total;
                        }elseif($distance <= 25){
                            $home['distance_25'][] = $round->total;
                        }elseif($distance <= 50){
                            $home['distance_50'][] = $round->total;
                        }elseif($distance <= 75){
                            $home['distance_75'][] = $round->total;
                        }elseif($distance > 75){
                            $home['distance_100'][] = $round->total;
                        }
                        
                        
                        for($hole = 1; $hole <= 18; $hole++){
                            $hole_score = $current_round->score('array')[$hole - 1];
                            
                            if($hole_score && $hole_score > 0){
                                $par = $current_round->tournament()->get_course()->scorecard('par', $hole);
                                
                                $distance = $current_round->tournament()->get_course()->scorecard('distance', $hole);
                                
                                $status = $hole_score - $par;
                                
                                $times_count = array();
                                
                                if(date('Y-m-d', strtotime('-1 month')) <= $round->date){
                                    $times_count[] = 'month';   
                                }
                                
                                if(date('Y-m-d', strtotime('-6 months')) <= $round->date){
                                    $times_count[] = 'half';   
                                }     
                                    $times_count[] = 'alltime';   
                                
                                
                                foreach($times_count as $time){
                                    
                                    $made[$time]['alltime_holes']++;  

                                    
                                    if($distance > 0 && $distance <= 150){
                                        $made['distance_100_'.$par][$time][] = $hole_score;     
                                    }elseif($distance > 150 && $distance <= 250){
                                        $made['distance_200_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 250 && $distance <= 350){
                                        $made['distance_300_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 350 && $distance <= 450){
                                        $made['distance_400_'.$par][$time][] = $hole_score;   
                                    }elseif($distance > 450 && $distance <= 550){
                                        $made['distance_500_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 550 && $distance <= 650){
                                        $made['distance_600_'.$par][$time][] = $hole_score;     
                                    }
                                    
                                    if($status == -2){
                                        $made['par_'.$par][$time]['eagle']++; 
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == -1){
                                        $made[$time]['alltime_birdies']++;  
                                        $made['par_'.$par][$time]['birdie']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 0){
                                        $made[$time]['alltime_pars']++;  
                                        $made['par_'.$par][$time]['par']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 1){
                                        $made['par_'.$par][$time]['bogey']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 2){
                                        $made['par_'.$par][$time]['double-bogey']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status >= 3){
                                        if($status >= 4){
                                            $made[$time]['alltime_meltdowns']++;  
                                        }
                                        $made['par_'.$par][$time]['threeplus']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }  
                                }
                            }
                        }
                    }
                
                    for($i = 3; $i <= 5; $i++){
                        foreach($scores as $score){
                            foreach($times as $time){
                                $made_count = $made['par_'.$i][$time][$score];
                                $total_count = $made['par_total_'.$i][$time];
                                if($total_count>0){
                                    $made['par_percentage_'.$i][$time][$score] = round( ($made_count/$total_count) * 100, 1);
                                }else{
                                    $made['par_percentage_'.$i][$time][$score] = '-';
                                }
                            }
                        }       
                    }
                                
                    for($i = 100; $i <= 600; $i+=100){
                        for($par = 3; $par <= 5; $par++){
                            foreach($times as $time){
                                $scoring_array = array_filter($made['distance_'.$i.'_'.$par][$time]);
                                if($scoring_array != NULL){
                                    $made['distance_average_'.$i.'_'.$par][$time] = round(array_sum($scoring_array) / count($scoring_array), 1);
                                }else{
                                 $made['distance_average_'.$i.'_'.$par][$time] = '-';
                                }
                            }
                        }
                    }
                
                
                    $courses_played = count(array_unique($course_array));
                    
                
                    for($i = 0; $i <= 100; $i+=25){
                        if($home['distance_'.$i] > 0){
                            $home['distance_average_'.$i] = round((array_sum($home['distance_'.$i]) / count($home['distance_'.$i])), 1);                
                        }else{
                            $home['distance_average_'.$i] = '-';
                        }
                    }
                
                
                    update_post_meta($player_id, 'distance_stats', $home);
                    update_post_meta($player_id, 'courses_played', $courses_played);
                    update_post_meta($player_id, 'made_stats', $made);
                    update_post_meta($player_id, 'stats_updated', time()); 
                
                return $made;
            }else{
                return $rounds;
            }
        }
        
        public function by_player($player_id, $end = NULL, $start = NULL, $season = NULL, $return = NULL){
 
            if($start == NULL){
                $start = date('Y-m-d', strtotime('1990-01-01'));
            }
            if($end == NULL){
                $end = date('Y-m-d');
            }  
                                    
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Round.ID as id, 
            meta1.meta_value as date, 
            meta2.meta_value as total, 
            meta4.meta_value as holes_played, 
            meta5.meta_value as points, 
            meta6.meta_value as position,
            meta7.meta_value as scorecard FROM Round
            LEFT JOIN PostMeta as meta1
            ON meta1.post_id = Round.ID
            LEFT JOIN PostMeta as meta2
            ON meta2.post_id = Round.ID
            LEFT JOIN PostMeta as meta3
            ON meta3.post_id = Round.ID
            LEFT JOIN PostMeta as meta4
            ON meta4.post_id = Round.ID
            LEFT JOIN PostMeta as meta5
            ON meta5.post_id = Round.ID
            LEFT JOIN PostMeta as meta6
            ON meta6.post_id = Round.ID
            LEFT JOIN PostMeta as meta7
            ON meta7.post_id = Round.ID
            WHERE meta1.meta_key = 'date' 
            AND (meta1.meta_value >= '$start' AND meta1.meta_value < '$end')
            AND meta2.meta_key = 'total'
            AND meta3.meta_key = 'player' 
            AND (
            meta3.meta_value = 'a:1:{i:0;s:1:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:4:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:2:\"$player_id\";}'
            )
            AND meta4.meta_key = 'holes_played'
            AND meta5.meta_key = 'points'
            AND meta6.meta_key = 'position'
            AND meta7.meta_key = 'scorecard'
            ORDER BY meta1.meta_value ASC", $this->getDI());
            $rounds = $query->execute();

            if($return){
                
                    $scores = array('eagle', 'birdie', 'par', 'bogey', 'double-bogey', 'threeplus');
                    $times = array('month', 'half', 'alltime');
                    $course_array = array();

                    for($i = 3; $i <= 5; $i++){
                        foreach($scores as $score){
                            foreach($times as $time){
                                $made['par_'.$i][$time][$score] = 0;
                                $made['par_total_'.$i][$time] = 0;
                                $made[$time]['alltime_holes'] = 0;
                                $made[$time]['alltime_birdies'] = 0;
                                $made[$time]['alltime_pars'] = 0;
                                $made[$time]['alltime_meltdowns'] = 0;
                            }
                        }       
                    }
                
                    for($i = 100; $i <= 600; $i+=100){
                        for($par = 3; $par <= 5; $par++){
                            foreach($times as $time){
                                $made['distance_'.$i.'_'.$par][$time][] = array();
                            }
                        }
                    }
                
                    for($i = 0; $i <= 100; $i+=25){
                        $home['distance_'.$i] = array();
                    }
                
                    foreach($rounds as $round){
                            $current_round = Round::single($round->id);
                            $course_array[] = $current_round->tournament()->get_course()->ID;
                        
                        $distance = $current_round->tournament()->get_course()->distance();
                        
                        if($distance <= 10){
                            $home['distance_0'][] = $round->total;
                        }elseif($distance <= 25){
                            $home['distance_25'][] = $round->total;
                        }elseif($distance <= 50){
                            $home['distance_50'][] = $round->total;
                        }elseif($distance <= 75){
                            $home['distance_75'][] = $round->total;
                        }elseif($distance > 75){
                            $home['distance_100'][] = $round->total;
                        }
                        
                        
                        for($hole = 1; $hole <= 18; $hole++){
                            $hole_score = $current_round->score('array')[$hole - 1];
                            
                            if($hole_score && $hole_score > 0){
                                $par = $current_round->tournament()->get_course()->scorecard('par', $hole);
                                
                                $distance = $current_round->tournament()->get_course()->scorecard('distance', $hole);
                                
                                $status = $hole_score - $par;
                                
                                $times_count = array();
                                
                                if(date('Y-m-d', strtotime('-1 month')) <= $round->date){
                                    $times_count[] = 'month';   
                                }
                                
                                if(date('Y-m-d', strtotime('-6 months')) <= $round->date){
                                    $times_count[] = 'half';   
                                }     
                                    $times_count[] = 'alltime';   
                                
                                
                                foreach($times_count as $time){
                                    
                                    $made[$time]['alltime_holes']++;  

                                    
                                    if($distance > 0 && $distance <= 150){
                                        $made['distance_100_'.$par][$time][] = $hole_score;     
                                    }elseif($distance > 150 && $distance <= 250){
                                        $made['distance_200_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 250 && $distance <= 350){
                                        $made['distance_300_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 350 && $distance <= 450){
                                        $made['distance_400_'.$par][$time][] = $hole_score;   
                                    }elseif($distance > 450 && $distance <= 550){
                                        $made['distance_500_'.$par][$time][] = $hole_score;    
                                    }elseif($distance > 550 && $distance <= 650){
                                        $made['distance_600_'.$par][$time][] = $hole_score;     
                                    }
                                    
                                    if($status == -2){
                                        $made['par_'.$par][$time]['eagle']++; 
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == -1){
                                        $made[$time]['alltime_birdies']++;  
                                        $made['par_'.$par][$time]['birdie']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 0){
                                        $made[$time]['alltime_pars']++;  
                                        $made['par_'.$par][$time]['par']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 1){
                                        $made['par_'.$par][$time]['bogey']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status == 2){
                                        $made['par_'.$par][$time]['double-bogey']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }elseif($status >= 3){
                                        if($status >= 4){
                                            $made[$time]['alltime_meltdowns']++;  
                                        }
                                        $made['par_'.$par][$time]['threeplus']++;  
                                        $made['par_total_'.$par][$time]++;
                                    }  
                                }
                            }
                        }
                    }
                
                    for($i = 3; $i <= 5; $i++){
                        foreach($scores as $score){
                            foreach($times as $time){
                                $made_count = $made['par_'.$i][$time][$score];
                                $total_count = $made['par_total_'.$i][$time];
                                if($total_count>0){
                                    $made['par_percentage_'.$i][$time][$score] = round( ($made_count/$total_count) * 100, 1);
                                }else{
                                    $made['par_percentage_'.$i][$time][$score] = '-';
                                }
                            }
                        }       
                    }
                                
                    for($i = 100; $i <= 600; $i+=100){
                        for($par = 3; $par <= 5; $par++){
                            foreach($times as $time){
                                $scoring_array = array_filter($made['distance_'.$i.'_'.$par][$time]);
                                if($scoring_array != NULL){
                                    $made['distance_average_'.$i.'_'.$par][$time] = round(array_sum($scoring_array) / count($scoring_array), 1);
                                }else{
                                 $made['distance_average_'.$i.'_'.$par][$time] = '-';
                                }
                            }
                        }
                    }
                
                
                    $courses_played = count(array_unique($course_array));
                    
                
                    for($i = 0; $i <= 100; $i+=25){
                        if($home['distance_'.$i] > 0){
                            $home['distance_average_'.$i] = round((array_sum($home['distance_'.$i]) / count($home['distance_'.$i])), 1);                
                        }else{
                            $home['distance_average_'.$i] = '-';
                        }
                    }
                
                
                    update_post_meta($player_id, 'distance_stats', $home);
                    update_post_meta($player_id, 'courses_played', $courses_played);
                    update_post_meta($player_id, 'made_stats', $made);
                    update_post_meta($player_id, 'stats_updated', time()); 
                
                return $made;
            }else{
                return $rounds;
            }
        }
    
        public function get_by_tournament($tournament_id){
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Round.* FROM Round
            LEFT JOIN PostMeta as meta1
            ON Round.ID = meta1.post_id
            LEFT JOIN PostMeta as meta2
            ON Round.ID = meta2.post_id
            WHERE Round.post_type = 'round' 
            AND Round.post_status = 'publish' 
            AND meta1.meta_key = 'tournament'
            AND meta1.meta_value = 'a:1:{i:0;s:4:\"$tournament_id\";}'
            AND meta2.meta_key = 'position'
            ORDER BY meta2.meta_value ASC", $this->getDI());

            if($query->execute()){
               $rounds = $query->execute();
            }else{
                $rounds = NULL;
            }
            return $rounds;
        }
        
        public function score($return = 'strokes'){
            $score_array = array_fill(0, 18 , 0);
            $score_array = explode(" ", $this->scorecard);
            if($return == 'array'){
                return $score_array;
            }elseif($return == 'front'){
                return array_sum(array_slice($score_array, 0, 9));
            }elseif($return == 'back'){
                return array_sum(array_slice($score_array, 9, 9));
            }elseif($return == 'holes'){
                return count($score_array);
            }else{
                $strokes = array_sum($score_array);
                return $strokes;
            }
        }
        
        public function hole_status($return = 'value', $hole){
            $status = $this->score('array')[$hole - 1] - $this->tournament()->get_course()->scorecard('par', $hole);
            if($return == 'value'){
                return sprintf("%+d", $status);   
            }elseif($return == 'class'){
                if($status == -2){
                    $class = 'eagle';  
                }elseif($status == -1){
                    $class = 'birdie';  
                }elseif($status == 0){
                    $class = 'par';  
                }elseif($status == 1){
                    $class = 'bogey';  
                }elseif($status == 2){
                    $class = 'double-bogey';  
                }elseif($status >= 3){
                    $class = 'threeplus';  
                }
                return $class;
            }
        }
    }