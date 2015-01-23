<?php

  use Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class Navigation extends \Phalcon\Mvc\Model
{

    public function getSource()
    {
        return "wp_posts";
    }
    
    public function afterFetch()
    {
       $this->url = get_post_meta($this->ID, '_menu_item_url', TRUE);
       $this->parent = get_post_meta($this->ID, '_menu_item_menu_item_parent', TRUE);
    }
            
    public function get()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'nav_menu_item'
               AND post_status = 'publish'
               ORDER BY menu_order ASC";      
      
        $navigation = new Navigation();
        $result = new Resultset(null, $navigation, $navigation->getReadConnection()->query($sql)); 
        
        return $result;
    }
    
    public function get_children()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'nav_menu_item'
               AND post_status = 'publish'
               ORDER BY menu_order ASC";      
      
        $navigation = new Navigation();
        $result = new Resultset(null, $navigation, $navigation->getReadConnection()->query($sql)); 
        
        $child_array = array();
        
        foreach($result as $item)
        {
            if($this->ID == $item->parent)
            {
                $child_array[] = $item;   
            }
        }
        return $child_array;
    }
    
    
    public function has_children()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'nav_menu_item'
               AND post_status = 'publish'
               ORDER BY menu_order ASC";      
      
        $navigation = new Navigation();
        $result = new Resultset(null, $navigation, $navigation->getReadConnection()->query($sql)); 
        
        $has_children = FALSE;
        foreach($result as $item)
        {
            if($this->ID == $item->parent)
            {
                $has_children = TRUE;   
            }
        }
        return $has_children;
    }
    
}