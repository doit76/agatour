<h3>Courses</h3>
<table class="uk-table">
    <thead>
        <tr>
            <th colspan="4"></th>
            <th colspan="2">Rating</th>
        </tr>
        <tr>
            <th></th>
            <th>Par</th>
            <th>Distance</th>
            <th>Location</th>
            <th>Scratch</th>
            <th>Slope</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $course) { ?>
            <tr>   
                <td><a href="/course/<?php echo $course->post_name; ?>"><?php echo $course->post_title; ?></a></td>
                <td><?php echo $course->par; ?></td>
                <td><?php print_r($course->distance()); ?></td>
                <td><?php print_r($course->angle()); ?></td>
                <td><?php print_r($course->scratch); ?></td>
                <td><?php print_r($course->slope); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>