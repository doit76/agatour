<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
        \Phalcon\Mvc\Model;

    class Page extends Model{

        public function getSource(){
            return "wp_posts";
        }
    
        public function afterFetch(){
        }
                    
        public function get_by_slug($slug){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Page WHERE post_name = '$slug' AND post_status = 'publish' AND post_type = 'page' LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }
    }