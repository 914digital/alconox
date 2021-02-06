<?php 

// Register Custom Post Type: Industry

add_action('init', 'industries_register');
	
function industries_register() {
	
	$labels = array(
		'name' => _x('Industries', 'post type general name'),
		'singular_name' => _x('Industry', 'post type singular name'),
		'add_new' => _x('Add New', 'Industry Item'),
		'add_new_item' => __('Add New Industry'),
		'edit_item' => __('Edit Industry'),
		'new_item' => __('New Industry'),
		'view_item' => __('View Industry Item'),
		'search_items' => __('Search Industry Items'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-chart-line',
		'rewrite' => array('slug' => 'industries','with_front' => false),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail'),
		'taxonomies' => array( 'category', 'post_tag' ),
		); 
	
	register_post_type( 'industries' , $args );
}

// Register Custom Post Type: Videos
	
add_action('init', 'videos_register');
	 
function videos_register() {
 
	$labels = array(
		'name' => _x('Videos', 'post type general name'),
		'singular_name' => _x('Video', 'post type singular name'),
		'add_new' => _x('Add New', 'Video Item'),
		'add_new_item' => __('Add New Video'),
		'edit_item' => __('Edit Video'),
		'new_item' => __('New Video'),
		'view_item' => __('View Video Item'),
		'search_items' => __('Search Videos Items'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-video-alt3',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail'),
		'taxonomies' => array( 'category', 'post_tag' ),
	  ); 
 
	register_post_type( 'videos' , $args );
}

// Register Custom Post Type: COAs
	
add_action('init', 'coas_register');
	 
function coas_register() {
 
	$labels = array(
		'name' => _x('COAs', 'post type general name'),
		'singular_name' => _x('COA', 'post type singular name'),
		'add_new' => _x('Add New', 'COA Item'),
		'add_new_item' => __('Add New COA'),
		'edit_item' => __('Edit COA'),
		'new_item' => __('New COA'),
		'view_item' => __('View COA Item'),
		'search_items' => __('Search COA Items'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => 'dashicons-media-document',
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail'),
		'taxonomies' => array( 'year'),
	  ); 
 
	register_post_type( 'coas' , $args );
}

add_action( 'init', 'coas_taxonomy', 0);

function coas_taxonomy() {

$labels = array(
'name' => _x( 'Year', 'taxonomy general name' ),
'singular_name' => _x( 'Year', 'taxonomy singular name' ),
'search_items' =>  __( 'Search Year' ),
'all_items' => __( 'All Years' ),
'parent_item' => __( 'Parent Year' ),
'parent_item_colon' => __( 'Parent Year:' ),
'edit_item' => __( 'Edit Year' ), 
'update_item' => __( 'Update Year' ),
'add_new_item' => __( 'Add New Year' ),
'new_item_name' => __( 'New Year Name' ),
'menu_name' => __( 'Year' ),
); 	

register_taxonomy('year-coas', array('coas'), array(
'hierarchical' => true,
'labels' => $labels,
'show_ui' => true,
'show_in_rest' => true,
'show_admin_column' => true,
'query_var' => true,
'rewrite' => array( 'slug' => 'year' ),
));
}


?>