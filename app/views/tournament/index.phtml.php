<h3><?php echo $year; ?> <?php echo ucwords($type); ?> Schedule</h3>

<h4>Select Year</h4>
<?php foreach (range(2013, date('Y') + 1) as $prev_year) { ?>
    <a class="<?php echo ((($prev_year == $year) ? 'active ' : '')); ?>button" href="/schedule/<?php echo $type; ?>/<?php echo $prev_year; ?>"><?php echo $prev_year; ?></a>
<?php } ?>

<h4>Select Schedule Type</h4>
    <a class="<?php echo ((($type == 'tour') ? 'active ' : '')); ?>button" href="/schedule/tour/<?php echo $year; ?>">Tour</a> 
    <a class="<?php echo ((($type == 'cup') ? 'active ' : '')); ?>button" href="/schedule/cup/<?php echo $year; ?>">Cup</a> 
    <a class="<?php echo ((($type == 'exhibition') ? 'active ' : '')); ?>button" href="/schedule/exhibition/<?php echo $year; ?>">Exhibition</a> 
    <a class="<?php echo ((($type == 'practice') ? 'active ' : '')); ?>button" href="/schedule/practice/<?php echo $year; ?>">Practice</a>

<?php if ($type == 'tour' || $type == 'exhibition') { ?>
    <?php echo $this->partial('tournament/points'); ?>
<?php } else { ?>
    <?php echo $this->partial('tournament/other'); ?>
<?php } ?>