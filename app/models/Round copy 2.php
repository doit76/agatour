<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class Round extends \Phalcon\Mvc\Model
    {

        public function getSource(){
            return "wp_posts";
        }

        public function single($round_id){
            $query = new Phalcon\Mvc\Model\Query("
            SELECT * FROM Round 
            WHERE ID = '$round_id'
            LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }  
        
        
        public function by_player($player_id, $end = NULL, $start = NULL){
 
            if($start == NULL){
                $start = date('Y-m-d', strtotime('1990-01-01'));
            }
            if($end == NULL){
                $end = date('Y-m-d');
            }              
            
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT meta2.meta_value as total, meta4.meta_value as holes_played FROM Round
            LEFT JOIN PostMeta as meta1
            ON meta1.post_id = Round.ID
            LEFT JOIN PostMeta as meta2
            ON meta2.post_id = Round.ID
            LEFT JOIN PostMeta as meta3
            ON meta3.post_id = Round.ID
            LEFT JOIN PostMeta as meta4
            ON meta4.post_id = Round.ID
            WHERE meta1.meta_key = 'date' 
            AND (meta1.meta_value >= '$start' AND meta1.meta_value < '$end')
            AND meta2.meta_key = 'total'
            AND meta3.meta_key = 'player' 
            AND (
            meta3.meta_value = 'a:1:{i:0;s:1:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:4:\"$player_id\";}'
            OR meta3.meta_value = 'a:1:{i:0;s:2:\"$player_id\";}'
            )
            AND meta4.meta_key = 'holes_played'", $this->getDI());
            return $query->execute();
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
            }elseif($return == 'holes'){
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
            return $query->execute();
        }
    }