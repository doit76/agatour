<style>
	nav.uk-navbar {
		background: #ffffff; }
	
	nav.uk-navbar ul.uk-navbar-nav li a {
		font-family:'Roboto Condensed',sans-serif;
		font-weight: 700;
		font-size: 18px;
		text-transform: uppercase;
		color: #104f91; }
	
	nav.uk-navbar ul.uk-navbar-nav li a:hover, 
	nav.uk-navbar ul.uk-navbar-nav li.uk-active a:hover,
	nav.uk-navbar ul.uk-navbar-nav li.uk-active a {
		background: #d8d8d8; }
		
</style>

        <?php $uri = $this->router->getRewriteUri(); ?>
        <?php
          $uri = explode("/", $uri);
        ?>
        
<nav class="uk-navbar">
    <ul class="uk-navbar-nav uk-hidden-small">
        <?php $navigation = Navigation::get(); ?>
        
        <?php foreach ($navigation as $item) { ?>
            <?php if (empty($item->parent)) { ?>
			<li class="<?php echo ((($item->has_children()) ? 'uk-parent' : '')); ?> <?php echo ((($uri[1] == $item->post_name) ? 'uk-active' : '')); ?>" <?php echo ((($item->has_children()) ? 'data-uk-dropdown' : '')); ?>><a href="<?php echo $item->url; ?>"><?php echo $item->post_name; ?></a>
                <?php if ($item->has_children()) { ?>
                    <div class="uk-dropdown uk-dropdown-navbar">
                        <ul class="uk-nav uk-nav-navbar">
                            <?php foreach ($item->get_children() as $child) { ?>
                                <li><a href="<?php echo $child->url; ?>"><?php echo $child->post_name; ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </li>
            <?php } ?>
        <?php } ?>
        
    </ul>
    <a class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas="{target:'#my-id'}"></a>
</nav>

<div id="my-id" class="uk-offcanvas">
    <div class="uk-offcanvas-bar">
        <ul class="uk-nav uk-nav-offcanvas" data-uk-nav>
        <?php foreach ($navigation as $item) { ?>
			<li class="<?php echo ((($item->has_children()) ? 'uk-parent' : '')); ?> <?php echo ((($uri[1] == $item->post_name) ? 'uk-active' : '')); ?>" <?php echo ((($item->has_children()) ? 'data-uk-dropdown' : '')); ?>><a href="<?php echo $item->url; ?>"><?php echo $item->post_name; ?></a>
                <?php if ($item->has_children()) { ?>
                    <div class="uk-dropdown uk-dropdown-navbar">
                        <ul class="uk-nav uk-nav-navbar">
                            <?php foreach ($item->get_children() as $child) { ?>
                                <li><a href="<?php echo $child->url; ?>"><?php echo $child->post_name; ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
        <?php } ?>
             
        </ul>
    </div>
</div>