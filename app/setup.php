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
	if ( is_tax('game_season') || is_tax('game_org') ) {
		$query->set( 'nopaging', 1 );
	}
	// hiding posts that have game date same as current date
	if ( !is_admin() && ( is_post_type_archive( 'game' ) || is_tax('game_season') || is_tax('game_org') || is_tax('team') ) ) {
		//Get original meta query
		$meta_query = $query->get('meta_query');

		//Add our meta query to the original meta queries
		$meta_query[] = array(
			'key'=>'game_date',
			'value'=> ((new \DateTime('today'))->format('Y-m-d')),
			'compare'=>'<',
		);
		$query->set('meta_query',$meta_query);
  	}
});

optilab()->bindIf('config', Config::class, true);