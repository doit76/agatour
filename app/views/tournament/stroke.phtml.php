<table class="uk-table tournament">
    <thead>
        <tr>
            <td rowspan="2">Pos</td>
            <td rowspan="2" class="flag">Country</td>
            <td rowspan="2" class="name">Player Name</td>
            <td rowspan="2">Front 9</td>
            <td rowspan="2">Back 9</td>
            <td rowspan="2">Strokes</td>
            <td rowspan="2">Total</td>
            <td rowspan="2">Adjusted</td>
            <?php if ($tournament->type != 'practice') { ?>
                <td rowspan="2">Points</td>
            <?php } ?>
            <td rowspan="2"></td>
        </tr>
    </thead>
    
    <?php foreach ($tournament->get_rounds() as $round) { ?>
        <tr>
            <td><?php echo $round->position; ?></td>
            <td><?php echo $round->player()->flag; ?></td>
            <td class="name"><?php echo $round->player()->post_title; ?></td>
            <td><?php echo $round->score('front'); ?></td>
            <td><?php echo $round->score('back'); ?></td>
            <td><?php echo $round->strokes; ?></td>
            <td><?php echo $round->total; ?></td>
            <td><?php echo $round->adjusted; ?></td>
             
            <?php if ($tournament->type != 'practice') { ?>
                <td><?php echo $round->points; ?></td>
            <?php } ?>
            
            <td><span class="uk-icon-dot-circle-o" data-uk-toggle="{target:'#round<?php echo $round->ID; ?>'}"></span></td>
        </tr>  
       <tr class="uk-hidden" id="round<?php echo $round->ID; ?>">
            <td style="padding: 0;" colspan="11">
                <table class="uk-table scorecard">
                    <tr>
                        <td>Hole</td>
                        <?php foreach (range(1, $round->score('holes')) as $hole) { ?>

                            <td class="hole"><?php echo $hole; ?></td>
                            <?php if ($hole == 9) { ?>
                                <td>OUT</td>
                            <?php } ?>
                            <?php if ($hole == 18) { ?>
                                <td>IN</td>
                            <?php } ?>
                            <?php if ($hole == 18 || $round->score('holes') == 9) { ?>
                                <td>TOT</td>
                            <?php } ?>                    
                        <?php } ?>               
                    </tr>                                        
                    <tr>
                        <td>Par</td>
                        <?php foreach (range(1, $round->score('holes')) as $hole) { ?>

                            <td><?php echo $tournament->get_course()->scorecard('par', $hole); ?></td>
                            <?php if ($hole == 9) { ?>
                                <td><?php echo $tournament->get_course()->scorecard('par', 'front'); ?></td>
                            <?php } ?>
                            <?php if ($hole == 18) { ?>
                                <td><?php echo $tournament->get_course()->scorecard('par', 'back'); ?></td>
                            <?php } ?>
                            <?php if ($hole == 18 || $round->score('holes') == 9) { ?>
                                <td><?php echo $tournament->get_course()->scorecard('par'); ?></td>
                            <?php } ?>                    
                        <?php } ?>              
                    </tr>
                    <tr>
                        <td>Score</td>
                        <?php foreach (range(1, $round->score('holes')) as $hole) { ?>

                            <td class="<?php echo $round->hole_status('class', $hole); ?>"><?php echo $round->score('array')[$hole - 1]; ?></td>
                            <?php if ($hole == 9) { ?>
                                <td><?php echo $round->score('front'); ?></td>
                            <?php } ?>
                            <?php if ($hole == 18) { ?>
                                <td><?php echo $round->score('back'); ?></td>
                            <?php } ?>
                            <?php if ($hole == 18 || $round->score('holes') == 9) { ?>
                                <td><?php echo $round->score(); ?></td>
                            <?php } ?>                    
                        <?php } ?>              
                    </tr>
                </table>
            </td>
        </tr>
        <?php } ?>
</table>