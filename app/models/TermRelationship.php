<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class TermRelationship extends \Phalcon\Mvc\Model{

        public function getSource(){
            return "wp_term_relationships";
        }         

        public function get_terms($post_id){
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM TermRelationship WHERE object_id = $post_id", $this->getDI());
            return $query->execute();
        }  
    }