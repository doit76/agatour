<style>
    a.heading {
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        font-size: 18px;
        margin-bottom: 5px;
        display: block; }
    
    p.meta {
        margin: 0;
        display: block; }
    
    span.date, span.category a {
        margin: 5px 0 10px 0;
        display: inline-block;
        font-weight: 300;
        font-size: 12px;
        text-decoration: none;
        padding: 2px 4px;
        text-transform: uppercase;
        color: #fff;
        background: #005391; }
    
    span.category:hover a {
        color: #005391;
        background: #fff; }
    
    p.excerpt {
        display: block;
        text-align: justify; }
  
    span.tag a {
        font-weight: 300;
        font-size: 12px;
        text-decoration: none;
        padding: 2px 4px;
        text-transform: uppercase;
        color: #005391;
        background: #d8d8d8; }
    
    span.tag:hover a {
        color: #005391;
        background: #fff; }
    
    a.more {
        font-size: 12px;
        text-decoration: none;
        padding: 2px 4px;
        float: right;
        text-transform: uppercase;
        display: block;
        color: #fff;
        background: #005391; }
    
    a.more:hover {
        color: #005391;
        background: #fff; }

</style>
<?php $meta_data = $post->get_tags(); ?>

<a class="heading" href="<?php echo $post->link; ?>"><?php echo $post->post_title; ?></a>
<p class="meta">
    <span class="date"><?php echo $post->date; ?></span>
    <?php if ((is_array($meta_data['category']) || ($meta_data['category']) instanceof Traversable)) { ?>
        <?php foreach ($meta_data['category'] as $tag) { ?>
            <span class="category"><a href="/tag/<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></a></span>
        <?php } ?>
    <?php } ?>
</p>
<span class="excerpt"><?php echo $post->excerpt; ?></span>
<p>
    <?php if ((is_array($meta_data['tag']) || ($meta_data['tag']) instanceof Traversable)) { ?>
        <?php foreach ($meta_data['tag'] as $tag) { ?>
            <span class="tag"><a href="/tag/<?php echo $tag->slug; ?>"><?php echo $tag->name; ?></a></span>
        <?php } ?>
    <?php } ?>
    <a class="more" href="<?php echo $post->link; ?>">Read More</a>
</p>