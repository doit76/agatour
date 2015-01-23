<style>
        table.scorecard tr td {
            text-align: center;
        }
        
        table.scorecard tr.team1 td.winner {
            background: <?php echo $tournament->get_team(1)['colour']; ?>; 
            color: #fff;}
        
        table.scorecard tr.team2 td.winner {
            background: <?php echo $tournament->get_team(2)['colour']; ?>;
            color: #fff;}

</style>


<?php foreach ($tournament->matchplay() as $name => $matchup) { ?>

    <table class="tournament">
        <thead>
            <tr>
                <td colspan="3"><?php echo $name; ?></td>
            </tr>
        </thead>
            <tr>
                <td>
                    <?php echo $tournament->players()[$name][1]; ?>
                </td>
                <td>
                    <?php echo $tournament->winner()[$name]; ?><br>
                    <a data-uk-toggle="{target:'#scorecard<?php echo $name; ?>'}">Details</a>

                </td>                
                <td>
                    <?php echo $tournament->players()[$name][2]; ?>
                </td>
            </tr>
            <tr>
                    <table class="uk-table scorecard uk-hidden" id="scorecard<?php echo $name; ?>">
                        <tr>
                            <td></td>
                            <?php foreach (range(1, 18) as $hole) { ?>
                                <td><?php echo $hole; ?></td>
                            <?php } ?>    
                        </tr>
                        <?php $i = 1; ?>
                        <?php foreach ($matchup as $player => $round) { ?>
                            <tr class="team<?php echo $i; ?>">
                                <td><?php echo $player; ?></td>
                                <?php foreach ($round as $hole => $score) { ?>
                                    <td class="<?php if (($player == $tournament->progress()[$name][$hole + 1])) { ?>winner<?php } ?>"><?php if (($hole + 1 <= $tournament->finished()[$name])) { ?><?php echo $score; ?><?php } ?></td>
                                <?php } ?>
                            </tr>
                            <?php $i = 2; ?>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <?php foreach (range(1, 18) as $hole) { ?>
                                <td><?php echo $tournament->leader()[$name][$hole]; ?></td>
                            <?php } ?>    
                        </tr>
                    </table>
            </tr>
    </table>
<?php } ?>