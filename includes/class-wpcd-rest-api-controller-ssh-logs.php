<?php
/**
 * WordPress App WPCD_REST_API_Controller_Ssh_Logs.
 *
 * @package wpcd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPCD_REST_API_Controller_Ssh_Logs
 *
 * Endpoints for interacting with ssh logs
 */
class WPCD_REST_API_Controller_Ssh_Logs extends WPCD_REST_API_Controller_Base {

	/**
	 * Controller base path
	 *
	 * @var string
	 */
	protected $name = 'ssh_logs';

	/**
	 * The post type of the log.
	 *
	 * @var string
	 */
	protected $log_post_name = 'wpcd_ssh_log';

	/**
	 * Implements base method
	 */
	public function register_routes() {
		$this->register_get_route( $this->name, 'list_logs' );
		$this->register_get_route( $this->name . static::RESOURCE_ID_PATH, 'get_log' );
	}

	/**
	 * Lists all ssh_logs
	 *
	 * GET /ssh_logs
	 *
	 * @return array
	 */
	public function list_logs(): array {
		$logs = get_posts(
			array(
				'post_type'      => $this->log_post_name,
				'post_status'    => 'private',
				'posts_per_page' => -1,
			),
		);
		return array_map( array( $this, 'get_log_data' ), $logs );
	}

	/**
	 * Returns a single log with the given ID
	 *
	 * GET /ssh_logs/{id}
	 *
	 * @param WP_REST_Request $request - incoming request object.
	 *
	 * @return array
	 * @throws Exception - SSH Log not found.
	 */
	public function get_log( WP_REST_Request $request ): array {
		$id  = (int) $request->get_param( 'id' );
		$log = $this->get_log_post( $id );
		return $this->get_log_data( $log );
	}

	/**
	 * Fetches post and verifies the correct post type
	 *
	 * @param int $id - requested post id.
	 *
	 * @return WP_Post
	 * @throws Exception - SSH Log not found.
	 */
	protected function get_log_post( int $id ): WP_Post {
		$log = get_post( $id );
		if ( ! ( $log && $this->log_post_name === $log->post_type ) ) {
			throw new Exception( 'SSH Log not found', 400 );
		}
		return $log;
	}

	/**
	 * Builds response data for an ssh log
	 *
	 * @param WP_Post $log - fetched wpcd_ssh_log post.
	 *
	 * @return array
	 */
	protected function get_log_data( WP_Post $log ): array {
		return array(
			'id'             => $log->ID,
			'name'           => $log->post_title,
			'parent_id'      => (int) get_post_meta( $log->ID, 'parent_post_id', true ),
			'ssh_cmd'        => get_post_meta( $log->ID, 'ssh_cmd', true ),
			'ssh_cmd_result' => get_post_meta( $log->ID, 'ssh_cmd_result', true ),
		);
	}

}

