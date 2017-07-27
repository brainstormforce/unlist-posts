<?php
/**
 * Bootstrap the plugin
 *
 * @package  Hide_Post
 * @since  1.0.0
 */

/**
 * Unlist_Posts setup
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Unlist_Posts' ) ) {

	/**
	 * Class Unlist_Posts
	 *
	 * @since  1.0.0
	 */
	class Unlist_Posts {

		/**
		 * Instance of Unlist_Posts
		 *
		 * @since  1.0.0
		 * @var Unlist_Posts
		 */
		private static $_instance = null;

		/**
		 * Instance of Unlist_Posts
		 *
		 * @since  1.0.0
		 * @return Unlist_Posts Instance of Unlist_Posts
		 */
		public static function instance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			$this->includes();
		}

		/**
		 * Initialize the plugin only when the WP_Query is ready.
		 *
		 * @since  1.0.0
		 */
		public function init() {
			add_filter( 'posts_where', array( $this, 'where_clause' ), 20, 2 );
			add_filter( 'get_next_post_where', array( $this, 'post_navigation_clause' ), 20, 1 );
			add_filter( 'get_previous_post_where', array( $this, 'post_navigation_clause' ), 20, 1 );
			add_action( 'wp_head', array( $this, 'hide_post_from_searchengines' ) );
			add_filter( 'comments_clauses', array( $this, 'comments_clauses' ), 20, 2 );
			add_filter( 'wp_list_pages_excludes', array( $this, 'wp_list_pages_excludes' ) );
		}

		/**
		 * Include required files.
		 *
		 * @since 1.0.0
		 */
		public function includes() {

			if ( is_admin() ) {
				require_once UNLIST_POSTS_DIR . 'class-unlist-posts-admin.php';
			}

		}

		/**
		 * Filter where clause to hide selected posts.
		 *
		 * @since  1.0.0
		 *
		 * @param  String   $where Where clause.
		 * @param  WP_Query $query WP_Query &$this The WP_Query instance (passed by reference).
		 *
		 * @return String $where Where clause.
		 */
		function where_clause( $where, $query ) {
			$hidden_posts = get_option( 'unlist_posts', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || $query->is_singular || empty( $hidden_posts ) ) {
				return $where;
			}

			global $wpdb;
			$where .= ' AND ' . $wpdb->prefix . 'posts.ID NOT IN ( ' . esc_sql( $this->hidden_post_string() ) . ' ) ';

			return $where;
		}

		/**
		 * Filter post navigation query to hide the selected posts.
		 *
		 * @since  1.0.0
		 *
		 * @param  String $where Where clause.
		 */
		function post_navigation_clause( $where ) {
			$hidden_posts = get_option( 'unlist_posts', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || in_array( get_the_ID(), $hidden_posts ) || empty( $hidden_posts ) ) {
				return $where;
			}

			$where .= ' AND p.ID NOT IN ( ' . esc_sql( $this->hidden_post_string() ) . ' ) ';

			return $where;
		}

		/**
		 * Add meta tags to block search engines on a page if the page is unlisted.
		 *
		 * @since  1.0.1
		 */
		public function hide_post_from_searchengines() {
			$hidden_posts = get_option( 'unlist_posts', array() );

			if ( in_array( get_the_ID(), $hidden_posts ) && false !== get_the_ID() ) {
				wp_no_robots();
			}
		}

		/**
		 * Filter where clause to hide selected posts.
		 *
		 * @since  1.0.1
		 *
		 * @param  Array    $clauses Comment Query Clauses.
		 * @param  WP_Query $query WP_Query &$this The WP_Query instance (passed by reference).
		 *
		 * @return String $where Where clause.
		 */
		public function comments_clauses( $clauses, $query ) {

			$hidden_posts = get_option( 'unlist_posts', array() );

			// bail if none of the posts are hidden or we are on admin page or singular page.
			if ( is_admin() || in_array( get_the_ID(), $hidden_posts ) || empty( $hidden_posts ) ) {
				return $clauses;
			}

			global $wpdb;

			$where  = $clauses['where'];
			$where .= ' AND comment_post_ID NOT IN ( ' . esc_sql( $this->hidden_post_string() ) . ' ) ';
			$clauses['where'] = $where;

			return $clauses;
		}

		/**
		 * Exclude the unlisted posts from the wp_list_posts()
		 *
		 * @since  1.0.2
		 * @param  Array $exclude_array Array of posts to be excluded from post list.
		 * @return Array Array of posts to be excluded from post list.
		 */
		public function wp_list_pages_excludes( $exclude_array ) {
			$hidden_posts   = get_option( 'unlist_posts', array() );
			$exclude_array  = array_merge( $exclude_array, $hidden_posts );

			return $exclude_array;
		}

		/**
		 * Convert the array of posts to comma separated string to make it compatible to wpdb query.
		 *
		 * @since  1.0.0
		 *
		 * @return String Comma separated string of post id's.
		 */
		function hidden_post_string() {
			$hidden_posts = get_option( 'unlist_posts', array() );

			return implode( ', ', $hidden_posts );
		}

	}

	Unlist_Posts::instance();
}// End if().
