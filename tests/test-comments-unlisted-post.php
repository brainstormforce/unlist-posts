<?php
/**
 * Class CommentsUnlistedPost
 *
 * @package Unlist_Posts
 */

/**
 * Make sure unlisted posts are hidden from get_adjacent_post().
 */
class CommentsUnlistedPost extends WP_UnitTestCase {
	/**
	 * Test that posts of an unlisted comment are shown.
	 */
	public function test_comments_unlisted_post() {
		$editor_user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);

		wp_set_current_user( $editor_user_id );

		// Create an listed post.
		$listed_post = self::factory()->post->create();

		// Add comments for the post.
		$comment_ids = array();
		for ( $i = 0; $i < 5; $i++ ) {
			$comment_ids[] = self::factory()->comment->create(
				array(
					'comment_post_ID' => $listed_post,
				)
			);
		}

		// Set the post as unlisted.
		$_POST['unlist_posts']       = true;
		$_POST['unlist_post_nounce'] = wp_create_nonce( 'unlist_post_nounce' );
		Unlist_Posts_Admin::instance()->save_meta( $listed_post );

		// Get comments for the post.
		$comments = get_comments(
			array(
				'post_id' => $listed_post,
			)
		);

		$this->assertCount( 5, $comments );
	}
}
