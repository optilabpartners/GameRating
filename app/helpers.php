<?php
namespace Optilab;
use Optilab\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
/**
 * Get the optilab container.
 *
 * @param string $abstract
 * @param array  $parameters
 * @param ContainerContract $container
 * @return ContainerContract|mixed
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function optilab($abstract = null, $parameters = [], ContainerContract $container = null)
{
    $container = $container ?: Container::getInstance();
    if (!$abstract) {
        return $container;
    }
    return $container->bound($abstract)
        ? $container->make($abstract, $parameters)
        : $container->make("optilab.{$abstract}", $parameters);
}


/**
 * Get / set the specified configuration value.
 *
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @param array|string $key
 * @param mixed $default
 * @return mixed|\Roots\Sage\Config
 * @copyright Taylor Otwell
 * @link https://github.com/laravel/framework/blob/c0970285/src/Illuminate/Foundation/helpers.php#L254-L265
 */
function config($key = null, $default = null)
{
    if (is_null($key)) {
        return optilab('config');
    }
    if (is_array($key)) {
        return optilab('config')->set($key);
    }
    return optilab('config')->get($key, $default);
}
/**
 * @param string $file
 * @param array $data
 * @return string
 */
function template($file, $data = [])
{
    return optilab('blade')->render($file, $data);
}
/**
 * Retrieve path to a compiled blade view
 * @param $file
 * @param array $data
 * @return string
 */
function template_path($file, $data = [])
{
    return optilab('blade')->compiledPath($file, $data);
}
/**
 * @param $asset
 * @return string
 */
function asset_path($asset)
{
    return optilab('assets')->getUri($asset);
}


function get_taxonomy_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array()) {    
	$chain = '';   
	$parent = &get_term($id, $taxonomy);

	if (is_wp_error($parent)) {
		return $parent;
	}

	if ($nicename)    
		$name = $parent -> slug;        
	else    
		$name = $parent -> name;

	if ($parent -> parent && ($parent -> parent != $parent -> term_id) && !in_array($parent -> parent, $visited)) {    
		$visited[] = $parent -> parent;    
		$chain .= get_taxonomy_parents($parent ->parent, $taxonomy, $link, $separator, $nicename, $visited);

	}

	if ($link) {
		// nothing, can't get this working :(
	} else    
		$chain .= $name . $separator;    
	return $chain;    
}

function game_rating_add_to_content( $content = null ) { 
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
			$content .= <<<HTML
			<a href="/team/{$teams[0]->slug}">
HTML;
			$content .= wp_get_attachment_image( $teams[0]->term_image, 'full', false, array('class' => 'mx-auto') );
		}
		$content .= "<br><h4>{$teams[0]->name}</h4></a>";
		$content .= '</div><div class="col-md-2 text-center"><strong>VS</strong></div>';
		$content .= '<div class="col-md-5 text-center">';
		if ( $teams[1]->term_image ) {
			$content .= <<<HTML
			<a href="/team/{$teams[1]->slug}">
HTML;
			$content .= wp_get_attachment_image( $teams[1]->term_image, 'full', false, array('class' => 'mx-auto') );
		}
		$content .= "<br><h4>{$teams[1]->name}</h4></a>";
		$content .= '</div></div>';
		if ( (strtotime(get_post_meta( $post->ID, 'game_date', true )) + 24*60*60) <= strtotime('today') ) {
			$content .= <<<HTML
			<div class="text-center">
				<a href="{$watch_url}" target="_blank" class="btn btn-outline btn-watch-now mx-auto">Watch Now</a><br>
			</div>
HTML;
		}
		$content .= <<<HTML
		<div class="text-center">
			<strong>Game Date:</strong> {$game_date}		
		</div>
		<hr>
HTML;
		if ( strtotime(get_post_meta( $post->ID, 'game_date', true )) <= strtotime('today') ) {
			$content .= <<<HTML
		<div class="row">
			<div class="col-md-12 text-center">
				<strong>User Rating</strong>
				<!-- Template -->
				<div id="arating-detail-{$post->ID}" data-post-id="{$post->ID}" class="aggregate-rating"></div>
				<script type="text/template" class="arating-detail-template" data-post-id="{$post->ID}">
					<strong class="rating-color-<%= Math.round(value) %>"><%= value %></strong>/10
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
		</div>
		<hr>
HTML;
		}
		$tags = get_the_terms( $post->ID, 'game_tag' );
		if ($tags) {
			$content .= "<ul class=\"nav nav-pills flex-column flex-sm-row justify-content-center\">";
			foreach ($tags as $tag) {
				$content .= "<li class=\"flex-sm-fill text-sm-center nav-link h6\" ><span class=\"badge badge-pill badge-default\">{$tag->name}</span></li>";
			}
			$content .= "</ul>";
		}
		$content .= <<<HTML
HTML;
	}
	return $content;
}

add_shortcode( 'todays_game', function($atts) {
	$a = shortcode_atts( array(
        'days' 	=> 3,
    ), $atts );
	$today = new \DateTime();
	$start_date = new \DateTime();
	$start_date = $start_date->sub(new \DateInterval('P' . $a['days'] . 'D'));
	$args = array(
		'post_type'		=> 'game',
		'orderby'		=> array(
			'date_range' => 'DESC'
		),
		'meta_type'	=> 'DATE',
		'meta_query' => array(
			'date_range' => array(
				'key'     => 'game_date',
				'value'   => array( $start_date->format('Y-m-d'), $today->format('Y-m-d') ),
				'compare' => 'BETWEEN',
		),
		'game_date' => array(
				'key'=>'game_date',
				'value'=> ((new \DateTime('today'))->format('Y-m-d')),
				'compare'=>'<',
			)
		),
		'posts_per_page' => -1,
		'nopaging'		=> true
	);
	$query = new \WP_Query( $args );
	$content = null;
	if ( $query->have_posts() ) {
		
		while ( $query->have_posts() ) {
			$content .= '<div class="game-result-wrap card">';
			$query->the_post();
			//$content .= '<div class="card-header"><h2>' . get_the_title() . '</h2></div>';
			$content .= '<div class="card-block">';
			$content .= game_rating_add_to_content();
			$content .= '<br></div>';
			$content .= '</div>';
		}
		
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		$content .= '<div class="alert alert-info>No game match in the past 7 days</div>';
	}
	return $content;
} );

add_shortcode( 'season_weeks', __NAMESPACE__ . '\\weeks');
function weeks($atts) {
	$a = shortcode_atts( array(
        'season' 	=> null,
        'org'		=> null
    ), $atts );
    
    
	$term = get_term_by('slug', $a['season'], 'game_season');

	if ($a['org'] == null) {
    	return '<div class="alert alert-warning">A valid organization slug is required to show the season weeks.</div>';
    }

    if (!$term) {
    	return '<div class="alert alert-warning">A valid top-level season term slug is required to show the season weeks.</div>';
    }

	$terms = get_terms( 'game_season', array(
		'parent' => $term->term_id,
	) );
	$content = '<h3>Game weeks for ' . $term->name . ' season.</h3><div class="row">';
	foreach ($terms as $term) {
		$term = get_term($term);
		$term_link = str_replace('game_org', $a['org'], get_term_link($term));
		$content .= '<div class="col-md-6 col-lg-4 text-center"><a class="mx-auto btn btn-link" href="' . $term_link . '">' . substr($term->name, 0, 7) . '</a></div>';
	}
	$content .= '</div>';
	return $content;
}