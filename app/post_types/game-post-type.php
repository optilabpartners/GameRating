<?php
namespace PostType;
// Register Custom Post Type
function game() {

	$labels = array(
		'name'                  => _x( 'Games', 'Post Type General Name', 'optilab' ),
		'singular_name'         => _x( 'Game', 'Post Type Singular Name', 'optilab' ),
		'menu_name'             => __( 'Game', 'optilab' ),
		'name_admin_bar'        => __( 'Game', 'optilab' ),
		'archives'              => __( 'Game Archives', 'optilab' ),
		'parent_item_colon'     => __( 'Parent Game:', 'optilab' ),
		'all_items'             => __( 'All Games', 'optilab' ),
		'add_new_item'          => __( 'Add New Game', 'optilab' ),
		'add_new'               => __( 'Add New', 'optilab' ),
		'new_item'              => __( 'New Game', 'optilab' ),
		'edit_item'             => __( 'Edit Game', 'optilab' ),
		'update_item'           => __( 'Update Game', 'optilab' ),
		'view_item'             => __( 'View Game', 'optilab' ),
		'search_items'          => __( 'Search Game', 'optilab' ),
		'not_found'             => __( 'Not found', 'optilab' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'optilab' ),
		'featured_image'        => __( 'Featured Image', 'optilab' ),
		'set_featured_image'    => __( 'Set featured image', 'optilab' ),
		'remove_featured_image' => __( 'Remove featured image', 'optilab' ),
		'use_featured_image'    => __( 'Use as featured image', 'optilab' ),
		'insert_into_item'      => __( 'Insert into lesson', 'optilab' ),
		'uploaded_to_this_item' => __( 'Uploaded to this lesson', 'optilab' ),
		'items_list'            => __( 'Games list', 'optilab' ),
		'items_list_navigation' => __( 'Games list navigation', 'optilab' ),
		'filter_items_list'     => __( 'Filter lessons list', 'optilab' ),
	);
	$args = array(
		'label'                 => __( 'Game', 'optilab' ),
		'description'           => __( 'Game', 'optilab' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-chart-area',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'								=> array('slug' => 'game/game_org/season/week', 'with_front' => false, 'pages' => false),
		'capability_type'       => 'post',
	);
	register_post_type( 'game', $args );

}
add_action( 'init', __NAMESPACE__ . '\\game', 0 );

// @todo Video post meta