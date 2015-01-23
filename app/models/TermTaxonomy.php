<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

    class TermTaxonomy extends \Phalcon\Mvc\Model{
        public function getSource(){
            return "wp_term_taxonomy";
        }               
    }