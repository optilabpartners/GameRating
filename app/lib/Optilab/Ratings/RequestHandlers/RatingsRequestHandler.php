<?php
namespace Optilab\Ratings\RequestHandlers;
use Optilab\Ratings;
/**
* RatingsRequest handler
*/
class RatingsRequestHandler extends RequestHandler
{
	public static function rating() {
		$rating = json_decode( file_get_contents( "php://input" ) );
		$method = static::method_identifier();
		switch ($method) {
			case 'POST':
			$rating = Ratings\Controllers\RatingsController::create(
				new Ratings\Models\RatingModel([ 
					'post_id' => $rating->post_id, 
					'value' => $rating->value
				]
			));
			if ($rating instanceof Ratings\Models\RatingModel) {
				echo json_encode($rating);
			}
			wp_die();
			break;
		}
	}

	public static function aggregate_rating() {

		$method = static::method_identifier();
		$post_id = (int)$_SERVER['HTTP_POSTID'];
		switch ($method) {
			case 'GET':
			$rating = Ratings\Controllers\RatingsController::fetchAverageRating($post_id);
			update_post_meta( $post_id, 'aggregate_rating', $rating );
			// if ($rating != null) {
			echo json_encode(array('id' => rand(), 'post_id' => $post_id, 'value' => $rating));
			// }
			wp_die();
			break;
		}
	}
}