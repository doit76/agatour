<?php

use Phalcon\Mvc\Controller;

class LiveController extends Controller
{

	public function scoreAction()
	{
        $scorers = Live::get();
        $q = 1;
        foreach($scorers as $score)
        {
            
            echo "Matchup " . $q . "<br><br>";
            $player_1 = get_post_meta($score->ID, 'player_1', TRUE );
            $player_2 = get_post_meta($score->ID, 'player_2', TRUE );
                        
            $scorecard_1 = explode(" ", get_post_meta($score->ID, 'player_1_score', TRUE ));
            $scorecard_2 = explode(" ", get_post_meta($score->ID, 'player_2_score', TRUE ));
            
            $score_1 = 0;
            $score_2 = 0;
            
            for($i = 1; $i <= 18 ; $i ++):
              
                if($scorecard_1[$i - 1] && $scorecard_2[$i - 1]):
            
                    if($scorecard_1[$i - 1] < $scorecard_2[$i - 1]):
                        $score_1++;
                    elseif($scorecard_1[$i - 1] > $scorecard_2[$i - 1]):
                        $score_2++;
                    endif;
                else:
                    break;
                endif;
            
                $final1 = $score_2 - $score_1;
                $final2 = $score_1 - $score_2;
                if((18 - $i) < $final1):
                    break;
                endif;
            
                if((18 - $i) < $final2):
                    break;
                endif;             
        
            endfor;
                
                        
            if($score_1 > $score_2):
            
                $final = $score_1 - $score_2;
                    
                if(18 - $i < $final):
        
                    echo "Team 1 WINS " . $final . "&" . (18 - $i);

                else:
            
                    echo "Team 1 " . ($score_1 - $score_2) . " UP<br>";
                echo "(" . ($i - 1). ")<br><br>";
                endif;
            
            
            elseif($score_1 < $score_2):
                $final = $score_2 - $score_1;
                if(18 - $i < $final):

                    echo "Team 2 WINS " . $final . "&" . (18 - $i);

            else:
            
                    echo "Team 2 " . $final . " UP<br>";
                echo "(" . ($i - 1). ")<br><br>";
                endif;
            
            else:
                echo "AS<br>";
                echo "(" . ($i - 1). ")<br><br>";
            endif;

            
            $q++;
        }
    }
    
}