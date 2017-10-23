<?php

add_filter('rewrite_rules_array', 'optilab_rewrite_rules');
function optilab_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['game/(.+)/(.+)/(.+)/(.+)/?$'] = 'index.php?game=$matches[4]'; // my custom structure will always have the post name as the 5th uri segment
    $newRules['game/(.+)/(.+)/(.+)/?$']      = 'index.php?game_season=$matches[3]'; 
    $newRules['game/(.+)/(.+)/?$']           = 'index.php?game_season=$matches[2]'; 
    $newRules['game/(.+)/?$']                = 'index.php?game_org=$matches[1]'; 

    return array_merge($newRules, $rules);
}

function the_team_image_taxonomy( $taxonomy ) {
	// use for tags instead of categories
	return 'team';
}
add_filter( 'taxonomy-term-image-taxonomy', 'the_team_image_taxonomy' );


function term_link($url, $term, $taxonomy) {

	if ($taxonomy != 'game_season') {
		return $url;
	}
	if ($term->parent !== 0) {
		$parent = get_term( $term->parent );
		$pos = strpos($url, $term->slug);
		$url = substr_replace($url, $parent->slug . '/', $pos, 0);
		$url = str_replace('game_season', 'game/game_org', $url);
	}
	return $url;
}
add_filter('term_link', __NAMESPACE__ . '\\term_link', 10, 3);

// function change_post_type_template($single_template) 
// {
//      global $post;

//      if ($post->post_type == 'game') 
//      {
//           $single_template = PLUGIN_BASEPATH . 'resources/views/single-game.php';
//      }

//      return $single_template;
// }
// add_filter( 'single_template', 'change_post_type_template' );