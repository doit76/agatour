<style>
    a.heading {
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        font-size: 20px;
        margin-bottom: 5px;
        display: block; }
    
     span.date {
        margin: 5px 0;
        display:block;
        font-weight: 300;
        font-size: 15px;
        text-decoration: none;
        text-transform: uppercase;
        color: #888; }
    
     span.category a {
        display: inline-block;
        margin: 5px 0;
        font-weight: 300;
        font-size: 12px;
        text-decoration: none;
        padding: 2px 4px;
        text-transform: uppercase;
        color: #fff;
        background: #005391; }
    
     span.tag a {
        font-size: 12px;
        text-decoration: none;
        padding: 2px 4px;
        text-transform: uppercase;
        color: #005391;
        background: #d8d8d8; }
    
</style>

<a class="heading"><?php echo $post->post_title; ?></a>
<span class="date"><?php echo $post->date; ?></span>
    <?php $meta_data = $post->get_tags(); ?>
    <?php if ((is_array($meta_data['category']) || ($meta_data['category']) instanceof Traversable)) { ?>
        <?php foreach ($meta_data['category'] as $tag) { ?>
            <span class="category"><a href="/tag/<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></a></span>
        <?php } ?>
    <?php } ?>

<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_sharing_toolbox"></div>
<p><?php echo $post->post_content; ?></p>

    <?php if ((is_array($meta_data['tag']) || ($meta_data['tag']) instanceof Traversable)) { ?>
        <?php foreach ($meta_data['tag'] as $tag) { ?>
            <span class="tag"><a href="/tag/<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></a></span>
        <?php } ?>
    <?php } ?>