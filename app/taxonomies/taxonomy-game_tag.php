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

add_action( 'game_tag_add_form_fields', function($taxonomy) {
	?>
  <div class="form-field term-game_org-wrap">
	<label for="game_org"><?php _e( 'Game Org', 'optilab' ); ?>
	<?php
	$args = array(
			'show_option_all'    => '',
			'show_option_none'   => 'Choose Org',
			'option_none_value'  => '-1',
			'orderby'            => 'ID',
			'order'              => 'ASC',
			'show_count'         => 0,
			'hide_empty'         => 1,
			'child_of'           => 0,
			'exclude'            => '',
			'include'            => '',
			'echo'               => 0,
			'selected'           => '',
			'hierarchical'       => 0,
			'name'               => 'game_org',
			'id'                 => 'gameOrg',
			'class'              => 'form-control',
			'depth'              => 0,
			'tab_index'          => 0,
			'taxonomy'           => 'game_org',
			'hide_if_empty'      => false,
			'value_field'	     => 'term_id',
	);
	echo wp_dropdown_categories( $args );
	?>
	<p class="description"><?php _e( 'Game Org','optilab ' ); ?></p>
  </div>
<?php
});

add_action( 'game_tag_edit_form_fields', function($term) {
  $game_org = get_term_meta( $term->term_id, 'game_org', true );
?>
  <tr class="form-field term-game_org-wrap">
	<th scope="row"><label for="game_org"><?php _e( 'Game Org', 'optilab' ); ?></label></th>
	<td>
		<?php
		$args = array(
				'show_option_all'    => '',
				'show_option_none'   => 'Choose Org',
				'option_none_value'  => '-1',
				'orderby'            => 'ID',
				'order'              => 'ASC',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'child_of'           => 0,
				'exclude'            => '',
				'include'            => '',
				'echo'               => 0,
				'selected'           => $game_org,
				'hierarchical'       => 0,
				'name'               => 'game_org',
				'id'                 => 'gameOrg',
				'class'              => 'form-control',
				'depth'              => 0,
				'tab_index'          => 0,
				'taxonomy'           => 'game_org',
				'hide_if_empty'      => false,
				'value_field'	     => 'term_id',
		);
		echo wp_dropdown_categories( $args );
		?>
		<p class="description"><?php _e( 'Game Org','optilab ' ); ?></p>
	</td>
  </tr>
<?php
});

add_action( 'created_game_tag', __NAMESPACE__ . '\\game_tag_form_custom_field_save', 10, 2 );
add_action( 'edited_game_tag', __NAMESPACE__ . '\\game_tag_form_custom_field_save', 10, 2 );
function game_tag_form_custom_field_save( $term_id, $tt_id ) {
    if ( isset( $_POST['short_name'] ) ) {
      update_term_meta( $term_id, 'short_name', $_POST['short_name'] );
    }
    if ( isset( $_POST['game_org'] ) ) {
      update_term_meta( $term_id, 'game_org', $_POST['game_org'] );
    }
}