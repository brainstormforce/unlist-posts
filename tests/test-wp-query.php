<?php
/**
 * Class TestWPQueryUnlisted
 *
 * @package Unlist_Posts
 */

/**
 * Make sure WP_Query does not return the unlisted posts.
 */
class TestWPQueryUnlisted extends WP_UnitTestCase {

	/**
	 * User ID for a editor user..
	 *
	 * @var int
	 */
	private $editor_user_id;

	/**
	 * Setup the tests class.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->editor_user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);
	}

	/**
	 * A single example test.
	 */
	public function test_adjacent_posts() {
		wp_set_current_user( $this->editor_user_id );

		// Create a post.
		$unlisted_post = self::factory()->post->create();

		$_POST['unlist_posts']       = true;
		$_POST['unlist_post_nounce'] = wp_create_nonce( 'unlist_post_nounce' );
		Unlist_Posts_Admin::instance()->save_meta( $unlisted_post );

		$query = new WP_Query(
			array(
				'post_type' => 'post',
			)
		);

		$this->assertEmpty( $query->posts );
	}
}
