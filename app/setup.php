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

optilab()->bindIf('config', Config::class, true);