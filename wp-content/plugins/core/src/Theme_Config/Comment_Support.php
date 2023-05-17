<?php declare(strict_types=1);

namespace Tribe\Plugin\Theme_Config;

class Comment_Support {

	/**
	 * Redirect any user trying to access comments page
	 */
	public function admin_comment_page_redirect(): void {
		global $pagenow;

		if ( $pagenow === 'edit-comments.php' || $pagenow === 'comment.php' ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
	}

	/**
	 * Remove comments metabox from dashboard
	 */
	public function remove_recent_comments_metabox(): void {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}

	public function disable_post_type_comment_support(): void {
		foreach ( get_post_types() as $post_type ) {
			if ( ! post_type_supports( $post_type, 'comments' ) ) {
				continue;
			}

			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}

	/**
	 * Remove comments page in menu
	 */
	public function remove_comments_menu_item(): void {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Remove comments links from admin bar
	 */
	public function remove_admin_bar_comments(): void {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
	}

}
