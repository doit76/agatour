<?php

    use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
    use Phalcon\Mvc\Model\Manager as manager;

    class Course extends \Phalcon\Mvc\Model
    {

        public function getSource(){
            return "wp_posts";
        }
        
        public function get_by_id($course_id){

            $query = new Phalcon\Mvc\Model\Query("
            SELECT * FROM Course 
            WHERE ID = '$course_id'
            LIMIT 1", $this->getDI());
            return $query->execute()[0];
            
        }

    public function initialize()
    {
        $this->hasMany("ID", "PostMeta", "post_id");
    }
    
    public function afterFetch()
    {
        $this->par = $this->scorecard();
        $this->holes = $this->scorecard();
        $this->scratch = get_post_meta($this->ID, 'scratch_rating', TRUE );
        $this->slope = get_post_meta($this->ID, 'slope_rating', TRUE );
        
        //$this->price = PostMeta::get($this->ID, 'rating_price');  
        //$this->course = PostMeta::get($this->ID, 'rating_course');
    }
 
    public function angle()
    {
      $y1 = -37.781961;
      $x1 = 144.956317;
        $y2 = $this->location()['lat'];
    $x2 = $this->location()['lng'];
                
        
          $bearingradians = atan2( tan($y2-$y1), tan($x2-$x1)  );

          $bearingdegrees = rad2deg($bearingradians);
          if( $bearingdegrees < 0 )
            $bearingdegrees += 360;

        
        if($bearingdegrees >= 0 && $bearingdegrees <= 45 || $bearingdegrees >= 315 && $bearingdegrees <= 360){
            $region = 'E';
        }elseif($bearingdegrees > 45 && $bearingdegrees < 135){
            $region = 'N';
        }elseif($bearingdegrees >= 135 && $bearingdegrees <= 225){
            $region = 'W';
        }elseif($bearingdegrees > 225 && $bearingdegrees < 315){
            $region = 'S';
        }
        
          return $region;
        
    }
        
        
    public function distance()
    {
        $distance = $this->vincentyGreatCircleDistance($this->location()['lat'], $this->location()['lng']);
        
        $distance = round($distance/1000);
        
        return $distance;   
    }
        
            
    public function location()
    {
        return get_post_meta($this->ID, 'location', TRUE );   
    }
        
    
    public function holes()
    {
        $scorecard = PostMeta::findFirst(array(
            "conditions" => "post_id = $this->ID AND meta_key = 'scorecard'",
        ));
        
        $holes = explode("|", $scorecard->meta_value);   
        
        return count($holes);
    }
        
    public function scorecard($value = "par", $hole = NULL)
    {
        $scorecard = PostMeta::findFirst(array(
            "conditions" => "post_id = $this->ID AND meta_key = 'scorecard'",
        ));
        
        $holes = explode("|", $scorecard->meta_value);

        if(count($holes) == 9){
            $holes = explode("|", $scorecard->meta_value.'|'.$scorecard->meta_value);
        }
        
        $par = array();
        $distance = array();
        
        foreach($holes as $single_hole){
            $details = explode(" ", $single_hole); 
            $par[] = $details[1];
            $distance[] = $details[0];
        }
        
        if($value == 'par'){
            if($hole == NULL){
              $total = array_sum($par);
            }elseif($hole == 'front'){
              $total = array_sum(array_slice($par, 0, 9));
            }elseif($hole == 'back'){
              $total = array_sum(array_slice($par, 9, 9));
            }else{
              $total = $par[$hole-1];
            }
        }else{
            if($hole == NULL){
              $total = array_sum($distance);
            }else{
              $total = $distance[$hole-1];
            }           
        }

        return $total;
    }
    
    public function test()
    {
                $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'tournament' 
               AND post_status = 'publish'"; 
        
         $rounds = new Tournament();
        $result = new Resultset(null, $rounds, $rounds->getReadConnection()->query($sql));
        
        foreach($result as $tourn):
            print_r($tourn->update_rounds());
        endforeach;
        
        
    }

    public function get()
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_type = 'course' 
               AND post_status = 'publish'
               ORDER BY post_title ASC";      
      
        $course = new Course();
        $result = new Resultset(null, $course, $course->getReadConnection()->query($sql)); 
        
        return $result;
    }
    
    public function single($slug)
    {
        $sql = "SELECT * FROM wp_posts
               WHERE post_name = '$slug' 
               AND post_status = 'publish'";      
      
        $course = new Course();
        $result = new Resultset(null, $course, $course->getReadConnection()->query($sql)); 
        
        return $result[0];
    }

    public function single_by_id($id)
    {
        $sql = "SELECT * FROM wp_posts
               WHERE ID = '$id' 
               AND post_status = 'publish'";      
      
        $course = new Course();
        $result = new Resultset(null, $course, $course->getReadConnection()->query($sql)); 
        
        return $result[0];
    }
        
            
    public static function vincentyGreatCircleDistance($latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
      // convert from degrees to radians
      $latFrom = deg2rad(-37.781961);
      $lonFrom = deg2rad(144.956317);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);

      $lonDelta = $lonTo - $lonFrom;
      $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
      $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

      $angle = atan2(sqrt($a), $b);
      return $angle * $earthRadius;
    }
        
    public static function vincentyGreatCircleDistanceAngle($latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
      // convert from degrees to radians
      $latFrom = deg2rad(-37.781961);
      $lonFrom = deg2rad(144.956317);
      $latTo = deg2rad($latitudeTo);
      $lonTo = deg2rad($longitudeTo);

      $lonDelta = $lonTo - $lonFrom;
      $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
      $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

      $angle = atan2(sqrt($a), $b);
      return $angle;
    }
}
