<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class MLX_Chat_Box {

	private static $instance = null;

	/** Option key */
	const OPTION_KEY = 'mlx_chat_box_options';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function init() {
		//require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box-i18n.php';
		require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box-cpt.php';
		require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box-admin.php';
		require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box-frontend.php';

		//MLX_Chat_Box_I18n::init();
		MLX_Chat_Box_CPT::init();

		if ( is_admin() ) {
			MLX_Chat_Box_Admin::init();
		} else {
			MLX_Chat_Box_Frontend::init();
		}
	}

	public static function activate() {
		$defaults = self::default_options();

		$existing = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$merged = wp_parse_args( $existing, $defaults );
		update_option( self::OPTION_KEY, $merged, false );

		// Ensure CPT is registered for rewrite rules.
		require_once MLX_CHAT_BOX_DIR . 'includes/class-mlx-chat-box-cpt.php';
		MLX_Chat_Box_CPT::register();
		flush_rewrite_rules();
	}

	public static function default_options() {
		return array(
			'enabled'            => 1,

			// Launcher/button
			'launcher_icon_type' => 'dashicon', // dashicon|image
			'launcher_dashicon'  => 'dashicons-whatsapp',
			'launcher_image_id'  => 0,

			// Positioning
			'position_mode'      => 'right', // right|left|custom
			'custom_css_pos'     => 'right: 30px; bottom: 30px;',
			'custom_css_pos_mobile' => 'right: 20px; bottom: 60px;',

            // Launcher styling
            'launcher_size_width'     => '56',
            'launcher_size_height'    => '56',
            'launcher_size_width_mobile'  => '32',
            'launcher_size_height_mobile' => '32',
            'launcher_icon_color'   => '#ffffff',
            'launcher_bg_color'     => '#25D366',
            'launcher_border_width' => 0,        // px
            'launcher_border_color' => '#25D366',
            'launcher_border_radius'=> 999,      // px (999 => pill/circle)

            // Trigger selectors
            'trigger_selector'   => '.mlx-chat-open', // elements matching this selector will open the panel

			// Colors
			'primary_color'      => '#25D366',
			'text_color'         => '#111111',
			'panel_bg'           => '#ffffff',

			// Contact destination
			'contact_mode'       => 'whatsapp', // whatsapp|custom
			'whatsapp_number'    => '', // digits with country code, e.g. 905xxxxxxxxx
			'custom_url'         => '',

			// Messages/templates
			'header_title'       => __( 'Need help?', 'modulux-chat-box' ),
			'search_placeholder' => __( 'Search…', 'modulux-chat-box' ),
			'offline_message'    => __( 'We are currently offline. Please contact us during business hours.', 'modulux-chat-box' ),
			'contact_label'      => __( 'Contact us', 'modulux-chat-box' ),

            'require_confirm' => 1,
            'confirm_text'    => __( 'My question/answer is not listed here.', 'modulux-chat-box' ),

			'product_template'   => 'Hi, I am writing for {product_title} {sku}. Can you please help? {url}',

			// Hours
			'use_hours'          => 0, // 0 no limit
			'hours'              => array(
				'monday'    => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'tuesday'   => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'wednesday' => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'thursday'  => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'friday'    => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'saturday'  => array( 'enabled' => 1, 'start' => '09:00', 'end' => '18:00' ),
				'sunday'    => array( 'enabled' => 0, 'start' => '09:00', 'end' => '18:00' ),
			),
		);
	}

	public static function get_options() {
		$defaults = self::default_options();
		$opts     = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $opts ) ) {
			$opts = array();
		}
		return wp_parse_args( $opts, $defaults );
	}
}
