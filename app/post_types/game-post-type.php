<?php
namespace PostType;
// Register Custom Post Type
function game() {

	$labels = array(
		'name'                  => _x( 'Games', 'Post Type General Name', 'sage' ),
		'singular_name'         => _x( 'Game', 'Post Type Singular Name', 'sage' ),
		'menu_name'             => __( 'Game', 'sage' ),
		'name_admin_bar'        => __( 'Game', 'sage' ),
		'archives'              => __( 'Game Archives', 'sage' ),
		'parent_item_colon'     => __( 'Parent Game:', 'sage' ),
		'all_items'             => __( 'All Games', 'sage' ),
		'add_new_item'          => __( 'Add New Game', 'sage' ),
		'add_new'               => __( 'Add New', 'sage' ),
		'new_item'              => __( 'New Game', 'sage' ),
		'edit_item'             => __( 'Edit Game', 'sage' ),
		'update_item'           => __( 'Update Game', 'sage' ),
		'view_item'             => __( 'View Game', 'sage' ),
		'search_items'          => __( 'Search Game', 'sage' ),
		'not_found'             => __( 'Not found', 'sage' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'sage' ),
		'featured_image'        => __( 'Featured Image', 'sage' ),
		'set_featured_image'    => __( 'Set featured image', 'sage' ),
		'remove_featured_image' => __( 'Remove featured image', 'sage' ),
		'use_featured_image'    => __( 'Use as featured image', 'sage' ),
		'insert_into_item'      => __( 'Insert into lesson', 'sage' ),
		'uploaded_to_this_item' => __( 'Uploaded to this lesson', 'sage' ),
		'items_list'            => __( 'Games list', 'sage' ),
		'items_list_navigation' => __( 'Games list navigation', 'sage' ),
		'filter_items_list'     => __( 'Filter lessons list', 'sage' ),
	);
	$args = array(
		'label'                 => __( 'Game', 'sage' ),
		'description'           => __( 'Game', 'sage' ),
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
		'rewrite'				=> array('slug' => 'game/game_org/season/week', 'with_front' => false, 'pages' => false),
		'capability_type'       => 'post',
	);
	register_post_type( 'game', $args );

}
add_action( 'init', __NAMESPACE__ . '\\game', 0 );

// @todo Video post meta