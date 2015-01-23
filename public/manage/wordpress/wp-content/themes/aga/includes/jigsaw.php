<?php

    Jigsaw::remove_column('round', 'date');
    Jigsaw::add_column('round', 'Player', function($post_id){
        $player = get_post_meta($post_id, 'player', true);
        echo get_the_title($player[0]);
    }, 2);

    Jigsaw::add_column('round', 'Matchup', function($post_id){
        $matchup = get_post_meta($post_id, 'matchup', true);
        echo $matchup;
    }, 2);

    Jigsaw::add_column('round', 'Tournament', function($post_id){
        $tournament = get_post_meta($post_id, 'tournament', true);
        echo get_the_title($tournament[0]);
    },3);

    Jigsaw::add_column('round', 'Tournament Date', function($post_id){
        $tournament = get_post_meta($post_id, 'tournament', true);
        echo get_the_time('d/m/y', $tournament[0]);
    },4);



    Jigsaw::remove_column('tournament', 'date');

    Jigsaw::add_column('tournament', 'Course', function($post_id){
        $course = get_post_meta($post_id, 'course', true);
        echo get_the_title($course[0]);
    },3);

    Jigsaw::add_column('tournament', 'Points', function($post_id){
        echo get_post_meta($post_id, 'points', true);
    },4);

    Jigsaw::add_column('tournament', 'Tournament Date', function($post_id){
        $tournament = get_post_meta($post_id, 'tournament', true);
        echo get_the_time('d/m/y', $tournament[0]);
    },5);


    Jigsaw::add_column('course', 'Holes', function($post_id){
        echo get_post_meta($post_id, 'holes', true);
    },2);