<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
        \Phalcon\Mvc\Model;

    class Tournament extends Model{

        public function getSource(){
            return "wp_posts";
        }
    
        public function afterFetch(){
            $this->date = date('d-M', strtotime($this->post_date));
            $this->type = $this->get_type();
            $this->scoring = PostMeta::get($this->ID, 'type');
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
        
        public function get_rounds(){
            return Round::get_by_tournament($this->ID);
        }
        
        public function get_type(){
            $meta_data = TermRelationship::get_terms($this->ID);
            $tags = array();
            $categories = array();
            foreach($meta_data as $meta){
                $term = Term::get_by_id($meta->term_taxonomy_id);
            }
            return $term->slug;
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
            }            
        
            $team['name'] = PostMeta::get($this->ID, 'team_'.$number.'_name');
            $team['colour'] = PostMeta::get($this->ID, 'team_'.$number.'_colour');
            $team['players'] = $matchup;
            
        $total = 0;
        
            for($i = 1; $i <= 4; $i++){

                $fourball = get_post_meta($this->ID, 'fourball_' . $i . '_winner', TRUE);
                $foursomes = get_post_meta($this->ID, 'foursomes_' . $i . '_winner', TRUE);
                $matchup = get_post_meta($this->ID, 'matchup_' . $i . '_winner', TRUE);

                if($fourball == $team_name){
                    $total++;   
                }elseif($fourball == 'halved'){
                    $total+=0.5;        
                }

                if($foursomes == $team_name){
                    $total++;   
                }elseif($foursomes == 'halved'){
                    $total+=0.5;        
                }

                if($matchup == $team_name){
                    $total++;   
                }elseif($matchup == 'halved'){
                    $total+=0.5;        
                }
            }
               
            $team['score'] = $total;
            return $team;
        }
        
        /*--------------------------------------------
            GET MATCHUP TEAM MEMBERS
        --------------------------------------------*/
    
        public function get_matchup(){
            
            for($i = 1; $i <= 4; $i++){
                
                for
                
                $matchup['fourball_'.$i] = get_post_meta($this->ID, 'fourball_'.$i, TRUE );
                $matchup['foursomes_'.$i] = get_post_meta($this->ID, 'foursomes_'.$i, TRUE );
                $matchup['headtohead_'.$i] = get_post_meta($this->ID, 'matchup_'.$i, TRUE );
                
            }
            
            /*
            $players = get_post_meta($this->ID, 'team_'.$number, TRUE );
            $team = array();
            foreach($players as $player_id){
                $team[] = $player_id; 
            }    */
            
            return $matchup;

        }
        
    }