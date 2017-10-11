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
	$game_date = date( 'F j, Y', strtotime(get_post_meta( $post->ID, 'game_date', true )));
	$org = get_the_terms( $post, 'game_org' )[0];
	$watch_url = get_term_meta( $org->term_id, 'watch_url', true);
	$teams = get_the_terms($post, 'team');
	if ($teams == false) {
		return $content;
	}
	if( $post->post_type == 'game' ) {
		$content .= '<div class="row">
		<div class="col-md-5 text-center">';
		if ( $teams[0]->term_image ) {
			$content .= wp_get_attachment_image( $teams[0]->term_image, 'full', false, array('class' => 'mx-auto') );
		}
		$content .= "<br><h4>{$teams[0]->name}</h4>";
		$content .= '</div><div class="col-md-2 text-center"><strong>VS</strong></div>';
		$content .= '<div class="col-md-5 text-center">';
		if ( $teams[1]->term_image ) {
			$content .= wp_get_attachment_image( $teams[1]->term_image, 'full', false, array('class' => 'mx-auto') );
		}
		$content .= "<br><h4>{$teams[1]->name}</h4>";
		$content .= '</div></div>';
		$content .= <<<HTML
		<div class="text-center">
			<a href="{$watch_url}" class="btn btn-primary mx-auto">Watch Now</a>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-6">
				<!-- Template -->
				<div class="arating-detail-{$post->ID}"></div>
				<script type="text/template" id="arating-detail-template" data-post-id="{$post->ID}">
					<strong>Rating: <%= value %>/10</strong>
				</script>
				<!-- End template -->
				<div class="rating-container">
				Rate:
					<form class="games-rating-stars">
						<input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-0" data-post-id="{$post->ID}" value="10" /><label for="ratingSlider{$post->ID}-0"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-1" data-post-id="{$post->ID}" value="9" /><label for="ratingSlider{$post->ID}-1"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-2" data-post-id="{$post->ID}" value="8" /><label for="ratingSlider{$post->ID}-2"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-3" data-post-id="{$post->ID}" value="7" /><label for="ratingSlider{$post->ID}-3"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-4" data-post-id="{$post->ID}"  value="6" /><label for="ratingSlider{$post->ID}-4"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-5" data-post-id="{$post->ID}" value="5" /><label for="ratingSlider{$post->ID}-5"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-6" data-post-id="{$post->ID}" value="4" /><label for="ratingSlider{$post->ID}-6"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-7" data-post-id="{$post->ID}" value="3" /><label for="ratingSlider{$post->ID}-7"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-8" data-post-id="{$post->ID}" value="2" /><label for="ratingSlider{$post->ID}-8"></label><!--
						--><input type="radio" name="ratingSlider{$post->ID}" class="input-add-rating" id="ratingSlider{$post->ID}-9" data-post-id="{$post->ID}"  value="1" /><label for="ratingSlider{$post->ID}-9"></label>
					</form>
					<span class="rating-preview"> 0/10</span>
				</div>
			</div>
			<div class="col-md-6">
				<strong>Game Date:</strong> {$game_date}
			</div> 
		</div>
HTML;
	}
	return $content;
}
// add_filter( 'the_content', __NAMESPACE__ . '\\game_rating_add_to_content' );
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