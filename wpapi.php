<?php

/** Plugin Name: WP-API ... */

Subscribe::check_direct_access();

final class WpApi {
	protected static $_instance;

	public static function getInstance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new static;
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
		if ( $this->is_request( 'admin' ) ) {
			include SUBSCRIBE_DIR . 'inc' . DS . 'admin' . DS . 'class-admin.php';
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
				break;
			case 'ajax':
				return defined( 'DOING_AJAX' );
				break;
			case 'frontend':
				return ! is_admin();
				break;
		}
	}

	private function init() {
		add_action( 'init', [ $this, 'register_routes' ] );
		add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
		add_action( 'parse_request', [ $this, 'parse_request' ] );
		register_activation_hook( __FILE__, array( $this, 'subscribe_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'subscribe_deactivation' ) );
	}

	private function register_routes() {
		add_rewrite_rule( '^api\/([\w0-9]+)\/([\w]+)\/([\w]+)',
			'index.php?api=1&version=$matches[1]&class=$matches[2]&method=$matches[3]',
			'top' );
		flush_rewrite_rules();
	}

	public function register_query_vars( $vars ) {
		$vars[] = 'api';
		$vars[] = 'version';
		$vars[] = 'class';
		$vars[] = 'method';

		return $vars;
	}

	public function parse_request( $query ) {
		if ( isset( $query->query_vars['api'] ) && intval( $query->query_vars['api'] == 1 ) ) {
			$version         = $query->query_vars['version'];
			$class           = $query->query_vars['class'];
			$method          = $query->query_vars['method'];
			$full_class_path = "\\App\\" . $version . "Controllers" . "\\" . ucfirst( $class ) . 'sController';
			$target_class    = new $full_class_path;
			if ( method_exists( $target_class, $method )){
				$target_clas = {$method}();
			}
			exit();
		}
	}

	/**
	 * Run once when plugin active
	 *
	 * @return void
	 */
	public function subscribe_activation() {

	}

	/**
	 * Run once when plugin deactivate
	 *
	 * @return void
	 */
	public function subscribe_deactivation() {

	}

	/**
	 * Check direct access in all files
	 *
	 * @return void
	 */
	public static function check_direct_access() {
		defined( 'ABSPATH' ) || exit( 'NO ACCESS!!!' );
	}

}

WpApi::getInstance();