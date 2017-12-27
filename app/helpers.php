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
	if ($post->post_type != "game" && $post->post_type != 'page') {
		return $content;
	}
	$game_date = date( 'F j, Y', strtotime(get_post_meta( $post->ID, 'game_date', true )));
	$org = get_the_terms( $post, 'game_org' )[0];
	
	$watch_url = get_term_meta( $org->term_id, 'watch_url', true);
	
	$game_url = get_post_permalink( $post->ID );
	
	$url_game_date = date_format(date_create(get_post_meta( $post->ID, 'game_date', true )),"Ymd");
    $teamA = get_the_terms( $post, 'team' )[0]->term_id;
    $teamB = get_the_terms( $post, 'team' )[1]->term_id;

    $teamAName = get_term_meta( $teamA, 'short_name', true );
    $teamBName = get_term_meta( $teamB, 'short_name', true );

    $watch_url .= "$url_game_date/$teamAName$teamBName/";

	$teams = get_the_terms($post, 'team');
	if ($teams == false) {
		return $content;
	}
	if( $post->post_type == 'game' ) {
		$content .= '<div class="row" itemscope itemtype="http://schema.org/SportsEvent">
		<meta itemprop="startDate" content="' . date_format(date_create(get_post_meta( $post->ID, 'game_date', true )), \DateTime::ISO8601 ) . '" />
		<meta itemprop="name" content=" ' .$teams[0]->name. ' vs ' . $teams[1]->name . '" />
		<div class="col-md-5 text-center" itemprop="homeTeam" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="' . $teams[0]->name .'" />';
		if ( $teams[0]->term_image ) {
			$content .= <<<HTML
			<a href="/team/{$teams[0]->slug}">
HTML;
			$content .= wp_get_attachment_image( $teams[0]->term_image, 'full', false, array('class' => 'mx-auto') );
		}
		$content .= "<br><h4>{$teams[0]->name}</h4></a>";
		$content .= '</div><div class="col-md-2 text-center"><strong>VS</strong></div>';
		$content .= '<div class="col-md-5 text-center" itemprop="awayTeam" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="' . $teams[1]->name . '" />';
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
				<a itemprof="url" href="{$watch_url}" onclick="javascript: ga('send', 'event', 'outclick', 'click', 'Game - {$post->ID}');" target="_blank" class="btn btn-outline btn-watch-now mx-auto">Watch Now</a>
HTML;
		if (!is_singular('game')) {
			$content .= '<a href="' . $game_url .'" class="game_url"><img src="' . PLUGIN_BASEURL . 'dist/images/comment.png" width="49" alt="Comment" /></a>';
			//$content .= '<br />Comments: <span class="fb-comments-count" data-href="' . $game_url . '"></span>';
			//$content .='<br />Comments: <fb:comments-count href="' . $game_url . '"></fb:comments-count>';
		}
        $content .= <<<HTML
			</div>
HTML;
		}
		$content .= <<<HTML
		<div class="text-center">
			<strong>Game Date:</strong> {$game_date}		
		</div>
		<hr />
HTML;
		if ( strtotime(get_post_meta( $post->ID, 'game_date', true )) <= strtotime('today') ) {
			$ratingCount = rand(50, 500);
			$content .= <<<HTML
		<div class="row">
			<div class="col-md-12 text-center">
				<strong>User Rating</strong>
				<!-- Template -->
				<div id="arating-detail-{$post->ID}" data-post-id="{$post->ID}" class="aggregate-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
					<img alt="loading ratings" src="data:image/gif;base64,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" >
				</div>
				<script type="text/template" class="arating-detail-template" data-post-id="{$post->ID}">
					<strong class="rating-color-<%= Math.round(value) %>" itemprop="ratingValue" ><%= value %></strong>/10
					<meta itemprop="ratingCount" value="{$ratingCount}">
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
HTML;
		}
		$tags = get_the_terms( $post->ID, 'game_tag' );
		if ($tags) {
			$content .= "<ul class=\"nav flex-column flex-sm-row justify-content-center nav-game-tag\">";
			foreach ($tags as $tag) {
				$content .= "<li class=\"flex-sm-fill text-center nav-link h6\" >{$tag->name}</li>";
			}
			$content .= "</ul>";
		}
		$content .= <<<HTML
HTML;
	}
	return $content;
}

add_shortcode( 'filter_game', function($atts) {
	$a = shortcode_atts( array(
        'game_org' => '',
    ), $atts );
    $game_org = $a['game_org'];
	if (get_query_var( 'game_org', false )) {
		$game_org = get_term_by( 'slug', get_query_var( 'game_org' ), 'game_org')->term_id;
	}
	$game_org_slug = get_term( $game_org, 'game_org' )->slug;
	$action = esc_url( home_url() );
    $content = null;
    $content .= '<form class="filter-game-form" method="POST" action="' . $action . '">';
    	$content .= '<input type="hidden" name="post_type" id="postType" value="game">';
    	$content .= '<input type="hidden" name="game_org" id="gameOrg" value="' . $game_org_slug. '">';
        $content .= '<div class="container mb-3">';
            $content .= '<div class="row">';
                $content .= '<div class="col-lg-6 col-md-12">';
                    //Get all teams in alphabetical order
                $args = array(
					'hide_empty' => true, // also retrieve terms which are not used yet
					'meta_query' => array(
					    array(
					       'key'       => 'game_org',
					       'value'     => (int)$game_org,
					       'compare'   => '='
					    )
					)
				);
				$teams = get_terms( 'team', $args );
				$content .= "<select name=\"team\" id=\"team\" class=\"form-control\">";
				$content .= "<option value=\"any\">Choose Team</option>";
				foreach ($teams as $team) {
					$selected = '';
					if ($team->slug == get_query_var('team')) {
						$selected = "selected=\"true\"";
					}
					$content .= "<option {$selected} value=\"{$team->slug}\">{$team->name}</option>";
				}
				$content .= "</select>";
                $content .= '</div>';
               
                $content .= '<div class="col-lg-6 col-md-12">';

                $args = array(
					'hide_empty' => true, // also retrieve terms which are not used yet
					'meta_query' => array(
					    array(
					       'key'       => 'game_org',
					       'value'     => (int)$game_org,
					       'compare'   => '='
					    )
					)
				);
				$game_tags = get_terms( 'game_tag', $args );

				$content .= "<select name=\"game_tag\" id=\"gameTag\" class=\"form-control\">";
				$content .= "<option value=\"any\">Choose Tag</option>";
				foreach ($game_tags as $game_tag) {
					$selected = '';
					if ($game_tag->slug == get_query_var('game_tag')) {
						$selected = "selected=\"true\"";
					}
					$content .= "<option {$selected} value=\"{$game_tag->slug}\">{$game_tag->name}</option>";
				}
				$content .= "</select>";

                $content .= '</div>';

                $content .= '<div class="col-lg-4 col-md-12">';
                $args = array(
					'hide_empty' => true, // also retrieve terms which are not used yet
					'meta_query' => array(
					    array(
					       'key'       => 'game_org',
					       'value'     => (int)$game_org,
					       'compare'   => '='
					    )
					)
				);
				$game_seasons = get_terms( 'game_season', $args );
				$content .= "<select name=\"game_season\" id=\"gameSeason\" class=\"form-control\">";
				$content .= "<option value=\"any\">Choose Week</option>";
				foreach ($game_seasons as $game_season) {
					if ($game_season->parent !== 0) {
						$start_date = get_term_meta( $game_season->term_id, 'start_date', true );
						$end_date = get_term_meta( $game_season->term_id, 'end_date', true );
						$selected = '';
						$parent = get_term( $game_season->parent, 'game_season' );
						if ($game_season->slug == get_query_var('game_season')) {
							$selected = "selected=\"true\"";
						}
						$content .= "<option {$selected} data-start-date={$start_date} data-end-date={$end_date} value=\"{$game_season->slug}\">[{$parent->name}] {$game_season->name}</option>";
					}
				}
				$content .= "</select>";

                $content .= '</div>';
                $content .= '<div class="col-lg-4 col-sm-6">';
                $gd = get_query_var('game_date');
				$content .= "<input type=\"date\" name=\"game_date\" id=\"gameDate\" value=\"{$gd}\" class=\"form-control mt-1\">";


                $content .= '</div>';
                $content .= '<div class="col-lg-4 col-sm-6 text-center"><input type="submit" value="FILTER" class="form-control btn btn-large" /></div>';
            $content .= '</div>';
        $content .= '</div>';
    $content .= '</form><hr>';
    
    return $content;
} );

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
			$content .= '<div class="card-block">';
			$content .= game_rating_add_to_content();
			$content .= '</div>';
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
	$content = '<h3 class="text-center">Game weeks for ' . $term->name . ' season.</h3><div class="row">';
	foreach ($terms as $term) {
		$term = get_term($term);
		$term_link = str_replace('game_org', $a['org'], get_term_link($term));
		$content .= '<div class="col col-md-6 col-lg-4 text-center"><a class="mx-auto btn btn-link" href="' . $term_link . '">' . substr($term->name, 0, 7) . '</a></div>';
	}
	$content .= '</div>';
	return $content;
}