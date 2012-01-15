<?php
/**
 * @author Ralf Albert
 * @version 0.1.2
 * @license GPLv3
 * 
 *  last change: 15/01/2012
 *  
 *  Changelog
 *  
 *  0.1.2
 *   - simpler plugin-start
 *   - fix error on user login
 *   - better stylesheet (gradient, woooo!!!)
 *  
 *  0.1.1
 *   - add stylesheet for output
 *   
 *  0.1.0
 *   - first public release
 */

/**
 * Plugin Name: Quick Login
 * Plugin URI: https://github.com/RalfAlbert/WP-Quicklogin/
 * Description: Provides a easy login for development
 * Version: 0.1.2
 * Author: Ralf Albert
 * Author URI: http://yoda.neun12.de
 * License: GPL
*/

! defined( 'ABSPATH' ) and die( 'Cheatin&#8217; uh?' );

if( ! class_exists( 'QuickLoginForDev' ) ){

	add_action( 'plugins_loaded', array( 'QuickLoginForDev', 'start_plugin' ) );

	class QuickLoginForDev
	{
		/**
		 * 
		 * Showing the role beneath the username
		 * @since 0.1.0
		 * @access public
		 * @var bool
		 */
		CONST SHOW_ROLES = TRUE;
		
		/**
		 * 
		 * Array with users
		 * @since 0.1.0
		 * @access public
		 * @var array $users_for_login
		 */
		private $users_for_login = array();
		
		/**
		 * 
		 * Create an instance of it self
		 * @since 0.1.0
		 * @access public
		 */
		public static function start_plugin(){
				new self;
		}
		
		/**
		 * 
		 * Constructor. Add actions and filters
		 * @since 0.1.0
		 * @access public
		 * @uses add_action()
		 * @uses add_filter()
		 */
		public function __construct(){
			add_filter( 'login_message', array( &$this, 'list_users' ), 10, 1 );
			add_action( 'login_init', array( &$this, 'catch_login' ) );
			add_action( 'login_head', array( &$this, 'print_style' ) );
		}
		
		
		/**
		 * 
		 * Print style outputs a link to the stylesheet in the head of the document
		 * @since 0.1.1
		 * @access public
		 */
		public function print_style(){
			echo '<link rel="stylesheet" type="text/css" href="' . plugins_url( basename( dirname( __FILE__ ) ) ) . '/quicklogin.css' . '" />';
		}
		
		/**
		 * 
		 * Create the array with valid users. User => Password
		 * @since 0.1.0
		 * @access public
		 * @uses apply_filters()
		 * @return array $users_for_login
		 */
		public function get_users_for_login(){
			$this->users_for_login = array(
				'User_1' => '&plkoji19>',
				'User_2' => '!hugzft28<',
				'User_3' => '*37rdeswa&',
			);
			
			return apply_filters( 'quicklogin_users_array', $this->users_for_login );
		}
		
		/**
		 * 
		 * Create a list with all users. 
		 * Hooked to the filter 'login_message', so the list wil be printed above the login form
		 * @since 0.1.0
		 * @access public
		 * @uses apply_filters()
		 * @param string $message
		 * @return string $message
		 */
		public function list_users( $message ){
	
			if( empty( $this->users_for_login ) )
				$this->get_users_for_login();
				
			$inner = '';
			foreach( $this->users_for_login as $user => $pwd ){
				$name = $user; 
				
				if( self::SHOW_ROLES ){
					$user_data = get_user_by( 'login', $user );
					$role = $user_data->roles[0];
					$name = sprintf( '%s (%s)', $user, $role );
				} 
				
				$data = new stdClass();
				$data->url		= site_url( 'wp-login.php' );
				$data->user		= urlencode( $user );
				$data->pwd		= urlencode( $pwd );
				$data->name		= esc_html( $name );
				
				$inner .= $this->single_line( $data );
			}

			$outer = $this->create_list( $inner );
			
			return $outer . $message;
		}
		
		/**
		 * 
		 * Create the list with users. Expect the single lines as parameter
		 * @since 0.1.0
		 * @access public
		 * @uses apply_filters()
		 * @param string $inner
		 * @return string
		 */
		protected function create_list( $inner = '' ){
			$out_format = '<div id="quicklogin"><h3>QuickLogin</h3><ul>%s</ul></div>';
			
			return sprintf( $out_format, $inner );			
		}
		
		/**
		 * 
		 * Create single lines in the users-list
		 * @since 0.1.0
		 * @access public
		 * @uses apply_filters()
		 * @param object $data
		 * @return string
		 */
		protected function single_line( $data = NULL ){			
			if(	NULL === $data )
				return '<li>No data present.</li>';
				
			$out_format = '<li><a class="ql_button play" href="%1$s?username=%2$s&amp;pwd=%3$s">%4$s</a></li>';
			
			return sprintf( $out_format, $data->url, $data->user, $data->pwd, $data->name );
		}
		
		/**
		 * 
		 * Catch login data submitted via url
		 * @since 0.1.0
		 * @access public
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
} // .end if-class-exists