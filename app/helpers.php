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
		if ( strtotime(get_post_meta( $post->ID, 'game_date', true )) <= strtotime('today') ) {
			$content .= <<<HTML
			<div class="text-center">
				<a href="{$watch_url}" class="btn btn-primary btn-watch-now mx-auto">Watch Now</a>
			</div>
HTML;
		}
		$content .= <<<HTML
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

add_shortcode( 'todays_game', function() {
	$today = new \DateTime();
	$start_date = new \DateTime();
	$start_date = $start_date->sub(new \DateInterval('P7D'));
	$args = array(
		'post_type'		=> 'game',
		'meta_key'   	=> 'game_date',
		'order_by'		=> 'meta_value',
		'meta_type'		=> 'DATE',
		'order'      => 'DESC',
		'meta_query' => array(
			array(
				'key'     => 'game_date',
				'value'   => array( $start_date->format('Y-m-d'), $today->format('Y-m-d') ),
				'compare' => 'BETWEEN',
			),
		),
	);
	$query = new \WP_Query( $args );

	if ( $query->have_posts() ) {
		echo '<div class="card">';
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="card-header"><h2>' . get_the_title() . '</h2></div>';
			echo '<div class="card-block">';
			echo game_rating_add_to_content();
			echo '<br></div>';
		}
		echo '</div><br>';
		/* Restore original Post Data */
		wp_reset_postdata();
	} else {
		echo '<div class="alert alert-info>No game match in the past 7 days</div>';
	}
} );