<?php

/** Plugin Name: WP-API ... */

WpApi::check_direct_access();

final class WpApi {

	private static $_instance;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
	}

	private function __construct() {
		$this->define_constants();
		$this->do_includes();
		$this->init();
	}

	/**
	 * Define All Constants
	 *
	 * @return void
	 */
	private function define_constants() {
		defined( 'DS' ) || define( 'DS', DIRECTORY_SEPARATOR );
		define( 'WPAPI_DIR', plugin_dir_path( __FILE__ ) );
		define( 'WPAPI_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Check request and then include files
	 *
	 * @return void
	 */
	private function do_includes() {
		if ( $this->is_request( 'frontend' ) ) {
			include WPAPI_DIR . DS . 'App' . DS . 'Repositories' . DS . 'Contracts' . DS . 'BaseRepository.php';
			include WPAPI_DIR . DS . 'App' . DS . 'Repositories' . DS . 'User' . DS . 'UserRepository.php';
			include WPAPI_DIR . DS . 'App' . DS . 'Utility' . DS . 'Response.php';
			include WPAPI_DIR . DS . 'App' . DS . 'v1' . DS . 'Controllers' . DS . 'UsersController.php';
		}
	}

	/**
	 * Check request type
	 *
	 * @param $type
	 *
	 * @return bool|void
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'frontend':
				return ! is_admin();
		}
	}

	private function init(): void {
		register_activation_hook( __FILE__, array( $this, 'wpapi_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'wpapi_deactivation' ) );

		add_action( 'init', [ $this, 'register_routes' ] );
		add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
		add_action( 'parse_request', [ $this, 'parse_request' ] );

	}

	public function register_routes(): void {
		add_rewrite_rule( '^api\/([\w0-9]+)\/([\w]+)\/([\w]+)',
			'index.php?api=1&version=$matches[1]&class=$matches[2]&method=$matches[3]',
			'top' );
		flush_rewrite_rules();
	}

	public function register_query_vars( $vars ): array {
		$vars[] = 'api';
		$vars[] = 'version';
		$vars[] = 'class';
		$vars[] = 'method';

		return $vars;
	}

	public function parse_request( $query ): void {
		if ( isset( $query->query_vars['api'] ) && intval( $query->query_vars['api'] == 1 ) ) {
			$version         = $query->query_vars['version'];
			$class           = $query->query_vars['class'];
			$method          = $query->query_vars['method'];
			$full_class_path = "\\App\\" . $version . DS . "Controllers" . "\\" . ucfirst( $class ) . 'sController';
			$target_class    = new $full_class_path;
			if ( method_exists( $target_class, $method ) ) {
				$target_class->{$method}();
			}

			exit();
		}
	}

	/**
	 * Run once when plugin active
	 *
	 * @return void
	 */
	public function wpapi_activation(): void {
		if ( ! wp_next_scheduled( 'wpvip_optimize_db' ) ) {
			wp_schedule_event( time(), 'daily', 'wpvip_optimize_db' );
		}
	}

	/**
	 * Run once when plugin deactivate
	 *
	 * @return void
	 */
	public function wpapi_deactivation(): void {

	}

	/**
	 * Check direct access in all files
	 *
	 * @return void
	 */
	public static function check_direct_access(): void {
		defined( 'ABSPATH' ) || exit( 'NO ACCESS!!!' );
	}

}

WpApi::get_instance();
