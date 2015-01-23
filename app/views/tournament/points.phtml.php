<table class="uk-table">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th>Winner</th>
            <th>Points</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tournaments as $tournament) { ?>
                    <?php 
/*
    if($tournament->type != 'cup' || $tournament->scoring != 'match'):

        $tournament->update_tournament(); 

        endif; */
        ?>  
            <tr>   
                <td><?php echo $tournament->date; ?></td>
                <td>
                    <a href="/tournament/<?php echo $tournament->post_name; ?>"><?php echo $tournament->post_title; ?></a><br>
                    <?php echo $tournament->get_course()->post_title; ?>
                </td>
                <td><?php echo $tournament->winner; ?> (<?php echo $tournament->score; ?>)</td>
                <td><?php echo $tournament->points; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>