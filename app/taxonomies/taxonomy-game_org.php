<?php
namespace Taxonomy;
use Optilab;
// Register Custom Taxonomy
function taxonomy_game_org() {

	$labels = array(
		'name'                       => _x( 'Game Organizations', 'Taxonomy General Name', 'optilab' ),
		'singular_name'              => _x( 'Game Organization', 'Taxonomy Singular Name', 'optilab' ),
		'menu_name'                  => __( 'Game Organizations', 'optilab' ),
		'all_items'                  => __( 'All Game Organizations', 'optilab' ),
		'parent_item'                => __( 'Parent Game Organization', 'optilab' ),
		'parent_item_colon'          => __( 'Parent Game Organization:', 'optilab' ),
		'new_item_name'              => __( 'New Game Organization Name', 'optilab' ),
		'add_new_item'               => __( 'Add New Game Organization', 'optilab' ),
		'edit_item'                  => __( 'Edit Game Organization', 'optilab' ),
		'update_item'                => __( 'Update Game Organization', 'optilab' ),
		'view_item'                  => __( 'View Game Organization', 'optilab' ),
		'separate_items_with_commas' => __( 'Separate game orgs with commas', 'optilab' ),
		'add_or_remove_items'        => __( 'Add or remove game orgs', 'optilab' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'optilab' ),
		'popular_items'              => __( 'Popular Game Organizations', 'optilab' ),
		'search_items'               => __( 'Search Game Organizations', 'optilab' ),
		'not_found'                  => __( 'Not Found', 'optilab' ),
		'no_terms'                   => __( 'No game_orgs', 'optilab' ),
		'items_list'                 => __( 'Game Organizations list', 'optilab' ),
		'items_list_navigation'      => __( 'Game Organizations list navigation', 'optilab' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
	);
	register_taxonomy( 'game_org', array( 'game' ), $args );
	if (class_exists('Optilab\\WordPress_Radio_Taxonomy')) {
		Optilab\WordPress_Radio_Taxonomy::load('game_org', 'game_orgdiv', 'game');
	}
	
}
add_action( 'init', __NAMESPACE__ . '\\taxonomy_game_org', 0 );

add_action( 'game_org_add_form_fields', function($taxonomy) {
	?>
  <div class="form-field term-watch_url-wrap">
    <label for="watch_url"><?php _e( 'Watch_url Now URL', 'sage' ); ?>
	<input type="text" name="watch_url" id="watchUrl" value="">
	<p class="description"><?php _e( 'Watch_urlnow url for games under this organization','sage' ); ?></p>
  </div>
<?php
});

add_action( 'game_org_edit_form_fields', function($term) {
  $watch_url = get_term_meta( $term->term_id, 'watch_url', true );
?>
  <tr class="form-field term-watch_url-wrap">
    <th scope="row"><label for="watch_url"><?php _e( 'Redirect URL', 'sage' ); ?></th>
    <td>
      <input type="text" name="watch_url" id="watch_url" value="<?= $watch_url; ?>">
      <p class="description"><?php _e( 'Enter a url that will describe the term','sage' ); ?></p>
    </td>
  </tr>
<?php
});

/** Save Custom Field Of Category Form */
add_action( 'created_game_org', __NAMESPACE__ . '\\game_org_form_custom_field_save', 10, 2 );
add_action( 'edited_game_org', __NAMESPACE__ . '\\game_org_form_custom_field_save', 10, 2 );
function game_org_form_custom_field_save( $term_id, $tt_id ) {
    if ( isset( $_POST['watch_url'] ) ) {
      update_term_meta( $term_id, 'watch_url', $_POST['watch_url'] );
    }
}