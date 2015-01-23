<h3><?php echo $course->post_title; ?></h3>

<style>
    table.scorecard {
        font-size: 10px; }

    table.scorecard tr td,table.scorecard tr th {
        padding: 0;
        text-align: center; }
    
</style>


<div class="uk-grid">
    <div class="uk-width-1-2">
        <iframe width="100%" height="250" frameborder="0" style="border:0"
        src="https://www.google.com/maps/embed/v1/place?q=<?php echo $course->map()['address']; ?>&key=AIzaSyA4I_qDsUql6A2_shX5slmp421hQsjUzuY"></iframe>
    </div>
    <div class="uk-width-1-2">
        <h4>Rating</h4>
        <div class="uk-progress">
            <div class="uk-progress-bar" style="width: <?php echo $course->price / 5 * 100; ?>%;">Price</div>
        </div>

        <div class="uk-progress">
            <div class="uk-progress-bar" style="width: <?php echo $course->course / 5 * 100; ?>%;">Course</div>
        </div>
        
    </div>
</div>

<table class="uk-table scorecard">
    <thead>
        <tr>
            <th>Hole</th>
            <?php foreach (range(1, 18) as $i) { ?>
                <th><?php echo $i; ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Distance (M)</td>
            <?php foreach (range(1, 18) as $i) { ?>
                <td><?php echo $course->scorecard('distance', $i); ?></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Par</td>
            <?php foreach (range(1, 18) as $i) { ?>
                <td><?php echo $course->scorecard('par', $i); ?></td>
            <?php } ?>
        </tr>
    </tbody>
</table>