<?php foreach ($players as $player) { ?>
    <a href="/player/<?php echo $player->post_name; ?>"><?php echo $player->post_title; ?></a><br>
    <?php foreach($player->getRounds() as $round):
            echo $round->score . '<br>';
        endforeach; ?>
<?php } ?>