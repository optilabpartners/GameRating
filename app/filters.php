<?php

add_filter('rewrite_rules_array', 'optilab_rewrite_rules');
function optilab_rewrite_rules($rules) {
    $newRules  = array();
    $newRules['game/(.+)/(.+)/(.+)/(.+)/?$'] 	= 'index.php?game=$matches[4]'; // my custom structure will always have the post name as the 5th uri segment
    $newRules['game/(.+)/(.+)/(.+)/?$']      	= 'index.php?game_season=$matches[3]'; 
    $newRules['game/(.+)/(.+)/?$']           	= 'index.php?game_season=$matches[2]'; 
    $newRules['game/(.+)/?$']                	= 'index.php?game_org=$matches[1]';
    $newRules['filter/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)/page/?([0-9]{1,})/?$']	= 'index.php?post_type=$matches[1]&game_org=$matches[2]&team=$matches[3]&game_season=$matches[4]&game_date=$matches[5]&game_tag=$matches[6]&paged=$matches[7]';
    $newRules['filter/(.+)/(.+)/(.+)/(.+)/(.+)/(.+)/?$']	= 'index.php?post_type=$matches[1]&game_org=$matches[2]&team=$matches[3]&game_season=$matches[4]&game_date=$matches[5]&game_tag=$matches[6]';

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
add_filter('posts_orderby', __NAMESPACE__ . '\\edit_posts_orderby', 10, 2);
add_filter('posts_join', __NAMESPACE__ . '\\edit_posts_join', 10, 2);
// add_filter('posts_limits', __NAMESPACE__ . '\\edit_posts_limits', 10, 2);
add_filter( 'posts_groupby', __NAMESPACE__ . '\\edit_posts_groupby', 10, 2 );

function edit_posts_join($join_statement, $wp_query) {

	if ( ( $wp_query->get("post_type") === "game" || is_search() || is_post_type_archive( 'game' )  || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) && !is_admin() && $wp_query->get('post_type') != 'nav_menu_item') {
		global $wpdb;
		// $join_statement .= " INNER JOIN $wpdb->postmeta ar ON ar.post_id = $wpdb->posts.ID";
		$join_statement .= " LEFT JOIN {$wpdb->prefix}ratings ar ON ar.post_id = $wpdb->posts.ID";
	}
	return $join_statement;	
}

function edit_posts_where($where_statement, $wp_query) {

	if ( ( $wp_query->get("post_type") === "game" || is_post_type_archive( 'game' )  || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) && !is_search() && !is_admin() && $wp_query->get('post_type') != 'nav_menu_item' ) {
		global $wpdb;
		$where_statement .= " OR ar.meta_key = 'aggregate_rating' ";
	}
	return $where_statement;	
}

function edit_posts_orderby($orderby_statement, $wp_query) {
	if ( ( $wp_query->get("post_type") === "game" || is_post_type_archive( 'game' )  || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) && !is_search() && !is_admin() && $wp_query->get('post_type') != 'nav_menu_item') {
		global $wpdb;
		$orderby_statement = "(AVG(ar.value)) DESC";
	}
	return $orderby_statement;
}

function edit_posts_groupby($groupby, $wp_query) {
	if ( ( $wp_query->get("post_type") === "game" || is_post_type_archive( 'game' )  || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) && !is_search() && !is_admin() && $wp_query->get('post_type') != 'nav_menu_item') {
		global $wpdb;
   		$groupby = "{$wpdb->posts}.ID";
	}
    
    return $groupby;
}

add_filter('terms_to_edit', function($terms, $taxonomy) {
	global $post;
	if ( $taxonomy != 'team' || ! $terms ) {
        return $terms;
    }
  	if ( $ids = get_post_meta( $post->ID, '_tt_ids_order', true ) ) {
  		$terms = null;
        // Order by term_taxonomy_ids order meta data.
        foreach ( explode( ',', $ids ) as $id ) {
        	$term_object = get_term_by( 'id', $id, $taxonomy );
            $terms .= $term_object->name . ',';

        }
        $terms = rtrim($terms, ',');
    }
    return $terms;
}, 10, 2);

// hook add_query_vars function into query_vars
add_filter('query_vars', function($aVars) {
	$aVars[] = "post_type";
	$aVars[] = "team"; 
	$aVars[] = "game_season";
	$aVars[] = "game_date";
	$aVars[] = "game_tag";
	return $aVars;
});

add_filter('wp_trim_excerpt', function($content) {
	global $post;
	if ($post->post_type == 'game') {
		$content = null;
	}
}, 10, 1);