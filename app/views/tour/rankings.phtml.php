<h3>Rankings</h3>

<style>
    table.standings tr th {
        vertical-align: middle;
        text-align: center; }

    table.standings tr td {
        vertical-align: middle;
        text-align: center; }
    

    table.standings tr td.flag {
        text-align: center;
        width: 25px; }
</style>

<table class="uk-table standings">
    <thead>
        <tr>
            <td></td>
            <td></td>
            <td>Events Played</td>
            <td>Wins</td>
            <td>Top 2</td>
            <td>Points</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($season->get_rankings() as $players) { ?>
            <?php if ($players->points > 0) { ?>
            <tr>   
                <td><?php echo $players->position; ?></td>
                <td><?php echo $players->name; ?></td>
                <td><?php echo $players->events; ?></td>
                <td><?php echo $players->wins; ?></td>
                <td><?php echo $players->top2; ?></td>
                <td><?php echo $players->points; ?></td>
            </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>