<style>
    ul.uk-pagination {
        margin-top: 20px; }
    
     ul.uk-pagination li a{
         text-shadow: none;
         color: #fff;
         background: #005391;
         border-radius: 0;
         font-weight: 300;
     }

     ul.uk-pagination li.uk-active span, ul.uk-pagination li:hover a{
         text-shadow: none;
         color: #005391;
         border-color: #005391;
         background: #fff;
         border-radius: 0;
     }    

         ul.uk-pagination li.uk-disabled span{
         text-shadow: none;
         color: #005391;
         border-color: #005391;
         background: #fff;
         border-radius: 0;
         font-weight: 300;
     }    
</style>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <ul class="uk-pagination uk-pagination-left">
            <?php if ($page->current != 1) { ?> 
                <li><a href="/<?php echo $pagination; ?>/<?php echo $page->before; ?>">Previous</a></li>
            <?php } ?>
            <?php foreach (range(1, $page->total_pages) as $i) { ?>
                <?php if ($i == $page->current) { ?> 
                    <li class="uk-active"><span><?php echo $i; ?></span></li>
                <?php } else { ?>
                    <li><a href="/<?php echo $pagination; ?>/<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php } ?>
            <?php } ?>
            <?php if ($page->current != $page->total_pages) { ?> 
                <li><a href="/<?php echo $pagination; ?>/<?php echo $page->next; ?>">Next</a></li>
            <?php } ?>
        </ul>
    </div>
</div>