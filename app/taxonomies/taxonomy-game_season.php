<?php
namespace Taxonomy;
use Optilab;
// Register Custom Taxonomy
function taxonomy_game_season() {

	$labels = array(
		'name'                       => _x( 'Game Seasons', 'Taxonomy General Name', 'optilab' ),
		'singular_name'              => _x( 'Game Season', 'Taxonomy Singular Name', 'optilab' ),
		'menu_name'                  => __( 'Game Seasons', 'optilab' ),
		'all_items'                  => __( 'All Game Seasons', 'optilab' ),
		'parent_item'                => __( 'Parent Game Season', 'optilab' ),
		'parent_item_colon'          => __( 'Parent Game Season:', 'optilab' ),
		'new_item_name'              => __( 'New Game Season Name', 'optilab' ),
		'add_new_item'               => __( 'Add New Game Season', 'optilab' ),
		'edit_item'                  => __( 'Edit Game Season', 'optilab' ),
		'update_item'                => __( 'Update Game Season', 'optilab' ),
		'view_item'                  => __( 'View Game Season', 'optilab' ),
		'separate_items_with_commas' => __( 'Separate game seasons with commas', 'optilab' ),
		'add_or_remove_items'        => __( 'Add or remove game seasons', 'optilab' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'optilab' ),
		'popular_items'              => __( 'Popular Game Seasons', 'optilab' ),
		'search_items'               => __( 'Search Game Seasons', 'optilab' ),
		'not_found'                  => __( 'Not Found', 'optilab' ),
		'no_terms'                   => __( 'No game_seasons', 'optilab' ),
		'items_list'                 => __( 'Game Seasons list', 'optilab' ),
		'items_list_navigation'      => __( 'Game Seasons list navigation', 'optilab' ),
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
	register_taxonomy( 'game_season', array( 'game' ), $args );
	
}
add_action( 'init', __NAMESPACE__ . '\\taxonomy_game_season', 0 );


\add_action( 'game_season_add_form_fields', function($taxonomy) {
	?>
  <div class="form-field term-start_date-wrap">
	<label for="start_date"><?php _e( 'Start Date', 'optilab' ); ?>
	<input type="date" name="start_date" id="start_date" value="">
	<p class="description"><?php _e( 'Start date of the season','optilab' ); ?></p>
  </div>
  <div class="form-field term-end_date-wrap">
	<label for="end_date"><?php _e( 'End Date', 'optilab' ); ?>
	<input type="date" name="end_date" id="end_date" value="">
	<p class="description"><?php _e( 'Ebd date of the season','optilab ' ); ?></p>
  </div>
  <!-- <div class="form-field week-start_date-wrap">
	<label for="week-start"><?php _e( 'End Date', 'optilab' ); ?>
	<select type="date" name="week-start" id="weekStart" >
		<option value="sunday">Sunday</option>
		<option value="monday">Monday</option>
		<option value="tuesday">Tuesday</option>
		<option value="wednesday">Wednesday</option>
		<option value="thursday">Thursday</option>
		<option value="friday">Friday</option>
		<option value="saturday">Saturday</option>
	</select>
	<p class="description"><?php _e( 'Day to start of the week','optilab ' ); ?></p>
  </div> -->
<?php
});

\add_action( 'game_season_edit_form_fields', function($term) {
  $start_date = get_term_meta( $term->term_id, 'start_date', true );
  $end_date = get_term_meta( $term->term_id, 'end_date', true );
?>
  <tr class="form-field term-start_date-wrap">
	<th scope="row"><label for="start_date"><?php _e( 'Start Date', 'optilab' ); ?></th>
	<td>
	  <input type="date" name="start_date" id="start_date" value="<?= $start_date; ?>">
	  <p class="description"><?php _e( 'Enter end date the season','optilab' ); ?></p>
	</td>
  </tr>
  <tr class="form-field term-end_date-wrap">
	<th scope="row"><label for="end_date"><?php _e( 'End Date', 'optilab' ); ?></th>
	<td>
	  <input type="date" name="end_date" id="end_date" value="<?= $end_date; ?>">
	  <p class="description"><?php _e( 'Enter end date the season','optilab' ); ?></p>
	</td>
  </tr>
<?php
});
/** Save Custom Field Of Category Form */
add_action( 'created_game_season', __NAMESPACE__ . '\\game_season_form_custom_field_save', 10, 2 ); 
add_action( 'edited_game_season', __NAMESPACE__ . '\\game_season_form_create_sub_term_save', 11, 2 ); 
add_action( 'edited_game_season', __NAMESPACE__ . '\\game_season_form_custom_field_save', 10, 2 );
 
function game_season_form_custom_field_save( $term_id, $tt_id ) {
	if ( isset( $_POST['start_date'] ) ) {           
	  update_term_meta( $term_id, 'start_date', $_POST['start_date'] );
	}
	if ( isset( $_POST['end_date'] ) ) {           
	  update_term_meta( $term_id, 'end_date', $_POST['end_date'] );
	}
}

function game_season_form_create_sub_term_save( $term_id, $tt_id ) {
	$children = get_term_children( $term_id, 'game_season' );
	if (count($children) > 0 ) {
		return false;
	}
	$term = get_term( $term_id );
	if ( isset( $_POST['start_date'] ) && isset( $_POST['end_date'] ) && $term->parent == false  ) {        
		$start_date = $_POST['start_date'];
		$date1 = new \DateTime($_POST['start_date']);
		$date2 = new \DateTime($_POST['end_date']);
		$interval = $date1->diff($date2);

		$i= 1;
		while ( $date1 < $date2) {
			$week = $date1->format("W");
			// $date1->modify('next sunday');
			$date1->add(new \DateInterval('P6D'));
			// create weeks
			$result = wp_insert_term( "Week " . sprintf("%02d", $i) ." ({$start_date} - {$date1->format('Y-m-d')})", "game_season", array( 'slug' => 'week-' . $i, 'parent' => $term_id ) );

			
			update_term_meta( $result['term_id'], 'start_date', $start_date );
			update_term_meta( $result['term_id'], 'end_date', $date1->format('Y-m-d') );
			update_term_meta( $result['term_id'], 'week_number', $i );

			$date1->add(new \DateInterval('P1D'));
			$start_date = $date1->format('Y-m-d');
			$i++;
		}

	}
}