<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/public
 * @author     Developer Junayed <admin@easeare.com>
 */
class Member_Registration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'member_registration_form', [$this, 'member_registration_form_callback'] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Member_Registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Member_Registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/member-registration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Member_Registration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Member_Registration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/member-registration-public.js', array( 'jquery' ), $this->version, false );

	}

	function member_registration_form_callback(){
		ob_start();
		require_once plugin_dir_path( __FILE__ )."partials/member-registration-public-display.php";
		return ob_get_clean();
	}

	function send_email($data){
		$email = $data['email'];
		$subject = "New Member";
		$emlData = [
			'logo' => get_option('mr_email_logo'),
			'body' => $data['body'],
			'footer' => ((get_option('mr_email_footer_text')) ? get_option('mr_email_footer_text') : '@2022 '.get_bloginfo( 'name' ).' inc.' )
		];
		$body = mr_email_template($emlData);

		$headers = array('Content-Type: text/html; charset=UTF-8');
		 
		wp_mail( $email, $subject, $body, $headers );
	}

	function form_submission(){
		if(isset($_POST['mrform_submit'])){
			if(!wp_verify_nonce( $_POST['registration_nonce'], 'mrnonce' )){
				return;
			}

			global $wpdb,$globalError;

			if(isset($_POST['captcha-1']) && isset($_POST['captcha-2']) && isset($_POST['captcha_value'])){
				$c1 = intval($_POST['captcha-1']);
				$c2 = intval($_POST['captcha-2']);
				$ans = intval($_POST['captcha_value']);

				if(($c1 + $c2) === $ans){
					if(isset($_POST['mrfileds'])){
						delete_transient( 'mrreg_success' );

						$formData = $_POST['mrfileds'];
		
						$formData = array_map(function($field){
							return is_array($field) ? array_map(function($el){
								return sanitize_text_field( stripslashes($el) );
							}, $field) : sanitize_text_field( stripslashes($field) );
						}, $formData);
		
						$nameID = null;
						$emailID = null;
						$phoneID = null;
		
						$formSetting = get_option( 'member_register_form_data' );
						if(($formSetting && is_array($formSetting))){
							foreach($formSetting as $setting){ 
								if($setting['type'] === 'name' && $nameID === null){
									$nameID = $setting['id'];
								}
								if($setting['type'] === 'email' && $emailID === null){
									$emailID = $setting['id'];
								}
								if($setting['type'] === 'phone' && $phoneID === null){
									$phoneID = $setting['id'];
								}
							}
						}
		
						$fname = $formData[$nameID]['fname'];
						$lname = $formData[$nameID]['lname'];
						$email = $formData[$emailID];
						$phone = $formData[$phoneID];

						$formData = base64_encode(serialize($formData));
		
						$wpdb->insert($wpdb->prefix.'member_records', array(
							'fname' => $fname,
							'lname' => $lname,
							'email' => $email,
							'phone' => $phone,
							'data' => $formData
						));

						$emlData = [
							'email' => $email,
							'body' => 'Congratulations! You are successfully registered.'
						];

						if(!is_wp_error( $wpdb ) && $wpdb->insert_id){
							$this->send_email($emlData);

							set_transient( 'mrreg_success', 'You are successfully registered.', 30 );
							if(isset($_POST['current_page'])){
								ob_start();
								wp_safe_redirect( $_POST['current_page'] );
								exit;
							}
						}else{
							$globalError = array('type' => 'error', 'msg' => "Something is wrong, try again.");
						}
					}
				}else{
					$globalError = array('type' => 'error', 'msg' => "Wrong captcha answer.");
				}
			}else{
				$globalError = array('type' => 'error', 'msg' => "Wrong captcha answer.");
			}
		}
	}
}
