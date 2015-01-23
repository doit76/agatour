    <style>
        table.tournament tr td.team { width: 40% }
        table.tournament tr td.score { width: 10% }

    </style>
    <?php $team_1 = $tournament->get_team(1); ?>
    <?php $team_2 = $tournament->get_team(2); ?>
    <?php $results = $tournament->get_matchup_result(); ?>
    <table class="tournament">
        <thead>
            <tr>
                <td class="team"><?php echo $team_1['name']; ?></td>
                <td class="score"></td>
                <td class="score"></td>
                <td class="team"><?php echo $team_2['name']; ?></td>
            </tr>
        </thead>
            <tr>
                <td>
                    <?php foreach ($team_1['players'] as $player) { ?>
                        <?php echo $player; ?><br>
                    <?php } ?>
                </td>
                <td><?php echo $results['team_1']; ?></td>
                <td><?php echo $results['team_2']; ?></td>
                <td>
                    <?php foreach ($team_2['players'] as $player) { ?>
                        <?php echo $player; ?><br>
                    <?php } ?>                
                </td>
            </tr>

    </table>

    <br>
    <br>

    <table class="tournament">
        <thead>
            <tr>
                <td colspan="3">Head to Head</td>
            </tr>
        </thead>
            <?php foreach (range(1, 4) as $matchup) { ?>
            <tr>
                <td>
                    <?php echo $team_1['headtohead_' . $matchup]; ?>
                </td>
                <td>
                    <?php echo $results['headtohead_' . $matchup]['winner']['result']; ?><br>
                    <a data-uk-toggle="{target:'#fourball<?php echo $matchup; ?>'}">Details</a>
                </td>                <td>
                    <?php echo $team_2['headtohead_' . $matchup]; ?>
                </td>
            </tr>
            <?php } ?>
    </table>

    <br>
    <br>

    <style>
        
        table.scorecard tr td.winner span {
            color: #fff;
            display: block;
            width: 25px;
            height: 25px;
        }

        table.scorecard tr.team1 td.winner span {
            background: <?php echo $tournament->get_team(1, 'colour'); ?>; }
        
        table.scorecard tr.team2 td.winner span {
            background: <?php echo $tournament->get_team(2, 'colour'); ?>; }
 
        table.scorecard tr td.leader span {
            display: block;
            width: 25px;
            height: 10px; }
        
        table.scorecard tr td.team1 span {
            background: <?php echo $tournament->get_team(1, 'colour'); ?>; }
        
        table.scorecard tr td.team2 span {
            background: <?php echo $tournament->get_team(2, 'colour'); ?>; }
 
        table.scorecard tr td.current_score {
            font-size: 10px; }
   
    </style>

    <table class="tournament">
        <thead>
            <tr>
                <td colspan="3">Fourball</td>
            </tr>
        </thead>
            <?php foreach (range(1, 2) as $matchup) { ?>
            <tr>
                <td>
                    <?php echo $team_1['fourball_' . $matchup]; ?>
                </td>
                <td>
                    <?php echo $results['fourball_' . $matchup]['winner']['result']; ?><br>
                    <a data-uk-toggle="{target:'#fourball<?php echo $matchup; ?>'}">Details</a>
                </td>
                <td>
                    <?php echo $team_2['fourball_' . $matchup]; ?>
                </td>
            </tr>

            <?php } ?>
    </table>

    <br>
    <br>

    <table class="tournament">
        <thead>
            <tr>
                <td colspan="3">Foursomes</td>
            </tr>
        </thead>
            <?php foreach (range(1, 2) as $matchup) { ?>
            <tr>
                <td>
                    <?php echo $team_1['foursomes_' . $matchup]; ?>
                </td>
                <td>
                    <?php echo $results['foursomes_' . $matchup]['winner']['result']; ?><br>
                    <a data-uk-toggle="{target:'#foursomes<?php echo $matchup; ?>'}">Details</a>
                </td>
                <td>
                    <?php echo $team_2['foursomes_' . $matchup]; ?>
                </td>
            </tr>
            <?php } ?>
    </table>