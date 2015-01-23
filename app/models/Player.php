<?php

    use \Phalcon\Mvc\Model;

    class Player extends Model{

        public function getSource(){
            return "wp_posts";
        }
        
        public function afterFetch(){   
            
            $this->rounds_updated = get_post_meta($this->ID, 'rounds_updated', TRUE );

            $this->adjustment = get_post_meta($this->ID, 'adjustment', TRUE );
            $this->country = get_post_meta($this->ID, 'country', TRUE );
            $this->flag = '<img src="/img/flags/'. $this->country . '.png"/>';
            $this->photo = '<img src="'.$this->get_photo().'"/>';
            $this->photo_url = $this->get_photo();
            $this->dob = date('Y-m-d', strtotime(get_post_meta($this->ID, 'dob', TRUE )));
            $this->age = $this->calculate_age();
            $this->height = get_post_meta($this->ID, 'height', TRUE );
            $this->weight = get_post_meta($this->ID, 'weight', TRUE );
            
            $this->pob = get_post_meta($this->ID, 'pob', TRUE );
            $this->college = get_post_meta($this->ID, 'college', TRUE );
            $this->clubs = get_post_meta($this->ID, 'clubs', TRUE );
            $this->glove = get_post_meta($this->ID, 'glove', TRUE );    
            $this->pro = date('Y', strtotime(get_post_meta($this->ID, 'debut', TRUE )));
            $this->debut = date('Y-m-d', strtotime(get_post_meta($this->ID, 'debut', TRUE )));
            
            $this->courses_played = get_post_meta($this->ID, 'courses_played', TRUE );
            $this->stats_updated = get_post_meta($this->ID, 'stats_updated', TRUE );
            
        }
        
        public function all(){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Player WHERE post_type = 'player' AND post_status = 'publish' ORDER BY post_title ASC", $this->getDI());
            return $query->execute();
        }
        
         public function all_rounds(){            
            return get_post_meta($this->ID, 'all_rounds', TRUE );
        }       
        
        
                

        public function single($slug){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Player WHERE post_name = '$slug' AND post_status = 'publish' LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }
        
        public function single_by_id($id){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Player WHERE ID = $id LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }
        
        public function calculate_age(){
            $then = date('Ymd', strtotime($this->dob));
            $diff = date('Ymd') - $then;
            return substr($diff, 0, -4);
        }
    
        public function get_photo(){
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->ID ), 'single-post-thumbnail' );
            return $image[0];
        }

        public function rounds_meta(){ 
            return get_post_meta($this->ID, 'all_rounds', TRUE );   
        }
        
        
        public function region_stats(){ 
            return get_post_meta($this->ID, 'region_stats', TRUE );   
        }
        
        public function distance_stats(){ 
            return get_post_meta($this->ID, 'distance_stats', TRUE );   
        }
        
        public function alltime_stats(){ 
            return get_post_meta($this->ID, 'made_stats', TRUE );   
        }
        
        public function cup_stats($season){
            $stats = (object) NULL;

            $stats->events = get_post_meta($this->ID, $season->post_name . '_events', TRUE );
            $stats->wins = get_post_meta($this->ID, $season->post_name . '_wins', TRUE );
            $stats->top2 = get_post_meta($this->ID, $season->post_name . '_top2', TRUE );
            $stats->position = get_post_meta($this->ID, $season->post_name . '_position', TRUE );
            $stats->points = get_post_meta($this->ID, $season->post_name . '_points', TRUE );
            $stats->reset_points = get_post_meta($this->ID, $season->post_name . '_reset_points', TRUE );
            $stats->reset_position = get_post_meta($this->ID, $season->post_name . '_reset_position', TRUE );

            return $stats;
        }
        
        public function season_stats($year){
            
            $season = Season::get(date('Y-m-d', strtotime($year . '-10-5')));
            
            $stats = (object) NULL;

            $stats->year = $year;
            $stats->events = get_post_meta($this->ID, 'season_' . $year . '_events', TRUE );
            $stats->wins = get_post_meta($this->ID, 'season_' . $year . '_wins', TRUE );
            $stats->top2 = get_post_meta($this->ID, 'season_' . $year . '_top2', TRUE );
            $stats->points = get_post_meta($this->ID, 'season_' . $year . '_points', TRUE );
                        
            if(!$season->reset){
                $stats->aga_points = get_post_meta($this->ID, $season->post_name . '_points', TRUE );
                $stats->aga_rank = get_post_meta($this->ID, $season->post_name . '_position', TRUE );
            }else{
                $stats->aga_points = get_post_meta($this->ID, $season->post_name . '_playoff_points', TRUE );
                $stats->aga_rank = get_post_meta($this->ID, $season->post_name . '_playoff_position', TRUE );               
            }

            $rounds = Round::by_player($this->ID, $season->end, $season->start);
            
            $stats->rounds = array();
            
            foreach($rounds as $round){
                $stats->rounds[] = Round::single($round->id);   
            }     
            
            return $stats;
        }
        
        public function get_handicap(){
           
            $handicaps = get_post_meta($this->ID, 'handicap_history', TRUE);
            
            foreach($handicaps as $handicap){
                    print_r($handicap);
                echo '<br>';
            }
            
        }
        
        public function handicap($end = NULL){
            
            if($end == NULL){
                $end = date('Y-m-d');
            } 
            
            $rounds = $this->all_rounds();
                                    
            $score = array();
            $count = 0;
            foreach($rounds as $round){
                    if($round['date'] < $end){
                        $score[] = $round['differential'];   
                        $count++;
                    }
            }

            
            if($count > 0){
                $score = array_slice($score, -20);
                asort($score);
            }
            
            $rounds_played = count($score);
            
            if($rounds_played < 3){
                $rounds_to_count = 0;
            }elseif($rounds_played < 7){
                $rounds_to_count = 1;
            }elseif($rounds_played < 9){
                $rounds_to_count = 2;
            }elseif($rounds_played < 11){
                $rounds_to_count = 3;
            }elseif($rounds_played < 13){
                $rounds_to_count = 4;
            }elseif($rounds_played < 15){
                $rounds_to_count = 5;
            }elseif($rounds_played < 17){
                $rounds_to_count = 6;
            }elseif($rounds_played < 19){
                $rounds_to_count = 7;
            }else{
                $rounds_to_count = 8;
            }

            if($rounds_to_count == 0){
                $handicap = 36;                
            }else{
                $top_scores = array_slice($score, 0, $rounds_to_count);
                $handicap = (array_sum($top_scores) / $rounds_to_count) * 0.93;
                
                if($handicap >= 36.4){
                    $handicap = 36.4;
                }
            }
            
            $handicap = sprintf("%.1f", $handicap);
            
            return $handicap;  
        }
        
        public function handicap_tracker(){
            $date = date('U', strtotime($this->debut));
                        
            while($date <= date('U', time())){
                $data = array();
                $format = date('Y, m, d', strtotime($date));
                $data[$format] = $this->handicap($date);
                
                $date = date('U', strtotime($date . '+1 week'));
            }
            
            return $data;
        }
    }