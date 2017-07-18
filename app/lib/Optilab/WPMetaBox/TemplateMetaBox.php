<?php
namespace Optilab\WPMetaBox;

/**
 *  Implement Template Specific MetaBoxes
 **/
class TemplateMetaBox
{
  public $_post;
  public $_metaboxes = array();
  function __construct($post)
  {
    $this->_post = $post;
  }

  public function add_meta_box($meta_id, $meta_title, callable $callback, $postype, $context = "side", $priority = "default" ) {
      $this->_metaboxes[] = array(
        'meta_id'       => $meta_id,
        'meta_title'    => $meta_title,
        'callback'      => $callback,
        'post_type'     => $postype,
        'context'       => $context,
        'priority'      => $priority,
      );
  }

  /**
   * setup Meta Box action
   */
  private function _initialize_meta_box( $args ) {
    add_meta_box(
        $args['meta_id'], // Metabox HTML ID attribute
        $args['meta_title'], // Metabox title
        $args['callback'], // callback name
        $args['post_type'], // post type
        $args['context'], // context (advanced, normal, or side)
        $args['priority'] // priority (high, core, default or low)
    );
  }

  function init(callable $valication_callback) {

    if ( $valication_callback() == true) {
      foreach ($this->_metaboxes as $metabox) {
        $this->_initialize_meta_box($metabox);
      }
    }
  }

}
