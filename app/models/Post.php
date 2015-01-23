<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset,
        \Phalcon\Mvc\Model;

    class Post extends Model{

        public function getSource(){
            return "wp_posts";
        }
    
        public function afterFetch(){
            $this->year = $this->formatDate($this->post_date, 'Y');
            $this->month = $this->formatDate($this->post_date, 'm');
            $this->day = $this->formatDate($this->post_date, 'd');

            $this->date = $this->formatDate($this->post_date, 'd M, Y');

            $this->link = '/'.$this->year.'/'.$this->month.'/'.$this->post_name;
            $this->excerpt = substr(strip_tags($this->post_content), 0, 300) . '...';
            $this->photo = '<img src="'.$this->get_photo().'"/>';
            $this->photo_url = $this->get_photo();
            
            $this->post_format = get_post_format( $this->ID );
            $this->video_url = get_post_meta($this->ID, '_tern_wp_youtube_video', TRUE); 
        }
        
        public function page_format(){
            if($this->post_format){
                $page = 'blog/layout-'.$this->post_format;  
            }else{
                $page = 'blog/layout';  
            }
            
            return $page;
        }
          
        public function single_format(){
            if($this->post_format){
                $page = 'blog/single-'.$this->post_format;  
            }else{
                $page = 'blog/single';  
            }
            
            return $page;
        }
            
        public function all(){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Post WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC", $this->getDI());
            return $query->execute();
        }
        
            
        public function latest(){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Post WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 5", $this->getDI());
            return $query->execute();
        }
        
        public function single($slug){            
            $query = new Phalcon\Mvc\Model\Query("SELECT * FROM Post WHERE post_name = '$slug' AND post_status = 'publish' LIMIT 1", $this->getDI());
            return $query->execute()[0];
        }
                                
        public function formatDate($date, $format){
            return date($format, strtotime($date));
        }       
 
        public function has_image(){
            if($this->get_photo()){
                return TRUE; 
            }else{
                return FALSE; }
        }
        
        public function get_photo(){
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->ID ), 'thumbnail' );
            return $image[0];
        }
        
        public function by_term_slug($term_slug){
            $query = new Phalcon\Mvc\Model\Query(
            "SELECT Post.* FROM Post
            LEFT JOIN TermRelationship
            ON Post.ID = TermRelationship.object_ID
            LEFT JOIN Term
            ON Term.term_id = TermRelationship.term_taxonomy_id
            WHERE Term.slug = '$term_slug'
            AND Post.post_status = 'publish'
            AND Post.post_type = 'post'
            ORDER BY Post.post_date DESC", $this->getDI());
            return $query->execute();
        }
        
        public function get_tags(){
            $meta_data = TermRelationship::get_terms($this->ID);
            $tags = array();
            $categories = array();
            foreach($meta_data as $meta){
                $term = Term::get_by_id($meta->term_taxonomy_id);
                
                if($term->get_term_type() == 'category'){
                    $terms['category'][] = $term;    
                }else{
                    $terms['tag'][] = $term;    
                }
            }
            return $terms;
        }
    }