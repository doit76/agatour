<style>
    div.image img { width: 100%; }
</style>
<?php foreach ($page->items as $post) { ?>
<div class="uk-grid">
    <?php if (($post->has_image())) { ?>
        <div class="uk-width-1-4 image"><?php echo $post->photo; ?></div>
        <div class="uk-width-3-4">
            <?php echo $this->partial($post->page_format()); ?>
        </div>
    <?php } else { ?>
        <div class="uk-width-1-1">
            <?php echo $this->partial($post->page_format()); ?>
        </div>   
    <?php } ?>
</div>
<hr class="article">
<?php } ?>
<?php echo $this->partial('blog/pagination'); ?>