<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class Term extends \Phalcon\Mvc\Model{

        public function getSource(){
            return "wp_terms";
        }

        public function get_by_slug($slug){
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Term WHERE slug = '$slug' LIMIT 1", $this->getDI());
            return $query->execute();
        }  

        public function get_by_id($id){
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Term WHERE term_id = '$id' LIMIT 1", $this->getDI());
            $meta_data = $query->execute();                                     
            return $meta_data[0];
        }  

        public function get_term_type(){
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM TermTaxonomy WHERE term_id = '$this->term_id' LIMIT 1", $this->getDI());
            $term_id = $query->execute();                                     
            return $term_id[0]->taxonomy;        
        }
    }