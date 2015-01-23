<?php

    add_action( 'init', 'create_posttype' );
    function create_posttype() {
      register_post_type( 'player',
        array(
          'labels' => array(
            'name' => __( 'Players' ),
            'singular_name' => __( 'Player' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'player'),
          'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' )
        )
      );
        
      register_post_type( 'tournament',
        array(
          'labels' => array(
            'name' => __( 'Tournaments' ),
            'singular_name' => __( 'Tournament' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'tournament'),
          'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' )
       )
      );
        
      register_post_type( 'season',
        array(
          'labels' => array(
            'name' => __( 'Seasons' ),
            'singular_name' => __( 'Season' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'season'),
          'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' )
       )
      );
        
      register_post_type( 'course',
        array(
          'labels' => array(
            'name' => __( 'Courses' ),
            'singular_name' => __( 'Course' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'course'),
          'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' )
       )
      );
        
      register_post_type( 'round',
        array(
          'labels' => array(
            'name' => __( 'Rounds' ),
            'singular_name' => __( 'Round' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'round'),
          'supports' => array( 'title', 'revisions' )
       )
      );
        
      register_post_type( 'live',
        array(
          'labels' => array(
            'name' => __( 'Live' ),
            'singular_name' => __( 'Live' )
          ),
          'public' => true,
          'has_archive' => true,
          'rewrite' => array('slug' => 'live'),
          'supports' => array( 'title', 'revisions' )
       )
      );
    }