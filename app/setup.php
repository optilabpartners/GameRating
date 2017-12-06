<?php
namespace Optilab;
use Optilab\Ratings;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Optilab\Assets\JsonManifest;
use Optilab\Config;

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('games-rating/main.css', asset_path('styles/games.css'), false, null);
	wp_enqueue_script('games-rating/main.js', asset_path('scripts/rating.js'), ['jquery'], null, true);
	wp_enqueue_script('games-rating/bootstrap.js', asset_path('scripts/bootstrap.js'), ['jquery'], null, true);
}, 100);



add_action('init', function () {
	/**
	 * Sage config
	 */
	$paths = [
		// 'dir.stylesheet' => get_stylesheet_directory(),
		// 'dir.template'   => get_template_directory(),
		'dir.upload'     	=> wp_upload_dir()['basedir'],
		// 'uri.stylesheet' => get_stylesheet_directory_uri(),
		// 'uri.template'   => get_template_directory_uri(),
	];
	config([
		'assets.manifest' => PLUGIN_BASEPATH."/../dist/assets.json",
		'assets.uri'      => PLUGIN_BASEURL."/dist",
		'view.compiled'   => "{$paths['dir.upload']}/cache/compiled",
		'view.namespaces' => ['Optilab' => WP_CONTENT_DIR],
	] + $paths);

	/**
	 * Add JsonManifest to Sage container
	 */
	optilab()->singleton('optilab.assets', function () {
		return new JsonManifest(config('assets.manifest'), config('assets.uri'));
	});

	if (get_query_var('team') == 'any') {
		remove_query_arg( 'team' );
	} else if (get_query_var('game_tag') == 'any') {
		remove_query_arg( 'team' );
	} else if (get_query_var('game_season') == 'any') {
		remove_query_arg( 'team' );
	}

});

add_filter('post_type_link', __NAMESPACE__ . '\\filter_game_link', 10, 2);

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

add_filter( 'the_excerpt', __NAMESPACE__ . '\\game_rating_add_to_content' );

function new_excerpt_more($more) {
       global $post;
	return '<a class="moretag" href="'. get_permalink($post->ID) . '"> Read the full article...</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\new_excerpt_more');

\add_action( 'wp_ajax_optirating', function() { Ratings\RequestHandlers\RatingsRequestHandler::rating(); } );
\add_action( 'wp_ajax_nopriv_optirating', function() { Ratings\RequestHandlers\RatingsRequestHandler::rating(); } );
\add_action( 'wp_ajax_aggregate_optirating', function() { Ratings\RequestHandlers\RatingsRequestHandler::aggregate_rating(); } );
\add_action( 'wp_ajax_nopriv_aggregate_optirating', function() { Ratings\RequestHandlers\RatingsRequestHandler::aggregate_rating(); } );

add_action( 'pre_get_posts', function ( $query ) {
	if ( is_tax('game_season') || is_tax('game_org')) {
		$query->set( 'nopaging', 1 );
	}

	// hiding posts that have game date same as current date
	if ( !is_admin() && $query->get('post_type') != 'nav_menu_item' && ( is_post_type_archive( 'game' )  || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) ) {
		//Get original meta query
		$meta_query = $query->get('meta_query');
		if (!$meta_query) {
			$meta_query = array();
		}

		//Add our meta query to the original meta queries
		$meta_query = array(
			array (
				'key'=>'game_date',
				'value'=> ((new \DateTime('today'))->format('Y-m-d')),
				'compare'=>'<',
			)
		);
		$query->set('meta_query',$meta_query);
  	}
  	return $query;
});


// Called in admin on updating terms - update our order meta.
add_action( 'set_object_terms', function ( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
    if ( $taxonomy != 'team' ) {
        return;
    }

    // Save in comma-separated string format - may be useful for MySQL sorting via FIND_IN_SET().
    update_post_meta( $object_id, '_tt_ids_order', implode( ',', $tt_ids ) );
}, 10, 6 );

// Reorder terms using our order meta.
function get_the_teams( $terms, $post_id, $taxonomy ) {
    if ( $taxonomy != 'team' || ! $terms ) {
        return $terms;
    }
    if ( $ids = get_post_meta( $post_id, '_tt_ids_order', true ) ) {
        $ret = $term_idxs = array();
        // Map term_ids to term_taxonomy_ids.
        foreach ( $terms as $term_id => $term ) {
            $term_idxs[$term->term_taxonomy_id] = $term_id;
        }
        // Order by term_taxonomy_ids order meta data.
        foreach ( explode( ',', $ids ) as $id ) {
            if ( isset( $term_idxs[$id] ) ) {
                $ret[] = $terms[$term_idxs[$id]];
                unset($term_idxs[$id]);
            }
        }
        // In case our meta data is lacking.
        foreach ( $term_idxs as $term_id ) {
            $ret[] = $terms[$term_id];
        }
        return $ret;
    }
    return $terms;
}

// // Called in front-end via the_tags() or related variations of.
add_filter( 'get_the_terms', __NAMESPACE__ .'\\get_the_teams', 10, 3 );

// // Called on admin edit.
add_filter( 'teams_to_edit', function ( $terms_to_edit, $taxonomy ) {
    global $post;
    if ( ! isset( $post->ID ) || $taxonomy != 'team' || ! $terms_to_edit ) {
        return $terms_to_edit;
    }
    // Ignore passed in term names and use cache just added by terms_to_edit().
    if ( $terms = get_object_term_cache( $post->ID, $taxonomy ) ) {
        $terms = Optilab\get_the_teams( $terms, $post->ID, $taxonomy );
        $term_names = array();
        foreach ( $terms as $term ) {
            $term_names[] = $term->name;
        }
        $terms_to_edit = esc_attr( join( ',', $term_names ) );
    }
    return $terms_to_edit;
}, 10, 2 );

add_action( 'get_search_form', function($form) {
    $action = esc_url( home_url( '/' ) );
    $search_string = "Search for Teams and Game-Tags";
    $form = <<<HTML
    <form method="get" id="searchform" action="{$action}" class="mr-0 ml-0">
    <div class="input-group">
      <input type="search" class="form-control" name="s" id="s" placeholder="{$search_string}" aria-label="{$search_string}">
      <input type="hidden" name="post_type" value="game">
      <span class="input-group-btn">
        <button class="btn btn-secondary" type="submit" id="searchsubmit" ><i class="fa fa-search" aria-hidden="true"></i></button>
      </span>
    </div>
  </form>
HTML;
    return $form;
}, 10, 1 );

optilab()->bindIf('config', Config::class, true);


add_action('pre_get_posts', function($query) {
	if ( !is_admin() && $query->is_main_query() && !$query->is_singular ) {
		if (is_archive() && is_post_type_archive('game')) {
			if (get_query_var('team') == 'any') {
				unset($query->query_vars['team']);
			}
			if (get_query_var('game_tag') == 'any') {
				unset($query->query_vars['game_tag']);
			}
			if (get_query_var('game_season') == 'any') {
				unset($query->query_vars['game_season']);
			}
			
		}
  	}

  	return $query;
}, 9, 1);
