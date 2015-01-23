<h3>AGA Tour <?php echo $season->year; ?> Summary</h3>

<style>
    table.standings tr td {
        vertical-align: middle;
        text-align: center; }
 
    table.standings tr td.tournament {
        vertical-align: middle;
        text-align: left; }
    
</style>

<table class="uk-table standings">
    <thead>
        <tr>
            <td rowspan="2">Events Played</td>
            <td rowspan="2">Wins</td>
            <td rowspan="2">Top 2</td>
            <td rowspan="2">Points</td>
            <td colspan="2">AGA Cup</td>
        </tr>
        <tr>
            <td>Points</td>
            <td>Rank</td>
        </tr>
    </thead>
    <tbody>
            <tr>   
                <td><?php echo $season->events; ?></td>
                <td><?php echo $season->wins; ?></td>
                <td><?php echo $season->top2; ?></td>
                <td><?php echo $season->points; ?></td>
                <td><?php echo $season->aga_points; ?></td>
                <td><?php echo $season->aga_rank; ?></td>
            </tr>
    </tbody>
</table>


<table class="uk-table standings">
    <thead>
        <tr>
            <td>Date</td>
            <td>Position</td>
            <td class="tournament">Tournament</td>
            <td>Total</td>
            <td>Differential</td>
            <td>Adjusted</td>
            <td>Points</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($season->rounds as $round) { ?>
            <tr>   
                <td><?php echo $round->tournament()->date; ?></td>
                <td><?php echo $round->position; ?></td>
                <td class="tournament"><?php echo $round->tournament()->post_title; ?></td>
                <td><?php echo $round->total; ?></td>
                <td><?php echo $round->differential; ?></td>
                <td><?php echo $round->official; ?></td>
                <td><?php echo $round->points; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>