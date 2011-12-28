<?php
/*
Plugin Name: Quick Login
Description: Provides a easy login for development
Version: 0.1.0
Author: Ralf Albert
Author URI: http://yoda.neun12.de
License: GPL
*/

! defined( 'ABSPATH' ) and die( __( 'Cheatin&#8217; uh?' ) );

if( ! class_exists( 'QuickLoginForDev' ) ){

	add_action( 'plugins_loaded', array( 'QuickLoginForDev', 'start_plugin' ) );

	class QuickLoginForDev
	{
		/**
		 * 
		 * Flag. Set to true if plugin was started
		 * @var bool $plugin_self true|false
		 */
		private static $plugin_self = FALSE;
		
		/**
		 * 
		 * Array with users
		 * @var array $users_for_login
		 */
		private $users_for_login = array();
		
		/**
		 * 
		 * Start create an instance of it self
		 * @param none
		 * @return void
		 */
		public static function start_plugin(){
			if( FALSE === self::$plugin_self ){
				self::$plugin_self = TRUE;
				new self;
			}
		}
		
		/**
		 * 
		 * Constructor. Add actions and filters
		 * @uses add_action()
		 * @uses add_filter()
		 * @param none
		 * @return void
		 */
		public function __construct(){
			add_filter( 'login_message', array( &$this, 'list_users' ), 10, 1 );
			add_action( 'login_init', array( &$this, 'catch_login' ) );
		}
		
		/**
		 * 
		 * Create the array with valid users. User => Password
		 * @uses apply_filters()
		 * @param none
		 * @return array $users_for_login
		 */
		public function get_users_for_login(){
			$this->users_for_login = array(
				'&User_1' => '&plkoji19>',
				'!User_2' => '!hugzft28<',
				'>User_3' => '*37rdeswa&',
			);
			
			return apply_filters( 'quicklogin_users_array', $this->users_for_login );
		}
		
		/**
		 * 
		 * Create a list with all users. 
		 * Hooked to the filter 'login_message', so the list wil be printed above the login form
		 * @uses apply_filters()
		 * @param string $message
		 * @return string $message
		 */
		public function list_users( $message ){
	
			if( empty( $this->users_for_login ) )
				$this->get_users_for_login();
				
			$inner = '';
			foreach( $this->users_for_login as $user => $pwd ){
				$data = new stdClass();
				
				$data->url  = site_url( 'wp-login.php' );
				$data->user = urlencode( $user );
				$data->pwd  = urlencode( $pwd );
				$data->name = esc_html( $user );
				
				$inner .= $this->single_line( $data );
			}

			$outer = $this->create_list( $inner );
			return apply_filters( 'quicklogin_list_users', $outer ) . $message;
		}
		
		/**
		 * 
		 * Create the list with users. Expect the single lines as parameter
		 * @uses apply_filters()
		 * @param string $inner
		 * @return string
		 */
		protected function create_list( $inner = '' ){
			$out_format = apply_filters( 'quicklogin_create_list_format', '<div class="message"><h3>QuickLogin</h3><ul>%s</ul></div>' );
			return sprintf( $out_format, $inner );			
		}
		
		/**
		 * 
		 * Create single lines in the users-list
		 * @uses apply_filters()
		 * @param object $data
		 * @return string
		 */
		protected function single_line( $data = NULL ){			
			if(	NULL === $data )
				return '<li>No data present.</li>';
				
			$out_format = apply_filters( 'quick_login_outputformat', '<li><a href="%1$s?username=%2$s&amp;pwd=%3$s">%4$s</a></li>' );
			return sprintf( $out_format, $data->url, $data->user, $data->pwd, $data->name );
		}
		
		/**
		 * 
		 * Catch login data submitted via url
		 * @uses is_wp_error()
		 * @uses wp_signon()
		 * @uses wp_safe_redirect()
		 * @uses admin_url()
		 * @param none
		 * @return void
		 */
		public function catch_login(){
			$user = isset( $_GET['username'] )  ? urldecode( $_GET['username'] ) : FALSE;
			$pwd  = isset( $_GET['pwd'] ) 		? urldecode( $_GET['pwd'] ) 	 : FALSE;
	
			if( FALSE != $user && FALSE != $pwd ){
				is_wp_error( wp_signon(
									  array( 'user_login' => $user,
											 'user_password' => $pwd
											) 
									 )
							)
				?  wp_safe_redirect( site_url( 'wp-login.php' ) ) : wp_safe_redirect( admin_url() );	
			}
		}

	} // .end class QuickLogin

} //.end if-class-exists