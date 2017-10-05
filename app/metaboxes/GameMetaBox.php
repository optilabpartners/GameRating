<?php
namespace Optlab;
use Optilab\WPMetaBox;

/**
* Game Metabox
*/
class GameMetaBox extends WPMetaBox\CodeBox
{
	
	function __construct()
	{
		add_action( 'add_meta_boxes_game',  array( $this, 'register' ) );
   		add_action( 'save_post',  array( $this, 'save' ) );
	}

	/**
   * Inintite TemplateMetaBox class and setup the metaboxes to be shown in the admin page
   **/
	public function register($post)
	{
	$setup = new WPMetaBox\TemplateMetaBox($post);
	// top section
	$setup->add_meta_box(
	  'game_date',
	  'Game Date',
	  function($post) {
	    // Add a nonce field so we can check for it later.
	    wp_nonce_field( 'game_date', 'games_rating_new_nonce' );
	    
	    $game_date = get_post_meta($post->ID, 'game_date', true);
	    ?>
	    <div class="form-field">
	      <input type="date" id="publishedDate" name="game_date" aria-required="true" required="true" value="<?php echo $game_date; ?>" > Date
	    </div>
	  <?php
	  },
	  $post->post_type , 'normal', 'core'
	);
	$setup->init(function() { return true; });
	}

	/** 
	* Save post handler
	*/
  	function save($post_id) {

		parent::save($post_id);
		if (  ! isset( $_POST['game_date'] ) ) {
			return;
		}
		update_post_meta($post_id, 'game_date', $_POST['game_date']);
	}
}

//Initiate the class
GameMetaBox::init();