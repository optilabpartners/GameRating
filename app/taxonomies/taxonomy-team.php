<?php
namespace Taxonomy;
use Optilab;
// Register Custom Taxonomy
function taxonomy_team() {

	$labels = array(
		'name'                       => _x( 'Game Teams', 'Taxonomy General Name', 'optilab' ),
		'singular_name'              => _x( 'Game Team', 'Taxonomy Singular Name', 'optilab' ),
		'menu_name'                  => __( 'Game Teams', 'optilab' ),
		'all_items'                  => __( 'All Game Teams', 'optilab' ),
		'parent_item'                => __( 'Parent Game Team', 'optilab' ),
		'parent_item_colon'          => __( 'Parent Game Team:', 'optilab' ),
		'new_item_name'              => __( 'New Game Team Name', 'optilab' ),
		'add_new_item'               => __( 'Add New Game Team', 'optilab' ),
		'edit_item'                  => __( 'Edit Game Team', 'optilab' ),
		'update_item'                => __( 'Update Game Team', 'optilab' ),
		'view_item'                  => __( 'View Game Team', 'optilab' ),
		'separate_items_with_commas' => __( 'Separate game orgs with commas', 'optilab' ),
		'add_or_remove_items'        => __( 'Add or remove game orgs', 'optilab' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'optilab' ),
		'popular_items'              => __( 'Popular Game Teams', 'optilab' ),
		'search_items'               => __( 'Search Game Teams', 'optilab' ),
		'not_found'                  => __( 'Not Found', 'optilab' ),
		'no_terms'                   => __( 'No teams', 'optilab' ),
		'items_list'                 => __( 'Game Teams list', 'optilab' ),
		'items_list_navigation'      => __( 'Game Teams list navigation', 'optilab' ),
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
	register_taxonomy( 'team', array( 'game' ), $args );
	
}
add_action( 'init', __NAMESPACE__ . '\\taxonomy_team', 0 );