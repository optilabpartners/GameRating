<?php
namespace Optilab;
use Optilab\Ratings;

add_action( 'init', __NAMESPACE__ . '\\optilab_setup' );
function optilab_setup() {

}


function filter_game_link($permalink, $post) {
  if(('game' == $post->post_type) && '' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
	$rewritecode = array(
	  '%game_org%',
	  '%game_season%',
	  '%game%'
	);

	$game_org = get_the_terms( $post, 'game_org' );
	if ($game_org !== false) {
		$game_org = $game_org[0]->slug;
	} else {
		$game_org = 'undefined';
	}
	

	$game_season_replace = null;
	$game_season = get_the_terms( $post, 'game_season' );

	if ($game_season !== false) {
		$game_season_replace = rtrim(get_taxonomy_parents(array_pop($game_season)->term_id, 'game_season', false, '/', true), '/');
	} else {
		$game_season_replace = 'undefined';
	}

	$rewritereplace = array(
		$game_org,
		$game_season_replace,
		$post->post_name
	);


	$permalink = str_replace($rewritecode, $rewritereplace, '/game/%game_org%/%game_season%/%game%/');
	$permalink = user_trailingslashit(home_url($permalink));
  }
  return $permalink;
}
add_filter('post_type_link', __NAMESPACE__ . '\\filter_game_link', 10, 2);

function game_rating_add_to_content( $content ) {    
	global $post;
	$teams = get_the_terms($post, 'team');
	if ($teams == false) {
		return $content;
	}
	if( $post->post_type == 'game' ) {
		$content .= '<div class="row">
		<div class="col-sm-5">';
		if ( $teams[0]->term_image ) {
			$content .= wp_get_attachment_image( $teams[0]->term_image, 'full' );
		}
		$content .= "<br><h4>{$teams[0]->name}</h4>";
		$content .= '</div><div class="col-sm-2"><strong>VS</strong></div>';
		$content .= '<div class="col-sm-5">';
		if ( $teams[1]->term_image ) {
			$content .= wp_get_attachment_image( $teams[1]->term_image, 'full' );
		}
		$content .= "<br><h4>{$teams[1]->name}</h4>";
		$content .= '</div></div>';
	}
	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\game_rating_add_to_content' );
add_filter( 'the_excerpt', __NAMESPACE__ . '\\game_rating_add_to_content' );

\add_action( 'wp_ajax_rating', function() {Ratings\RequestHandlers\RatingsRequestHandler::rating(); } );
\add_action( 'wp_ajax_aggregate_rating', function() {Ratings\RequestHandlers\RatingsRequestHandler::aggregate_rating(); } );