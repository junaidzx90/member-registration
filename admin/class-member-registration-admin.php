<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Member_Registration
 * @subpackage Member_Registration/admin
 * @author     Developer Junayed <admin@easeare.com>
 */
class Member_Registration_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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
		if(isset($_GET['page']) && ($_GET['page'] === 'form-settings' || $_GET['page'] === 'member-registration')){
			wp_enqueue_style( 'mrfontawesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/member-registration-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
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

		if(isset($_GET['page']) && $_GET['page'] === 'form-settings'){
			wp_enqueue_script("jquery-ui-draggable");
			wp_enqueue_script("jquery-ui-droppable"); 

			wp_enqueue_script( 'mr-vue', plugin_dir_url( __FILE__ ) . 'js/vue.min.js', array(  ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/member-registration-admin.js', array( 'jquery', 'jquery-ui-droppable', 'jquery-ui-draggable', 'mr-vue' ), $this->version, true );
			wp_localize_script( $this->plugin_name, 'mrajax', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce( 'mrnonce' )
			) );
		}
	}

	function admin_menu_pages(){
		add_menu_page( "Member registration", "Member regis...", "manage_options", "member-registration", [$this, "member_registration_table"], "dashicons-buddicons-buddypress-logo", 45 );
		add_submenu_page( "member-registration", "Members", "Members", "manage_options", "member-registration", [$this, "member_registration_table"], null );
		add_submenu_page( "member-registration", "Form Settings", "Form Settings", "manage_options", "form-settings", [$this, "mr_form_settings_page"], null );
		add_submenu_page( "member-registration", "Settings", "Settings", "manage_options", "mr-settings", [$this, "mr_settings_page"], null );
		
		add_settings_section( 'mr_setting_section', '', '', 'mr_setting_page' );
		
		add_settings_field( 'mr_shortcode', 'Shortcode', [$this, 'mr_shortcode_cb'], 'mr_setting_page','mr_setting_section' );
		// Captcha label
		add_settings_field( 'mr_captcha_label', 'Captcha label', [$this, 'mr_captcha_label_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_captcha_label' );
		// Button text
		add_settings_field( 'mr_button_text', 'Button text', [$this, 'mr_button_text_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_button_text' );
		// Email logo
		add_settings_field( 'mr_email_logo', 'Email logo', [$this, 'mr_email_logo_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_email_logo' );
		// Email subject
		add_settings_field( 'mr_email_subject', 'Email subject', [$this, 'mr_email_subject_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_email_subject' );
		// Email body
		add_settings_field( 'mr_email_body', 'Email body', [$this, 'mr_email_body_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_email_body' );
		// Email footer text
		add_settings_field( 'mr_email_footer_text', 'Email footer text', [$this, 'mr_email_footer_text_cb'], 'mr_setting_page','mr_setting_section' );
		register_setting( 'mr_setting_section', 'mr_email_footer_text' );
	}

	function mr_shortcode_cb(){
		echo '<input type="text" readonly value="[member_registration_form]">';
	}
	function mr_captcha_label_cb(){
		echo '<input type="text" placeholder="Captcha" name="mr_captcha_label" value="'.get_option('mr_captcha_label').'">';
	}
	function mr_button_text_cb(){
		echo '<input type="text" placeholder="Register" name="mr_button_text" value="'.get_option('mr_button_text').'">';
	}
	function mr_email_logo_cb(){
		echo '<input type="url" placeholder="Logo URL" name="mr_email_logo" value="'.get_option('mr_email_logo').'" class="widefat">';
	}
	function mr_email_subject_cb(){
		echo '<input type="text" placeholder="Subject" name="mr_email_subject" value="'.get_option('mr_email_subject').'" class="widefat">';
		echo '<p>Use <code>{name}, {email}, {phone}</code> as a placeholder.</p>';
	}
	function mr_email_body_cb(){
		wp_editor( wpautop( get_option('mr_email_body') ), 'mr_email_body', [
			'media_buttons' => false,
			'editor_height' => 200,
			'textarea_name' => 'mr_email_body'
		] );
		echo '<p>Use <code>{name}, {email}, {phone}</code> as a placeholder.</p>';
	}
	function mr_email_footer_text_cb(){
		echo '<input type="text" placeholder="@2022 '.get_bloginfo( 'name' ).' inc." name="mr_email_footer_text" value="'.get_option('mr_email_footer_text').'" class="widefat">';
	}

	function member_registration_table(){
		if(isset($_GET['page']) && $_GET['page'] === 'member-registration' && isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['member']) && !empty($_GET['member'])){
			require_once plugin_dir_path( __FILE__ )."partials/member-view.php";
		}else{
			$members = new MembersTbl();
			?>
			<div class="wrap" id="members-table">
				<h3 class="heading3">Members</h3>
				<hr>
				
				<form action="" method="post">
					<?php
					$members->prepare_items();
					$members->display();
					?>
				</form>
			</div>
			<?php
		}
	}
	function mr_form_settings_page(){
		require_once plugin_dir_path( __FILE__ )."partials/member-registration-admin-settings.php";
	}
	function mr_settings_page(){
		?>
		<div id="members">
			<h3>Settings</h3>
			<hr>
			<div style="width: 75%" class="members-content">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'mr_setting_section' );
					do_settings_sections( 'mr_setting_page' );
					?>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<?php
	}

	function save_settings(){
		if(!wp_verify_nonce( $_POST['nonce'], 'mrnonce' )){
			die("Invalid request");
		}

		update_option( 'member_register_form_data', '' );
		if(isset($_POST['data'])){
			$data = $_POST['data'];

			$data = array_map(function($el){
				return array_map(function($v){
					return is_array($v) ? array_map(function($i){
						return sanitize_text_field( stripslashes($i) );
					}, $v) : sanitize_text_field( stripslashes($v) );
				}, $el);
			}, $data);

			update_option( 'member_register_form_data', $data );
			echo json_encode(array("success" => "Success"));
			die;
		}
	}

	function get_settings_data(){
		if(!wp_verify_nonce( $_GET['nonce'], 'mrnonce' )){
			die("Invalid request");
		}

		echo json_encode(array('success' => get_option( 'member_register_form_data' )));
		die;
	}
}
