<?php

    require_once 'includes/post_types.php';
    require_once 'includes/taxonomies.php';
    require_once 'includes/jigsaw.php';

    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'post-formats', array( 'video' ) );

    function register_my_menus() {
      register_nav_menus(
        array(
          'header-menu' => __( 'Header Menu' ),
          'extra-menu' => __( 'Extra Menu' )
        )
      );
    }
    add_action( 'init', 'register_my_menus' );