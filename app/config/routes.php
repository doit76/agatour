<?php

// Create the router
$router = new \Phalcon\Mvc\Router();

//Define a route
$router->add("/{championship:[a-z]+}", "Tour::index");

$router->add("/{year:[0-9]+}/{month:[0-9]+}/{slug:[a-zA-Z0-9_-]+}", "Blog::single");

$router->add("/", "Index::index");

$router->add("/about", "Index::about");
$router->add("/contact", "Index::contact");
$router->add("/history", "Index::history");
$router->add("/awards", "Index::awards");

/* PLAYERS */
$router->add("/players", "Player::index");
$router->add("/player/{slug}", "Player::single")->setName("get-player");
$router->add("/player/round", "Ajax::round");

/* COURSES */
$router->add("/courses", "Course::index");
$router->add("/course/{slug}", "Course::single");

/* BLOG */
$router->add("/blog[/]?{page:[0-9]*}", "Blog::index");
$router->add("/category/{category}[/]?{page:[0-9]*}", "Blog::category");
$router->add("/tag/{tag}[/]?{page:[0-9]*}", "Blog::tag");

/* TOURNAMENTS */
$router->add("/schedule[/]?{type:[a-z]*}[/]?{year:[0-9]*}", "Tournament::index");
$router->add("/tournament/{slug}", "Tournament::single");

$router->add("/rankings", "Tour::rankings");

$router->add("/live", "Live::score");

$router->add("/update-tournament", "Tournament::updateall");
$router->add("/update-player", "Tournament::insertall");

$router->handle();