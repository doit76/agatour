<table class="uk-table">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th>Winner</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tournaments as $tournament) { ?>
            <tr>   
                <td><?php echo $tournament->date; ?></td>
                <td>
                    <a href="/tournament/<?php echo $tournament->post_name; ?>"><?php echo $tournament->post_title; ?></a><br>
                    <?php echo $tournament->get_course()->post_title; ?>
                </td>
                <td><?php echo $tournament->winner; ?> (<?php echo $tournament->score; ?>)</td>
            </tr>
        <?php } ?>
    </tbody>
</table>