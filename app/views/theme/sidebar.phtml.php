<style>
    a.heading-sidebar {
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        font-size: 12px;
        margin-bottom: 2px;
        display: block; }
    
    hr.article-sidebar {
        margin: 2px 0 ; }
</style>

<?php $latest_posts = Post::latest(); ?>

<div class="uk-grid uk-margin-bottom">
    <div class="uk-width-1-1">
        <h3>Latest News</h3>
        <?php foreach ($latest_posts as $post) { ?>
            <a class="heading-sidebar" href="<?php echo $post->link; ?>"><?php echo $post->post_title; ?></a>
            <hr class="article-sidebar">
        <?php } ?>
    </div>
</div>

<style>
    table.tournament {
        width: 100%; }
    
</style>


<?php $latest_tournament = Tournament::latest(); ?>
<div class="uk-grid uk-margin-bottom">
    <div class="uk-width-1-1">
    <table class="uk-table tournament">
            <?php if (($latest_tournament->scoring == 'stroke')) { ?>

        <thead>
            <tr>
                <td colspan="3"><a style="color: #fff;" href="/tournament/<?php echo $latest_tournament->post_name; ?>"><?php echo $latest_tournament->post_title; ?></a></td>
            </tr>
            <tr>
                <td rowspan="2">Pos</td>
                <td rowspan="2" class="name">Player Name</td>
                <td rowspan="2">Adjusted</td>
            </tr>
        </thead>

            <?php foreach ($latest_tournament->get_rounds() as $round) { ?>
            <tr>
                <td><?php echo $round->position; ?></td>
                <td><?php echo $round->player()->post_title; ?></td>
                <td><?php echo $round->total; ?> (<?php echo $round->adjusted; ?>)</td>                
            </tr>        
            <?php } ?>
            <?php } elseif (($latest_tournament->scoring == 'match')) { ?>
                <thead>
                    <tr>
                        <td colspan="3"><a style="color: #fff;" href="/tournament/<?php echo $latest_tournament->post_name; ?>"><?php echo $latest_tournament->post_title; ?></a></td>
                    </tr>
                </thead>
        
                <?php foreach ($latest_tournament->matchplay() as $name => $matchup) { ?>
                 <tr>
                    <td>
                        <?php echo $latest_tournament->players()[$name][1]; ?>
                    </td>
                    <td>
                        <?php echo $latest_tournament->winner()[$name]; ?><br>
                    </td>                
                    <td>
                        <?php echo $latest_tournament->players()[$name][2]; ?>
                    </td>
                </tr>              
                <?php } ?>
            <?php } ?>
    </table>
    </div>
</div>