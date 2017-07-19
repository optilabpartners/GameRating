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