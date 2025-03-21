<?php

namespace Cool_FormKit\Admin\Entries;

use WPForms\Admin\Notice;
use Cool_FormKit\Admin\Entries\CFKEF_Entries_Posts;
use Cool_FormKit\Admin\Register_Menu_Dashboard\CFKEF_Dashboard;

/**
 * Bulk actions on All Forms page.
 *
 * @since 1.7.3
 */
class CFKEF_Post_Bulk_Actions {

	/**
	 * Allowed actions.
	 *
	 * @since 1.7.3
	 *
	 * @const array
	 */
	const ALLOWED_ACTIONS = [
		'trash',
		'restore',
		'delete',
		'empty_trash',
	];

	/**
	 * Forms ids.
	 *
	 * @since 1.7.3
	 *
	 * @var array
	 */
	private $ids;

	/**
	 * Current action.
	 *
	 * @since 1.7.3
	 *
	 * @var string
	 */
	private $action;

	/**
	 * Current view.
	 *
	 * @since 1.7.3
	 *
	 * @var string
	 */
	private $view;

	private $posts_type = 'cfkef-entries';

	/**
	 * Determine if the class is allowed to load.
	 *
	 * @since 1.7.3
	 *
	 * @return bool
	 */
	private function allow_load() {

		// Load only on the `All Forms` admin page.
		return CFKEF_Dashboard::current_screen($this->posts_type);
	}

	/**
	 * Initialize class.
	 *
	 * @since 1.7.3
	 */
	public function init() {
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.7.3
	 */
	private function hooks() {
		$this->view=CFKEF_Entries_Posts::get_view();

		if($this->allow_load()){
			add_action('admin_init', [$this, 'after_admin_init']);
			add_action('removable_query_args', [$this, 'removable_query_args']);
		}
	}

	public function after_admin_init(){
		$this->process();
		$this->notices();
	}

	/**
	 * Process the bulk actions.
	 *
	 * @since 1.7.3
	 */
	private function process() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$this->ids    = isset( $_GET['entry_id'] ) ? array_map( 'absint', (array) $_GET['entry_id'] ) : [];

		$action=isset($_REQUEST['action']) ? str_replace(' ', '_', strtolower($_REQUEST['action'])) : false;

		$this->action = isset( $_REQUEST['action'] ) ? sanitize_key( $action ) : false;

		if ( $this->action === '-1' ) {
			$this->action = ! empty( $_REQUEST['action2'] ) ? sanitize_key( $_REQUEST['action2'] ) : false;
		}

		if($this->action === 'empty_trash'){
			$this->ids = [0];
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( empty( $this->ids ) || empty( $this->action ) ) {
			return;
		}
		
		// Check exact action values.
		if ( ! in_array( $this->action, self::ALLOWED_ACTIONS, true ) ) {
			return;
		}
		
		if ( empty( $_GET['_wpnonce'] ) ) {
			return;
		}
		
		// Check the nonce.
		if (
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'bulk-entries' ) &&
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'bulk-entries' )
			) {
				return;
		}

		// Finally, we can process the action.
		$this->process_action();
	}

	/**
	 * Process action.
	 *
	 * @since 1.7.3
	 *
	 * @uses process_action_trash
	 * @uses process_action_restore
	 * @uses process_action_delete
	 * @uses process_action_empty_trash
	 */
	private function process_action() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$method = "process_action_{$this->action}";

		// Check that we have a method for this action.
		if ( ! method_exists( $this, $method ) ) {
			return;
		}

		if ( empty( $this->ids ) || ! is_array( $this->ids ) ) {
			return;
		}

		$query_args = [];

		if ( count( $this->ids ) === 1 ) {
			$query_args['type'] = 'cfkef_form';
		}

		$result = [];

		foreach ( $this->ids as $id ) {
			$result[ $id ] = $this->$method( $id );
		}

		$count_result = count( array_keys( array_filter( $result ) ) );

		// Empty trash action returns count of deleted forms.
		if ( $method === 'process_action_empty_trash' ) {
			$count_result = $result[1] ?? 0;
		}

		$query_args[ rtrim( $this->action, 'e' ) . 'ed' ] = $count_result;

		// Unset get vars and perform redirect to avoid action reuse.
		wp_safe_redirect(
			add_query_arg(
				$query_args,
				remove_query_arg( [ 'action', 'action2', '_wpnonce', 'form_id', 'paged', '_wp_http_referer' ] )
			)
		);
		exit;
	}

	/**
	 * Trash the form.
	 *
	 * @since 1.7.3
	 *
	 * @param int $id Form ID to trash.
	 *
	 * @return bool
	 */
	private function process_action_trash( $id ) {
		if ( ! current_user_can( 'delete_post', $id ) ) {
			return false; // User does not have permission to move this post to trash
		}

		// Check if the post exists
		if ( get_post_status( $id ) ) {
			// Move the post to trash
			wp_trash_post( $id );
			return true; // Move to trash successful
		}

		return false; // Post not found
	}

	/**
	 * Restore the form.
	 *
	 * @since 1.7.3
	 *
	 * @param int $id Form ID to restore from trash.
	 *
	 * @return bool
	 */
	private function process_action_restore( $id ) {

		if ( ! current_user_can( 'edit_post', $id ) ) {
			return false; // User does not have permission to restore this post
		}

		// Check if the post exists and is in trash
		if ( get_post_status( $id ) === 'trash' ) {
			// Restore the post with publish status
			wp_update_post( [
				'ID' => $id,
				'post_status' => 'publish',
			] );
			return true; // Restore successful
		}

		return false; // Post not found or not in trash
	}

	/**
	 * Delete the form.
	 *
	 * @since 1.7.3
	 *
	 * @param int $id Form ID to delete.
	 *
	 * @return bool
	 */
	private function process_action_delete( $id ) {
		if ( ! current_user_can( 'delete_post', $id ) ) {
			return false; // User does not have permission to delete the post
		}

		// Check if the post exists
		if ( get_post_status( $id ) ) {
			// Permanently delete the post
			wp_delete_post( $id, true );
			return true; // Deletion successful
		}

		return false; // Post not found
	}

	/**
	 * Empty trash.
	 *
	 * @since 1.7.3
	 *
	 * @param int $id Form ID. This parameter is not used in this method,
	 *                but we need to keep it here because all the `process_action_*` methods
	 *                should be called with the $id parameter.
	 *
	 * @return bool
	 */
	private function process_action_empty_trash( $id ) {
		$posts = get_posts([
			'post_type' => $this->posts_type,
			'post_status' => 'trash',
			'posts_per_page' => -1,
		]);

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
        $search= isset($_GET['cfkef-entries-search']) ? sanitize_text_field($_GET['cfkef-entries-search']) : '';
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

		$args = [
			'post_type' => $this->posts_type,
			'post_status' => array('trash'),
			's' => $search,
		];

		global $wpdb;

		$post_placeholders=implode(',', array_fill(0, count($args['post_status']), "%s"));

        $post_status_query = $wpdb->prepare("post_status IN ($post_placeholders)", array_map('esc_sql', $args['post_status']));


        $query = $wpdb->prepare(
            "SELECT * FROM $wpdb->posts WHERE post_type = '%s' AND $post_status_query",
            $this->posts_type,
        );

        if(!empty($search)){
            $query .= $wpdb->prepare(" AND post_title LIKE '%%%s%%'", $wpdb->esc_like($search));
        }

		$posts=$wpdb->get_results($wpdb->prepare($query));
		
		foreach($posts as $post){
			if ( ! current_user_can( 'delete_post', $post->ID ) ) {
				return false; // User does not have permission to delete the post
			}
			wp_delete_post( $post->ID, true );
		}

		return true; // Deletion successful
	}

	/**
	 * Define bulk actions available for forms overview table.
	 *
	 * @since 1.7.3
	 *
	 * @return array
	 */
	public function get_dropdown_items() {

		$items = [];

		if ( $this->view === 'trash' ) {
			$items = [
				'restore' => esc_html__( 'Restore', 'wpforms-lite' ),
				'delete'  => esc_html__( 'Delete Permanently', 'wpforms-lite' ),
			];
		} else {
			$items = [
				'trash' => esc_html__( 'Move to Trash', 'wpforms-lite' ),
			];
		}

		// phpcs:disable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity

		/**
		 * Filters the Bulk Actions dropdown items.
		 *
		 * @since 1.7.5
		 *
		 * @param array $items Dropdown items.
		 */
		$items = apply_filters( 'cfkef_admin_entries_bulk_actions_get_dropdown_items', $items );

		// phpcs:enable WPForms.Comments.ParamTagHooks.InvalidParamTagsQuantity

		if ( empty( $items ) ) {
			// We should have dummy item, otherwise, WP will hide the Bulk Actions Dropdown,
			// which is not good from a design point of view.
			return [
				'' => '&mdash;',
			];
		}

		return $items;
	}

	/**
	 * Admin notices.
	 *
	 * @since 1.7.3
	 */
	public function notices() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// phpcs:disable WordPress.Security.NonceVerification
		$results = [
			'trashed'    => ! empty( $_REQUEST['trashed'] ) ? sanitize_key( $_REQUEST['trashed'] ) : false,
			'restored'   => ! empty( $_REQUEST['restored'] ) ? sanitize_key( $_REQUEST['restored'] ) : false,
			'deleted'    => ! empty( $_REQUEST['deleted'] ) ? sanitize_key( $_REQUEST['deleted'] ) : false,
			'type'       => ! empty( $_REQUEST['type'] ) ? sanitize_key( $_REQUEST['type'] ) : 'cfkef_form',
		];
		// phpcs:enable WordPress.Security.NonceVerification

		// Display notice in case of error.
		if ( in_array( 'error', $results, true ) ) {
			add_action( 'cfkef_admin_notices', function() {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Security check failed. Please try again.', 'cool-formkit' ) . '</p></div>';
			});

			return;
		}

		$this->notices_success( $results );
	}

	/**
	 * Admin success notices.
	 *
	 * @since 1.7.3
	 *
	 * @param array $results Action results data.
	 */
	private function notices_success( array $results ) {

		$type = $results['type'] ?? '';

		if ( $type !== 'cfkef_form' ) {
			return;
		}

		$method  = "get_notice_success_for_{$type}";
		$actions = [ 'trashed', 'restored', 'deleted'];

		foreach ( $actions as $action ) {
			$count = (int) $results[ $action ];

			if ( ! $count ) {
				continue;
			}

			$notice = $this->$method( $action, $count );

			if ( ! $notice ) {
				continue;
			}

			add_action( 'cfkef_admin_notices', function() use ($notice) {
				echo '<div class="notice notice-success"><p>' . esc_html($notice) . '</p></div>';
			});
		}
	}

	/**
	 * Remove certain arguments from a query string that WordPress should always hide for users.
	 *
	 * @since 1.7.3
	 *
	 * @param array $removable_query_args An array of parameters to remove from the URL.
	 *
	 * @return array Extended/filtered array of parameters to remove from the URL.
	 */
	public function removable_query_args( $removable_query_args ) {

		$removable_query_args[] = 'trashed';
		$removable_query_args[] = 'restored';
		$removable_query_args[] = 'deleted';
		$removable_query_args[] = 'type';

		return $removable_query_args;
	}

	/**
	 * Get notice success message for form.
	 *
	 * @since 1.9.2.3
	 *
	 * @param string $action Action type.
	 * @param int    $count  Count of forms.
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private function get_notice_success_for_cfkef_form( string $action, int $count ): string {

		switch ( $action ) {
			case 'restored':
				/* translators: %1$d - restored forms count. */
				$notice = _n( '%1$d form has been restored successfully.', '%1$d forms have been restored successfully.', $count, 'cool-formkit' );
				break;

			case 'deleted':
				/* translators: %1$d - deleted forms count. */
				$notice = _n( '%1$d form has been permanently deleted.', '%1$d forms have been permanently deleted.', $count, 'cool-formkit' );
				break;

			case 'trashed':
				/* translators: %1$d - trashed forms count. */
				$notice = _n( '%1$d form has been moved to Trash.', '%1$d forms have been moved to Trash.', $count, 'cool-formkit' );
				break;

			default:
				// phpcs:ignore WPForms.Formatting.EmptyLineBeforeReturn.AddEmptyLineBeforeReturnStatement
				return '';
		}

		return sprintf( $notice, $count );
	}
}
