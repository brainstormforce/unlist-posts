<?php
/**
 * Plugin Name:     Unlist Posts & Pages
 * Plugin URI:      https://github.com/Nikschavan/hide-post
 * Description:     Unlist Posts from dispying anywhere on the site, only access the post with a direct link.
 * Author:          Brainstorm Force
 * Author URI:      https://www.brainstormforce.com
 * Text Domain:     unlist-posts
 * Domain Path:     /languages
 * Version:         1.0.3
 *
 * @package         Hide_Post
 */

defined( 'ABSPATH' ) or exit;

define( 'UNLIST_POSTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'UNLIST_POSTS_URI', plugins_url( '/', __FILE__ ) );
define( 'UNLIST_POSTS_VER', '1.0.3' );

require_once UNLIST_POSTS_DIR . 'class-unlist-posts.php';
