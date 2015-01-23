<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
    \Phalcon\Mvc\Model;

    class PostMeta extends Model
    {

        public function getSource(){
        return "wp_postmeta";
        }
        
        public function get($id,$key){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM PostMeta WHERE meta_key = '$key' AND post_id = '$id' LIMIT 1", $this->getDI());
            return $query->execute()[0]->meta_value;
        }
    }