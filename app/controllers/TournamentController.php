<?php

    use Phalcon\Mvc\Controller;

    class TournamentController extends Controller
    {

        public function indexAction(){
            if($this->dispatcher->getParam("type")){
                $type = $this->dispatcher->getParam("type");
            }else{
                $type = 'tour';
            }

            if($this->dispatcher->getParam("year")){
                $year = date('Y-m-d', strtotime($this->dispatcher->getParam("year") . '-08-01'));
            }else{
                $year = date('Y-m-d', time());
            }

            $season = Season::get($year);

            $this->view->year = date('Y', strtotime($year));
            $this->view->type = $type;
            $this->view->title = $season->post_title;
            $this->view->tournaments = Tournament::by_type($type, $season);

        }

        public function singleAction(){
            $this->view->fullwidth = TRUE;
            $slug = $this->dispatcher->getParam("slug");
            $tournament = Tournament::single($slug);
            $this->view->title = $tournament->post_title;
            $this->view->tournament = $tournament;
        }
        
        public function insertallAction(){
            
            $players = Player::all();
            foreach($players as $player){
                
                echo $player->post_title . ' - ';
                $stats = Round::player_update($player->ID);
                echo 'DONE<br>';
            } 
        }
            
        public function updateallAction(){
                       
            $tournaments = Tournament::all();
            foreach($tournaments as $tournament){
                    
                if($tournament->completed == TRUE && $tournament->full_date > date('Y-m-d', strtotime('2015-01-02')) && $tournament->scoring == 'stroke'){
                        echo $tournament->post_title . '<br>--------------<br>';
                        $course = $tournament->get_course();
                        $par = $course->scorecard("par");
                        $scratch = $course->scratch;
                        $slope = $course->slope;
                    
                    
                        if($tournament->points == 2500){

                            $points_array = array(2500,2000,1500,1000,750,500,250,125,100,90,80,70);

                        }elseif($tournament->points == 2000){

                            $points_array = array(2000,1200,740,540,440,400,360,340,320,300,280,260,240);

                        }elseif($tournament->points == 1000){

                            $points_array = array(1000,600,380,270,220,200,180, 170,160,150,140,130,120);

                        }elseif($tournament->points == 500){

                            $points_array = array(500,300,190,135,110,100,90, 85,80,75,70,65,60);

                        }elseif($tournament->points == 50){

                            $points_array = array(50,30,19,13,11,10,9, 8,7,6,5,4,3);

                        }else{
                            $points_array = array_fill(0,13,0);                        
                        }

                        $tournament_rounds = array();
                    
                     if($tournament->holes_played == 18){
                         
                        foreach($tournament->get_rounds() as $round){ 
                            $breakdown = explode(" ", $round->scorecard);
                            $strokes = array_sum($breakdown);
                            
                            $total = $strokes - $par;
                            $total = sprintf("%+d", $total);
                            
                            $differential = ((($strokes - $scratch) * 113) / $slope);
                            if($differential > 40){
                                $differential = 40;
                            }
                            
                            $current_p = Player::single_by_id($round->player_id);

                            $current_handicap = $current_p->handicap($tournament->full_date);
                            
                            $daily_handicap = $current_handicap * ($slope/113);
                            $adjusted = sprintf("%+d", $total - $daily_handicap);
                            
                            $differential = sprintf("%+.1f", $differential);
                            $official = sprintf("%+d", $adjusted);
                            
                            echo $current_p->post_title . '(' . $current_handicap . ')' . ' - ';
                            echo 'Total: ' . $total . ' Handicap: ' . $differential . ' Adjusted: ' . $official . '<br>';
                            update_post_meta($round->ID, 'total', $total);
                            update_post_meta($round->ID, 'differential', $differential);
                            update_post_meta($round->ID, 'official', $official);
                            update_post_meta($round->ID, 'strokes', $strokes);
                            
                            
                            if($tournament->full_date > '2014-12-25'){
                                update_post_meta($round->ID, 'adjusted', $adjusted);
                            } 
                            
                            $tournament_rounds[$round->ID] = $official;
                            
                        }
                     
                         
                asort($tournament_rounds);
                $tie = array_count_values($tournament_rounds);
                $winner = array();
                $last = 999999;
                $position = 0;
                         
                foreach($tournament_rounds as $round_id => $score)
                { 
                                    
                    $current_round = Round::single($round_id);    
                        
                    if($position == 0)
                    {
                        $position++;
                    }elseif($score != $last){
                        $position += $last_tie;
                    }

                    if($position == 1){
                        $current_player = Player::single_by_id($current_round->player_id);
                        $winner[] = $current_player->post_title;
                        update_post_meta($tournament->ID, 'score', $score);
                    }
                        
                    
                    $points = 0;
                    for($start = $position;$start < $position +  $tie[$score]; $start++){
                
                $points += $points_array[$start - 1];
                
            }
                        
                        
                 $points = ceil($points/ $tie[$score]);    
                     
                    $last = $score;
                    $last_tie = $tie[$score];
                     update_post_meta($current_round->ID, 'position', $position);
                     update_post_meta($current_round->ID, 'points', $points);
                        
                        
                        echo $position  . '('. $points .')' . $current_round->adjusted . '<br>';   
                       
                }
                         
                     update_post_meta($tournament->ID, 'completed', TRUE); 
                                     
                     }
                  echo '--------------<br>';  
                }elseif($tournament->scoring == 'match' && $tournament->full_date > date('Y-m-d', strtotime('2015-01-17'))){
                    
                        echo $tournament->post_title . '<br>';
                    
                         foreach($tournament->get_rounds() as $round){ 
                            $breakdown = explode(" ", $round->scorecard);
                            $scorecard[$round->matchup][$round->player()->post_title] = $breakdown;
                         }
                    
                        foreach($scorecard as $number => $matchup){
                            echo $number . '<br>';
                            $i = 1;
                            $best = array();
                            $player_1 = 0;
                            $player_2 = 0;
                            $hole = 1;
                            
                            $progress[$number] = array();
                            
                            foreach($matchup as $round_id => $score){
                                if($i == 1){
                                    $best = $score;
                                    $players[$number][$i] = $round_id;
                                }else{
                                    $players[$number][$i] = $round_id;
                                    foreach($score as $par){                                        
                                        if($par > $best[$hole - 1]){
                                            $player_1++;
                                            $progress[$number][$hole] = $players[$number][1];
                                        }elseif($par < $best[$hole - 1]){
                                            $player_2++;
                                            $progress[$number][$hole] = $players[$number][2];

                                        }
                                        
                                        $matchup_score = $player_1 - $player_2;
                                        
                                        if($matchup_score > 0){
                                            $live[$number] = $matchup_score . 'UP';
                                        }elseif($matchup_score < 0){
                                            $live[$number] = abs($matchup_score) . 'UP';
                                        }else{
                                            $live[$number] = 'AS';
                                        }
                                        
                                        $leader[$number][$hole] = $live[$number];
                                        
                                        if(abs($matchup_score) > (18-$hole)){
                                            if($matchup_score > 0){
                                                $live[$number] = $players[$number][1] . ' WINS ' . $matchup_score . '&' . (18-$hole);
                                            }elseif($matchup_score < 0){
                                                $live[$number] = $players[$number][2]. ' WINS ' . $matchup_score . '&' . (18-$hole);
                                            }
                                            echo $live[$number] . '<br>';
                                            $finished[$number] = $hole;
                                            break;
                                        }
                                        
                                        echo $live[$number] . '<br>';
                                        $finished[$number] = $hole;
                                        $hole++;
                                   }
                                }
                                $i++;
                            }
                        }
                    
                    update_post_meta($tournament->ID, 'players', $players);
                    update_post_meta($tournament->ID, 'progress', $progress);
                    update_post_meta($tournament->ID, 'leader', $leader);
                    update_post_meta($tournament->ID, 'finished', $finished);
                    update_post_meta($tournament->ID, 'winner', $live);
                    update_post_meta($tournament->ID, 'matchplay', $scorecard);
                }
            }
        }
    }