<?php

add_action( 'init', __NAMESPACE__ . '\\setup' );
function setup() {

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

    //var_dump($rewritecode, $rewritereplace, '/game/%game_org%/%game_season%/%game%/' );
    $permalink = str_replace($rewritecode, $rewritereplace, '/game/%game_org%/%game_season%/%game%/');
    $permalink = user_trailingslashit(home_url($permalink));
  }
  return $permalink;
}
add_filter('post_type_link', __NAMESPACE__ . '\\filter_game_link', 10, 2);