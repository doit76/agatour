<!DOCTYPE html>
<html>
    <head profile="http://www.w3.org/2005/10/profile">
        <link rel="icon" type="image/png" href="/img/favicon.png">
        <title><?php if ((isset($title))) { ?><?php echo $title; ?><?php } else { ?>AGATOUR<?php } ?></title>
        <meta property="og:title" content="<?php if ((isset($title))) { ?><?php echo $title; ?><?php } else { ?>AGATOUR<?php } ?>" />        
        <meta property="og:image" content="<?php if ((isset($image))) { ?><?php echo $image; ?><?php } ?>" />
        <meta property="og:description" content="<?php if ((isset($description))) { ?><?php echo $description; ?><?php } else { ?>Official Website of the AGA Tour.<?php } ?>" />

        <?php echo $this->tag->stylesheetLink('css/uikit.almost-flat.css'); ?>
        <?php echo $this->tag->stylesheetLink('css/style.css'); ?>

        <?php echo $this->tag->javascriptInclude('http://code.jquery.com/jquery-2.1.1.min.js'); ?>
        <?php echo $this->tag->javascriptInclude('js/uikit.min.js'); ?>

        <?php echo $this->partial('analytics'); ?>
        <?php echo $this->partial('addthis'); ?>
    </head>
    <body>
        <div class="uk-container uk-container-center uk-margin-top uk-margin-large-bottom">
            <div class="uk-grid uk-margin-top uk-margin-bottom uk-hidden-small">
                <div class="uk-width-2-10 logo"><?php echo $this->tag->image(array('img/logo.png', 'alt' => 'AGA Tour')); ?></div>
                <div class="uk-width-5-10">...</div>
                <div class="uk-width-3-10 social">
                    <a target="_blank" href="http://facebook.com/agatourgolf" class="uk-icon-medium uk-icon-facebook"></a>
                    <a target="_blank" href="http://twitter.com/agatour_golf" class="uk-icon-medium uk-icon-twitter"></a>
                    <a target="_blank" href="http://youtube.com/agatour" class="uk-icon-medium uk-icon-youtube"></a>
                    <a target="_blank" href="http://instagram.com/agatour" class="uk-icon-medium uk-icon-instagram"></a>
                </div>
            </div>
            <?php echo $this->partial('theme/nav'); ?>
            <div class="page-content uk-margin-top uk-margin-large-bottom">
                <div class="uk-grid">
                    <?php if (isset($fullwidth)) { ?>
                        <div class="uk-width-1-1"><?php echo $this->getContent(); ?></div>
                    <?php } else { ?>
                        <div class="uk-width-large-7-10 uk-width-small-1-1"><?php echo $this->getContent(); ?></div>
                        <div class="uk-width-3-10 uk-hidden-small"><?php echo $this->partial('theme/sidebar'); ?></div>
                    <?php } ?>
				</div>
            </div>								

            <div class="uk-grid uk-margin-top uk-margin-bottom uk-text-center">
                <div class="uk-width-1-1 copyright">
				    &copy; 2012-2014 AGA TOUR, Inc | All Rights Reserved.<br>
                    AGA TOUR, Nandos Cup, and the AGA Ranking System are registered trademarks.<br>
				</div>
            </div>
        </div>
    </body>
</html>