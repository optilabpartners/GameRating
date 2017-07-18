<?php
/*
Plugin Name: Game Ratings
Plugin URI:  N/A
Description: Game rating plugin
Version:     20170711
Author:      Hitankar Eay
Author URI:  mailto:hitankar@gmail.com
License:     IP
License URI: N/A
Text Domain: optilab
Domain Path: /languages
*/

/**
 * Do not edit anything in this file unless you know what you're doing
 */

define('PLUGIN_BASEPATH', plugin_dir_path(__FILE__));
define('PLUGIN_BASEURL', plugin_dir_url(__FILE__));


/**
 * Helper function for prettying up errors
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('Sage &rsaquo; Error', 'sage');
    $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('5.6.4', phpversion(), '>=')) {
    $_error(__('You must be using PHP 5.6.4 or greater.', 'sage'), __('Invalid PHP version', 'sage'));
}

/**
 * Ensure compatible version of WordPress is used
 */
if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
    $_error(__('You must be using WordPress 4.7.0 or greater.', 'sage'), __('Invalid WordPress version', 'sage'));
}

/**
 * Ensure dependencies are loaded
 */
if (!class_exists('Optilab\\Container')) {
    if (!file_exists($composer = __DIR__.'/vendor/autoload.php')) {
        $_error(
            __('You must run <code>composer install</code> from the Sage directory.', 'sage'),
            __('Autoloader not found.', 'sage')
        );
    }
    require_once $composer;
}

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($_error) {
    $file = "app/{$file}.php";
    require_once(plugin_dir_path(__FILE__). $file);
}, ['helpers', 'setup', 'filters', 'admin']);

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

function activate() {
    flush_rewrite_rules( true );
}
