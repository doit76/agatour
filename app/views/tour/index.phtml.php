<h3><?php echo $season->post_title; ?></h3>

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

<?php if ($season->reset) { ?>
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
        <?php foreach ($season->get_standings() as $players) { ?>
            <?php if ($players->events > 0) { ?>
            <tr>   
                <td><?php echo $players->playoff_position; ?></td>
                <td><?php echo $players->name; ?></td>
                <td><?php echo $players->events; ?></td>
                <td><?php echo $players->wins; ?></td>
                <td><?php echo $players->top2; ?></td>
                <td><?php echo $players->playoff_points; ?></td>
            </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>

<?php } else { ?>

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
        <?php foreach ($season->get_standings() as $players) { ?>
            <?php if ($players->events > 0) { ?>
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

<?php } ?>