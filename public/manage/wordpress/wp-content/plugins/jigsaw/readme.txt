=== Jigsaw ===
Contributors: jarednova
Tags: admin, configuration
Requires at least: 3.7
Stable tag: 0.5
Tested up to: 4.0
PHP version: 5.3.0 or greater
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple ways to customize your WordPress build.

== Description ==
Simple ways to make admin customizations for WordPress. You know all that brain space you saved for [memorizing hooks](http://wptavern.com/learn-three-wordpress-filters-a-day)? Use it for something better. For example, you can...

* On [GitHub](https://github.com/upstatement/zoneboard)

### Add a column to an admin page!

###### `Jigsaw::add_column($post_type, $column_label, $callback_function, $order = 10);`

`
Jigsaw::add_column('slides', 'Preview', function($pid){
  	$data = array();
	$data['post'] = new TimberPost($pid);
	Timber::render('admin/slide-table-preview.twig', $data);
}, 5);
`

`
Jigsaw::add_column(array('slides', 'post'), 'Preview', function($pid){
  	$data = array();
	$data['post'] = new TimberPost($pid);
	Timber::render('admin/slide-table-preview.twig', $data);
});
`

### Remove a column from the admin

###### `Jigsaw::remove_column($post_types, $column_slug);`

`
Jigsaw::remove_column('slides', 'author');
`

`
Jigsaw::remove_column(array('slides', 'post'), 'author');
`

### Add something to the admin bar

###### `Jigsaw::add_toolbar_item($label, $url_or_callback_function);`
`
Jigsaw::add_toolbar_item('Clear Cache', function(){
	$total_cache->flush_all();
});
`

### Add a dropdown

###### `Jigsaw::add_toolbar_group($label, $items);`
`
$optionOne = new stdClass();
$optionOne->label = 'All Caches';
$optionOne->action = function(){
	$total_cache->flush_all();
};
$optionTwo = new stdClass();
$optionTwo->label = 'Page Cache';
$optionTwo->action = function(){
	$total_cache->flush_page_cache();
};
$optionThree = array('Home', 'http://localhost');
Jigsaw::add_toolbar_group('Clear Cache', array($optionOne, $optionTwo, $optionThree));
`

### Show an admin notice

###### `Jigsaw::show_notice($message, $level = 'updated');`

`
Jigsaw::show_notice('Cache has been flushed', 'updated');
`
...or
`
Jigsaw::show_notice('Error flushing cache, is the plugin activated?', 'error');
`

### Add a CSS file to the admin

###### `Jigsaw::add_css($css_file);`

`
Jigsaw::add_css('css/my-admin-style.css');
`

#JigsawPermalinks

### Set the base of the author permalink

###### `JigsawPermalinks::set_author_base($base_string);`

`
JigsawPermalinks::set_author_base('writers');
`
After this you have to reset permalinks to see it working.

### Remove a custom post type permalink

###### `JigsawPermalinks::remove_permalink_slug($custom_post_type)`;

`
JigsawPermalinks::remove_permalink_slug('event');
`

or

`
JigsawPermalinks::remove_permalink_slug(array('event', 'book', 'my_other_cpt'));
`

### Set a custom permalink
###### `JigsawPermalinks::set_permalink($post_type, $structure);`

`
JigsawPermalinks::set_permalink('gallery', '/galleries/%year%/%gallery%');
`



== Installation ==

1. Activate the plugin through the 'Plugins' menu in WordPress

== Support ==

Please use the [GitHub repo](https://github.com/upstatement/jigsaw/issues?state=open) to file bugs or questions.