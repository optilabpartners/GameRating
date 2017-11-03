<?php
namespace Taxonomy;
use Optilab;
// Register Custom Taxonomy
function taxonomy_game_tag() {

	$labels = array(
		'name'                       => _x( 'Game Tags', 'Taxonomy General Name', 'optilab' ),
		'singular_name'              => _x( 'Game Tag', 'Taxonomy Singular Name', 'optilab' ),
		'menu_name'                  => __( 'Game Tags', 'optilab' ),
		'all_items'                  => __( 'All Game Tags', 'optilab' ),
		'parent_item'                => __( 'Parent Game Tag', 'optilab' ),
		'parent_item_colon'          => __( 'Parent Game Tag:', 'optilab' ),
		'new_item_name'              => __( 'New Game Tag Name', 'optilab' ),
		'add_new_item'               => __( 'Add New Game Tag', 'optilab' ),
		'edit_item'                  => __( 'Edit Game Tag', 'optilab' ),
		'update_item'                => __( 'Update Game Tag', 'optilab' ),
		'view_item'                  => __( 'View Game Tag', 'optilab' ),
		'separate_items_with_commas' => __( 'Separate game tags with commas', 'optilab' ),
		'add_or_remove_items'        => __( 'Add or remove game tags', 'optilab' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'optilab' ),
		'popular_items'              => __( 'Popular Game Tags', 'optilab' ),
		'search_items'               => __( 'Search Game Tags', 'optilab' ),
		'not_found'                  => __( 'Not Found', 'optilab' ),
		'no_terms'                   => __( 'No Tags', 'optilab' ),
		'items_list'                 => __( 'Game Tags list', 'optilab' ),
		'items_list_navigation'      => __( 'Game Tags list navigation', 'optilab' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'game_tag', array( 'game' ), $args );
	
}
add_action( 'init', __NAMESPACE__ . '\\taxonomy_game_tag', 0 );